<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\Category;

class CatAuthTest extends TestCase
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
    public function test_cat_midl_auth_422()
    {
        $call = $this->get('clothing/category/auth/?org_id=' . $this->_organization['id'], [
            'Authorization' => 'Bearer ' . $this->_userBearer
        ]);

        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests non-existing category id
     *
     * @return void
     */
    public function test_cat_midl_auth_404()
    {
        $catId = intval(Category::orderByDesc('id')->first()['id']) + 1;
        $call = $this->get('clothing/category/auth/'
            . '?org_id=' . $this->_organization['id']
            . '&cat_id=' . $catId, [
            'Authorization' => 'Bearer ' . $this->_userBearer
        ]);

        $call->assertResponseStatus(404);
        $this->assertEquals('category not found', $call->response->content());
    }

    /**
     * Tests access to foreign organization's category
     *
     * @return void
     */
    public function test_cat_midl_auth_403()
    {
        $catId = Category::factory()->create()['id'];
        $call = $this->get('clothing/category/auth/'
            . '?org_id=' . $this->_organization['id']
            . '&cat_id=' . $catId, [
            'Authorization' => 'Bearer ' . $this->_userBearer
        ]);

        $call->assertResponseStatus(403);
        $this->assertEquals('access denied', $call->response->content());
    }

    /**
     * Tests successful category authorization
     *
     * @return void
     */
    public function test_cat_midl_auth_pass()
    {
        $call = $this->get('clothing/category/auth/'
            . '?org_id=' . $this->_organization['id']
            . '&cat_id=' . $this->_clothingUnits[0]->clothing()->first()['category']
        , [
            'Authorization' => 'Bearer ' . $this->_userBearer
        ]);

        $call->assertResponseStatus(200);
        $this->assertEquals('category resource authorized', $call->response->content());
    }
}
