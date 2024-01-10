<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genderNames = ['male', 'female', 'other'];
        $genderName = fake()->randomElement($genderNames);
        $createdAt = fake()->dateTimeBetween(startDate: '-1 month');
        return [
            'category_id' => fake()->randomElement($categories = Category::all())->id,
            'first_name' => fake()->firstName($genderName),
            'last_name' => fake()->lastName($genderName),
            'gender' => array_search($genderName, $genderNames) + 1,
            'email' => fake()->email(),
            'tel' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'building' => fake()->buildingNumber(),
            'detail' => implode(PHP_EOL, [
                fake()->realText(fake()->numberBetween(30, 60)),
                fake()->realText(fake()->numberBetween(30, 60)),
            ]),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}
