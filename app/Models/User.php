<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;

    /** Roles */
    const ROLE_ADMIN = 'administrator';
    const ROLE_INVENTORY_MANAGER = 'inventory_manager';
    const ROLE_WAREHOUSE = 'warehouse';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $search_fields = ['name', 'email', 'role'];

    /**
     * Is admin?
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Is inventory manager?
     *
     * @return bool
     */
    public function isInventoryManager()
    {
        return $this->role === self::ROLE_INVENTORY_MANAGER;
    }

    /**
     * Is warehouse?
     *
     * @return bool
     */
    public function isWarehouse()
    {
        return $this->role === self::ROLE_WAREHOUSE;
    }

    /**
     * Exclude me from select
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotMe(Builder $query): Builder
    {
        return $query->where('id', '<>', Auth::user()->id)->where('email', '<>', 'emilioaor@gmail.com');
    }

    /**
     * Status available
     *
     * @return array
     */
    public static function rolesAvailable()
    {
        return [
            self::ROLE_ADMIN => __('role.' . self::ROLE_ADMIN),
            self::ROLE_INVENTORY_MANAGER => __('role.' . self::ROLE_INVENTORY_MANAGER),
            self::ROLE_WAREHOUSE => __('role.' . self::ROLE_WAREHOUSE),
        ];
    }

    /**
     * Digital inventories
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function digitalInventories()
    {
        return $this->hasMany(DigitalInventory::class);
    }

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
     * Digital inventory observations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function digitalInventoryObservations()
    {
        return $this->hasMany(DigitalInventoryObservation::class);
    }

    /**
     * Product serials loaded by this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productSerials()
    {
        return $this->hasMany(ProductSerial::class);
    }
}
