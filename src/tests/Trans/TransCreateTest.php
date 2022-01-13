<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TransCreateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests successful transaction creation not specifying additional information
     *
     * @return void
     */
    public function test_trans_create_201_1()
    {
        $call = $this->post('report/transaction/'
            . '?org_id=' . $this->_organization['id']
            . '&unit_id=' . $this->_clothingUnits[0]['id']
            . '&house_id=' . $this->_userWarehouse['id'],
            [],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(201);
        $this->seeInDatabase('exchange', [
            'clothing_unit' =>  $this->_clothingUnits[0]['id'],
            'issuer_warehouse' => $this->_organizationWarehouse['id'],
            'receiver_warehouse' => $this->_userWarehouse['id'],
            'facilitator' => $this->_admin['id']
        ]);
    }

    /**
     * Tests successful transaction creation specifying additional information
     *
     * @return void
     */
    public function test_trans_create_201_2()
    {
        $info = 'toster';
        $call = $this->post('report/transaction/'
            . '?org_id=' . $this->_organization['id']
            . '&unit_id=' . $this->_clothingUnits[0]['id']
            . '&house_id=' . $this->_userWarehouse['id'],
            ['information' => $info],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(201);
        $this->seeInDatabase('exchange', [
            'clothing_unit' =>  $this->_clothingUnits[0]['id'],
            'issuer_warehouse' => $this->_organizationWarehouse['id'],
            'receiver_warehouse' => $this->_userWarehouse['id'],
            'facilitator' => $this->_admin['id'],
            'information' => $info
        ]);
    }
}
