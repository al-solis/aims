<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class DispositionReportExport implements FromView, ShouldAutoSize, WithTitle, WithDrawings
{
    public function __construct(private array $data)
    {
    }

    public function view(): View
    {
        return view('reports.employee-disposition-excel', $this->data);
    }

    public function drawings(): array
    {
        $left = new Drawing();
        $left->setName('Logo Left');
        $left->setPath(public_path('images/sosia.png'));
        $left->setHeight(70);
        $left->setCoordinates('A1');
        $left->setOffsetX(10);
        $left->setOffsetY(5);

        $right = new Drawing();
        $right->setName('Logo Right');
        $right->setPath(public_path('images/logo.PNG'));
        $right->setHeight(70);
        $right->setCoordinates('N1');
        $right->setOffsetX(10);
        $right->setOffsetY(5);

        return [$left, $right];
    }

    public function title(): string
    {
        return 'Monthly Disposition';
    }
}