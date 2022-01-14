<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Org\Organization;
use App\Models\Org\OrgUser;

class OrgDeleteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests successful organization's deletion
     *
     * @return void
     */
    public function test_org_delete_204()
    {
        $orgId = $this->_organization['id'];
        $houseId = $this->_organizationWarehouse['id'];
        $userId = $this->_user['id'];
        $adminId = $this->_admin['id'];
        $ownerId = $this->_owner['id'];
        $foreignOrg = OrgUser::factory()->create(['user' => $userId])['organization'];


        $call = $this->delete('org/?org_id=' . $orgId, [],
            ['Authorization' => 'Bearer ' . $this->_ownerBearer]
        );

        $call->assertResponseStatus(204);
        $this->notSeeInDatabase('clothing_unit', ['organization' => $orgId]);
        $this->notSeeInDatabase('status', ['organization' => $orgId]);
        $this->notSeeInDatabase('clothing', ['organization' => $orgId]);
        $this->notSeeInDatabase('category', ['organization' => $orgId]);
        $this->notSeeInDatabase('organization_warehouse', ['organization' => $orgId]);
        $this->notSeeInDatabase('warehouse', ['id' => $houseId]);
        $this->notSeeInDatabase('organization_user', ['organization' => $orgId]);

        $this->seeInDatabase('user', ['id' => $userId]);
        $this->notSeeInDatabase('user', ['id' => $adminId]);
        $this->notSeeInDatabase('user', ['id' => $ownerId]);

        $this->seeInDatabase('session', ['user' => $userId]);
        $this->notSeeInDatabase('session', ['user' => $adminId]);
        $this->notSeeInDatabase('session', ['user' => $ownerId]);
        $this->notSeeInDatabase('organization', ['id' => $orgId]);
        $this->assertEquals('organization is deleted', $call->response->content());
    }
}
