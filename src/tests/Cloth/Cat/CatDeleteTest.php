<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\Category;

class CatDeleteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests successful category deletion
     *
     * @return void
     */
    public function test_cat_delete_204()
    {
        $catId = Category::factory()->create([
            'organization' => $this->_organization['id']
        ])['id'];
        $call = $this->delete('clothing/category/'
            . '?org_id=' . $this->_organization['id']
            . '&cat_id=' . $catId, [], [
                'Authorization' => 'Bearer ' . $this->_adminBearer
            ]
        );

        $call->assertResponseStatus(204);
        $this->notSeeInDatabase('category', [
            'id' => $catId,
            'organization' => $this->_organization['id']
        ]);
    }

    /**
     * Tests category deletion which has assigned to clothing
     *
     * @return void
     */
    public function test_cat_delete_400()
    {
        $call = $this->delete('clothing/category/'
            . '?org_id=' . $this->_organization['id']
            . '&cat_id=' . $this->_clothingUnits[0]->clothing()['category'], [], [
                'Authorization' => 'Bearer ' . $this->_adminBearer
            ]
        );

        $call->assertResponseStatus(400);
        $this->assertEquals('cannot delete, entity has dependency', $call->response->content());
    }
}
