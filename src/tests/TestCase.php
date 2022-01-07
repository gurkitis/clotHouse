<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User\User;
use App\Models\Org\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\User\Create;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    /**
     * @var User
     */
    protected User $_owner;

    /**
     * @var User
     */
    protected User $_user;

    /**
     * @var Organization
     */
    protected Organization $_organization;

    /**
     * @var string
     */
    protected string $_userBearer;

    /**
     * @var string
     */
    protected string $_ownerBearer;

    protected const _password = 'option1234';

    protected function setVariables(): void
    {
        $this->_organization = Organization::factory()->create();
        $this->_owner = $this->_organization->orgUsers()->first()->user()->first();
        $this->_owner->update(['password' => hash('sha256', self::_password)]);
        $this->_ownerBearer = $this->_owner->session()->first()['session_id'];

        $this->_user = User::factory()->create(['password' => hash('sha256', self::_password)]);
        $this->_userBearer = $this->_user->session()->first()['session_id'];
        $request = Request::create('user', 'POST', [
            'name' => $this->_user['name'],
            'surname' => $this->_user['surname'],
            'email' => $this->_user['email'],
            'password' => self::_password
        ]);
        $request->attributes->set('org_id', $this->_organization['id']);
        $response = (new Create())->create($request);
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
