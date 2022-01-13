<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UnitDeleteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests successful clothing unit deletion
     *
     * @return void
     */
    public function test_unit_delete_204()
    {
        $call = $this->delete('clothing/unit/'
            . '?org_id=' . $this->_organization['id']
            . '&unit_id=' . $this->_clothingUnits[0]['id'],
            [],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(204);
        $this->notSeeInDatabase('clothing_unit', ['id' => $this->_clothingUnits[0]['id']]);
        $this->notSeeInDatabase('exchange', ['clothing_unit' => $this->_clothingUnits[0]['id']]);
    }
}
