<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Org\Organization;
use App\Models\User\User;
use App\Http\Controllers\User\Create;
use Illuminate\Http\Request;

class OrgSetAdminTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Organization
     */
    private Organization $organization;

    /**
     * @var User
     */
    private User $owner;

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
        $this->owner = $this->organization->orgUsers()->first()->user()->first();
        $this->user = User::factory()->make(['password' => hash('sha256', self::password)]);
        $this->bearer = $this->owner->session()->first()['session_id'];
        $request = Request::create('user', 'POST', [
            'name' => $this->user['name'],
            'surname' => $this->user['surname'],
            'email' => $this->user['email'],
            'password' => self::password
        ]);
        $request->attributes->set('org_id', $this->organization['id']);
        (new Create())->create($request);
    }

    /**
     * Tests required data fields
     *
     * @return void
     */
    public function test_org_setAdmin_422_1()
    {
        $call = $this->post('org/admin/?org_id=' . $this->organization['id'], [], [
            'Authorization' => 'Bearer ' . $this->bearer
        ]);
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests organization's owner as input data
     *
     * @return void
     */
    public function test_org_setAdmin_422_2()
    {
        $call = $this->post('org/admin/?org_id=' . $this->organization['id'] . '&user_id=' . $this->owner['id'], [], [
            'Authorization' => 'Bearer ' . $this->bearer
        ]);
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests setting user's role to admin role
     *
     * @return void
     */
    public function test_org_setAdmin_200()
    {
        $userId = User::firstWhere('email', $this->user['email'])['id'];
        $call = $this->post('org/admin/?org_id=' . $this->organization['id'] . '&user_id=' . $userId, [], [
            'Authorization' => 'Bearer ' . $this->bearer
        ]);
        $call->assertResponseStatus(204);
        $this->seeInDatabase('organization_user', [
           'user' => $userId,
           'organization' => $this->organization['id'],
           'is_admin' => TRUE,
           'is_owner' => FALSE
        ]);
        $this->assertEquals('user has been promoted', $call->response->content());
    }
}
