<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Org\Organization;
use App\Models\User\User;

class OrgEditTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Organization
     */
    private Organization $organization;

    /**
     * @var User
     */
    private User $user;

    /**
     * @var string
     */
    private string $bearer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organization = Organization::factory()->create();
        $this->user = $this->organization->orgUsers()->first()->user()->first();
        $this->bearer = $this->user->session()->first()['session_id'];
    }

    /**
     * Tests required input data fields
     *
     * @return void
     */
    public function test_org_edit_422()
    {
        $call = $this->put('org/?org_id=' . $this->organization['id'], [], [
            'Authorization' => 'Bearer ' . $this->bearer
        ]);
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests successful organization data edit
     *
     * @return void
     */
    public function test_org_edit_200()
    {
        $name = Organization::factory()->make()['name'];
        $call = $this->put('org/?org_id=' . $this->organization['id'], [
            'name' => $name
        ], [
            'Authorization' => 'Bearer ' . $this->bearer
        ]);
        $call->assertResponseStatus(200);
        $call->seeJsonEquals([
            'name' => $name
        ]);
        $this->seeInDatabase('organization', [
            'id' => $this->organization['id'],
            'name' => $name
        ]);
    }
}
