<?php

namespace App\Models;

use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class InventoryMovement extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UuidGeneratorTrait;

    /** Types */
    const TYPE_DIGITAL = 'digital';
    const TYPE_PHYSICAL = 'physical';

    protected $fillable = ['digital_inventory_id', 'product_id', 'type', 'qty'];

    /**
     * InventoryMovement constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (Auth::check()) {
            $this->user_id = Auth::user()->id;
        }

        parent::__construct($attributes);
    }

    /**
     * Digital inventory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function digitalInventory()
    {
        return $this->belongsTo(DigitalInventory::class)->withTrashed();
    }

    /**
     * Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * Created by
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
