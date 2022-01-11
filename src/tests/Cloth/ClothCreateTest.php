<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\Clothing;

class ClothCreateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests required input data fields
     *
     * @return void
     */
    public function test_cloth_create_422()
    {
        $call = $this->post('clothing/'
            . '?org_id=' . $this->_organization['id']
            . '&cat_id=' . $this->_clothingUnits[0]->clothing()['category'],
            [],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());

        $call = $this->post('clothing/'
            . '?org_id=' . $this->_organization['id'],
            ['name' => Clothing::factory()->make()['name']],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests successful clothing creation
     *
     * @return void
     */
    public function test_cloth_create_201()
    {
        $clothData = Clothing::factory()->make([
            'organization' => $this->_organization['id'],
            'category' => $this->_clothingUnits[0]->clothing()['category']
        ])->toArray();
        $call = $this->post('clothing/'
            . '?org_id=' . $this->_organization['id']
            . '&cat_id=' . $this->_clothingUnits[0]->clothing()['category'],
            [
                'image' => $clothData['image'],
                'name' => $clothData['name']
            ],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(201);
        $this->seeInDatabase('clothing', $clothData);
    }
}
