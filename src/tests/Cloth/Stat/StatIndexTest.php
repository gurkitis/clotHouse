<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\Status;

class StatIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests status retrieval specifying its id
     *
     * @return void
     */
    public function test_stat_index_200_1()
    {
        $call = $this->get('clothing/status/'
            . '?org_id=' . $this->_organization['id']
            . '&stat_id=' . $this->_clothingUnits[1]['status'],
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(200);
        $call->seeJsonEquals($this->_clothingUnits[1]->status()->toArray());
    }

    /**
     * Tests status retrieval not specifying its id
     *
     * @return void
     */
    public function test_stat_index_200_2()
    {
        $call = $this->get('clothing/status/'
            . '?org_id=' . $this->_organization['id'],
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(200);
        $call->seeJsonEquals(Status::where('organization', $this->_organization['id'])->get()->toArray());
    }
}
