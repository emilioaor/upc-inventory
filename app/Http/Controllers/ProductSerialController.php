<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\ProductSerialGroup;
use App\Service\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductSerialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $productSerialGroups = ProductSerialGroup::query()
            ->with(['user'])
            ->search($request->search)
            ->orderBy('id', 'DESC')
            ->paginate()
        ;

        return view('product-serial.index', compact('productSerialGroups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('product-serial.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $productSerialGroup = new ProductSerialGroup($request->all());
        $productSerialGroup->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('product-serial.edit', [$productSerialGroup->uuid])]);
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
        $productSerialGroup = ProductSerialGroup::query()->with(['productSerials.product'])->uuid($id)->firstOrFail();

        return view('product-serial.form', compact('productSerialGroup'));
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
        $productSerialGroup = ProductSerialGroup::query()->uuid($id)->firstOrFail();
        $productSerialGroup->fill($request->all());
        $productSerialGroup->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('product-serial.edit', [$id])]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $productSerial = ProductSerial::query()->uuid($id)->firstOrFail();
        $productSerial->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Search by product
     *
     * @param $id
     * @return JsonResponse
     */
    public function byProduct($id)
    {
        $product = Product::query()->uuid($id)->firstOrFail();

        return response()->json(['success' => true, 'data' => $product->productSerials()->get()]);
    }

    /**
     * Add serial
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addSerial(Request $request)
    {
        $exists = ProductSerial::query()->with(['product'])->whereIn('serial', $request->serial)->first();

        if ($exists) {
            return response()->json(['success' => false, 'err' => 'serial_exists', 'data' => $exists], 400);
        }

        DB::beginTransaction();

        $productSerials = [];

        foreach ($request->serial as $serial) {
            $productSerial = new ProductSerial($request->all());
            $productSerial->serial = $serial;
            $productSerial->save();

            $productSerials[] = $productSerial;
        }

        DB::commit();

        return response()->json(['success' => true, 'data' => $productSerials]);
    }
}
