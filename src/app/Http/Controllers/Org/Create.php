<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Http\Controllers\House\OrgCreate;
use App\Http\Controllers\User\Login;
use App\Models\Org\OrgUser;
use App\Models\User\User;
use App\Models\Org\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\User\Create as UserCreate;

class Create extends Controller
{
    /**
     * org-create
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        // Validate input data
        try {
            $this->validate($request, [
                'name' => 'required|string|min:1|max:255',
                'surname' => 'required|string|min:1|max:255',
                'email' => 'required|email|min:5|max:255',
                'password' => 'required|string|min:8|max:255',
                'orgName' => 'required|string|min:1|max:255|unique:organization,name',
                'userAddress' => 'filled|string|min:1|max:255',
                'orgAddress' => 'required|string|min:1|max:255'
            ]);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 422);
            }
        }

        $user = User::firstWhere('email', $request->post('email'));
        if (empty($user) === FALSE) {
            // Authenticate user
            $authRequest = Request::create('login', 'POST', [
                'email' => $request->post('email'),
                'password' => $request->post('password')
            ]);
            $authResponse = (new Login)->login($authRequest);
            if ($authResponse->status() !== 200) {
                return Response('unsuccessful authentication', 401);
            }

            // Create organization and organization warehouse
            $organization = $this->createOrg($request);

            // Create new user-org relation
            $orgUser = new OrgUser();
            $orgUser->fill([
                'is_admin' => TRUE,
                'is_owner' => TRUE,
                'user' => $user['id'],
                'organization' => $organization['id']
            ]);
            $orgUser->save();
        } else {
            // Create organization and organization warehouse
            $organization = $this->createOrg($request);

            // Create new user
            $data = [
                'name' => $request->post('name'),
                'surname' => $request->post('surname'),
                'email' => $request->post('email'),
                'password' => $request->post('password'),
            ];
            if (empty($request->post('userAddress')) === FALSE) {
                $data['address'] = $request->post('userAddress');
            }
            $registerRequest = Request::create('user', 'POST', $data);
            $registerRequest->attributes->set('org_id', $organization['id']);
            $registerResponse = (new UserCreate())->create($registerRequest);
            if ($registerResponse->status() !== 201) {
                return Response($registerResponse, 500);
            }

            // Update role to owner
            $user = User::firstWhere('email', $request->post('email'));
            $orgUser = OrgUser::where('user', $user['id'])->where('organization', $organization['id'])->first();
            $orgUser->update([
                'is_admin' => TRUE,
                'is_owner' => TRUE
            ]);
        }

        return Response('organization created', 201);
    }

    /**
     * @param Request $request
     * @return Organization|Response
     */
    private function createOrg(Request $request)
    {
        // Create organization
        $org = new Organization();
        $org->fill([
            'name' => $request->post('orgName')
        ]);
        $org->save();

        // Create organization warehouse
        $orgHouseRequest = Request::create('warehouse/org', 'POST', [
            'org_id' => $org['id'],
            'address' => $request->post('orgAddress')
        ]);
        $orgHouseResponse = (new OrgCreate())->create($orgHouseRequest);
        if ($orgHouseResponse->status() !== 201) {
            return Response('org-create', 500);
        }

        return $org;
    }
}
