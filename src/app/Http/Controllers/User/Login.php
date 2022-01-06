<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\User\User as UserModel;
use App\Models\User\Session as SessionModel;
use Laravel\Lumen\Http\ResponseFactory;

class Login extends Controller
{
    /**
     * user-login
     *
     * @param Request $request
     * @return Response|ResponseFactory
     */
    public function login(Request $request)
    {
        // Validate POST params
        try {
            $this->validate($request, [
                'email' => 'required|email|min:5|max:255',
                'password' => 'required|string|min:8|max:255'
            ]);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 422);
            }
        }

        $user = UserModel::firstWhere('email', $request->post('email'));

        // Validate email
        if (empty($user)) return Response('unsuccessful authentication', 401);

        // Validate password
        if (hash('sha256', $request->post('password')) !== $user['password']) {
            return Response('unsuccessful authentication', 401);
        }

        // Update session data
        $session = SessionModel::firstWhere('user', $user['id']);
        $session->update([
            'session_id' => hash('sha256', date(DATE_ATOM) . $this->generateRandomString()),
            'last_request_at' => date(DATE_ATOM)
        ]);

        // Get user's associated organizations
        $organizations = DB::table('organization_user')
            ->select('organization.*')
            ->join('organization', 'organization_user.organization', '=', 'organization.id')
            ->where('organization_user.user', '=', $user['id'])
            ->get();

        return Response(json_encode([
            'barer_token' => $session['session_id'],
            'organizations' => $organizations
        ]), 200);
    }

    /**
     * Generates random string
     *
     * @param $length
     * @return string
     */
    private function generateRandomString($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
