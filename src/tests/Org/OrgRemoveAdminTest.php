<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class OrgRemoveAdminTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests required input data fields
     *
     * @return void
     */
    public function test_org_removeAdmin_422_1()
    {
        $call = $this->delete('org/admin/?org_id=' . $this->_organization['id'],[],[
            'Authorization' => 'Bearer ' . $this->_ownerBearer
        ]);
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests permission removal on organization's owner
     *
     * @return void
     */
    public function test_org_removeAdmin_422_2()
    {
        $call = $this->delete('org/admin/?org_id=' . $this->_organization['id'] . '&user_id=' . $this->_owner['id'],[],[
            'Authorization' => 'Bearer ' . $this->_ownerBearer
        ]);
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    public function test_org_removeAdmin_200()
    {
        $call = $this->delete('org/admin/?org_id=' . $this->_organization['id'] . '&user_id=' . $this->_user['id'],[],[
            'Authorization' => 'Bearer ' . $this->_ownerBearer
        ]);
        $call->assertResponseStatus(204);
        $this->seeInDatabase('organization_user', [
            'user' => $this->_user['id'],
            'organization' => $this->_organization['id'],
            'is_admin' => FALSE,
            'is_owner' => FALSE
        ]);
        $this->assertEquals('invalid input data', $call->response->content());
    }
}
