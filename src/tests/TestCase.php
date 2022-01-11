<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User\User;
use App\Models\Org\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\User\Create;
use App\Models\House\Warehouse;
use App\Models\Org\OrgUser;
use App\Models\Cloth\ClothingUnit;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    /**
     * @var Organization
     */
    protected Organization $_organization;

    /**
     * @var User
     */
    protected User $_owner;

    /**
     * @var User
     */
    protected User $_admin;

    /**
     * @var User
     */
    protected User $_user;

    /**
     * @var string
     */
    protected string $_ownerBearer;

    /**
     * @var string
     */
    protected string $_adminBearer;

    /**
     * @var string
     */
    protected string $_userBearer;

    /**
     * @var Warehouse
     */
    protected Warehouse $_organizationWarehouse;

    /**
     * @var Warehouse
     */
    protected Warehouse $_ownerWarehouse;

    /**
     * @var Warehouse
     */
    protected Warehouse $_adminWarehouse;

    /**
     * @var Warehouse
     */
    protected Warehouse $_userWarehouse;

    /**
     * @var array
     */
    protected array $_clothingUnits;

    protected const _password = 'option1234';

    /**
     * @return void
     */
    protected function setVariables(): void
    {
        // Setup organization
        $this->_organization = Organization::factory()->create();
        $this->_organizationWarehouse = Warehouse::find($this->_organization->orgHouses()->first()['warehouse']);

        // Create clothing units
        $this->_clothingUnits = ClothingUnit::factory()->count(3)->create([
            'organization' => $this->_organization['id'],
            'warehouse' => $this->_organizationWarehouse['id']
        ])->toArray();

        // Setup owner
        $this->_owner = $this->_organization->orgUsers()->first()->user()->first();
        $this->_owner->update(['password' => hash('sha256', self::_password)]);
        $this->_ownerBearer = $this->_owner->session()->first()['session_id'];
        $this->_ownerWarehouse = Warehouse::find($this->_owner['warehouse']);

        //--------------------------------------------------------------------------------------
        // Setup admin
        $this->_admin = User::factory()->create(['password' => hash('sha256', self::_password)]);
        $this->_adminBearer = $this->_admin->session()->first()['session_id'];
        $this->_adminWarehouse = Warehouse::find($this->_admin['warehouse']);

        // Add admin to organization
        $request = Request::create('user', 'POST', [
            'name' => $this->_admin['name'],
            'surname' => $this->_admin['surname'],
            'email' => $this->_admin['email'],
            'password' => self::_password
        ]);
        $request->attributes->set('org_id', $this->_organization['id']);
        $response = (new Create())->create($request);

        // Set admin role
        $orgUser = OrgUser::where('user', $this->_admin['id'])
            ->where('organization', $this->_organization['id'])
            ->first()
            ->update([
            'is_admin' => TRUE
        ]);

        //--------------------------------------------------------------------------------------
        // Setup user
        $this->_user = User::factory()->create(['password' => hash('sha256', self::_password)]);
        $this->_userBearer = $this->_user->session()->first()['session_id'];
        $this->_userWarehouse = Warehouse::find($this->_user['warehouse']);

        // Add user to organization
        $request = Request::create('user', 'POST', [
            'name' => $this->_user['name'],
            'surname' => $this->_user['surname'],
            'email' => $this->_user['email'],
            'password' => self::_password
        ]);
        $request->attributes->set('org_id', $this->_organization['id']);
        $response = (new Create())->create($request);

        // Set user role
        OrgUser::where('user', $this->_user['id'])
            ->where('organization', $this->_organization['id'])
            ->first()
            ->update([
                'is_admin' => FALSE
            ]);
    }

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }
}
