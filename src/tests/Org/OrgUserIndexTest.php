<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User\User;
use App\Models\Org\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\User\Create;

class OrgUserIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests successful organization's user data retrieval
     *
     * @return void
     */
    public function test_org_user_index_200()
    {
        $call = $this->get('org/users/?org_id=' . $this->_organization['id'], [
            'Authorization' => 'Bearer ' . $this->_userBearer
        ]);
        $call->assertResponseStatus(200);
        $call->seeJsonEquals([[
            'id' => $this->_owner['id'],
            'role' => 'owner'
        ], [
            'id' => $this->_admin['id'],
            'role' => $this->_admin->organizations()->first()->getRole()
        ], [
            'id' => $this->_user['id'],
            'role' => $this->_user->organizations()->first()->getRole()
        ]]);
    }
}
