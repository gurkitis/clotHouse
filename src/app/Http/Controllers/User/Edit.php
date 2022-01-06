<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class Edit extends Controller
{
    public function edit(Request $request, $id = NULL)
    {
        // Validate form input data
        $rules = [
            'name' => 'filled|string|min:1|max:255',
            'surname' => 'filled|string|min:1|max:255',
            'email' => 'filled|email|min:5|max:255',
            'password' => 'filled|string|min:8|max:255'
        ];
        try {
            $this->validate($request, $rules);
        } catch (ValidationException $e) {
            if (env('app_env') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 422);
            }
        }

        // Check if email is already taken
        if (empty($request->input('email')) === FALSE) {
            if (empty(User::firstWhere('email', $request->input('email'))) === FALSE) {
                return Response('e-mail already taken', 400);
            }
        }

        // Process password if defined
        $password = NULL;
        if (empty($request->input('password')) === FALSE) {
            $password = hash('sha256', $request->input('password'));
        }

        // Update user data
        $user = User::firstWhere('id', $request->attributes->get('user_id'));
        foreach ($request->all(array_keys($rules)) as $key => $value) {
            if ($value === NULL) continue;
            if ($key === 'password') $value = $password;
            $user->update([$key => $value]);
        }

        return Response(json_encode([
            'name' => $user['name'],
            'surname' => $user['surname'],
            'email' => $user['email']
        ]), 200);
    }
}
