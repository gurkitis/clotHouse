<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Http\Middleware\UserAuth;
use Illuminate\Http\Request;
use App\Models\User\User;
use App\Models\Org\OrgUser;
use App\Models\User\Session;
use App\Models\Org\Organization;

class UserAuthTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Tests required fields
     *
     * @return void
     */
    public function test_user_auth_422()
    {
        // Setup
        $password = 'option1234';
        $user = User::factory()->create([
            'password' => hash('sha256', $password)
        ]);
        $session = Session::firstWhere('user', $user['id']);
        $orgUser = OrgUser::factory()->create([
            'user' => $user['id'],
            'is_admin' => FALSE,
            'is_owner' => FALSE
        ]);

        // Test missing barer token
        $call = $this->get('user/auth/user/?org_id=' . $orgUser['organization']);
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());

        // Test missing org_id param
        $call = $this->get('user/auth/user', ['Authorization' => 'Bearer ' . $session['session_id']]);
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests authorization with invalid bearer token
     *
     * @return void
     */
    public function test_user_auth_401_1()
    {
        // Setup
        $password = 'option1234';
        $user = User::factory()->create([
            'password' => hash('sha256', $password)
        ]);
        $session = Session::firstWhere('user', $user['id']);
        $orgUser = OrgUser::factory()->create([
            'user' => $user['id'],
            'is_admin' => FALSE,
            'is_owner' => FALSE
        ]);

        // Test
        $call = $this->get('user/auth/user/?org_id=' . $orgUser['organization'], [
           'Authorization' => 'Bearer ' . $session['session_id'] . 'invalid'
        ]);
        $call->assertResponseStatus(401);
        $this->assertEquals('unsuccessful authentication', $call->response->content());
    }

    /**
     * Tests user access to admin route
     *
     * @return void
     */
    public function test_user_auth_401_2()
    {
        // Setup
        $password = 'option1234';
        $user = User::factory()->create([
            'password' => hash('sha256', $password)
        ]);
        $session = Session::firstWhere('user', $user['id']);
        $orgUser = OrgUser::factory()->create([
            'user' => $user['id'],
            'is_admin' => FALSE,
            'is_owner' => FALSE
        ]);

        // Test
        $call = $this->get('user/auth/admin/?org_id=' . $orgUser['organization'], [
            'Authorization' => 'Bearer ' . $session['session_id']
        ]);
        $call->assertResponseStatus(401);
        $this->assertEquals('access denied', $call->response->content());
    }

    /**
     * Tests user access to owner resource
     *
     * @return void
     */
    public function test_user_auth_401_3()
    {
        // Setup
        $password = 'option1234';
        $user = User::factory()->create([
            'password' => hash('sha256', $password)
        ]);
        $session = Session::firstWhere('user', $user['id']);
        $orgUser = OrgUser::factory()->create([
            'user' => $user['id'],
            'is_admin' => FALSE,
            'is_owner' => FALSE
        ]);

        // Test
        $call = $this->get('user/auth/owner/?org_id=' . $orgUser['organization'], [
            'Authorization' => 'Bearer ' . $session['session_id']
        ]);
        $call->assertResponseStatus(401);
        $this->assertEquals('access denied', $call->response->content());
    }

    /**
     * Tests admin access to owner resource
     *
     * @return void
     */
    public function test_user_auth_401_4()
    {
        // Setup
        $password = 'option1234';
        $user = User::factory()->create([
            'password' => hash('sha256', $password)
        ]);
        $session = Session::firstWhere('user', $user['id']);
        $orgUser = OrgUser::factory()->create([
            'user' => $user['id'],
            'is_admin' => TRUE,
            'is_owner' => FALSE
        ]);

        // Test
        $call = $this->get('user/auth/owner/?org_id=' . $orgUser['organization'], [
            'Authorization' => 'Bearer ' . $session['session_id']
        ]);
        $call->assertResponseStatus(401);
        $this->assertEquals('access denied', $call->response->content());
    }

    /**
     * Tests user access to foreign organization
     *
     * @return void
     */
    public function test_user_auth_404()
    {
        // Setup
        $password = 'option1234';
        $user = User::factory()->create([
            'password' => hash('sha256', $password)
        ]);
        $session = Session::firstWhere('user', $user['id']);
        $orgUser = OrgUser::factory()->create([
            'user' => $user['id'],
            'is_admin' => FALSE,
            'is_owner' => FALSE
        ]);
        $org = Organization::factory()->create();

        // Test
        $call = $this->get('user/auth/user/?org_id=' . $org['id'], [
            'Authorization' => 'Bearer ' . $session['session_id']
        ]);
        $call->assertResponseStatus(404);
        $this->assertEquals('user not found in organization', $call->response->content());
    }

    /**
     * Tests successful user authorization
     *
     * @return void
     */
    public function test_user_auth_pass()
    {
        // Setup
        $password = 'option1234';
        $user = User::factory()->create([
            'password' => hash('sha256', $password)
        ]);
        $session = Session::firstWhere('user', $user['id']);
        $orgUser = OrgUser::factory()->create([
            'user' => $user['id'],
            'is_admin' => FALSE,
            'is_owner' => FALSE
        ]);

        // Test user
        $call = $this->get('user/auth/user/?org_id=' . $orgUser['organization'], [
            'Authorization' => 'Bearer ' . $session['session_id']
        ]);
        $call->assertResponseStatus(200);
        $this->assertEquals('user authorization successful', $call->response->content());

        // Test admin
        $orgUser->update(['is_admin' => TRUE, 'is_owner' => FALSE]);

        $call = $this->get('user/auth/user/?org_id=' . $orgUser['organization'], [
            'Authorization' => 'Bearer ' . $session['session_id']
        ]);
        $call->assertResponseStatus(200);
        $this->assertEquals('user authorization successful', $call->response->content());

        $call = $this->get('user/auth/admin/?org_id=' . $orgUser['organization'], [
            'Authorization' => 'Bearer ' . $session['session_id']
        ]);
        $call->assertResponseStatus(200);
        $this->assertEquals('admin authorization successful', $call->response->content());

        // Test owner
        $orgUser->update(['is_admin' => TRUE, 'is_owner' => TRUE]);

        $call = $this->get('user/auth/user/?org_id=' . $orgUser['organization'], [
            'Authorization' => 'Bearer ' . $session['session_id']
        ]);
        $call->assertResponseStatus(200);
        $this->assertEquals('user authorization successful', $call->response->content());

        $call = $this->get('user/auth/admin/?org_id=' . $orgUser['organization'], [
            'Authorization' => 'Bearer ' . $session['session_id']
        ]);
        $call->assertResponseStatus(200);
        $this->assertEquals('admin authorization successful', $call->response->content());

        $call = $this->get('user/auth/owner/?org_id=' . $orgUser['organization'], [
            'Authorization' => 'Bearer ' . $session['session_id']
        ]);
        $call->assertResponseStatus(200);
        $this->assertEquals('owner authorization successful', $call->response->content());
    }
}
