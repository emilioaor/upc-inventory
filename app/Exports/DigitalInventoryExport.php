<?php

namespace App\Exports;

use App\Models\DigitalInventory;
use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DigitalInventoryExport implements FromCollection, WithHeadings
{

    protected $digitalInventory;

    /**
     * DigitalInventoryExport constructor.
     * @param DigitalInventory $digitalInventory
     */
    public function __construct(DigitalInventory $digitalInventory)
    {
        $this->digitalInventory = $digitalInventory;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $products = [];
        $productControl = [];
        $digitalInventoryMovements = $this->digitalInventory->digitalInventoryMovements()->with(['product'])->get();
        $physicalInventoryMovements = $this->digitalInventory->physicalInventoryMovements()->with(['product'])->get();

        foreach ($digitalInventoryMovements as $digitalInventoryMovement) {

            if (! isset($productControl[$digitalInventoryMovement->product->id])) {

                $productControl[$digitalInventoryMovement->product->id] = count($products);

                $products[] = [
                    'product' => $digitalInventoryMovement->product->name,
                    'upc' => $digitalInventoryMovement->product->upc,
                    'sku' => $digitalInventoryMovement->product->sku,
                    'digital' => 0,
                    'physical' => 0,
                    'diff' => 0
                ];
            }

            $index = $productControl[$digitalInventoryMovement->product->id];
            $products[$index]['digital'] += $digitalInventoryMovement->qty;
            $products[$index]['diff'] = $products[$index]['physical'] - $products[$index]['digital'];
        }

        foreach ($physicalInventoryMovements as $physicalInventoryMovement) {

            if (! isset($productControl[$physicalInventoryMovement->product->id])) {

                $productControl[$physicalInventoryMovement->product->id] = count($products);

                $products[] = [
                    'product' => $physicalInventoryMovement->product->name,
                    'upc' => $physicalInventoryMovement->product->upc,
                    'sku' => $physicalInventoryMovement->product->sku,
                    'digital' => 0,
                    'physical' => 0,
                    'diff' => 0
                ];
            }

            $index = $productControl[$physicalInventoryMovement->product->id];
            $products[$index]['physical'] += $physicalInventoryMovement->qty;
            $products[$index]['diff'] = $products[$index]['physical'] - $products[$index]['digital'];
        }

        return new Collection($products);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Product',
            'UPC',
            'SKU',
            'Digital',
            'Physical',
            'Diff'
        ];
    }
}
