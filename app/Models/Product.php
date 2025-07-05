<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'price',
        'sale_price',
        'stock_quantity',
        'manage_stock',
        'in_stock',
        'status',
        'images',
        'attributes',
        'material',
        'color',
        'size',
        'weight',
        'dimensions',
        'warranty',
        'is_featured',
        'views_count',
        'rating',
        'reviews_count',
        'category_id',
        'created_by'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'rating' => 'decimal:1',
        'manage_stock' => 'boolean',
        'in_stock' => 'boolean',
        'is_featured' => 'boolean',
        'images' => 'array',
        'attributes' => 'array',
        'stock_quantity' => 'integer',
        'views_count' => 'integer',
        'reviews_count' => 'integer'
    ];

    /**
     * Relación con categoría
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación con el usuario que creó el producto
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope para productos activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para productos destacados
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope para productos en stock
     */
    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

    /**
     * Scope para productos por categoría
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope para buscar productos
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'ILIKE', "%{$search}%")
              ->orWhere('description', 'ILIKE', "%{$search}%")
              ->orWhere('sku', 'ILIKE', "%{$search}%");
        });
    }

    /**
     * Generar slug automáticamente
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'TRA-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->getOriginal('slug'))) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Obtener URL del producto
     */
    public function getUrlAttribute()
    {
        return route('products.show', $this->slug);
    }

    /**
     * Obtener precio final (con descuento si aplica)
     */
    public function getFinalPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Verificar si tiene descuento
     */
    public function hasDiscount()
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }

    /**
     * Obtener porcentaje de descuento
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->hasDiscount()) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    /**
     * Obtener primera imagen
     */
    public function getMainImageAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            return asset('storage/products/' . $this->images[0]);
        }
        return asset('images/default-product.jpg');
    }

    /**
     * Obtener todas las imágenes con URLs completas
     */
    public function getImageUrlsAttribute()
    {
        if (!$this->images) return [];
        
        return collect($this->images)->map(function ($image) {
            return asset('storage/products/' . $image);
        })->toArray();
    }

    /**
     * Verificar si está en stock
     */
    public function isInStock()
    {
        if (!$this->manage_stock) {
            return $this->in_stock;
        }
        
        return $this->stock_quantity > 0;
    }

    /**
     * Obtener estado del stock
     */
    public function getStockStatusAttribute()
    {
        if (!$this->isInStock()) {
            return 'out_of_stock';
        }
        
        if ($this->manage_stock && $this->stock_quantity <= 5) {
            return 'low_stock';
        }
        
        return 'in_stock';
    }

    /**
     * Incrementar contador de vistas
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Formatear precio para mostrar
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->final_price, 2, ',', '.');
    }

    /**
     * Obtener productos relacionados (misma categoría)
     */
    public function relatedProducts($limit = 4)
    {
        return Product::active()
            ->where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->inStock()
            ->limit($limit)
            ->get();
    }
}
