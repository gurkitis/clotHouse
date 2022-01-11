<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CatCreateTest extends TestCase
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
    public function test_cat_create_422()
    {
        $call = $this->post('clothing/category/?org_id=' . $this->_organization['id'], [], [
            'Authorization' => 'Bearer ' . $this->_adminBearer
        ]);

        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests successful category creation
     *
     * @return void
     */
    public function test_cat_create_201()
    {
        $name = 'toster';
        $call = $this->post('clothing/category/?org_id=' . $this->_organization['id'], [
            'name' => $name
        ], [
            'Authorization' => 'Bearer ' . $this->_adminBearer
        ]);

        $call->assertResponseStatus(201);
        $this->seeInDatabase('category', [
            'name' => $name,
            'organization' => $this->_organization['id']
        ]);
    }
}
