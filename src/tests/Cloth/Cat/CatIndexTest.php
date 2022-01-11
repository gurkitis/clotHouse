<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\Category;

class CatIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Test successful data retrieval specifying category id
     *
     * @return void
     */
    public function test_cat_index_200_1()
    {
        $call = $this->get('clothing/category/'
            . '?org_id=' . $this->_organization['id']
            . '&cat_id=' . $this->_clothingUnits[0]->clothing()['category'], [
            'Authorization' => 'Bearer ' . $this->_userBearer
        ]);

        $call->assertResponseStatus(200);
        $call->seeJsonEquals($this->_clothingUnits[0]->clothing()->category()->toArray());
    }

    /**
     * Test successful data retrieval not specifying category id
     *
     * @return void
     */
    public function test_cat_index_200_2()
    {
        $call = $this->get('clothing/category/'
            . '?org_id=' . $this->_organization['id'], [
            'Authorization' => 'Bearer ' . $this->_userBearer
        ]);

        $call->assertResponseStatus(200);
        $cats = Category::where('organization', $this->_organization['id'])->get();
        $call->seeJsonEquals($cats->toArray());
    }
}
