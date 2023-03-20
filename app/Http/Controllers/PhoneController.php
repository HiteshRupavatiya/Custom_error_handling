<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;

class PhoneController extends Controller
{
    use ListingApiTrait;

    public function list(Request $request)
    {
        $this->ListingValidation();
        $phone = Phone::query();

        $searchableFields = ['phone_type', 'phone_number'];
        $data = $this->filterSearchPagination($phone, $searchableFields);

        return ok('Phones Fetched Successfully', [
            'users' => $data['query']->with('user')->get(),
            'count' => $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'phone_type'   => 'required|alpha|min:5|max:25',
            'phone_number' => 'required|numeric|digits:10|unique:phones,phone_number',
            'user_id'      => 'required|exists:users,id'
        ]);

        $phone = Phone::create($request->only(
            [
                'phone_type',
                'phone_number',
                'user_id'
            ]
        ));

        return ok('Phone Created Successfully', $phone);
    }

    public function get($id)
    {
        $phone = Phone::find($id);
        if ($phone) {
            return ok('Phone Fetched Successfully', $phone);
        }
        return error('Phone Not Found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'phone_type'   => 'required|alpha|min:5|max:25',
            'phone_number' => 'required|numeric|digits:10|unique:phones,phone_number'
        ]);

        $phone = Phone::find($id);

        if ($phone) {
            $phone->update($request->only(
                [
                    'phone_type',
                    'phone_number'
                ]
            ));
            return ok('Phone Updated Successfully');
        }
        return error('Phone Not Found');
    }

    public function delete($id)
    {
        $phone = Phone::find($id);
        if ($phone) {
            $phone->delete();
            return ok('Phone Deleted Successfully');
        }
        return error('Phone Not Found');
    }
}
