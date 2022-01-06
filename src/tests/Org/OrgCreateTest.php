<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Org\Organization;
use App\Models\House\Warehouse;
use App\Models\User\User;
use App\Models\Org\OrgUser;

class OrgCreateTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Organization
     */
    private $org;

    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private array $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->org = Organization::factory()->create();
        $this->user = $this->org->orgUsers()->first()->user()->first();
        $this->user->update(['password' => hash('sha256', 'option1234')]);
        $this->data = [
            'name' => $this->user['name'],
            'surname' => $this->user['surname'],
            'email' => $this->user['email'],
            'password' => 'option1234',
            'orgName' => Organization::factory()->make()['name'],
            'orgAddress' => Warehouse::factory()->make()['address']
        ];
    }

    /**
     * Tests required data inputs
     *
     * @return void
     */
    public function test_org_create_422()
    {
        // Test required fields
        for ($x = 0; $x < count($this->data); $x++) {
            $tmp = $this->data;
            $call = $this->post('org', array_splice($tmp, $x, $x));
            $call->assertResponseStatus(422);
            $this->assertEquals('invalid input data', $call->response->content());
        }

        // Test unique field
        $this->data['orgName'] = $this->org['name'];
        $call = $this->post('org', array_splice($tmp, $x, $x));
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests failed authentication for existing user
     *
     * @return void
     */
    public function test_org_create_401()
    {
        $this->data['password'] = 'cookies332';
        $call = $this->post('org', $this->data);
        $call->assertResponseStatus(401);
        $this->assertEquals('unsuccessful authentication', $call->response->content());
    }

    /**
     * Tests organization creation from existing user
     *
     * @return void
     */
    public function test_org_create_201_1()
    {
        $call = $this->post('org', $this->data);
        $call->assertResponseStatus(201);
        $this->seeInDatabase('organization', ['name' => $this->data['orgName']]);
        $org = Organization::firstWhere('name', $this->data['orgName']);
        $this->seeInDatabase('organization_user', [
            'user' => $this->user['id'],
            'organization' => $org['id'],
            'is_owner' => TRUE,
            'is_admin' => TRUE
        ]);
        $this->seeInDatabase('organization_warehouse', ['organization' => $org['id']]);
    }

    /**
     * Tests organization creation from new user
     *
     * @return void
     */
    public function test_org_create_201_2()
    {
        $this->data['email'] = User::factory()->make()['email'];
        $call = $this->post('org', $this->data);
        $this->assertEquals(201, $call->response->status(),
            $call->response->content()
        );
        $call->assertResponseStatus(201);
        $this->seeInDatabase('organization', ['name' => $this->data['orgName']]);
        $this->seeInDatabase('user', ['email' => $this->data['email']]);
        $org = Organization::firstWhere('name', $this->data['orgName']);
        $user = User::firstWhere('email', $this->data['email']);
        $this->seeInDatabase('organization_user', [
            'user' => $user['id'],
            'organization' => $org['id'],
            'is_owner' => TRUE,
            'is_admin' => TRUE
        ]);
        $this->seeInDatabase('organization_warehouse', ['organization' => $org['id']]);
    }
}
