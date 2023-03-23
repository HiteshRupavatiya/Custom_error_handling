<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;

class EmployeesExport implements FromCollection
{
    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date   = $end_date;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Employee::where('user_id', Auth::user()->id)->whereBetween('joining_date', [$this->start_date, $this->end_date])->get();
    }
}
