<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Http\Requests\StoreMerchantRequest;
use App\Http\Requests\UpdateMerchantRequest;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct(){
        $this->middleware('permission:merchant|create merchant|edit merchant|delete merchant', ['only' => ['index','show']]);
        $this->middleware('permission:create merchant', ['only' => ['create','store']]);
        $this->middleware('permission:edit merchant', ['only' => ['edit','update']]);
        $this->middleware('permission:delete merchant', ['only' => ['destroy']]);
    }

    public function index()
    {
        $data = Merchant::orderBy('id', 'desc')->get();
        return view('merchant.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('merchant.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMerchantRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required'
        ]);
        $data = new Merchant();
        $data->name = $request->name;
        $data->merchant = $request->type;
        $data->public_key = $request->public_key;
        $data->private_key = $request->private_key;
        $data->sandbox = $request->sandbox;
        $data->status = $request->status;
        $data->save();
        return redirect()->back()->with('success', 'Merchant Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function show(Merchant $merchant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Merchant::find($id);
        return view('merchant.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMerchantRequest  $request
     * @param  \App\Models\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required'
        ]);
        $data = Merchant::find($id);
        $data->name = $request->name;
        $data->merchant = $request->type;
        $data->public_key = $request->public_key;
        $data->private_key = $request->private_key;
        $data->sandbox = $request->sandbox;
        $data->status = $request->status;
        $data->save();
        return redirect()->back()->with('success', 'Merchant Updated Successfully');   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Merchant $merchant)
    {
        //
    }
}
