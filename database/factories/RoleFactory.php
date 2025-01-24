<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
final class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => Str::uuid(),
            'display_name' => fake()->unique()->word(),
            'guard_name' => 'admin',
            'created_by' => Admin::where('role', 'admin')->first(),
            'updated_by' => Admin::where('role', 'admin')->first(),
        ];
    }
}
