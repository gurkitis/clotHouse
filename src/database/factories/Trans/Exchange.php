<?php

namespace Database\Factories\Trans;

use App\Models\Trans\Exchange as Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cloth\ClothingUnit;
use App\Models\House\Warehouse;
use App\Models\User\User;
use App\Models\House\OrgHouse;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;

class Exchange extends Factory
{
    protected $model = Model::class;

    public function definition(): array
    {
    	return [
            'date' => date(DATE_ATOM),
            'information' => NULL,
            'facilitator' => User::factory(),

            'receiver_warehouse' => function (array $attributes) {
                $orgId = User::find($attributes['facilitator'])->organizations()->first()['organization'];
                return OrgHouse::factory()->create([
                    'organization' => $orgId
                ])['warehouse'];
            },

            'clothing_unit' => function (array $attributes) {
                $orgId = User::find($attributes['facilitator'])->organizations()->first()['organization'];
                return ClothingUnit::factory()->create([
                    'organization' => $orgId
                ])['id'];
            },

            'issuer_warehouse' => function () {
                if ($this->faker->boolean) {
                    return NULL;
                } else {
                    return Warehouse::factory()->create()['id'];
                }
            },
        ];
    }
}
