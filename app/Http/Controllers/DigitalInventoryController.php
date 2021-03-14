<?php

namespace App\Http\Controllers;

use App\Exports\DigitalInventoryExport;
use App\Models\DigitalInventory;
use App\Service\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DigitalInventoryController extends Controller
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
            ->orderBy('id', 'DESC')
            ->paginate();

        return view('digital-inventory.index', compact('digitalInventories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('digital-inventory.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        $digitalInventory = new DigitalInventory($request->all());
        $digitalInventory->file = $digitalInventory->attachDocument($request->file, 'digital-inventory');
        $digitalInventory->save();

        $p = $digitalInventory->loadInventory();

        if (! $p['success']) {
            return response()->json(['success' => false, 'errors' => $p['errors'], 'line' => $p['line']], 400);
        }

        DB::commit();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('digital-inventory.index')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
                'user'
            ])
            ->uuid($id)
            ->firstOrFail()
        ;

        return view('digital-inventory.form', compact('digitalInventory'));
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
        $digitalInventory = DigitalInventory::query()->uuid($id)->firstOrFail();
        $digitalInventory->fill($request->all());
        $digitalInventory->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('digital-inventory.edit', [$id])]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Export to excel
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function excel($id)
    {
        $digitalInventory = DigitalInventory::query()->uuid($id)->firstOrFail();

        return Excel::download(new DigitalInventoryExport($digitalInventory), 'digital-inventory-' . $id . '.xlsx');
    }
}
