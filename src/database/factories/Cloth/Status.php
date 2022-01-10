<?php

namespace Database\Factories\Cloth;

use App\Models\Cloth\Status as Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Org\Organization;

class Status extends Factory
{
    protected $model = Model::class;

    public function definition(): array
    {
    	return [
    	    'status' => $this->faker->word,
            'organization' => Organization::factory()
    	];
    }
}
