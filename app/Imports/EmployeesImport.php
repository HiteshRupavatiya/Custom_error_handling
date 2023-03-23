<?php

namespace App\Imports;

use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class EmployeesImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Employee([
            'first_name'   => $row[1],
            'last_name'    => $row[2],
            'email'        => $row[3],
            'phone'        => $row[4],
            'joining_date' => $row[5],
            'company_id'   => $row[6],
            'created_at'   => $row[7],
            'updated_at'   => $row[8],
            'deleted_at'   => $row[9],
            'user_id'      => $row[10]
        ]);
    }
}
