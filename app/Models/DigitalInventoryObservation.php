<?php

namespace App\Models;

use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DigitalInventoryObservation extends Model
{
    use HasFactory;
    use UuidGeneratorTrait;

    protected $fillable = ['digital_inventory_id', 'content'];

    /**
     * DigitalInventoryObservation constructor.
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
     * Digital inventory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function digitalInventory()
    {
        return $this->belongsTo(DigitalInventory::class);
    }

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
