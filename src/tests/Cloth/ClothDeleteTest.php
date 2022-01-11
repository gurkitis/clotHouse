<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\Clothing;

class ClothDeleteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests clothing deletion which doesn't have dependencies
     *
     * @return void
     */
    public function test_cloth_delete_204()
    {
        $clothId = Clothing::factory()->create([
            'organization' => $this->_organization['id']
        ])['id'];
        $call = $this->delete('clothing/'
            . '?org_id=' . $this->_organization['id']
            . '&cloth_id=' . $clothId,
            [],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(204);
        $this->notSeeInDatabase('clothing', [
            'id' => $clothId,
            'organization' => $this->_organization['id']
        ]);
    }

    /**
     * Tests clothing deletion which does have dependencies
     *
     * @return void
     */
    public function test_cloth_delete_400()
    {
        $call = $this->delete('clothing/'
            . '?org_id=' . $this->_organization['id']
            . '&cloth_id=' . $this->_clothingUnits[0]->clothing()['id'],
            [],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(400);
        $this->assertEquals('cannot delete, entity has dependency', $call->response->content());
        $this->seeInDatabase('clothing', [
            'id' => $this->_clothingUnits[0]->clothing()['id'],
            'organization' => $this->_organization['id']
        ]);
    }
}
