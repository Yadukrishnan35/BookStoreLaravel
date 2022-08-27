<?php

namespace App\Http\Controllers;

use App\Mail\sendCancelledOrderDetails;
use App\Mail\sendOrderDetails;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrdersController extends Controller
{
    public function getBookById($id)
    {
        return DB::table('books')->where('id', $id)->first();
    }

    public function placeOrders(Request $request)
    {
       
        $request->validate([
            'cartId_json' => 'required',
            'address_id' => 'required|integer'
        ]);
        $cartId_json = $request->cartId_json;
        $length = sizeof($cartId_json);

        // return $length;
        for ($i = 0; $i < $length; $i++) {
            $getUser = $request->user();
            $cart = DB::table('carts')->where('id', $cartId_json[$i])->first();

            $book = $this->getBookById($cart->book_id);
            $order = new Order();
            $order->user_id = $getUser->id;
            $order->cartId_json = $cartId_json[$i];
            $order->cart_id = $cartId_json[$i];
            $order->address_id = $request->input('address_id');
            $order->book_name = $book->name;
            $order->book_author = $book->author;
            $order->book_price = $book->price;
            $order->book_quantity = $cart->book_quantity;
            $order->total_price = $cart->book_quantity * $book->price;
            // $uniqId = Str::random(10);
            $order->order_id = $order->uniqiD(9);
            $order->save();

                
                $book->quantity  -= $cart->book_quantity;
                DB::table('books')->where('id', $cart->book_id)->update(['quantity'=>$book->quantity]);
                
        
            Mail::to($getUser->email)->send(new sendOrderDetails($getUser, $order, $book));
            return response()->json([
                'message' => 'Order Placed Successfully',
                'OrderId' => $order->order_id,
                'Quantity' => $cart->book_quantity,
                'Total_Price' => $order->total_price,
                'Message' => 'Mail Sent to Users Mail With Order Details'
            ], 200);

            Log::info('Order Placed Successfully');
            Cache::remember('orders', 3600, function () {
                return DB::table('orders')->get();
            });
        }
    }



    public function cancelOrders(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string'
        ]);
        $getUser = $request->user();
        $order = DB::table('orders')->where('order_id', $request->order_id)->first();
        $cart = DB::table('carts')->where('id', $order->cart_id)->first();
        $book = DB::table('books')->where('id', $cart->book_id)->first();
        $response = DB::table('orders')->where('order_id', $request->order_id)->delete();

        $book->quantity += $cart->book_quantity;
        DB::table('books')->where('id', $cart->book_id)->update(['quantity'=>$book->quantity]);

        Mail::to($getUser->email)->send(new sendCancelledOrderDetails($getUser, $order, $book));
        return response()->json([
            'message' => 'Order Cancelled Successfully',
            'OrderId' => $order->order_id,
            'Quantity' => $cart->book_quantity,
            'Total_Price' => $order->total_price,
            'Message' => 'Mail Sent to Users Mail With Order Details'
        ], 200);

        Log::info('Order Cancelled Successfully');
        Cache::forget('orders');
    }

    function uniqiD($limit)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $limit);
    }
}
