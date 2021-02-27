<?php

namespace App\Events;

use App\Models\DigitalInventory;
use App\Models\InventoryMovement;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DigitalInventoryUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $digitalInventoryId;
    public $physicalInventoryMovements;

    /**
     * Construct
     * @param int $digitalInventoryId
     */
    public function __construct(int $digitalInventoryId)
    {
        $this->digitalInventoryId = $digitalInventoryId;

        $digitalInventory = DigitalInventory::query()->find($digitalInventoryId);

        $this->physicalInventoryMovements = $digitalInventory
            ->physicalInventoryMovements()
            ->with(['user', 'product'])
            ->get()
            ->toArray()
        ;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('digital-inventory-' . $this->digitalInventoryId);
    }
}
