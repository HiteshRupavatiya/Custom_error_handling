<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    use ListingApiTrait;

    public function list(Request $request)
    {
        $this->ListingValidation();
        $candidate = Candidate::query();

        $searchableFields = ['first_name', 'last_name', 'email', 'phone'];

        $data = $this->filterSearchPagination($candidate, $searchableFields);

        return ok('Candidate Fetched Successfully', [
            'candidate' => $data['query']->get(),
            'count'     => $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'first_name' => 'required|alpha|min:5|max:20',
            'last_name'   => 'required|alpha|min:5|max:30',
            'email'       => 'required|email|unique:candidates,email',
            'phone'       => 'required|numeric|digits:10|unique:candidates,phone',
            'resume'      => 'required|mimes:pdf,doc,docx',
            'job_id'      => 'required|exists:jobs,id'
        ]);

        $file = $request->file('resume');
        $fileName = time() . $file->getClientOriginalName();

        $candidate = Candidate::create(
            $request->only(
                [
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'job_id'
                ]
            )   +
                [
                    'resume' => $fileName
                ]
        );

        if ($candidate)
            $file->move(public_path('storage/resumes'), $fileName);

        return ok('Candidate Created Successfully', $candidate);
    }

    public function get($id)
    {
        $candidate = Candidate::find($id);
        if ($candidate) {
            return ok('Candidate Fetched Successfully', $candidate);
        }
        return error('Candidate Not Found');
    }
}
