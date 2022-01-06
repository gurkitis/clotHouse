<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Org\Organization;
use App\Models\House\Warehouse;

class HouseOrgCreateTest extends TestCase
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
     * Tests required data inputs
     *
     * @return void
     */
    public function test_house_org_create_422()
    {
        $call = $this->post('warehouse/org/?org_id=' . $this->org['id'], [], [
            'Authorization' => 'Bearer ' . $this->bearer
        ]);
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests successful organization warehouse creation
     *
     * @return void
     */
    public function test_house_org_create_201()
    {
        $call = $this->post('warehouse/org/?org_id=' . $this->org['id'], [
            'address' => 'testAddress'
        ], ['Authorization' => 'Bearer ' . $this->bearer]);
        $call->assertResponseStatus(201);
        $this->seeInDatabase('organization_warehouse', [
            'organization' => $this->org['id'],
            'warehouse' => Warehouse::orderByDesc('id')->first()['id']
        ]);
        $this->seeInDatabase('warehouse', [
            'id' => Warehouse::orderByDesc('id')->first()['id'],
            'address' => 'testAddress'
        ]);
    }
}
