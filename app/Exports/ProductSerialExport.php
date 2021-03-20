<?php

namespace App\Exports;

use App\Models\ProductSerialGroup;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;


class ProductSerialExport extends StringValueBinder implements FromView, ShouldAutoSize, WithCustomValueBinder
{

    protected $productSerialGroup;

    /**
     * ProductSerialExport constructor.
     * @param ProductSerialGroup $productSerialGroup
     */
    function __construct(ProductSerialGroup $productSerialGroup)
    {
        $this->productSerialGroup = $productSerialGroup;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return \view('excel.product-serial', [
            'productSerialGroup' => $this->productSerialGroup,
            'productSerialsByProduct' => $this->productSerialGroup->productSerialsByProduct()
        ]);
    }
}
