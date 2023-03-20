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
        $user = User::query()->get();
        $this->ListingValidation();
        $searchableFields = ['name', 'email'];
        return ok('Users Fetched Successfully', $this->filterSearchPagination($user, $searchableFields));
    }
}
