<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class HouseShowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests successful warehouse data retrieval
     *
     * @return void
     */
    public function test_house_show_200()
    {
        $call = $this->get('warehouse/?'
            . 'org_id=' . $this->_organization['id']
            . '&house_id=' . $this->_userWarehouse['id'],[
            'Authorization' => 'Bearer ' . $this->_adminBearer
        ]);

        $call->assertResponseStatus(200);
        $call->seeJsonEquals([
            'id' => $this->_userWarehouse['id'],
            'address' => $this->_userWarehouse['address']
        ]);
    }
}
