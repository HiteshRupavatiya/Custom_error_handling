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
        $users = User::get();
        $this->ListingValidation();
        $searchableFields = ['name', 'email'];
        $data = $this->filterSearchPagination($users, $searchableFields);
        return ok('Users Fetched Successfully', $data);
    }
}
