<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSerial;
use Illuminate\Http\Request;
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
        $productSerials = ProductSerial::query()
            ->with(['product', 'user'])
            ->search($request->search)
            ->orderBy('id', 'DESC')
        ;

        $productSerials = ! $request->paginate || $request->paginate === 'true' ?
            $productSerials->paginate() :
            $productSerials->get()
        ;

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $productSerials]);
        }

        return view('product-serial.index', compact('productSerials'));
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
        $exists = ProductSerial::query()->with(['product'])->where('serial', $request->serial)->first();

        if ($exists) {
            return response()->json(['success' => false, 'err' => 'serial_exists', 'data' => $exists], 400);
        }

        $productSerial = new ProductSerial($request->all());
        $productSerial->save();

        return response()->json(['success' => true]);
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
        $product = Product::query()->uuid($id)->firstOrFail();


        return view('product-serial.form', compact('product'));
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
        //
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
}
