<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    use ListingApiTrait;

    public function list(Request $request)
    {
        $this->ListingValidation();
        $company = Company::query();

        $searchableFields = ['company_name', 'email', 'website'];

        $data = $this->filterSearchPagination($company, $searchableFields);

        return ok('Companies Fetched Successfully', [
            'companies' => $data['query']->where('user_id', Auth::user()->id)->with('employees', 'jobs')->get(),
            'count'     => $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|min:5|max:30|unique:companies,company_name',
            'email'        => 'required|email|unique:companies,email',
            'logo'         => 'required|image|mimes:jpeg,png,jpg|dimensions:min_width=100,min_height=100',
            'website'      => 'required|url|unique:companies,website'
        ]);

        $file = $request->file('logo');
        $fileName = time() . $file->getClientOriginalName();

        $user_id = Auth::user()->id;

        $company = Company::create(
            $request->only(
                [
                    'company_name',
                    'email',
                    'website'
                ]
            )   +
                [
                    'logo'    => $fileName,
                    'user_id' => $user_id
                ]
        );

        if ($company)
            $file->move(public_path('storage/'), $fileName);

        return ok('Company Created Successfully', $company);
    }

    public function get($id)
    {
        $company = Company::where('user_id', Auth::user()->id)->find($id);
        if ($company) {
            return ok('Company Fetched Successfully', $company);
        }
        return error('Company Not Found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'company_name' => 'required|string|min:5|max:30|unique:companies,company_name',
            'email'        => 'required|email|unique:companies,email',
            'logo'         => 'image|mimes:jpeg,png,jpg|dimensions:min_width=100,min_height=100',
            'website'      => 'required|url|unique:companies,website'
        ]);

        $company = Company::where('user_id', Auth::user()->id)->find($id);

        $file = $request->file('logo');

        if ($company) {
            if ($request->hasFile('logo')) {
                if ($company->logo) {
                    unlink(public_path('storage/') . $company->logo);
                }
                $fileName = time() . $file->getClientOriginalName();
                $file->move(public_path('storage/'), $fileName);

                $company->update(
                    [
                        'logo' => $fileName
                    ]
                );
            }

            $company->update($request->only(
                [
                    'company_name',
                    'email',
                    'website'
                ]
            ));
            return ok('Company Updated Successfully');
        }
        return error('Company Not Found');
    }

    public function delete($id)
    {
        $company = Company::where('user_id', Auth::user()->id)->find($id);
        if ($company) {
            $company->delete();
            return ok('Company Deleted Successfully');
        }
        return error('Company Not Found');
    }

    public function forceDelete($id)
    {
        $company = Company::onlyTrashed()->where('user_id', Auth::user()->id)->find($id);
        if ($company) {
            if ($company->logo) {
                unlink(public_path('storage/') . $company->logo);
            }
            $company->forceDelete();
            return ok('Company Forced Deleted Successfully');
        }
        return error('Company Not Found');
    }
}
