<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProductSerial extends Model
{
    use HasFactory;
    use UuidGeneratorTrait;
    use SearchTrait;

    protected $fillable = ['serial', 'product_id'];

    protected $search_fields = ['serial', 'products.name'];

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
     * Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
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
