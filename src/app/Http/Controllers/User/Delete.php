<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\House\Warehouse;
use App\Models\User\Session;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Delete extends Controller
{
    public function delete($id)
    {
        // Validate input data
        if (empty($id) === TRUE || is_int($id) === FALSE) {
            return Response('invalid input data', 500);
        }

        // Delete session -> user -> warehouse
        $user = User::firstWhere('id', $id);
        Session::destroy($user->session()->first()['id']);
        User::destroy($user['id']);
        Warehouse::destroy($user['warehouse']);

        return Response('', 204);
    }
}
