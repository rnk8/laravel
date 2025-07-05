<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Mostrar lista de productos
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filtros
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('category') && $request->category) {
            $query->byCategory($request->category);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('stock_quantity', '>', 5);
                    break;
                case 'low_stock':
                    $query->where('stock_quantity', '<=', 5)->where('stock_quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', 0);
                    break;
            }
        }

        // Ordenamiento
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $products = $query->paginate(12)->appends(request()->query());
        $categories = Category::active()->ordered()->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Almacenar nuevo producto
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'nullable|unique:products,slug',
            'description' => 'nullable',
            'short_description' => 'nullable|max:500',
            'sku' => 'nullable|unique:products,sku',
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'material' => 'nullable|max:100',
            'color' => 'nullable|max:50',
            'size' => 'nullable|max:50',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|max:100',
            'warranty' => 'nullable|max:100',
            'status' => 'required|in:active,inactive,discontinued',
            'is_featured' => 'boolean',
            'manage_stock' => 'boolean',
            'in_stock' => 'boolean',
        ]);

        // Generar slug si no se proporciona
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Generar SKU si no se proporciona
        if (empty($validated['sku'])) {
            $validated['sku'] = 'TRA-' . strtoupper(Str::random(8));
        }

        $validated['created_by'] = auth()->id();

        $product = Product::create($validated);

        return redirect()->route('products.show', $product)
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Mostrar producto específico
     */
    public function show(Product $product)
    {
        $product->load('category', 'creator');
        $product->incrementViews();
        
        $relatedProducts = $product->relatedProducts(4);

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->ordered()->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Actualizar producto
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'slug' => ['nullable', Rule::unique('products')->ignore($product->id)],
            'description' => 'nullable',
            'short_description' => 'nullable|max:500',
            'sku' => ['nullable', Rule::unique('products')->ignore($product->id)],
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'material' => 'nullable|max:100',
            'color' => 'nullable|max:50',
            'size' => 'nullable|max:50',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|max:100',
            'warranty' => 'nullable|max:100',
            'status' => 'required|in:active,inactive,discontinued',
            'is_featured' => 'boolean',
            'manage_stock' => 'boolean',
            'in_stock' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('products.show', $product)
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Eliminar producto
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    /**
     * API: Obtener productos para autocomplete/búsqueda
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::active()
            ->search($query)
            ->limit(10)
            ->get(['id', 'name', 'sku', 'price', 'images'])
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->formatted_price,
                    'image' => $product->main_image,
                    'url' => $product->url
                ];
            });

        return response()->json($products);
    }

    /**
     * Actualizar stock de producto
     */
    public function updateStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'operation' => 'required|in:set,add,subtract'
        ]);

        switch ($validated['operation']) {
            case 'set':
                $product->stock_quantity = $validated['stock_quantity'];
                break;
            case 'add':
                $product->stock_quantity += $validated['stock_quantity'];
                break;
            case 'subtract':
                $product->stock_quantity = max(0, $product->stock_quantity - $validated['stock_quantity']);
                break;
        }

        $product->in_stock = $product->stock_quantity > 0;
        $product->save();

        return response()->json([
            'success' => true,
            'new_stock' => $product->stock_quantity,
            'stock_status' => $product->stock_status
        ]);
    }

    /**
     * Cambiar estado de producto destacado
     */
    public function toggleFeatured(Product $product)
    {
        $product->is_featured = !$product->is_featured;
        $product->save();

        return response()->json([
            'success' => true,
            'is_featured' => $product->is_featured
        ]);
    }

    /**
     * Exportar productos a CSV
     */
    public function export(Request $request)
    {
        $products = Product::with('category')
            ->when($request->category, function ($query, $category) {
                return $query->byCategory($category);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->get();

        $filename = 'productos_tramontina_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            
            // Encabezados CSV
            fputcsv($file, [
                'ID', 'Nombre', 'SKU', 'Categoría', 'Precio', 'Precio Oferta',
                'Stock', 'Estado', 'Material', 'Color', 'Peso', 'Destacado', 'Fecha Creación'
            ]);

            // Datos
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->sku,
                    $product->category->name ?? '',
                    $product->price,
                    $product->sale_price,
                    $product->stock_quantity,
                    $product->status,
                    $product->material,
                    $product->color,
                    $product->weight,
                    $product->is_featured ? 'Sí' : 'No',
                    $product->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
