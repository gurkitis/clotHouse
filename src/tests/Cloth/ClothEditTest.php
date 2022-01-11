<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\Clothing;

class ClothEditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests editing clothing's category and name
     *
     * @return void
     */
    public function test_cloth_edit_200_1()
    {
        $name = 'testName';
        $call = $this->put('clothing/'
            . '?org_id=' . $this->_organization['id']
            . '&cloth_id=' . $this->_clothingUnits[0]->clothing()['id'],
            [
                'name' => $name,
                'category' => $this->_clothingUnits[1]->clothing()['category']
            ], [
                'Authorization' => 'Bearer ' .$this->_adminBearer
            ]
        );

        $call->assertResponseStatus(200);
        $call->seeJsonEquals([
            'id' => $this->_clothingUnits[0]->clothing()['id'],
            'image' => $this->_clothingUnits[0]->clothing()['image'],
            'name' => $name,
            'category' => $this->_clothingUnits[1]->clothing()['category'],
            'organization' => $this->_organization['id']
        ]);
        $this->seeInDatabase('clothing', [
            'id' => $this->_clothingUnits[0]->clothing()['id'],
            'name' => $name,
            'category' => $this->_clothingUnits[1]->clothing()['category'],
            'organization' => $this->_organization['id']
        ]);
    }

    /**
     * Tests editing clothing's name
     *
     * @return void
     */
    public function test_cloth_edit_200_2()
    {
        $name = 'testName';
        $call = $this->put('clothing/'
            . '?org_id=' . $this->_organization['id']
            . '&cloth_id=' . $this->_clothingUnits[0]->clothing()['id'],
            [
                'name' => $name
            ], [
                'Authorization' => 'Bearer ' .$this->_adminBearer
            ]
        );

        $call->assertResponseStatus(200);
        $call->seeJsonEquals([
            'id' => $this->_clothingUnits[0]->clothing()['id'],
            'image' => $this->_clothingUnits[0]->clothing()['image'],
            'name' => $name,
            'category' => $this->_clothingUnits[0]->clothing()['category'],
            'organization' => $this->_organization['id']
        ]);
        $this->seeInDatabase('clothing', [
            'id' => $this->_clothingUnits[0]->clothing()['id'],
            'name' => $name,
            'category' => $this->_clothingUnits[0]->clothing()['category'],
            'organization' => $this->_organization['id']
        ]);
    }
}
