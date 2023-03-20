<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
