<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\House\Warehouse;
use App\Models\Org\OrgUser;
use App\Models\User\Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\User\User;
use App\Http\Controllers\Org\UserCreate as OrgUserCreate;

class Create extends Controller
{
    /**
     * user-create
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        // Validate input form data
        try {
            $this->validate($request, [
                'name' => 'required|string|min:1|max:255',
                'surname' => 'required|string|min:1|max:255',
                'email' => 'required|email|min:1|max:255',
                'password' => 'required|string|min:8|max:255',
                'address' => 'nullable|string|min:1|max:255'
            ]);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 422);
            }
        }

        $user = User::firstWhere('email', $request->post('email'));

        // Check if user already exist in system
        if (empty($user) === FALSE) {

            // Check already is in desired organization
            if (empty($user->organizations()->where('organization', $request->attributes->get('org_id'))->first()) === FALSE) {
                return Response('user already exists', 400);
            }
        } else {
            $house = new Warehouse();
            if (empty($request->post('address')) === FALSE) {
                $house->fill([
                    'address' => $request->post('address')
                ]);
            }
            $house->save();

            $user = new User();
            $user->fill([
                'name' => $request->post('name'),
                'surname' => $request->post('surname'),
                'email' => $request->post('email'),
                'password' => hash('sha256', $request->post('password')),
                'warehouse' => $house['id']
            ]);
            $user->save();

            $session = new Session();
            $session->fill([
                'session_id' => hash('sha256', $request->post('password') . date(DATE_ATOM)),
                'last_request_at' => date(DATE_ATOM),
                'user' => $user['id']
            ]);
            $session->save();
        }

        // Add user to organization
        $createRequest = Request::create('', '', [
            'user_id' => $user['id'],
            'org_id' => $request->attributes->get('org_id')
        ]);
        $createResponse = (new OrgUserCreate())->userCreate($createRequest);
        if ($createResponse->status() !== 201) {
            return Response('org-user-create', 500);
        }

        return Response('', 201);
    }
}
