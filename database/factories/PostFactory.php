<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    // ['user_id', 'title', 'subtitle', 'picture', 'content'];

    protected $model = Post::class;

    public function definition()
    {
        $content = "";
        $p = random_int(2, 6);
        for ($x = 0; $x < $p; $x++) {
            $content = $content . '<p class="mb-2">' . $this->faker->paragraph(random_int(5, 10)) . "</p>";
            if ($x == 1) {
                $content = $content . '<img src="https://placeimg.com/320/240/animals" alt="image" class="w-[320px] mx-auto my-4">';
            }
        }

        return [
            'title' => $this->faker->sentence(),
            'subtitle' => $this->faker->paragraph(2),
            'published' => new \DateTime(),
            'content' => $content,
        ];
    }
}
