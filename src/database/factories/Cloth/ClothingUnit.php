<?php

namespace Database\Factories\Cloth;

use App\Models\Cloth\ClothingUnit as Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cloth\Status;
use App\Models\Cloth\Clothing;
use App\Models\House\Warehouse;
use App\Models\Org\Organization;
use App\Models\House\OrgHouse;
use App\Models\Trans\Exchange;
use App\Models\Org\OrgUser;

class ClothingUnit extends Factory
{
    protected $model = Model::class;

    public function definition(): array
    {
    	return [
    	    'identificator' => $this->faker->unique()->md5,
            'organization' => Organization::factory(),
            'status' => function (array $attributes) {
                return Status::factory()->create([
                    'organization' => $attributes['organization']
                ])['id'];
            },
            'clothing' => function (array $attributes) {
                return Clothing::factory()->create([
                    'organization' => $attributes['organization']
                ])['id'];
            },
            'warehouse' => function (array $attributes) {
                return OrgHouse::factory()->create([
                    'organization' => $attributes['organization']
                ])['warehouse'];
            },
    	];
    }

    /**
     * @return ClothingUnit
     */
    public function configure(): ClothingUnit
    {
        return $this->afterMaking(function (Model $clothingUnit) {
            Exchange::factory()->make([
                'issuer_warehouse' => NULL,
                'receiver_warehouse' => $clothingUnit['warehouse'],
                'facilitator' => OrgUser::where('organization', $clothingUnit['organization'])
                    ->where('is_owner', TRUE)
                    ->first()['user'],
                'clothing_unit' => $clothingUnit['id'],
                'information' => "clothing unit's initialization"
            ]);
        })->afterCreating(function (Model $clothingUnit) {
            Exchange::factory()->create([
                'issuer_warehouse' => NULL,
                'receiver_warehouse' => $clothingUnit['warehouse'],
                'facilitator' => OrgUser::where('organization', $clothingUnit['organization'])
                    ->where('is_owner', TRUE)
                    ->first()['user'],
                'clothing_unit' => $clothingUnit['id'],
                'information' => "clothing unit's initialization"
            ]);
        });
    }
}
