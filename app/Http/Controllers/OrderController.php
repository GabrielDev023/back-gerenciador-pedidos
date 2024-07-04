<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $ids_produto = [];

        foreach($request->products as $item) {
            $ids_produto[] = $item['product_id'];
            $quantity = $item['quantity'];
        }

        $order = Order::create([
            'customer_id' => $request->customer_id,
            'product_id' =>  json_encode($ids_produto),
            'quantity' => $quantity,
            'total' => $request->total,
        ]); 

        return response()->json($order, 201);
    }

    public function index()
    {
        return response()->json(OrderResource::collection(Order::all()));
    }

    public function show($id)
    {
        $order = Order::find($id);

        if ($order) {
            return response()->json(new OrderResource($order));
        }

        return response()->json(['message' => 'Order not found'], 404);
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if ($order) {
            $order->delete();
            return response()->json(['message' => 'Order deleted'], 200);
        }

        return response()->json(['message' => 'Order not found'], 404);
    }   
}
