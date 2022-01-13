<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UnitOrgIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests successful organization's clothing units data retrieval
     *
     * @return void
     */
    public function test_unit_org_index_200()
    {
        $call = $this->get('clothing/unit/org/'
            . '?org_id=' . $this->_organization['id'],
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(200);
        $this->seeJsonEquals($this->_clothingUnits->toArray());
    }
}
