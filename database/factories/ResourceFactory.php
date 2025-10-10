<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceFactory extends Factory
{
    public function definition(): array
    {
        $types = ['psychologue', 'SAMU', 'centre universitaire', 'médecin', 'association', 'pharmacie', 'hôpital', 'soutien scolaire', 'urgence', 'info santé'];
        return [
            'type' => $this->faker->randomElement($types),
            'name' => $this->faker->company,
            'contact' => $this->faker->phoneNumber,
            'link' => $this->faker->optional()->url,
        ];
    }
}