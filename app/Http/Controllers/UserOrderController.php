<?php

namespace App\Http\Controllers;

use App\Models\UserOrder;
use Illuminate\Http\Request;




class UserOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // $user = auth()->authenticate();
        $orders = UserOrder::orderBy('created_at', 'desc')->get();
        return $orders;
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
        // $user = JWTAuth::parseToken()->authenticate();
        $user = auth()->authenticate();
        // $note = $request->note;
        // $note = "";
        
        // thêm vào bảng order và order detail
        $order = UserOrder::create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
       
        return $order->id;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = UserOrder::findOrFail($id);
        return $order;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserOrder $userOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $order = UserOrder::findOrFail($id);
        $order->update($request->all());
        return response()->json(['Message' => 'Successfully Update order']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserOrder $userOrder)
    {
        //
    }
}