<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User\User;
use App\Models\User\Session;

class UserLoginTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Tests POST parameters requirements
     *
     * @return void
     */
    public function test_user_login_422()
    {
        $password = 'option1234';
        $user = User::factory()->create([
            'password' => hash('sha256', $password)
        ]);

        // Email isn't set
        $call = $this->post('/user/login', [
            'password' => $password
        ]);
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());

        // Password isn't set
        $call = $this->post('/user/login', [
            'email' => $user['email']
        ]);
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());

        // None is set
        $call = $this->post('/user/login');
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests non existing email
     *
     * @return void
     */
    public function test_user_login_401_1()
    {
        $password = 'option1234';
        $user = User::factory()->create([
            'password' => hash('sha256', $password)
        ]);

        $call = $this->post('/user/login', [
            'email' => $user['email'] . 'InvaliD',
            'password' => $password
        ]);
        $call->assertResponseStatus(401);
        $this->assertEquals('unsuccessful authentication', $call->response->content());
    }

    /**
     * Tests invalid password
     *
     * @return void
     */
    public function test_user_login_401_2()
    {
        $password = 'option1234';
        $user = User::factory()->create([
            'password' => hash('sha256', $password)
        ]);

        $call = $this->post('/user/login', [
            'email' => $user['email'],
            'password' => $password . '5'
        ]);
        $call->assertResponseStatus(401);
        $this->assertEquals('unsuccessful authentication', $call->response->content());
    }

    /**
     * Tests successful authentication
     *
     * @return void
     */
    public function test_user_login_200()
    {
        $password = 'option1234';
        $user = User::factory()->create([
            'password' => hash('sha256', $password)
        ]);

        $call = $this->post('/user/login', [
            'email' => $user['email'],
            'password' => $password
        ]);
        $call->assertResponseStatus(200);
        $call->seeJson();
        $call->seeJsonContains([
            'barer_token' => Session::firstWhere('user', $user['id'])['session_id']
        ]);
        $call->seeJsonStructure([
            'barer_token',
            'organizations'
        ]);
    }
}
