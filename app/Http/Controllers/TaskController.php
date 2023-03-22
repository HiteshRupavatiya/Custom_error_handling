<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;

class TaskController extends Controller
{
    use ListingApiTrait;

    public function list(Request $request)
    {
        $this->ListingValidation();
        $task = Task::query();

        $searchableFields = ['title', 'description'];

        $data = $this->filterSearchPagination($task, $searchableFields);

        return ok('Tasks Fetched Successfully', [
            'tasks' => $data['query']->get(),
            'count' => $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|min:6|max:100',
            'description'  => 'required|string|max:250',
            'employee_id'  => 'required|exists:employees,id'
        ]);

        $task = Task::create(
            $request->only(
                [
                    'title',
                    'description',
                    'employee_id'
                ]
            )
        );

        return ok('Task Created Successfully', $task);
    }

    public function get($id)
    {
        $task = Task::find($id);
        if ($task) {
            return ok('Task Fetched Successfully', $task);
        }
        return error('Task Not Found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'        => 'required|string|min:6|max:100',
            'description'  => 'required|string|max:250',
        ]);

        $task = Task::find($id);

        if ($task) {
            $task->update($request->only(
                [
                    'title',
                    'description'
                ]
            ));

            return ok('Task Updated Successfully');
        }
        return error('Task Not Found');
    }

    public function delete($id)
    {
        $task = Task::find($id);
        if ($task) {
            $task->delete();
            return ok('Task Deleted Successfully');
        }
        return error('Task Not Found');
    }

    public function forceDelete($id)
    {
        $task = Task::onlyTrashed()->find($id);
        if ($task) {
            $task->forceDelete();
            return ok('Task Forced Deleted Successfully');
        }
        return error('Task Not Found');
    }
}
