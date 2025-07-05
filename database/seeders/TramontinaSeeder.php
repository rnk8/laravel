<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;

class TramontinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador si no existe
        $admin = User::firstOrCreate(
            ['email' => 'admin@tramontina.com'],
            [
                'name' => 'Administrador Tramontina',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Crear categorías principales
        $categories = [
            [
                'name' => 'Utensilios de Cocina',
                'slug' => 'utensilios-cocina',
                'description' => 'Utensilios esenciales para cocinar',
                'icon' => 'utensils',
                'sort_order' => 1
            ],
            [
                'name' => 'Cuchillería',
                'slug' => 'cuchilleria',
                'description' => 'Cuchillos y sets de cuchillos profesionales',
                'icon' => 'knife',
                'sort_order' => 2
            ],
            [
                'name' => 'Sartenes y Ollas',
                'slug' => 'sartenes-ollas',
                'description' => 'Sartenes, ollas y cacerolas de alta calidad',
                'icon' => 'cookware',
                'sort_order' => 3
            ],
            [
                'name' => 'Electrodomésticos',
                'slug' => 'electrodomesticos',
                'description' => 'Pequeños electrodomésticos para el hogar',
                'icon' => 'appliances',
                'sort_order' => 4
            ],
            [
                'name' => 'Accesorios',
                'slug' => 'accesorios',
                'description' => 'Accesorios y complementos para la cocina',
                'icon' => 'accessories',
                'sort_order' => 5
            ]
        ];

        $createdCategories = [];
        foreach ($categories as $categoryData) {
            $category = Category::create($categoryData);
            $createdCategories[] = $category;
        }

        // Crear productos para cada categoría
        $products = [
            // Utensilios de Cocina
            [
                'category' => 'Utensilios de Cocina',
                'products' => [
                    [
                        'name' => 'Set de Utensilios de Acero Inoxidable',
                        'short_description' => 'Set completo de 7 piezas en acero inoxidable',
                        'description' => 'Set de utensilios de acero inoxidable 304, incluye: espumadera, cuchara, tenedor, cucharón, espátula, batidor y soporte. Resistente al calor y apto para lavavajillas.',
                        'price' => 89.90,
                        'sale_price' => 79.90,
                        'stock_quantity' => 25,
                        'material' => 'Acero inoxidable 304',
                        'color' => 'Plateado',
                        'weight' => 850.00,
                        'warranty' => '2 años',
                        'is_featured' => true,
                    ],
                    [
                        'name' => 'Espátula de Silicona Premium',
                        'short_description' => 'Espátula resistente al calor hasta 230°C',
                        'description' => 'Espátula de silicona de grado alimentario con mango ergonómico de acero inoxidable. Resistente al calor, flexible y antiadherente.',
                        'price' => 24.90,
                        'stock_quantity' => 45,
                        'material' => 'Silicona + Acero inoxidable',
                        'color' => 'Rojo',
                        'weight' => 120.00,
                        'warranty' => '1 año',
                    ],
                    [
                        'name' => 'Colador de Pasta de Acero',
                        'short_description' => 'Colador profesional con asas ergonómicas',
                        'description' => 'Colador de acero inoxidable con perforaciones precisas y asas ergonómicas. Ideal para pasta, verduras y legumbres.',
                        'price' => 34.90,
                        'stock_quantity' => 30,
                        'material' => 'Acero inoxidable',
                        'color' => 'Plateado',
                        'weight' => 380.00,
                        'dimensions' => '24 x 12 x 8 cm',
                        'warranty' => '1 año',
                    ]
                ]
            ],
            // Cuchillería
            [
                'category' => 'Cuchillería',
                'products' => [
                    [
                        'name' => 'Cuchillo del Chef Century 8"',
                        'short_description' => 'Cuchillo profesional de acero alemán',
                        'description' => 'Cuchillo del chef de 8 pulgadas con hoja de acero alemán DIN 1.4116. Mango ergonómico de polipropileno con acabado antideslizante.',
                        'price' => 129.90,
                        'sale_price' => 99.90,
                        'stock_quantity' => 15,
                        'material' => 'Acero alemán DIN 1.4116',
                        'color' => 'Negro',
                        'size' => '8 pulgadas',
                        'weight' => 280.00,
                        'warranty' => '25 años',
                        'is_featured' => true,
                    ],
                    [
                        'name' => 'Set de Cuchillos Plenus 4 Piezas',
                        'short_description' => 'Set completo con tabla de corte',
                        'description' => 'Set de 4 cuchillos: chef, sierra, deshuesar y pelador. Incluye tabla de corte de bambú. Hojas de acero inoxidable.',
                        'price' => 189.90,
                        'stock_quantity' => 12,
                        'material' => 'Acero inoxidable',
                        'color' => 'Madera',
                        'weight' => 950.00,
                        'warranty' => '10 años',
                    ],
                    [
                        'name' => 'Cuchillo Santoku Japonés 7"',
                        'short_description' => 'Cuchillo multiuso estilo japonés',
                        'description' => 'Cuchillo Santoku de 7 pulgadas con hoja de acero japonés. Diseño tradicional para cortar, picar y rebanar con precisión.',
                        'price' => 89.90,
                        'stock_quantity' => 8,
                        'material' => 'Acero japonés',
                        'color' => 'Plateado',
                        'size' => '7 pulgadas',
                        'weight' => 220.00,
                        'warranty' => '5 años',
                    ]
                ]
            ],
            // Sartenes y Ollas
            [
                'category' => 'Sartenes y Ollas',
                'products' => [
                    [
                        'name' => 'Sartén Antiadherente Paris 24cm',
                        'short_description' => 'Sartén con revestimiento Starflon T1',
                        'description' => 'Sartén de aluminio con revestimiento antiadherente Starflon T1. Base difusora de calor y mango ergonómico baquelizado.',
                        'price' => 69.90,
                        'sale_price' => 59.90,
                        'stock_quantity' => 35,
                        'material' => 'Aluminio',
                        'color' => 'Negro',
                        'size' => '24 cm',
                        'weight' => 680.00,
                        'warranty' => '3 años',
                        'is_featured' => true,
                    ],
                    [
                        'name' => 'Olla a Presión Solar 4.5L',
                        'short_description' => 'Olla a presión de acero inoxidable',
                        'description' => 'Olla a presión de 4.5 litros en acero inoxidable 18/10. Sistema de seguridad múltiple y válvula de presión controlada.',
                        'price' => 189.90,
                        'stock_quantity' => 18,
                        'material' => 'Acero inoxidable 18/10',
                        'color' => 'Plateado',
                        'size' => '4.5 litros',
                        'weight' => 2100.00,
                        'warranty' => '5 años',
                    ],
                    [
                        'name' => 'Cacerola Allegra 20cm',
                        'short_description' => 'Cacerola con tapa de vidrio templado',
                        'description' => 'Cacerola de aluminio forjado con base difusora. Incluye tapa de vidrio templado con válvula de vapor.',
                        'price' => 89.90,
                        'stock_quantity' => 22,
                        'material' => 'Aluminio forjado',
                        'color' => 'Plateado',
                        'size' => '20 cm',
                        'weight' => 1200.00,
                        'warranty' => '2 años',
                    ]
                ]
            ],
            // Electrodomésticos
            [
                'category' => 'Electrodomésticos',
                'products' => [
                    [
                        'name' => 'Procesadora de Alimentos Megatron',
                        'short_description' => 'Procesadora multiuso 800W',
                        'description' => 'Procesadora de alimentos con motor de 800W. Incluye 12 accesorios: cuchillas, discos, batidor y recipientes de diferentes tamaños.',
                        'price' => 399.90,
                        'sale_price' => 349.90,
                        'stock_quantity' => 8,
                        'material' => 'Acero inoxidable + Plástico ABS',
                        'color' => 'Blanco',
                        'weight' => 3500.00,
                        'warranty' => '3 años',
                        'is_featured' => true,
                    ],
                    [
                        'name' => 'Licuadora Versatile 1200W',
                        'short_description' => 'Licuadora de alta potencia con jarra de vidrio',
                        'description' => 'Licuadora de 1200W con jarra de vidrio de 2.5 litros. 5 velocidades + función pulse. Cuchillas de acero inoxidable.',
                        'price' => 229.90,
                        'stock_quantity' => 14,
                        'material' => 'Vidrio + Acero inoxidable',
                        'color' => 'Negro',
                        'weight' => 4200.00,
                        'warranty' => '2 años',
                    ]
                ]
            ],
            // Accesorios
            [
                'category' => 'Accesorios',
                'products' => [
                    [
                        'name' => 'Tabla de Corte Bambú 35cm',
                        'short_description' => 'Tabla ecológica con canal perimetral',
                        'description' => 'Tabla de corte de bambú natural con canal perimetral para líquidos. Superficie antibacteriana natural y fácil mantenimiento.',
                        'price' => 49.90,
                        'stock_quantity' => 28,
                        'material' => 'Bambú natural',
                        'color' => 'Natural',
                        'size' => '35 x 25 cm',
                        'weight' => 850.00,
                        'warranty' => '1 año',
                    ],
                    [
                        'name' => 'Termómetro Digital para Carnes',
                        'short_description' => 'Termómetro instantáneo con pantalla LCD',
                        'description' => 'Termómetro digital de lectura instantánea para carnes y líquidos. Rango de -50°C a 300°C con precisión de ±1°C.',
                        'price' => 39.90,
                        'stock_quantity' => 3,
                        'material' => 'Acero inoxidable + Plástico ABS',
                        'color' => 'Negro',
                        'weight' => 85.00,
                        'warranty' => '1 año',
                    ]
                ]
            ]
        ];

        // Insertar productos
        foreach ($products as $categoryProducts) {
            $category = Category::where('name', $categoryProducts['category'])->first();
            
            foreach ($categoryProducts['products'] as $productData) {
                $productData['category_id'] = $category->id;
                $productData['created_by'] = $admin->id;
                $productData['status'] = 'active';
                $productData['manage_stock'] = true;
                $productData['in_stock'] = $productData['stock_quantity'] > 0;
                
                // Agregar atributos específicos
                $productData['attributes'] = [
                    'brand' => 'Tramontina',
                    'origin' => 'Brasil',
                    'model' => 'TRA-' . strtoupper(substr($productData['name'], 0, 3)) . rand(100, 999)
                ];

                Product::create($productData);
            }
        }

        $this->command->info('✅ Datos de Tramontina creados exitosamente!');
        $this->command->info('📊 Categorías: ' . Category::count());
        $this->command->info('📦 Productos: ' . Product::count());
        $this->command->info('👤 Usuario admin: admin@tramontina.com / password123');
    }
}
