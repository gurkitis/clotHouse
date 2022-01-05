<?php

namespace Database\Factories\House;

use App\Models\House\Warehouse as Model;
use Illuminate\Database\Eloquent\Factories\Factory;

class Warehouse extends Factory
{
    protected $model = Model::class;

    public function definition(): array
    {
    	return [
    	    'address' => $this->faker->address
    	];
    }
}
