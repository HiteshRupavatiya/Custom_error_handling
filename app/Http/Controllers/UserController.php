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
            'users' => $data['query']->get(),
            'count' => $data['count']
        ]);
    }
}
