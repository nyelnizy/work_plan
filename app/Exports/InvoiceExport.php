<?php

namespace App\Exports;

use App\Models\Work;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoiceExport implements FromCollection, WithHeadings
{
    private $start_date, $end_date;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Work::where('start_date', '>=', $this->start_date)->
        where('start_date', '<=', $this->end_date)
            ->where('work_type', 'Status')
            ->orderBy('start_date')
            ->selectRaw('date(start_date) as date,
        start_time as start,end_time as end,
        hours as hours,summary as description')->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Start (Ghana)',
            'End (Ghana)',
            'Hours',
            'Description',
        ];
    }
}
