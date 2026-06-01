<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\Restaurante;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $count = 0;

        foreach ($this->data() as $emailSlug => $productos) {
            $restaurante = Restaurante::whereHas('usuario', fn($q) => $q->where('email', 'like', "contacto@{$emailSlug}%"))->first();
            if (!$restaurante) continue;

            foreach ($productos as $p) {
                Producto::create([
                    'restaurante_id' => $restaurante->id,
                    'nombre' => $p[0],
                    'precio' => $p[1],
                    'stock' => $p[2] ?? 50,
                    'categoria_id' => $p[3] ?? null,
                    'descripcion' => $p[4] ?? '',
                    'foto' => $this->photoFor($p[0]),
                    'activo' => true,
                ]);
                $count++;
            }
        }

        $this->command->info("{$count} productos sembrados correctamente.");
    }

    private function data(): array
    {
        return [
            // ============ McDonald's ============
            'mcdonalds' => [
                ['Big Mac', 29.50, 100, null, 'Dos carnes de res, queso, lechuga, pepinillos y salsa especial'],
                ['Cuarto de Libra con Queso', 27.00, 100, null, 'Cuarto de libra de carne angus con queso fundido'],
                ['McNuggets 10 piezas', 22.00, 80, null, '10 piezas de pollo empanizado con 3 salsas'],
                ['Papas Fritas Grandes', 12.50, 200, null, 'Papas crujientes doradas a la perfección'],
                ['McFlurry Oreo', 15.00, 60, null, 'Helado suave de vainilla con trozos de Oreo'],
                ['Coca-Cola Mediana', 8.00, 200, null, 'Refresco de cola bien frío'],
                ['McPollo', 24.00, 80, null, 'Sándwich de pollo empanizado con lechuga y mayonesa'],
            ],

            // ============ Starbucks ============
            'starbucks' => [
                ['Latte Venti', 28.00, 100, null, 'Café latte con leche vaporizada, tamaño grande'],
                ['Caramel Macchiato', 32.00, 80, null, 'Café con leche, vainilla y drizzle de caramelo'],
                ['Frappuccino Moka', 35.00, 60, null, 'Frappé de café con chocolate y crema batida'],
                ['Cold Brew', 24.00, 80, null, 'Café de extracción fría 20 horas, suave y concentrado'],
                ['Cheesecake de Fresa', 22.00, 30, null, 'Tarta de queso crema con coulis de fresa'],
                ['Croissant de Mantequilla', 18.00, 40, null, 'Croissant horneado, hojaldrado y dorado'],
                ['Té Chai Latte', 26.00, 60, null, 'Té chai especiado con leche vaporizada'],
            ],

            // ============ Burger King ============
            'burgerkinglapaz' => [
                ['Whopper', 32.00, 100, null, 'Carne de res a la parrilla con tomate, lechuga, mayonesa y pepinillos'],
                ['BK Royal Crispy Chicken', 28.00, 80, null, 'Pechuga de pollo empanizada crispy con lechuga y mayonesa'],
                ['Doble Whopper', 42.00, 60, null, 'Dos carnes de res a la parrilla con queso derretido'],
                ['Papas Fritas King', 14.00, 150, null, 'Papas fritas crujientes tamaño grande'],
                ['Onion Rings', 16.00, 80, null, 'Aros de cebolla empanizados y fritos'],
                ['Whopper Jr.', 22.00, 100, null, 'Versión pequeña de la Whopper clásica'],
                ['Sundae de Chocolate', 12.00, 60, null, 'Helado suave con salsa de chocolate'],
            ],

            // ============ KFC ============
            'kfclapaz' => [
                ['Bucket 8 piezas', 68.00, 40, null, '8 piezas de pollo frito receta original'],
                ['Pepsi Personal', 10.00, 200, null, 'Gaseosa Pepsi tamaño personal'],
                ['Puré de Papas con Gravy', 15.00, 100, null, 'Puré cremoso con salsa gravy de la casa'],
                ['Alitas BBQ (6uds)', 32.00, 60, null, 'Alitas bañadas en salsa BBQ ahumada'],
                ['Parrillero Mix', 45.00, 50, null, 'Pechuga, alitas y presa con papas y ensalada'],
                ['Ensalada Coleslaw', 12.00, 80, null, 'Ensalada cremosa de col y zanahoria'],
            ],

            // ============ Pizza Hut ============
            'pizzahut' => [
                ['Pizza Personal Pepperoni', 35.00, 50, null, 'Masa tradicional con pepperoni y queso mozzarella'],
                ['Pizza Mediana Suprema', 68.00, 30, null, 'Pepperoni, jamón, champiñones, pimiento y cebolla'],
                ['Pan de Ajo (6uds)', 18.00, 60, null, 'Pan tostado con mantequilla de ajo y perejil'],
                ['Alitas BBQ (8uds)', 38.00, 40, null, 'Alitas de pollo con salsa BBQ'],
                ['Pizza Familiar Hawaiana', 89.00, 20, null, 'Jamón y piña, masa pan tamaño familiar'],
                ['Cheesy Breadsticks', 22.00, 50, null, 'Tiras de pan con queso derretido y salsa marinara'],
            ],

            // ============ Domino's Pizza ============
            'dominoslapaz' => [
                ['Pizza Mediana Pepperoni', 45.00, 40, null, 'Pepperoni clásico con queso mozzarella'],
                ['Pizza Grande Americana', 79.00, 25, null, 'Pepperoni, salchicha, cebolla y pimiento'],
                ['Chicken Kickes 12uds', 32.00, 50, null, 'Bocaditos de pollo empanizado'],
                ['Brownie de Chocolate', 18.00, 30, null, 'Brownie caliente con helado de vainilla'],
                ['Pizza Personal Hawaiana', 32.00, 40, null, 'Jamón y piña en masa personal'],
                ['Cheesy Bread', 20.00, 50, null, 'Pan con queso fundido y orégano'],
            ],

            // ============ Subway ============
            'subwaylapaz' => [
                ['Sub 15cm Italiano BMT', 35.00, 60, null, 'Salame, pepperoni y jamón con vegetales frescos'],
                ['Sub 15cm Teriyaki de Pollo', 38.00, 50, null, 'Pollo con salsa teriyaki, lechuga y tomate'],
                ['Cookie de Chispas de Chocolate', 6.00, 100, null, 'Galleta recién horneada con chispas de chocolate'],
                ['Wrap de Pavo', 32.00, 40, null, 'Tortilla de harina con pavo, lechuga y aderezo'],
                ['Sub 30cm Veggie Delite', 42.00, 30, null, 'Vegetales frescos con queso en pan integral'],
                ['Ensalada Pollo Asado', 36.00, 30, null, 'Mix de lechugas con pollo asado y crutones'],
            ],

            // ============ Papa John's ============
            'papajohnslapaz' => [
                ['Pizza Mediana The Works', 72.00, 25, null, 'Pepperoni, salchicha, pimiento, cebolla, champiñón y aceituna'],
                ['Pizza Personal Garden', 34.00, 30, null, 'Pimiento, cebolla, champiñón, tomate y aceituna negra'],
                ['Papas Cargadas', 24.00, 40, null, 'Papas fritas con queso cheddar, bacon y cebollín'],
                ['Breadsticks con Salsa', 20.00, 50, null, 'Palitos de ajo con salsa marinara'],
                ['Pizza Familiar Pepperoni', 84.00, 20, null, 'Pepperoni en masa original familiar'],
                ['Coca-Cola 2L', 15.00, 80, null, 'Gaseosa Coca-Cola tamaño familiar'],
            ],

            // ============ Hard Rock Cafe ============
            'hardrockcafe' => [
                ['Legendary Burger', 68.00, 40, null, 'Hamburguesa con carne angus, bacon, queso cheddar y aros de cebolla'],
                ['BBQ Pulled Pork Sandwich', 58.00, 30, null, 'Cerdo desmenuzado con salsa BBQ ahumada'],
                ['NACHOS Supreme', 52.00, 25, null, 'Nachos con carne, queso, guacamole y crema agria'],
                ['Parrillada Mix Hard Rock', 120.00, 15, null, 'Costillas, pollo y carne con papas y ensalada'],
                ['Milkshake de Fresa', 32.00, 30, null, 'Batido cremoso de fresa con crema batida'],
                ['Brownie Hot Fudge', 38.00, 25, null, 'Brownie caliente con helado y salsa de chocolate'],
                ['Classic Burger', 52.00, 50, null, 'Hamburguesa clásica con lechuga, tomate y cebolla'],
            ],

            // ============ TGI Fridays ============
            'tgifridayslapaz' => [
                ['Costillas BBQ Full Rack', 95.00, 20, null, 'Costillares completos bañados en salsa BBQ ahumada'],
                ['Loaded Potato Skins', 38.00, 30, null, 'Cáscaras de papa rellenas de queso, bacon y cebollín'],
                ['Fajitas de Pollo', 62.00, 25, null, 'Tiras de pollo salteadas con pimiento y cebolla, servidas con tortillas'],
                ['Mojito Clásico', 35.00, 50, null, 'Ron, hierbabuena, limón y soda'],
                ['Jack Daniel\'s Burger', 72.00, 30, null, 'Hamburguesa con salsa Jack Daniel\'s, bacon y cheddar'],
                ['Brownie Obsession', 42.00, 20, null, 'Brownie con helado, crema batida y salsa de caramelo'],
                ['Fridays Salad', 44.00, 25, null, 'Ensalada César con pollo a la parrilla y crutones'],
            ],

            // ============ Chili's ============
            'chilis' => [
                ['Baby Back Ribs Half Rack', 78.00, 20, null, 'Costillas baby back glaseadas con salsa BBQ original'],
                ['Quesadilla de Pollo', 42.00, 30, null, 'Tortilla rellena de pollo, queso derretido y pimiento'],
                ['Fajitas Tex-Mex', 68.00, 25, null, 'Carne o pollo salteado con pimiento y cebolla'],
                ["Chili's Nachos", 48.00, 30, null, 'Nachos con chili, queso, jalapeños y crema agria'],
                ['Margarita Frozen', 38.00, 50, null, 'Margarita de limón con hielo picado y sal en el borde'],
                ['Molten Chocolate Cake', 40.00, 20, null, 'Pastel de chocolate con centro fundido y helado'],
            ],

            // ============ Dunkin' ============
            'dunkinlapaz' => [
                ['Donut Glaseado', 8.00, 100, null, 'Donut clásico cubierto de glaseado brillante'],
                ['Donut Chocolate', 9.00, 100, null, 'Donut cubierto de chocolate fundido'],
                ['Café Americano Regular', 14.00, 200, null, 'Café negro recién preparado, tamaño regular'],
                ['Latte Mediano', 20.00, 150, null, 'Café latte con leche entera vaporizada'],
                ['Munchkins 10uds', 12.00, 80, null, 'Bolitas de donut en variedad de sabores'],
                ['Sándwich de Desayuno', 22.00, 50, null, 'Huevo, queso y jamón en pan brioche tostado'],
                ['Iced Coffee', 18.00, 100, null, 'Café frío con hielo y leche opcional'],
            ],

            // ============ Gustu ============
            'gustu' => [
                ['Menú Degustación 8 tiempos', 350.00, 10, null, 'Recorrido gastronómico por los sabores de Bolivia en 8 platos'],
                ['Ceviche de Camarón con Chuño', 85.00, 15, null, 'Camarón fresco con chuño crocante y leche de tigre'],
                ['Lomo de Llama con Quinua', 95.00, 15, null, 'Lomo de llama sellado con costra de quinua y puré de oca'],
                ['Tarta de Chocolate Amazónico', 55.00, 20, null, 'Chocolate silvestre boliviano con helado de copoazú'],
                ['Pisco Sour Boliviano', 45.00, 30, null, 'Singani, limón, clara de huevo y amargo de angostura'],
                ['Carpaccio de Cupesí', 78.00, 12, null, 'Carpaccio de carne de cupesí con láminas de parmesano'],
            ],

            // ============ Ali Pacha ============
            'alipacha' => [
                ['Menú Degustación Vegano', 280.00, 10, null, '8 pasos veganos con ingredientes ancestrales bolivianos'],
                ['Causa de Yuca con Palta', 48.00, 20, null, 'Causa de yuca con palta, tomate confitado y mayonesa de hierbas'],
                ['Risotto de Quinua con Hongos', 62.00, 18, null, 'Risotto cremoso de quinua real con hongos silvestres'],
                ['Lasaña de Verduras Andinas', 55.00, 15, null, 'Lasaña con capas de oca, zanahoria y espinaca con bechamel de castañas'],
                ['Mousse de Cacao Amazónico', 38.00, 20, null, 'Mousse vegano de chocolate con nibs de cacao tostado'],
                ['Smoothie Bowl Antioxidante', 35.00, 20, null, 'Açai, frutos rojos, granola y semillas de chía'],
            ],

            // ============ Popular Cocina Boliviana ============
            'popularcocina' => [
                ['Fricasé Paceño', 42.00, 30, null, 'Cerdo cocido lentamente con ají amarillo, mote y chuño'],
                ['Plato Paceño', 38.00, 30, null, 'Choclo, habas, queso, papa y llajua con carne asada'],
                ['Silpancho Clásico', 35.00, 35, null, 'Carne apanada, arroz, papa, huevo frito y ensalada'],
                ['Chairo Tradicional', 32.00, 25, null, 'Sopa espesa de cordero con chuño, mote y verduras'],
                ['Pique Macho', 55.00, 25, null, 'Carne de res, salchicha, papa frita, locoto y cebolla'],
                ['Api con Pastel', 18.00, 50, null, 'Bebida caliente de maíz morado con pastel de queso'],
            ],

            // ============ Mi Chola ============
            'michola' => [
                ['Salteña Mi Chola', 15.00, 100, null, 'Salteña jugosa de carne, pollo o mixta con salsa picante'],
                ['Anticuchos (3 brochetas)', 40.00, 40, null, 'Corazón de res marinado con papas y ají de maní'],
                ['Pique Macho Especial', 58.00, 25, null, 'Pique macho con lomo, salchicha, huevo y papas fritas'],
                ['Lechón al Horno', 65.00, 20, null, 'Lechón horneado con guarnición de papa, arroz y ensalada'],
                ['Chicha de Maíz Jarra', 25.00, 30, null, 'Chicha tradicional de maíz fermentado'],
                ['Helado de Canela', 14.00, 40, null, 'Helado artesanal con infusión de canela y clavo de olor'],
            ],

            // ============ Biofilia Café ============
            'biofilia' => [
                ['Acai Bowl', 42.00, 30, null, 'Açai, granola, banana, frutos rojos y miel de caña'],
                ['Tostada de Palta Integral', 28.00, 40, null, 'Pan integral con palta, huevo poché y microgreens'],
                ['Café V60 Origen Bolivia', 22.00, 50, null, 'Café filtrado de finca boliviana'],
                ['Smoothie Verde', 28.00, 30, null, 'Espinaca, manzana, jengibre y piña'],
                ['Cheesecake Vegano', 32.00, 20, null, 'Cheesecake sin lácteos con base de nueces y coulis de maracuyá'],
                ['Matcha Latte', 26.00, 40, null, 'Té matcha ceremonial con leche de avena'],
            ],

            // ============ Brutal Burger ============
            'brutalburger' => [
                ['Smash Burger Clásica', 38.00, 50, null, 'Doble carne smash, queso americano, lechuga y pepinillos'],
                ['Bacon Cheese Fries', 28.00, 40, null, 'Papas fritas con queso cheddar, bacon y cebollín'],
                ['BBQ Onion Burger', 42.00, 40, null, 'Carne angus, cebolla caramelizada, bacon y salsa BBQ'],
                ['Milkshake de Oreo', 30.00, 35, null, 'Batido cremoso de vainilla con trozos de Oreo'],
                ['Veggie Burger', 36.00, 30, null, 'Hamburguesa vegetal con portobello, queso suizo y rúcula'],
                ['Chicken Crispy Burger', 36.00, 45, null, 'Pechuga de pollo empanizada con coleslaw y mayonesa ahumada'],
                ['Loaded Nachos', 34.00, 30, null, 'Nachos con carne, queso, guacamole y pico de gallo'],
            ],

            // ============ The Carrot Tree ============
            'thecarrottree' => [
                ['Power Bowl Quinoa', 44.00, 30, null, 'Quinua, palta, camote, garbanzos, espinaca y tahini'],
                ['Sándwich de Pavo y Palta', 36.00, 30, null, 'Pan integral con pavo, palta, tomate y germinados'],
                ['Smoothie de Mango y Jengibre', 26.00, 40, null, 'Batido de mango, jengibre fresco y leche de coco'],
                ['Ensalada César Veggie', 34.00, 30, null, 'Lechuga romana, crutones integrales, parmesano y aderezo de yogur'],
                ['Tarta de Zanahoria', 28.00, 20, null, 'Carrot cake con frosting de queso crema y nueces'],
                ['Chai Latte Especiado', 24.00, 40, null, 'Té chai casero con leche de almendras'],
            ],

            // ============ The Steakhouse ============
            'thesteakhouse' => [
                ['Bife de Chorizo 300g', 98.00, 20, null, 'Corte argentino madurado con guarnición a elección'],
                ['Ojo de Bife 350g', 115.00, 15, null, 'Ojo de bife angus madurado 21 días'],
                ['Entraña a la Parrilla', 72.00, 25, null, 'Entraña marinada con chimichurri y papas rústicas'],
                ['Papas Rústicas con Romero', 22.00, 50, null, 'Papas horneadas con romero y ajo'],
                ['Provoleta', 38.00, 30, null, 'Provolone a la parrilla con orégano, tomate y albahaca'],
                ['Vino Malbec Copa', 35.00, 60, null, 'Malbec argentino copa 180ml'],
                ['Parrillada para 2', 185.00, 10, null, 'Selección de cortes con guarniciones y vino incluido'],
            ],

            // ============ Santo Ramen ============
            'santoramen' => [
                ['Tonkotsu Ramen', 58.00, 30, null, 'Caldo de cerdo 18h, noodles, huevo marinado, chashu y alga nori'],
                ['Shoyu Ramen', 52.00, 30, null, 'Caldo de pollo y salsa de soja, noodles, cebollín y huevo'],
                ['Gyozas de Cerdo (6uds)', 32.00, 40, null, 'Empanaditas japonesas rellenas de cerdo y verduras'],
                ['Edamame con Sal Marina', 18.00, 50, null, 'Edamame al vapor con sal marina y sésamo'],
                ['Baos de Pollo Teriyaki (2uds)', 28.00, 35, null, 'Pan bao esponjoso con pollo teriyaki y pepino encurtido'],
                ['Pudding de Sésamo Negro', 22.00, 25, null, 'Postre cremoso de sésamo negro con jengibre'],
            ],

            // ============ Sach'a Yuntas ============
            'sachayuntas' => [
                ['Fricasé Auténtico', 38.00, 30, null, 'Cerdo cocido con ají amarillo, mote y chuño'],
                ['Pique Macho Sacha', 48.00, 25, null, 'Carne de res salteada con papas fritas, salchicha y locoto'],
                ['Silpancho de Llama', 40.00, 30, null, 'Llama apanada con arroz, papa, huevo y ensalada'],
                ['Chairo Paceño', 28.00, 25, null, 'Sopa tradicional con cordero, chuño y verduras'],
                ['Api Morado con Pastel', 16.00, 50, null, 'Api de maíz morado con pastel de queso frito'],
                ['Mondongo', 42.00, 20, null, 'Cerdo cocido con ají colorado, mote y pepino'],
            ],

            // ============ Yati Bolivia ============
            'yati' => [
                ['Quinua Risotto con Hongos', 52.00, 20, null, 'Quinua real con mix de hongos, parmesano y trufa'],
                ['Lomo de Cordero con Cañahua', 68.00, 15, null, 'Cordero patagónico con costra de cañahua y puré de oca'],
                ['Crema de Tarwi', 32.00, 25, null, 'Crema suave de tarwi con crujiente de quinua y menta'],
                ['Trucha del Titicaca', 58.00, 20, null, 'Trucha sellada con salsa de cítricos y espárragos'],
                ['Mousse de Copoazú', 35.00, 20, null, 'Mousse de copoazú amazónico con nibs de cacao'],
                ['Singani Sour', 42.00, 40, null, 'Singani artesanal, limón, clara de huevo y amargo'],
            ],

            // ============ Terra ============
            'terralapaz' => [
                ['Tartar de Atún con Palta', 62.00, 20, null, 'Atún fresco cortado a cuchillo con palta y salsa de soja'],
                ['Pulpo a la Parrilla', 72.00, 15, null, 'Pulpo tierno a la parrilla con puré de papa morada'],
                ['Risotto de Hongos y Trufa', 55.00, 20, null, 'Risotto cremoso con mix de hongos y aceite de trufa'],
                ['Tiradito Nikkei', 58.00, 18, null, 'Pescado blanco en leche de tigre nikkei con ají limo'],
                ['Panna Cotta de Maracuyá', 32.00, 25, null, 'Panna cotta cremosa con coulis de maracuyá'],
                ['Cóctel Terra', 48.00, 40, null, 'Ginebra, flor de Jamaica, limón y soda premium'],
            ],

            // ============ La Rufina ============
            'larufina' => [
                ['Fricasé Familiar', 55.00, 20, null, 'Fricasé de cerdo para 2 personas con mote y chuño'],
                ['Plato Paceño Especial', 35.00, 35, null, 'Choclo, habas, queso, papa y carne de res'],
                ['Silpancho Paceño', 32.00, 40, null, 'Carne apanada, arroz, papa frita, huevo y ensalada'],
                ['Lechón Entero (x kg)', 90.00, 10, null, 'Lechón horneado al estilo paceño'],
                ['Sopa de Maní', 22.00, 30, null, 'Sopa cremosa de maní con fideos y verduras'],
                ['Api con Pastelito', 16.00, 50, null, 'Api caliente con pastel de queso frito'],
            ],

            // ============ Yerba Buena ============
            'yerbabuena' => [
                ['Buddha Bowl', 44.00, 25, null, 'Quinua, garbanzos, camote, kale, palta y tahini'],
                ['Tostada de Champiñones', 30.00, 30, null, 'Pan de masa madre con champiñones salteados y queso de cabra'],
                ['Smoothie Tropical', 28.00, 40, null, 'Mango, piña, coco y jengibre fresco'],
                ['Omelette de Espinacas', 34.00, 30, null, 'Claras de huevo con espinaca, tomate cherry y queso feta'],
                ['Brownie Vegano', 26.00, 20, null, 'Brownie sin harina ni lácteos con cacao orgánico'],
                ['Golden Milk Latte', 22.00, 40, null, 'Leche dorada con cúrcuma, jengibre y pimienta negra'],
            ],

            // ============ Tinto ============
            'tintolapaz' => [
                ['Tabla de Quesos (3 variedades)', 48.00, 20, null, 'Selección de quesos nacionales con mermelada y frutos secos'],
                ['Tabla de Embutidos Ibéricos', 55.00, 15, null, 'Jamón serrano, chorizo, salchichón y lomo ibérico'],
                ['Copa de Vino Tinto Malbec', 32.00, 60, null, 'Malbec argentino en copa 180ml'],
                ['Copa de Vino Blanco Sauvignon Blanc', 30.00, 50, null, 'Sauvignon Blanc chileno en copa 180ml'],
                ['Bruschetta de Tomate y Albahaca', 24.00, 30, null, 'Pan tostado con tomate fresco, albahaca y aceite de oliva'],
                ['Bondiola Glaseada', 42.00, 20, null, 'Bondiola de cerdo glaseada en miel y mostaza'],
            ],

            // ============ ASAU ============
            'asau' => [
                ['Ceviche Nikkei', 62.00, 20, null, 'Pescado blanco, leche de tigre nikkei, ají limo y canchita'],
                ['Roll de Quinua y Palta', 48.00, 25, null, 'Roll de quinua con palta, queso crema y salsa de soja'],
                ['Lomo Saltado Fusión', 55.00, 20, null, 'Lomo de res salteado con verduras y salsa oriental'],
                ['Tiradito de Trucha', 52.00, 18, null, 'Trucha del Titicaca en leche de tigre con rocoto'],
                ['Maki de Pulpo (8uds)', 68.00, 15, null, 'Maki de pulpo, palta y masago'],
                ['Pisco Sour Clásico', 40.00, 40, null, 'Pisco quebranta, limón, clara de huevo y amargo de angostura'],
            ],

            // ============ La Suisse ============
            'lasuisse' => [
                ['Fondue de Queso para 2', 85.00, 15, null, 'Fondue clásica de queso gruyère y emmental con pan artesanal'],
                ['Raclette Completa', 92.00, 12, null, 'Queso raclette fundido con papas, pepinillos y cebolla'],
                ['Fetuccini Alfredo con Pollo', 48.00, 25, null, 'Fetuccini cremoso con pollo y parmesano'],
                ['Lasaña Clásica', 52.00, 20, null, 'Lasaña de carne con salsa boloñesa y bechamel'],
                ['Tiramisú Casero', 32.00, 25, null, 'Tiramisú tradicional con mascarpone y café expresso'],
                ['Café Suizo', 22.00, 50, null, 'Café con crema batida y virutas de chocolate'],
            ],

            // ============ Pronto Dalicatessen ============
            'prontodalicatessen' => [
                ['Sándwich de Pastrami', 42.00, 30, null, 'Pastrami de res, queso suizo, mostaza y pepinillos en pan de centeno'],
                ['Tabla de Quesos y Vino', 65.00, 15, null, '3 quesos artesanales con vino de la casa y frutos secos'],
                ['Café Latte Artesanal', 20.00, 50, null, 'Café latte con leche vegetal opcional'],
                ['Croissant de Jamón y Queso', 28.00, 30, null, 'Croissant horneado con jamón ahumado y queso gruyère'],
                ['Tarta de Queso New York', 34.00, 20, null, 'Cheesecake estilo New York con coulis de frutos rojos'],
                ['Ensalada Mediterránea', 38.00, 25, null, 'Lechuga, tomate cherry, aceituna, queso feta y aliño de limón'],
            ],

            // ============ El Hornito ============
            'elhornito' => [
                ['Pizza Margherita', 42.00, 30, null, 'Masa madre 48h, mozzarella, tomate San Marzano y albahaca'],
                ['Pizza Pepperoni Picante', 48.00, 25, null, 'Pepperoni, jalapeño y queso mozzarella en masa de horno de leña'],
                ['Pizza Prosciutto', 55.00, 20, null, 'Jamón serrano, rúcula, parmesano y reducción balsámica'],
                ['Calzone de la Casa', 52.00, 15, null, 'Masa rellena de ricotta, espinaca, champiñones y mozzarella'],
                ['Tiramisú Clásico', 30.00, 20, null, 'Tiramisú de mascarpone con cacao amargo'],
                ['Pan de Ajo con Queso', 18.00, 40, null, 'Pan artesanal con mantequilla de ajo y queso parmesano'],
            ],

            // ============ Mochi Mochi ============
            'mochimochi' => [
                ['Rainbow Roll (8uds)', 62.00, 20, null, 'Roll de salmón, palta y queso crema cubierto de pescado variado'],
                ['Dragon Roll (8uds)', 68.00, 18, null, 'Ebi tempura, palta, masago y anguila glaseada'],
                ['Nigiri Mixto (6uds)', 55.00, 15, null, 'Nigiri de salmón, atún, ebi, pez mantequilla, hamachi y tamago'],
                ['Gohan de Salmón', 42.00, 20, null, 'Bol de arroz con salmón fresco, palta y sésamo'],
                ['Edamame con Sriracha', 20.00, 40, null, 'Edamame salteado con aceite de sésamo y sriracha'],
                ['Mochi Helado (3uds)', 28.00, 30, null, 'Mochi relleno de helado de matcha, mango y vainilla'],
            ],

            // ============ Barbarian Burger ============
            'barbarianburger' => [
                ['Barbarian XXL', 55.00, 25, null, 'Triple carne, triple queso, bacon, aros de cebolla y salsa barbarian'],
                ['Smash Burger Clásica', 36.00, 50, null, 'Doble carne smash con queso americano y pepinillos'],
                ['BBQ Pulled Pork Burger', 48.00, 30, null, 'Cerdo desmenuzado BBQ, coleslaw y aros de cebolla'],
                ['Papas Cargadas Barbarian', 32.00, 35, null, 'Papas fritas con queso cheddar, bacon, jalapeño y cebollín'],
                ['Chicken Wrap BBQ', 38.00, 30, null, 'Pollo a la parrilla, queso, tomate y salsa BBQ en tortilla'],
                ['Milkshake de Chocolate', 28.00, 30, null, 'Batido cremoso de chocolate belga con crema batida'],
            ],

            // ============ Fogón Boliviano ============
            'fogonboliviano' => [
                ['Parrillada Fogón para 2', 150.00, 15, null, 'Lomo, costilla, chorizo, morcilla, papa, yuca y ensalada'],
                ['Lomo de Res 300g', 85.00, 20, null, 'Lomo fino a la parrilla con guarnición a elección'],
                ['Costillar de Cerdo BBQ', 72.00, 15, null, 'Costillar completo glaseado con salsa BBQ ahumada'],
                ['Chorizo Criollo (2uds)', 32.00, 30, null, 'Chorizo artesanal a la parrilla con papas y ensalada'],
                ['Morcilla Boliviana (2uds)', 28.00, 25, null, 'Morcilla casera con especias andinas'],
                ['Vino Tinto Copa', 28.00, 50, null, 'Vino tinto boliviano de altura, copa 150ml'],
            ],

            // ============ La Casa del Pique ============
            'lacasadelpique' => [
                ['Pique Macho Clásico', 48.00, 30, null, 'Carne de res, salchicha, papas fritas, locoto, cebolla y huevo'],
                ['Pique Macho Familiar', 85.00, 15, null, 'Pique macho gigante para 3-4 personas'],
                ['Silpancho', 32.00, 40, null, 'Carne apanada con arroz, papa, huevo frito y ensalada'],
                ['Plato Paceño', 30.00, 35, null, 'Choclo, habas, queso, papa con llajua y carne'],
                ['Fricasé de Cerdo', 36.00, 25, null, 'Cerdo cocido en ají amarillo con mote y chuño'],
                ['Refresco de Frutas', 10.00, 100, null, 'Jugo natural de frutas de temporada'],
            ],

            // ============ Café Origen ============
            'cafeorigen' => [
                ['Café Origen V60', 22.00, 60, null, 'Café de especialidad boliviano filtrado V60'],
                ['Cappuccino Clásico', 24.00, 60, null, 'Cappuccino con leche vaporizada y cacao espolvoreado'],
                ['Brunch Completo', 55.00, 20, null, 'Huevos benedictinos, panceta, pan artesanal, fruta y café'],
                ['Tostada de Palta y Huevo', 30.00, 30, null, 'Pan masa madre, palta, huevo poché y microgreens'],
                ['Waffle de Frutas', 36.00, 25, null, 'Waffle belga con frutas frescas, miel y crema batida'],
                ['Smoothie de Frutos Rojos', 28.00, 30, null, 'Fresas, arándanos, moras con yogur griego y miel'],
            ],

            // ============ Nonna Mia ============
            'nonnamia' => [
                ['Ñoquis de Papa con Boloñesa', 44.00, 30, null, 'Ñoquis caseros con salsa boloñesa de res y parmesano'],
                ['Fetuccini Alfredo con Pollo', 48.00, 25, null, 'Fetuccini artesanal con salsa alfredo y pollo grillé'],
                ['Lasaña de Carne', 52.00, 20, null, 'Lasaña clásica con boloñesa, bechamel y mozzarella'],
                ['Spaghetti al Pesto', 42.00, 30, null, 'Spaghetti con pesto de albahaca, piñones y parmesano'],
                ['Tiramisú de la Nonna', 34.00, 25, null, 'Tiramisú con mascarpone, café expreso y cacao amargo'],
                ['Bruschetta Caprese', 22.00, 40, null, 'Pan artesanal con tomate, mozzarella fresca y albahaca'],
            ],

            // ============ Pollos Real ============
            'pollosreal' => [
                ['Pollo Entero al Spiedo', 55.00, 20, null, 'Pollo entero asado al spiedo con papas fritas y ensalada'],
                ['Medio Pollo al Spiedo', 32.00, 30, null, 'Medio pollo con papas fritas y ensalada coleslaw'],
                ['Pechuga a la Parrilla', 38.00, 25, null, 'Pechuga de pollo marinada con vegetales salteados'],
                ['Alitas BBQ (8uds)', 35.00, 30, null, 'Alitas de pollo bañadas en salsa BBQ casera'],
                ['Papas Fritas Grandes', 14.00, 80, null, 'Papas crujientes doradas'],
                ['Ensalada César con Pollo', 34.00, 25, null, 'Lechuga romana, pollo, crutones, parmesano y aderezo césar'],
            ],

            // ============ Dragón Rojo ============
            'dragonrojo' => [
                ['Arroz Chaufa Especial', 38.00, 30, null, 'Arroz frito con pollo, cerdo, huevo, verduras y sillao'],
                ['Wantán Frito (10uds)', 28.00, 40, null, 'Wantanes fritos rellenos de carne y verduras'],
                ['Tallarín Saltado de Carne', 42.00, 30, null, 'Tallarines salteados con lomo de res, verduras y salsa de soja'],
                ['Aeropuerto Dragón', 35.00, 25, null, 'Arroz chaufa con tallarín saltado mixto'],
                ['Sopa Wantán', 22.00, 30, null, 'Sopa de wantanes con verduras y carne de cerdo'],
                ['Roll Primavera (6uds)', 24.00, 35, null, 'Rolls de primavera vegetales fritos con salsa agridulce'],
            ],

            // ============ El Marino ============
            'elmarino' => [
                ['Ceviche Clásico', 52.00, 25, null, 'Pescado fresco marinado en limón, cebolla morada, camote y choclo'],
                ['Parihuela para 2', 85.00, 15, null, 'Caldo de pescado con mariscos, langostinos y almejas'],
                ['Arroz con Mariscos', 55.00, 20, null, 'Arroz marinero con calamar, camarón, mejillón y pescado'],
                ['Tiradito de Pescado', 48.00, 20, null, 'Pescado blanco en leche de tigre con ají limo'],
                ['Pulpo al Olivo', 62.00, 15, null, 'Pulpo cocido a la parrilla con crema de aceituna negra'],
                ['Jugo de Maracuyá', 14.00, 50, null, 'Jugo natural de maracuyá fresco'],
            ],

            // ============ Dulce Tentación ============
            'dulcetentacion' => [
                ['Cheesecake de Maracuyá', 32.00, 20, null, 'Cheesecake cremoso con coulis de maracuyá y base de galleta'],
                ['Torta Selva Negra (porción)', 28.00, 20, null, 'Bizcocho de chocolate con cerezas, crema y virutas de chocolate'],
                ['Macarons (6uds)', 35.00, 30, null, 'Macarons franceses en sabores variados'],
                ['Helado Artesanal 2 bolas', 18.00, 50, null, 'Helado cremoso artesanal de vainilla, chocolate o frutos rojos'],
                ['Mousse de Chocolate', 26.00, 25, null, 'Mousse de chocolate belga con crema batida y frutos rojos'],
                ['Café Expresso', 14.00, 80, null, 'Café expresso italiano intenso'],
            ],

            // ============ Ñami Fusión ============
            'namifusion' => [
                ['Tartar de Atún con Palta', 65.00, 18, null, 'Atún fresco cortado a cuchillo con palta, sésamo y soja'],
                ['Raviolis de Quinua con Hongos', 52.00, 20, null, 'Raviolis artesanales rellenos de quinua y hongos silvestres'],
                ['Lomo de Res con Salsa de Cacao', 72.00, 15, null, 'Lomo de res sellado con salsa de cacao amazónico y puré de camote'],
                ['Ceviche de Camarón con Leche de Tigre', 58.00, 20, null, 'Camarón fresco marinado con leche de tigre y canchita'],
                ['Panna Cotta de Café con Singani', 36.00, 20, null, 'Panna cotta de café boliviano con gelée de singani'],
                ['Maridaje de Cócteles (3)', 65.00, 15, null, '3 cócteles de autor maridados con pasapalos'],
            ],

            // ============ Napoli D'Oro ============
            'napolidoro' => [
                ['Pizza Margherita D.O.C.', 48.00, 25, null, 'Mozzarella di bufala, tomate San Marzano, albahaca y aceite de oliva'],
                ['Pizza Diavola', 52.00, 20, null, 'Salame picante, mozzarella, tomate y aceituna negra'],
                ['Pizza Quattro Formaggi', 55.00, 20, null, 'Mozzarella, gorgonzola, parmesano y fontina'],
                ['Pizza Prosciutto e Funghi', 58.00, 18, null, 'Jamón cocido, champiñones, mozzarella y tomate'],
                ['Cannoli Siciliani (2uds)', 28.00, 25, null, 'Canoli rellenos de ricotta, chocolate y pistacho'],
                ['Arancini (4uds)', 26.00, 30, null, 'Bolitas de arroz rellenas de queso y ragú, empanizadas y fritas'],
            ],

            // ============ Samurai Ramen ============
            'samurairamen' => [
                ['Tonkotsu Ramén Clásico', 56.00, 25, null, 'Caldo de cerdo 20h, noodles, chashu, huevo marinado y alga'],
                ['Miso Ramen Picante', 52.00, 25, null, 'Caldo miso picante con cerdo, maíz, mantequilla y ajo negro'],
                ['Yakitori de Pollo (3 brochetas)', 32.00, 35, null, 'Brochetas de pollo glaseadas con salsa tare'],
                ['Gyozas de Pollo (6uds)', 30.00, 40, null, 'Gyozas caseras rellenas de pollo, jengibre y ajo'],
                ['Takoyaki (6uds)', 28.00, 30, null, 'Bolitas de pulpo con salsa takoyaki y katsuobushi'],
                ['Mochi de Matcha (3uds)', 24.00, 25, null, 'Mochi relleno de crema de té matcha'],
            ],

            // ============ El Rancho ============
            'elrancho' => [
                ['Parrillada El Rancho para 2', 140.00, 12, null, 'Lomo, costilla, chorizo, morcilla, papas, yuca y chimichurri'],
                ['Bife Ancho 400g', 110.00, 15, null, 'Bife ancho angus madurado 28 días, sellado a la parrilla'],
                ['Matambre a la Pizza', 65.00, 20, null, 'Matambre relleno con queso, tomate, orégano y aceituna'],
                ['Choripán Completo', 28.00, 40, null, 'Chorizo a la parrilla en pan con chimichurri y salsas'],
                ['Provoleta con Tomate y Orégano', 35.00, 30, null, 'Provolone fundido a la parrilla con tomate y orégano'],
                ['Ensalada Criolla', 22.00, 40, null, 'Cebolla, tomate, pimiento, vinagreta y especias'],
            ],

            // ============ Café La Paz ============
            'cafelapaz' => [
                ['Salteña de la Casa', 12.00, 100, null, 'Salteña jugosa de carne, pollo o mixta con salsa picante'],
                ['Pastel de Queso', 8.00, 100, null, 'Pastelito de queso horneado, servido caliente'],
                ['Café Tradicional Taza', 10.00, 200, null, 'Café boliviano filtrado, taza grande'],
                ['Té de Coca', 8.00, 100, null, 'Té tradicional de hoja de coca boliviana'],
                ['Empanada de Queso', 10.00, 80, null, 'Empanada frita rellena de queso derretido'],
                ['Api con Pastelito', 14.00, 50, null, 'Api de maíz morado con pastel de queso'],
            ],

            // ============ Pan Artesano ============
            'panartesano' => [
                ['Sourdough Country Loaf', 28.00, 30, null, 'Pan de masa madre con harina integral y semillas'],
                ['Croissant de Mantequilla', 16.00, 50, null, 'Croissant hojaldrado con mantequilla francesa'],
                ['Pain au Chocolat', 18.00, 40, null, 'Pan de chocolate con masa laminada'],
                ['Cinnamon Roll', 22.00, 30, null, 'Rollo de canela con glaseado de queso crema'],
                ['Pan de Bono (6uds)', 24.00, 35, null, 'Panecillos de queso y yuca'],
                ['Café Latte Artesanal', 20.00, 60, null, 'Café latte en taza de cerámica con arte latte'],
            ],

            // ============ La Perla del Pacífico ============
            'laperladelpacifico' => [
                ['Ceviche Clásico Peruano', 52.00, 25, null, 'Pescado fresco, limón, cebolla morada, camote y canchita'],
                ['Ceviche Mixto', 62.00, 20, null, 'Pescado, camarón, calamar y pulpo marinados en cítricos'],
                ['Arroz con Mariscos', 58.00, 20, null, 'Arroz bomba con langostinos, calamar, mejillones y almejas'],
                ['Pulpo a la Parrilla', 68.00, 15, null, 'Pulpo cocido a la parrilla con puré de papa amarilla'],
                ['Leche de Tigre (vaso)', 18.00, 40, null, 'Vaso de leche de tigre clásica con canchita y choclo'],
                ['Suspiro Limeño', 28.00, 20, null, 'Postre peruano de manjar blanco con merengue de puerto'],
            ],

            // ============ Sabores del Oriente ============
            'saboresdeloriente' => [
                ['Majao Tradicional', 38.00, 25, null, 'Arroz con carne, plátano, huevo y queso, típico del Beni'],
                ['Locro de Gallina', 42.00, 20, null, 'Sopa espesa de gallina con arroz, plátano y verduras'],
                ['Masaco de Yuca con Charque', 35.00, 25, null, 'Yuca majada con charque desmenuzado y cebolla'],
                ['Pacumutu', 32.00, 20, null, 'Arroz con queso, leche y canela, típico de Santa Cruz'],
                ['Sopa de Maní', 24.00, 30, null, 'Sopa cremosa de maní con fideos y verduras frescas'],
                ['Refresco de Tamarindo', 12.00, 60, null, 'Refresco natural de tamarindo con hielo'],
            ],

            // ============ Wok & Roll ============
            'wokandroll' => [
                ['Tallarín Saltado Mixto', 38.00, 30, null, 'Tallarines salteados con pollo, res, camarón y verduras'],
                ['Arroz Chaufa de la Casa', 36.00, 30, null, 'Arroz frito con pollo, cerdo, huevo, cebollín y sillao'],
                ['Wantán Frito Mixto (8uds)', 30.00, 35, null, 'Wantanes fritos rellenos de carne y camarón'],
                ['Sopa Wantán Picante', 24.00, 30, null, 'Sopa de wantán con vegetales y toque picante'],
                ['Roll de Primavera (6uds)', 22.00, 40, null, 'Rolls de verduras frescos o fritos con salsa agridulce'],
                ['Dim Sum de Cerdo (6uds)', 32.00, 25, null, 'Dim sum al vapor rellenos de cerdo y jengibre'],
            ],
        ];
    }

    private function photoFor(string $nombre): ?string
    {
        $map = [
            'burger.jpg'       => ['Big Mac', 'Whopper', 'Burger', 'McPollo', 'Smash Burger', 'BBQ Onion', 'Chicken Crispy', 'Veggie Burger', 'Classic Burger', 'Legendary Burger', "Jack Daniel's Burger", 'Barbarian', 'Pastrami'],
            'pizza.jpg'        => ['Pizza', 'Pizza Personal', 'Pizza Mediana', 'Pizza Grande', 'Pizza Familiar', 'Pepperoni', 'Suprema', 'Hawaiana', 'The Works', 'Americana', 'Garden', 'Calzone'],
            'pasta.jpg'        => ['Pasta', 'Tallarín', 'Tallarines', 'Spaghetti', 'Fetuccini', 'Lasaña', 'Ñoquis', 'Ravioli', 'Raviolis', 'Cannoli'],
            'risotto.jpg'      => ['Risotto', 'Arancini'],
            'fried-chicken.jpg'=> ['McNuggets', 'Bucket', 'Pollito', 'Frito', 'Pollo Frito', 'Chicken Kickes', 'Parrillero Mix', 'Pechuga', 'Pollo al Spiedo', 'Gallina'],
            'wings.jpg'        => ['Alitas', 'Wings', 'Takoyaki'],
            'fries.jpg'        => ['Papas Fritas', 'Papas Cargadas', 'Papas Rústicas', 'Bacon Cheese Fries', 'Loaded Potato', 'Patatas Bravas'],
            'breadsticks.jpg'  => ['Breadsticks', 'Cheesy Bread', 'Pan de Ajo', 'Palitos', 'Bruschetta', 'Provoleta', 'Pan de Bono', 'Pain au Chocolat'],
            'soft-drink.jpg'   => ['Coca-Cola', 'Pepsi', 'Gaseosa', 'Refresco', 'Soda', 'Jugo'],
            'coffee.jpg'       => ['Latte', 'Americano', 'Cold Brew', 'V60', 'Café', 'Capuchino', 'Cappuccino', 'Macchiato', 'Frappé', 'Moka', 'Iced Coffee', 'Golden Milk'],
            'frappuccino.jpg'  => ['Frappuccino'],
            'tea-latte.jpg'    => ['Chai Latte', 'Té', 'Matcha'],
            'cheesecake.jpg'   => ['Cheesecake'],
            'croissant.jpg'    => ['Croissant', 'Pain au Chocolat', 'Waffle'],
            'donut.jpg'        => ['Donut', 'Munchkins', 'Dunkin'],
            'cookie.jpg'       => ['Cookie'],
            'breakfast-sandwich.jpg' => ['Sándwich de Desayuno', 'Huevo', 'Omelette', 'Brunch'],
            'sandwich-sub.jpg' => ['Sub ', 'Sándwich', 'Sandwich'],
            'wrap.jpg'         => ['Wrap'],
            'ice-cream.jpg'    => ['McFlurry', 'Sundae', 'Helado', 'Milkshake', 'Batido'],
            'ceviche.jpg'      => ['Ceviche', 'Tiradito', 'Pulpo', 'Leche de Tigre', 'Parihuela', 'Trucha', 'Juanes', 'Pulpo al Olivo', 'Pulpo a la Parrilla'],
            'sushi.jpg'        => ['Sushi', 'Roll', 'Nigiri', 'Rainbow Roll', 'Maki', 'Uramaki', 'Philadelphia Roll', 'Gohan', 'Takoyaki'],
            'ramen.jpg'        => ['Ramen', 'Tonkotsu', 'Shoyu', 'Miso'],
            'gyoza.jpg'        => ['Gyoza', 'Dim Sum', 'Wantán'],
            'bao.jpg'          => ['Bao', 'Bao bun'],
            'steak.jpg'        => ['Bife', 'Ojo de Bife', 'Entraña', 'Lomo', 'Carpaccio', 'Steak', 'Parrillada', 'Chorizo', 'Choripán', 'Parrillero', 'Parrilla', 'Degustación', 'Menú Degustación'],
            'ribs.jpg'         => ['Costillas', 'Ribs', 'Baby Back Ribs', 'Bondiola'],
            'nachos.jpg'       => ['Nachos'],
            'fajitas.jpg'      => ['Fajitas', 'Quesadilla', 'Tex-Mex'],
            'salad.jpg'        => ['Ensalada', 'Salad', 'Edamame', 'Tostada'],
            'bowl.jpg'         => ['Bowl', 'Power Bowl', 'Buddha Bowl', 'Acai Bowl'],
            'smoothie.jpg'     => ['Smoothie'],
            'soup.jpg'         => ['Sopa', 'Crema', 'Chairo', 'Locro'],
            'bolivian-main.jpg'=> ['Fricasé', 'Silpancho', 'Pique Macho', 'Plato Paceño', 'Lechón', 'Mondongo', 'Chairo', 'Api', 'Salteña', 'Anticuchos', 'Chicha', 'Majao', 'Masaco', 'Pacumutu', 'Morcilla', 'Locro', 'Aeropuerto', 'Causa'],
            'dessert-chocolate.jpg' => ['Brownie', 'Mousse', 'Tiramisú', 'Pudding', 'Panna Cotta', 'Fudge', 'Molten Chocolate', 'Macarons', 'Suspiro Limeño', 'Chocolate', 'Cannoli'],
            'cake.jpg'         => ['Tarta', 'Pastel', 'Cake', 'Carrot Cake', 'Torta', 'Waffle', 'Raclette'],
            'wine.jpg'         => ['Vino', 'Malbec', 'Sauvignon Blanc', 'Copa de Vino'],
            'cocktail.jpg'     => ['Mojito', 'Margarita', 'Sour', 'Cóctel', 'Pisco', 'Singani'],
        ];

        $lower = mb_strtolower($nombre);

        foreach ($map as $file => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($lower, mb_strtolower($kw))) {
                    return '/seed-platos/' . $file;
                }
            }
        }

        $fallbackMap = [
            'cerdo'       => 'bolivian-main',
            'carne'       => 'steak',
            'pollo'       => 'fried-chicken',
            'pescado'     => 'ceviche',
            'cordero'     => 'steak',
            'res'         => 'steak',
            'vegetal'     => 'salad',
            'verdura'     => 'salad',
            'queso'       => 'breadsticks',
            'huevo'       => 'breakfast-sandwich',
            'arroz'       => 'bolivian-main',
            'fideo'       => 'pasta',
            'camarón'     => 'ceviche',
            'marisco'     => 'ceviche',
            'postre'      => 'dessert-chocolate',
            'helado'      => 'ice-cream',
            'bebida'      => 'soft-drink',
        ];

        foreach ($fallbackMap as $word => $type) {
            if (str_contains($lower, $word)) {
                return '/seed-platos/' . $type . '.jpg';
            }
        }

        return null;
    }
}
