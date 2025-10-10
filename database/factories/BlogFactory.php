<?php
// filepath: c:\Users\Lenovo\Desktop\laravel\SmartHealth_Laravel\database\factories\BlogFactory.php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    protected $model = Blog::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $categories = [
            'Alimentation saine',
            'Activité physique',
            'Sommeil & récupération',
            'Gestion du stress',
            'Bien-être mental',
            'Vie étudiante',
            'Prévention santé',
            'Développement personnel'
        ];

        // *** MODIFIÉ : Seulement vos 2 images PNG ***
        $defaultImages = [
            'defaults/blog-health-1.png',
            'defaults/blog-health-2.png'
        ];

        return [
            'title' => $this->faker->sentence(rand(3, 8)),
            'category' => $this->faker->randomElement($categories),
            'content' => $this->faker->paragraphs(rand(3, 8), true),
            'user_id' => User::factory(),
            // *** MODIFIÉ : 100% des blogs auront une image (une des 2) ***
            'image_url' => $this->faker->randomElement($defaultImages),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the blog should have a specific category.
     */
    public function category(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }

    /**
     * Indicate that the blog should have an image.
     */
    public function withImage(): static
    {
        // *** MODIFIÉ : Seulement vos 2 images PNG ***
        $defaultImages = [
            'defaults/blog-health-1.png',
            'defaults/blog-health-2.png'
        ];

        return $this->state(fn (array $attributes) => [
            'image_url' => $this->faker->randomElement($defaultImages),
        ]);
    }

    /**
     * Indicate that the blog should not have an image.
     */
    public function withoutImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_url' => null,
        ]);
    }

    /**
     * Indicate that the blog belongs to a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}