<?php

namespace Database\Factories\Cloth;

use App\Models\Cloth\Clothing as Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cloth\Category;
use App\Models\Org\Organization;

class Clothing extends Factory
{
    protected $model = Model::class;

    public function definition(): array
    {
    	return [
    	    'name' => $this->faker->realText,
            'image' => $this->faker->image,
            'category' => Category::factory(),
            'organization' => Organization::factory()
    	];
    }
}
