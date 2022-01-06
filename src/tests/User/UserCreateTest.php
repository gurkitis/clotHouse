<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Org\Organization;
use App\Models\User\User;
use App\Models\User\Session;
use App\Models\House\Warehouse;

class UserCreateTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Tests required form's parameters
     *
     * @return void
     */
    public function test_user_create_422()
    {
        // Setup
        $org = Organization::factory()->create();
        $user = User::orderByDesc('id')->first();
        $session = Session::firstWhere('user', $user['id']);
        $formData = User::factory()->make();
        $data = [
            'name' => $formData['name'],
            'surname' => $formData['surname'],
            'email' => $formData['email'],
            'password' => 'testUser',
            'address' => Warehouse::firstWhere('id', $formData['warehouse'])
        ];

        // Tests
        for ($x = 0; $x < 4; $x++) {
            $tmp = $data;
            $call = $this->post('user/?org_id=' . $org['id'],
                array_splice($tmp, $x, $x),
                ['Authorization' => 'Bearer ' . $session['session_id']]
            );
            $call->assertResponseStatus(422);
            $this->assertEquals('invalid input data', $call->response->content());
        }
        $call = $this->post('user', $data, ['Authorization' => 'Bearer ' . $session['session_id']]);
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests if user already exists in system
     *
     * @return void
     */
    public function test_user_create_400()
    {
        // Setup
        $org = Organization::factory()->create();
        $user = User::orderByDesc('id')->first();
        $session = Session::firstWhere('user', $user['id']);

        // Test
        $call = $this->post('user/?org_id=' . $session['session_id'], [
            'name' => $user['name'],
            'surname' => $user['surname'],
            'email' => $user['email'],
            'password' => 'testins'
        ], ['Authorization' => 'Bearer ' . $session['session_id']]);
        $call->assertResponseStatus(400);
        $this->assertEquals('user already exists', $call->response->content());
    }

    /**
     * Tests registering new user to system
     *
     * @return void
     */
    public function test_user_create_200_1()
    {
        // Setup
        $org = Organization::factory()->create();
        $user = User::orderByDesc('id')->first();
        $session = Session::firstWhere('user', $user['id']);
        $formData = User::factory()->make();

        // Test
        $call = $this->post('user/?org_id=' . $session['session_id'], [
            'name' => $formData['name'],
            'surname' => $formData['surname'],
            'email' => $formData['email'],
            'password' => 'tosteris',
        ], ['Authorization' => 'Bearer ' . $session['session_id']]);
        $call->assertResponseStatus(201);
    }

    /**
     * Tests registering existing user to foreign organization
     *
     * @return void
     */
    public function test_user_create_200_2()
    {
        // Setup
        $org = Organization::factory()->create();
        $user = User::orderByDesc('id')->first();
        $session = Session::firstWhere('user', $user['id']);
        Organization::factory()->create();
        $formData = User::orderByDesc('id')->first();
        $formData->update(['password' => hash('sha256', 'tosteris')]);

        // Test
        $call = $this->post('user/?org_id=' . $session['session_id'], [
            'name' => $formData['name'],
            'surname' => $formData['surname'],
            'email' => $formData['email'],
            'password' => 'tosteris',
        ], ['Authorization' => 'Bearer ' . $session['session_id']]);
        $call->assertResponseStatus(201);
    }
}
