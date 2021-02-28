<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use App\Exceptions\DigitalInventoryException;
use App\Imports\DigitalInventoryImport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class DigitalInventory extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;

    protected $fillable = ['description', 'inventory_crossover_enabled'];

    protected $search_fields = ['description'];

    /**
     * DigitalInventory constructor.
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
     * Created by
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
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
     * Digital inventory movements
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function digitalInventoryMovements()
    {
        return $this->inventoryMovements()
            ->where('type', InventoryMovement::TYPE_DIGITAL)
            ->with(['user'])
            ->orderBy('id', 'DESC')
        ;
    }

    /**
     * Physical inventory movements
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function physicalInventoryMovements()
    {
        return $this->inventoryMovements()
            ->where('type', InventoryMovement::TYPE_PHYSICAL)
            ->with(['user'])
            ->orderBy('id', 'DESC')
        ;
    }

    /**
     * Digital inventory observations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function digitalInventoryObservations()
    {
        return $this->hasMany(DigitalInventoryObservation::class)
            ->with(['user'])
            ->orderBy('id', 'DESC')
        ;
    }

    /**
     * Attach document
     *
     * @param string $base64
     * @param string $filename
     * @return string
     */
    public function attachDocument(string $base64, string $filename): string
    {
        $explode = explode(',', $base64);
        $filename = sprintf('%s-%s', $filename, ((string) time()));
        $format = 'xlsx';

        $path = sprintf('digital-inventory/%s/%s.%s', $this->uuid, $filename, $format);
        Storage::disk('public')->put($path, base64_decode($explode[1]));

        return $path;
    }

    /**
     * Process the Excel file and load inventory
     *
     * @return array
     */
    public function loadInventory()
    {
        try {
            Excel::import(new DigitalInventoryImport($this), storage_path('app/public/' . $this->file));
        } catch (DigitalInventoryException $ex) {

            return ['success' => false, 'errors' => $ex->getErrors(), 'line' => $ex->getFileLine()];
        }

        return ['success' => true];
    }
}
