<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Trans\Exchange;

class TransIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests successful clothing unit's transactions data retrieval
     *
     * @return void
     */
    public function test_trans_index_200()
    {
        $call = $this->get('report/history/'
            . '?org_id=' . $this->_organization['id']
            . '&unit_id=' . $this->_clothingUnits[0]['id'],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(200);
        $call->seeJsonEquals(Exchange::where('clothing_unit', $this->_clothingUnits[0]['id'])->get()->toArray());
    }
}
