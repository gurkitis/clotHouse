<?php

namespace Database\Factories\Cloth;

use App\Models\Cloth\Category as Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Org\Organization;

class Category extends Factory
{
    protected $model = Model::class;

    public function definition(): array
    {
    	return [
    	    'name' => $this->faker->word,
            'organization' => Organization::factory()
    	];
    }
}
