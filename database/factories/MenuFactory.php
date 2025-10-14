<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Menu>
 */
class MenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Menu::class;

    private static $slugs = [];

    public function definition(): array
    {
        $data = [
            'Hamburguesas con papas',
            'Estofado de pollo',
            'Lentejas con arroz',
            'Kanlu Wantan',
            'Chicharrón de Pollo',
            'Arroz a la cubana',
            'Tallarines en salsa blanca',
            'Arroz Chaufa',
            'Lomo Saltado',
            'Salpicon de Pollo',
            'Saltado de vainitas',
            'Sopa de Semola',
            'Caldo de Gallina',
            'Sopa Blanca',
            'Tallarines a la Alfredo',
            'Aguadito',
            'Salchibroster',
            'Arroz con pollo',
            'Pescado Frito',
            'Tallarines verdes',
            'Sopa de moron',
            'Guiso de Quinua',
            'Papa a la Huancaina',
            'Pollo a la brasa',
            'Aguadito',
            'Bisteck a la olla',
            'Estofado de Res',
            'Cuy al Horno',
            'Cerveza',
            'Coca Cola',
            'Inca Kola',
            'Fanta',
            'Chicha Morada',
            'Pisco Sour',
            'Machu Picchu',
            'Kola Escocesa',
            'Cafe',
            'Refresco de piña',
            'Champán',
            'Vino',
            'Limonada',
            'Emoliente',
            'Frutillada',
            'Agua',
            'Sprite',
            'Jugo de Naranja',
            'Pisco',
            'Kola Real'
        ];

        $name = $this->faker->randomElement($data);
        $slug = Str::slug($name);
        $originalSlug = $slug;

        $counter = 1;
        while (in_array($slug, self::$slugs)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        self::$slugs[] = $slug;

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => $this->faker->paragraph(1),
            'price' => $this->faker->randomFloat(2, 5, 100),
            // 'photo' => $this->faker->imageUrl(640, 480),
            'photo' => Str::replace('-', '', $slug) . "." . $this->faker->randomElement(['jpeg', 'png', 'jpg', 'gif']),
            'stock' => $this->faker->numberBetween(0, 100),
            'priority' => $this->faker->randomElement(['H', 'M', 'L']),
            'enabled' => $this->faker->boolean(),
            'category_id' => Category::factory()
        ];
    }
}
