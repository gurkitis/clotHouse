<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;
use App\Models\User\User;

class UserShowTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    /**
     * Tests required URI fields
     *
     * @return void
     */
    public function test_user_show_422()
    {
        $call = $this->get('user');
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Find user by user_id
     *
     * @return void
     */
    public function test_user_show_200_1()
    {
        $user = User::factory()->create();

        $call = $this->get('user/?user_id=' . $user['id']);
        $call->assertResponseStatus(200);
        $call->seeJsonEquals([
            'id' => $user['id'],
            'name' => $user['name'],
            'surname' => $user['surname'],
            'email' => $user['email']
        ]);
    }

    /**
     * Find user by email
     *
     * @return void
     */
    public function test_user_show_200_2()
    {
        $user = User::factory()->create();

        $call = $this->get('user/?email=' . $user['email']);
        $call->assertResponseStatus(200);
        $call->seeJsonEquals([
            'id' => $user['id'],
            'name' => $user['name'],
            'surname' => $user['surname'],
            'email' => $user['email']
        ]);
    }

    /**
     * Find non-existing user by id
     *
     * @return void
     */
    public function test_user_show_200_3()
    {
        $user = User::factory()->create();

        $call = $this->get('user/?user_id=' . ($user['id'] + 1));
        $call->assertResponseStatus(200);
        $call->seeJsonEquals([]);
    }

    /**
     * Find non-existing user by email
     *
     * @return void
     */
    public function test_user_show_200_4()
    {
        $user = User::factory()->create();

        $call = $this->get('user/?email=' . $user['email'] . 'not');
        $call->assertResponseStatus(200);
        $call->seeJsonEquals([]);
    }
}
