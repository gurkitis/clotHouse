<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Org\Organization;
use App\Models\Org\OrgUser;

class OrgUserAuthTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Collection|Model
     */
    private $org;

    /**
     * @var Collection|Model
     */
    private $user;

    /**
     * @var string
     */
    private $bearer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->org = Organization::factory()->create();
        $this->user = $this->org->orgUsers()->first()->user()->first();
        $this->bearer = $this->user->session()->first()['session_id'];
    }

    /**
     * Tests required data input fields
     *
     * @return void
     */
    public function test_user_midl_auth_422()
    {
        $call = $this->get('org/user/auth/?org_id=' . $this->org['id'], [
            'Authorization' => 'Bearer ' . $this->bearer
        ]);
        $call->assertResponseStatus('422');
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests access to non-existing user
     *
     * @return void
     */
    public function test_user_midl_auth_404()
    {
        $call = $this->get('org/user/auth/?org_id=' . $this->org['id'] . '&user_id=' . ($this->user['id'] + 1), [
            'Authorization' => 'Bearer ' . $this->bearer
        ]);
        $call->assertResponseStatus('400');
        $this->assertEquals('user not found', $call->response->content());
    }

    /**
     * Tests access to foreign organization user
     *
     * @return void
     */
    public function test_user_midl_autl_403()
    {
        $org = Organization::factory()->create();
        $user = $org->orgUsers()->first()->user()->first();

        $call = $this->get('org/user/auth/?org_id=' . $this->org['id'] . '&user_id=' . $user['id'], [
            'Authorization' => 'Bearer ' . $this->bearer
        ]);
        $call->assertResponseStatus('403');
        $this->assertEquals('denied access', $call->response->content());
    }

    /**
     * Tests access to users organization's user
     *
     * @return void
     */
    public function test_org_user_midl_auth_pass_1()
    {
        $orgUser = OrgUser::factory()->create(['organization' => $this->org['id']]);
        $call = $this->get('org/user/auth/?org_id=' . $this->org['id'] . '&user_id=' . $orgUser['user'], [
            'Authorization' => 'Bearer ' . $this->bearer
        ]);
        $call->assertResponseStatus('200');
        $this->assertEquals('user resource authorized', $call->response->content());
    }

    /**
     * Tests access to client user
     *
     * @return void
     */
    public function test_org_user_midl_auth_pass_2()
    {
        $call = $this->get('org/user/auth/?org_id=' . $this->org['id'] . '&user_id=' . $this->user['id'], [
            'Authorization' => 'Bearer ' . $this->bearer
        ]);
        $call->assertResponseStatus('200');
        $this->assertEquals('user resource authorized', $call->response->content());
    }
}
