<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Org\Organization;

class UserEditTest extends TestCase
{
    /**
     * Tests required form fields
     *
     * @return void
     */
    public function test_user_edit_422()
    {
        // Setup
        $org = Organization::factory()->create();
        $user = $org->orgUsers()->first()
            ->user()->first();
        $bearer = $user->session()->first()['session_id'];
        $data = [
            'name' => '',
            'surname' => '',
            'email' => '',
            'password' => '1234567'
        ];

        // Test
        foreach ($data as $key => $value) {
            $call = $this->put('/user/?org_id=' . $org['id'],
                [$key => $value],
                ['Authorization' => 'Bearer ' . $bearer]
            );
            $this->assertEquals(422, $call->response->status(),
                json_encode([
                    $key => $value
                ])
            );
            $call->assertResponseStatus(422);
            $this->assertEquals('invalid input data', $call->response->content());
        }
    }

    /**
     * Tests if input field is unique
     *
     * @return void
     */
    public function test_user_edit_400()
    {
        // Setup
        $org = Organization::factory()->create();
        $user = $org->orgUsers()->first()
            ->user()->first();
        $bearer = $user->session()->first()['session_id'];

        // Test
        $call = $this->put('/user/?org_id=' . $org['id'],
            ['email' => $user['email']],
            ['Authorization' => 'Bearer ' . $bearer]
        );
        $call->assertResponseStatus(400);
        $this->assertEquals('e-mail already taken', $call->response->content());
    }

    /**
     * Tests successful user email and password edit
     *
     * @return void
     */
    public function test_user_edit_200_1()
    {
        // Setup
        $org = Organization::factory()->create();
        $user = $org->orgUsers()->first()
            ->user()->first();
        $bearer = $user->session()->first()['session_id'];
        $email = \App\Models\User\User::factory()->make()['email'];

        // Test
        $call = $this->put('/user/?org_id=' . $org['id'],
            [
                'email' => $email,
                'password' => 'tosteris'
            ],
            ['Authorization' => 'Bearer ' . $bearer]
        );
        $this->assertEquals(200, $call->response->status(),
            $call->response->content()
        );
        $call->assertResponseStatus(200);
        $call->seeJsonEquals([
            'name' => $user['name'],
            'surname' => $user['surname'],
            'email' => $email
        ]);
        $this->seeInDatabase('user', [
            'id' => $user['id'],
            'password' => hash('sha256', 'tosteris')
        ]);
    }

    /**
     * Tests successful user's name and surname edit
     *
     * @return void
     */
    public function test_user_edit_200_2()
    {
        // Setup
        $org = Organization::factory()->create();
        $user = $org->orgUsers()->first()
            ->user()->first();
        $bearer = $user->session()->first()['session_id'];
        $data = \App\Models\User\User::factory()->make();

        // Test
        $call = $this->put('/user/?org_id=' . $org['id'],
            [
                'name' => $data['name'],
                'surname' => $data['surname']
            ],
            ['Authorization' => 'Bearer ' . $bearer]
        );
        $call->assertResponseStatus(200);
        $call->seeJsonEquals([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $user['email']
        ]);

    }
}
