<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSerial extends Model
{
    use HasFactory;
    use UuidGeneratorTrait;
    use SearchTrait;

    protected $fillable = ['serial', 'product_id', 'product_serial_group_id'];

    protected $search_fields = ['serial', 'products.name'];

    /**
     * Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Product serial group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productSerialGroup()
    {
        return $this->belongsTo(ProductSerialGroup::class);
    }

    /**
     * Scope search
     *
     * @param Builder $query
     * @param null|string $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $query->select(['product_serials.*'])->join('products', 'products.id', '=', 'product_serials.product_id');

        return $this->_search($query, $search);
    }
}
