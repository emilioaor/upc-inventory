<?php

namespace App\Imports;

use App\Exceptions\DigitalInventoryException;
use App\Models\DigitalInventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DigitalInventoryImport implements ToCollection, WithHeadingRow
{
    protected $digitalInventory;


    /**
     * DigitalInventoryImport constructor.
     * @param DigitalInventory $digitalInventory
     */
    public function __construct(DigitalInventory $digitalInventory)
    {
        $this->digitalInventory = $digitalInventory;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $rows = $this->getValidRows($collection);

        foreach ($rows as $row) {

            $product = Product::query()->upcOrSku($row['upc'], $row['sku'])->first();

            if (!$product) {
                $product = new Product($row->toArray());
                $product->save();
            }

            $inventoryMovement = new InventoryMovement();
            $inventoryMovement->digital_inventory_id = $this->digitalInventory->id;
            $inventoryMovement->product_id = $product->id;
            $inventoryMovement->type = InventoryMovement::TYPE_DIGITAL;
            $inventoryMovement->qty = $row['inventory'];
            $inventoryMovement->save();
        }
    }

    /**
     * Is row valid?
     *
     * @param array $row
     * @param $index
     * @return bool
     * @throws DigitalInventoryException
     */
    public function isRowValid(array $row, $index)
    {
        if (
            isset($row['upc']) ||
            isset($row['sku']) ||
            isset($row['serial']) ||
            isset($row['name']) ||
            isset($row['location']) ||
            isset($row['inventory'])
        ) {

            $row['upc_or_sku'] = $row['upc'] . $row['sku'];

            $validator = Validator::make($row, [
                'upc_or_sku' => 'required',
                'name' => 'required',
                'inventory' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                throw new DigitalInventoryException($validator->errors()->all(), $index + 2);
            }

            return true;
        }

        return false;
    }

    /**
     * Get valid rows
     *
     * @param Collection $collection
     * @return array
     * @throws DigitalInventoryException
     */
    public function getValidRows(Collection $collection)
    {
        $rows = [];

        foreach ($collection as $i => $row) {
            if ($this->isRowValid($row->toArray(), $i)) {
                $rows[] = $row;
            }
        }

        if (count($rows) === 0) {
            throw new DigitalInventoryException([__('validation.empty', ['attribute' => 'file'])], 1);
        }

        return $rows;
    }
}
