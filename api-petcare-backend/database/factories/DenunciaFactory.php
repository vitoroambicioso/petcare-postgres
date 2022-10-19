<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DenunciaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'idUsuario' => '1',
            'tipo' => fake()->name(),
            'cor' => 'caramelo',
            'localizacao' => fake()->locale(),
            'rua' => fake()->locale(),
            'bairro' => fake()->locale(),
            'pontoDeReferencia' => fake()->locale(),
            'picture' => fake()->name().'.png'
        ];
    }
}
