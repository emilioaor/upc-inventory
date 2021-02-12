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
}
