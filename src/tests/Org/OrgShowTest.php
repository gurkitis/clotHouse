<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;
use App\Models\Org\Organization;
use App\Models\User\User;

class OrgShowTest extends TestCase
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

    private const password = 'option1234';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->organization = Organization::factory()->create();
        $this->user = $this->organization->orgUsers()->first()->user()->first();
        $this->user->update([
            'password' => hash('sha256', self::password)
        ]);
        $this->bearer = $this->user->session()->first()['session_id'];
    }

    /**
     * Tests to find data about existing organization
     *
     * @return void
     */
    public function test_org_show_200_1()
    {
        $call = $this->get('org/?org_id=' . $this->organization['id'], [
            'Authorization' => 'Bearer ' . $this->bearer
        ]);
        $call->assertResponseStatus(200);
        $call->seeJsonEquals([
            'id' => $this->organization['id'],
            'name' => $this->organization['name']
        ]);
    }

    /**
     * Tests to find data about non-existing organization
     *
     * @return void
     */
    public function test_org_show_200_2()
    {
        $call = $this->get('org/?org_id=' . ($this->organization['id'] + 1), [
            'Authorization' => 'Bearer ' . $this->bearer
        ]);
        $call->assertResponseStatus(200);
        $call->seeJsonEquals([]);
    }

    /**
     * Tests to find data about all user's organizations
     *
     * @return void
     */
    public function test_org_show_200_3()
    {
        $org = Organization::factory()->create();
        $usr = $org->orgUsers()->first()->user()->first();
        $this->post('user/?org_id=' . $org['id'], [
            'name' => $this->user['name'],
            'surname' => $this->user['surname'],
            'email' => $this->user['email'],
            'password' => self::password
        ],[
            'Authorization' => 'Bearer ' . $usr->session()->first()['session_id']
        ]);

        $call = $this->get('org', [
            'Authorization' => 'Bearer ' . $this->bearer
        ]);
        $call->assertResponseStatus(200);
        $call->seeJsonEquals([ [
                'id' => $this->organization['id'],
                'name' => $this->organization['name']
            ], [
                'id' => $org['id'],
                'name' => $org['name']
            ]
        ]);
    }
}
