<?php

namespace Database\Factories;

use App\Models\Autor;
use App\Models\Editora;
use App\Models\Genero;
use App\Models\Vendedor;
use Illuminate\Database\Eloquent\Factories\Factory;

class LivroFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titulo' => fake()->sentence(3), // Ex: "O Mistério do Café"
            'data_publicacao' => fake()->date(),
            'isbn' => fake()->unique()->isbn13(), // Gera um ISBN-13 realista
            'sinopse' => fake()->paragraph(3),
            'capa' => 'capas/livro_exemplo.jpg', // Caminho fictício
            'preco' => fake()->randomFloat(2, 25, 150), // Preço entre 25.00 e 150.00
            'quantidade' => fake()->numberBetween(0, 100),

            // Lógica de Sorteio Aleatório 
            // O first() pega um ID já existente. Se não houver nada, criamos um na hora.
            'autor_id' => Autor::inRandomOrder()->first()?->id ?? Autor::factory(),
            'genero_id' => Genero::inRandomOrder()->first()?->id ?? Genero::factory(),
            'editora_id' => Editora::inRandomOrder()->first()?->id ?? Editora::factory(),
            'vendedor_id' => Vendedor::inRandomOrder()->first()?->id ?? Vendedor::factory(),
        ];
    }
}
