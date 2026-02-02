<?php

namespace App\Http\Controllers\Admin;

use Flash;
use App\Models\Product;
use App\Models\RewardPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\ProductDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductDataTable $dataTable, Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('products.view') ;
        // $products = Product::all();
        // return view('admin.product.index')->with('products', $products);
       return $dataTable->render('admin.product.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('products.create') ;
        return view('admin.product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductRequest $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('products.create') ;
        $input = $request->except(['point', 'bag']);
        try {
            DB::beginTransaction();
            $product = Product::create($input);
            $rewardPoint = RewardPoint::create(['product_id'=> $product->id, 'bag'=> $request->bag, 'point'=> $request->point]) ;
            Flash::success('Product saved successfully.');
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            Flash::error($ex->getMessage());
        }
       
        
        return redirect(route('products.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('products.view') ;
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('products.edit') ;
        $product = Product::with('reward_point')->find($id);
       // return $product;
        if (empty($product)) {
            Flash::error('Product not found');

            return redirect(route('products.index'));
        }

        return view('admin.product.edit')->with('product', $product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, $id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('products.edit') ;
        $product = Product::find($id);
        $input = $request->except(['point', 'bag']);
        $product->update($input);
        if (empty($product)) {
            Flash::error('Product not found');

            return redirect(route('products.index'));
        }
        RewardPoint::updateOrCreate(
            ['id' => $product->reward_point->id ?? null],
            [
            'product_id' => $product->id,
            'point' => $request->point,
            'bag' => $request->bag,
        ]);
     
        Flash::success('Product updated successfully.');

        return redirect(route('products.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('products.delete') ;
        $product = Product::find($id);
        if (empty($product)) {
            Flash::error('Product not found');
            return redirect(route('products.index'));
        }

        $product->delete();

        Flash::success('Product deleted successfully.');
        return redirect(route('products.index'));
    }
}
