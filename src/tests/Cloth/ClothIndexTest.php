<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\Clothing;

class ClothIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests clothing data retrieval specifying clothing id
     *
     * @return void
     */
    public function test_cloth_index_200_1()
    {
        $call = $this->get('clothing/'
            . '?org_id=' . $this->_organization['id']
            . '&cloth_id=' . $this->_clothingUnits[0]->clothing()['id'],
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(200);
        $call->seeJsonEquals($this->_clothingUnits[0]->clothing()->toArray());
    }

    /**
     * Tests clothing data retrieval without specifying clothing id
     *
     * @return void
     */
    public function test_cloth_index_200_2()
    {
        $call = $this->get('clothing/'
            . '?org_id=' . $this->_organization['id'],
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(200);
        $call->seeJsonEquals(Clothing::where('organization', $this->_organization['id'])->get()->toArray());
    }
}
