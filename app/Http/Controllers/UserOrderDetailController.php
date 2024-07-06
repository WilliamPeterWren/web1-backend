<?php

namespace App\Http\Controllers;

use App\Models\UserOrderDetail;
use Illuminate\Http\Request;
use App\Models\Stock;


class UserOrderDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $orderdetail = UserOrderDetail::where('order_id','=', $id)->get();
        return $orderdetail;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->authenticate();

        UserOrderDetail::create([
            'order_id' => $request->order_id,
            'quantity' => $request->quantity,
            'stock_id' => $request->stock_id,
        ]);

        Stock::findOrFail($request->stock_id)->decrement('quantity', $request->quantity);
        $user->cartItems()->where('stock_id', $request->stock_id)->delete();
        
    }

    /**
     * Display the specified resource.
     */
    public function show(UserOrderDetail $userOrderDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserOrderDetail $userOrderDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserOrderDetail $userOrderDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserOrderDetail $userOrderDetail)
    {
        //
    }
}