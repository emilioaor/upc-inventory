<?php

namespace App\Models;

use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
