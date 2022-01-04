<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User\User as UserModel;
use App\Models\House\Warehouse as WarehouseModel;
use App\Http\Controllers\User\User;

class UserShowTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return void
     */
    public function test_user_show_422()
    {
        $response = $this->call('GET', '/user');
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('invalid input data', $response->content());
    }

    /**
     * @return void
     */
    public function test_user_show_200_1()
    {
        $data = $this->createUser();
        $id = UserModel::all()->last()['id'];
        $response = $this->call('GET', '/user/' . $id);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->content());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'id' => $id,
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
        ]), $response->content());
    }

    /**
     * @return void
     */
    public function test_user_show_200_2()
    {
        $data = $this->createUser();
        $id = UserModel::all()->last()['id'];
        $response = $this->call('GET', '/user/', ['email' => $data['email']]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->content());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'id' => $id,
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
        ]), $response->content());
    }

    /**
     * @return void
     */
    public function test_user_show_200_3()
    {
        $id = UserModel::all()->last()['id'];
        $response = $this->call('GET', '/user/' . ++$id);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->content());
        $this->assertJsonStringEqualsJsonString(json_encode(json_decode("{}")), $response->content());
    }

    /**
     * @return void
     */
    public function test_user_show_200_4()
    {
        $data = $this->createUser();
        $id = UserModel::all()->last()['id'];
        $response = $this->call('GET', '/user/', ['email' => $data['email'] . 'x']);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->content());
        $this->assertJsonStringEqualsJsonString(json_encode(json_decode("{}")), $response->content());
    }

    /**
     * @return array
     */
    private function createUser(): array
    {
        $warehouse = new WarehouseModel();
        $warehouse->fill([]);
        $warehouse->save();
        $user = new UserModel();
        $data = [
            'name' => 'name',
            'surname' => 'surname',
            'email' => 'test@testing.com',
            'password' => hash('sha256', 'option1234'),
            'warehouse' => WarehouseModel::all()->last()['id']
        ];
        $user->fill($data);
        $user->save();
        return $data;
    }
}
