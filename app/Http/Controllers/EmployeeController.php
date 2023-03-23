<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;
use App\Exports\EmployeesExport;
use App\Imports\EmployeesImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    use ListingApiTrait;

    public function list(Request $request)
    {
        $this->ListingValidation();
        $employee = Employee::query();

        $searchableFields = ['first_name', 'last_name', 'email', 'phone'];

        $data = $this->filterSearchPagination($employee, $searchableFields);

        return ok('Employees Fetched Successfully', [
            'employees' => $data['query']->with('tasks')->where('user_id', Auth::user()->id)->get(),
            'count'     => $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'first_name'   => 'required|string|min:5|max:40',
            'last_name'    => 'required|string|min:5|max:40',
            'email'        => 'required|email|unique:employees,email',
            'phone'        => 'required|numeric|digits:10|unique:employees,phone',
            'joining_date' => 'required|date|date_format:Y-m-d|before_or_equal:' . now(),
            'company_id'   => 'required|exists:companies,id'
        ]);

        $employee = Employee::create(
            $request->only(
                [
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'joining_date',
                    'company_id'
                ]
            )
        );

        return ok('Employee Created Successfully', $employee);
    }

    public function get($id)
    {
        $employee = Employee::where('user_id', Auth::user()->id)->find($id);
        if ($employee) {
            return ok('Employee Fetched Successfully', $employee);
        }
        return error('Employee Not Found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name'   => 'required|string|min:5|max:40',
            'last_name'    => 'required|string|min:5|max:40',
            'email'        => 'required|email|unique:employees,email',
            'phone'        => 'required|numeric|digits:10|unique:employees,phone',
            'joining_date' => 'required|date|date_format:Y-m-d|before_or_equal:' . now(),
        ]);

        $employee = Employee::where('user_id', Auth::user()->id)->find($id);

        if ($employee) {
            $employee->update($request->only(
                [
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'joining_date'
                ]
            ));

            return ok('Employee Updated Successfully');
        }
        return error('Employee Not Found');
    }

    public function delete($id)
    {
        $employee = Employee::where('user_id', Auth::user()->id)->find($id);
        if ($employee) {
            $employee->delete();
            return ok('Employee Deleted Successfully');
        }
        return error('Employee Not Found');
    }

    public function forceDelete($id)
    {
        $employee = Employee::onlyTrashed()->where('user_id', Auth::user()->id)->find($id);
        if ($employee) {
            $employee->forceDelete();
            return ok('Employee Forced Deleted Successfully');
        }
        return error('Employee Not Found');
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|date_format:Y-m-d|before:' . now(),
            'end_date'   => 'required|date|date_format:Y-m-d|before_or_equal:' . now(),
        ]);

        return Excel::download(new EmployeesExport($request->start_date, $request->end_date), 'employee_data.csv');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv'
        ]);

        Excel::import(new EmployeesImport, $request->file('file')->store('temp'));

        return ok('Employee Imported Successfully');
    }
}
