<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;

class JobController extends Controller
{
    use ListingApiTrait;

    public function list(Request $request)
    {
        $this->ListingValidation();
        $job = Job::query();

        $searchableFields = ['type'];

        $data = $this->filterSearchPagination($job, $searchableFields);

        return ok('Jobs Fetched Successfully', [
            'jobs'  => $data['query']->get(),
            'count' => $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'type'       => 'required|string|min:6|max:40|unique:jobs,type',
            'vacancy'    => 'required|numeric|min:1',
            'company_id' => 'required|exists:companies,id'
        ]);

        $job = Job::create(
            $request->only(
                [
                    'type',
                    'vacancy',
                    'company_id'
                ]
            )
        );

        return ok('Job Created Successfully', $job);
    }

    public function get($id)
    {
        $job = Job::find($id);
        if ($job) {
            return ok('Job Fetched Successfully', $job);
        }
        return error('Job Not Found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type'    => 'required|string|min:6|max:40|unique:jobs,type',
            'vacancy' => 'required|numeric|min:1'
        ]);

        $job = Job::find($id);

        if ($job) {
            $job->update($request->only(
                [
                    'type',
                    'vacancy'
                ]
            ));

            return ok('Job Updated Successfully');
        }
        return error('Job Not Found');
    }

    public function delete($id)
    {
        $job = Job::find($id);
        if ($job) {
            $job->delete();
            return ok('Job Deleted Successfully');
        }
        return error('Job Not Found');
    }

    public function forceDelete($id)
    {
        $job = Job::onlyTrashed()->find($id);
        if ($job) {
            $job->forceDelete();
            return ok('Job Forced Deleted Successfully');
        }
        return error('Job Not Found');
    }
}
