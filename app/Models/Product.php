<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;

    protected $fillable = ['name', 'upc', 'sku', 'serial', 'location'];

    protected $search_fields = ['name', 'upc', 'sku', 'serial', 'location'];

    /**
     * Inventory movements
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Scope UPC or SKU
     *
     * @param Builder $query
     * @param $upc
     * @param $sku
     * @return Builder
     */
    public function scopeUpcOrSku(Builder $query, $upc, $sku)
    {
        return $query
            ->where(function ($q) use ($upc) {
                $q->where('upc', $upc)->whereNotNull('upc');
            })
            ->orWhere(function ($q) use ($sku) {
                $q->where('sku', $sku)->whereNotNull('sku');
            })
        ;
    }
}
