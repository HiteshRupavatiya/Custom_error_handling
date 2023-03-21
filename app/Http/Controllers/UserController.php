<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ListingApiTrait;

    public function list(Request $request)
    {
        $this->ListingValidation();
        $user = User::query();

        $searchableFields = ['name', 'email'];
        $data = $this->filterSearchPagination($user, $searchableFields);

        return ok('Users Fetched Successfully', [
            'users' => $data['query']->with('phones')->get(),
            'count' => $data['count']
        ]);
    }

    public function get($id)
    {
        $user = User::find($id);
        if ($user) {
            return ok('User Fetched Successfully', $user);
        }
        return error('User Not Found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|alpha|min:5|max:30',
            'email' => 'required|email|unique:users,email|max:40',
        ]);

        $user = User::find($id);

        if ($user) {
            $user->update($request->only(
                [
                    'name',
                    'email',
                ]
            ));
            return ok('User Updated Successfully');
        }
        return error('User Not Found');
    }

    public function delete($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return ok('User Deleted successfully');
        }
        return error('User Not Found');
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return ok('You Logged Out Successfully');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password'      => 'required|current_password',
            'new_password'  => 'required|min:8'
        ]);

        $user = Auth::user();

        $user->update(
            [
                'password' => Hash::make($request->new_password),
            ]
        );

        return ok('Password Changed Successfully');
    }
}
