<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProductSerialGroup extends Model
{
    use HasFactory;
    use UuidGeneratorTrait;
    use SearchTrait;

    protected $fillable = ['wholesaler', 'invoice_number'];

    protected $search_fields = ['wholesaler', 'invoice_number'];

    /**
     * ProductSerial constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (Auth::check() && ! $this->id) {
            $this->user_id = Auth::user()->id;
        }

        parent::__construct($attributes);
    }

    /**
     * Created by
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Product serials
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productSerials()
    {
        return $this->hasMany(ProductSerial::class);
    }

    /**
     * Product serials by product
     *
     * @return array
     */
    public function productSerialsByProduct()
    {
        $products = [];
        $productControl = [];

        foreach ($this->productSerials as $productSerial) {

            if (! isset($productControl[$productSerial->product_id])) {

                $temp = $productSerial->product->toArray();
                $temp['productSerials'] = [];
                $products[] = $temp;
                $productControl[$productSerial->product_id] = count($products) - 1;
            }

            $index = $productControl[$productSerial->product_id];
            $products[$index]['productSerials'][] = $productSerial->toArray();
        }

        return $products;
    }
}
