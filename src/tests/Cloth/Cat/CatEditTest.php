<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CatEditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests successful category's data edit
     *
     * @return void
     */
    public function test_cat_edit_200()
    {
        $name = 'tosteris';
        $call = $this->put('clothing/category/'
            . '?org_id=' . $this->_organization['id']
            . '&cat_id=' . $this->_clothingUnits[0]->clothing()['category'], [
                'name' => $name
        ], [
                'Authorization' => 'Bearer ' . $this->_adminBearer
        ]);

        $call->assertResponseStatus(200);
        $call->seeJsonEquals([
            'id' => $this->_clothingUnits[0]->clothing()['category'],
            'name' => $name,
            'organization' => $this->_organization['id']
        ]);
        $this->seeInDatabase('category', [
            'id' => $this->_clothingUnits[0]->clothing()['category'],
            'name' => $name,
            'organization' => $this->_organization['id']
        ]);
    }
}
