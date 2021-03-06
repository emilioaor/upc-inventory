<?php

namespace App\Http\Controllers;

use App\Events\DigitalInventoryUpdated;
use App\Models\DigitalInventory;
use App\Models\DigitalInventoryObservation;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $digitalInventories = DigitalInventory::query()
            ->with(['user'])
            ->search($request->search)
            ->where('inventory_crossover_enabled', true)
            ->orderBy('id', 'DESC')
            ->paginate();

        return view('inventory.index', compact('digitalInventories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $digitalInventory = DigitalInventory::query()
            ->where('id', $request->digital_inventory_id)
            ->where('inventory_crossover_enabled', true)
            ->firstOrFail()
        ;
        $product = Product::query()->upcOrSku($request->upc, $request->upc)->first();

        if (! $product) {
            return response()->json(['success' => false, 'data' => null]);
        }

        $inventoryMovement = new InventoryMovement();
        $inventoryMovement->digital_inventory_id = $digitalInventory->id;
        $inventoryMovement->product_id = $product->id;
        $inventoryMovement->type = InventoryMovement::TYPE_PHYSICAL;
        $inventoryMovement->scan_method = $request->scan_method;

        if ($request->scan_method === InventoryMovement::SCAN_METHOD_BOXES) {
            $inventoryMovement->qty_per_box = $request->qty_per_box;
            $inventoryMovement->boxes_qty = 1;
            $inventoryMovement->qty = $request->qty_per_box;
        } else {
            $inventoryMovement->qty = 1;
        }

        $inventoryMovement->save();

        $inventoryMovement->product = $inventoryMovement->product;

        broadcast(new DigitalInventoryUpdated($digitalInventory->id));

        return response()->json(['success' => true, 'data' => $inventoryMovement]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::query()->where('upc', $id)->first();

        return response()->json(['success' => true, 'data' => $product]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $digitalInventory = DigitalInventory::query()
            ->with([
                'digitalInventoryMovements.product',
                'physicalInventoryMovements.product',
                'user',
                'digitalInventoryObservations'
            ])
            ->uuid($id)
            ->where('inventory_crossover_enabled', true)
            ->firstOrFail()
        ;

        return view('inventory.form', compact('digitalInventory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $inventoryMovement = InventoryMovement::query()->findOrFail($id);

        if ($inventoryMovement->scan_method === InventoryMovement::SCAN_METHOD_BOXES) {

            $inventoryMovement->qty_per_box = $request->qty_per_box;
            $inventoryMovement->boxes_qty = $request->boxes_qty;
            $inventoryMovement->qty = $inventoryMovement->qty_per_box * $inventoryMovement->boxes_qty;

        } else {
            $inventoryMovement->qty = $request->qty;
        }

        $inventoryMovement->save();

        broadcast(new DigitalInventoryUpdated($inventoryMovement->digital_inventory_id));

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $inventoryMovement = InventoryMovement::query()->findOrFail($id);
        $inventoryMovement->delete();

        broadcast(new DigitalInventoryUpdated($inventoryMovement->digital_inventory_id));

        return response()->json(['success' => true]);
    }

    /**
     * Add observation
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function observation(Request $request, $id)
    {
        $digitalInventory = DigitalInventory::query()->uuid($id)->firstOrFail();

        $observation = new DigitalInventoryObservation($request->all());
        $observation->digital_inventory_id = $digitalInventory->id;
        $observation->save();

        $observations = $digitalInventory->digitalInventoryObservations()->get();

        return response()->json(['success' => true, 'data' => $observations]);
    }
}
