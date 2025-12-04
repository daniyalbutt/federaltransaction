<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use App\Http\Requests\StoreBrandsRequest;
use App\Http\Requests\UpdateBrandsRequest;
use Illuminate\Http\Request;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct(){
        $this->middleware('permission:brand|create brand|edit brand|delete brand', ['only' => ['index','show']]);
        $this->middleware('permission:create brand', ['only' => ['create','store']]);
        $this->middleware('permission:edit brand', ['only' => ['edit','update']]);
        $this->middleware('permission:delete brand', ['only' => ['destroy']]);
    }

    public function index()
    {
        $data = Brands::orderBy('id', 'desc')->get();
        return view('brand.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('brand.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBrandsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'required',
            'background_image' => 'required',
            'address_first_line' => 'required',
            'address_second_line' => 'required',
            'address_third_line' => 'required',
        ]);
        $data = new Brands();
        $data->name = $request->name;
        $data->status = $request->status;
        $data->address_first_line = $request->address_first_line;
        $data->address_second_line = $request->address_second_line;
        $data->address_third_line = $request->address_third_line;
        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data->image = 'images/'.$imageName;
        }
        if ($request->hasFile('background_image')) {
            $bgName = time().'_background.'.$request->background_image->extension();
            $request->background_image->move(public_path('images'), $bgName);
            $data->background_image = 'images/'.$bgName;
        }
        $data->save();
        return redirect()->back()->with('success', 'Brand Created Successfully');   
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Brands  $brands
     * @return \Illuminate\Http\Response
     */
    public function show(Brands $brands)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Brands  $brands
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Brands::find($id);
        return view('brand.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBrandsRequest  $request
     * @param  \App\Models\Brands  $brands
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'address_first_line' => 'required',
            'address_second_line' => 'required',
            'address_third_line' => 'required',
        ]);
        $data = Brands::find($id);
        $data->name = $request->name;
        $data->status = $request->status;
        $data->address_first_line = $request->address_first_line;
        $data->address_second_line = $request->address_second_line;
        $data->address_third_line = $request->address_third_line;
        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data->image = 'images/'.$imageName;
        }
        if ($request->hasFile('background_image')) {
            $bgName = time().'_background.'.$request->background_image->extension();
            $request->background_image->move(public_path('images'), $bgName);
            $data->background_image = 'images/'.$bgName;
        }
        $data->save();
        return redirect()->back()->with('success', 'Brand Updated Successfully');   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Brands  $brands
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Brands::find($id)->delete();
        return redirect()->back()->with('success', 'Brand Deleted Successfully');   
    }
}
