<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "orders";
    protected $fillable = [
        'order_id',
        'user_id',
        'cart_id',
        'address_id',
        'book_name',
        'book_author',
        'book_price',
        'book_quantity',
        'total_price',
       
    ];

    public static function placeOrder($request, $currentUser, $book, $cart)
    {
        $total_price = $book->price * $cart->book_quantity;
        $order = new Order();
        $order->user_id = $currentUser->id;
        $order->cart_id = $request->cart_id;
        $order->address_id = $request->address_id;
        $order->book_name = $book->name;
        $order->book_author = $book->author;
        $order->book_price = $book->price;
        $order->book_quantity = $cart->book_quantity;
        $order->total_price = $total_price;
        $order->order_id = $order->uniqiD(9);
        $order->save();

        return $order;        
    }

    public static function getOrderByCartId($cartId)
    {
        $order = Order::where('cart_id', $cartId)->first();

        return $order;
    }

    public static function getOrderByUserId($userId)
    {
        $order = Order::where('user_id', $userId)->get();

        return $order;
    }

    public static function getOrderByIDandUserID($ordersId, $userID)
    {
        $order = Order::where('id', $ordersId)->where('user_id', $userID)->first();

        return $order;
    }

    /**
     * Function to get order by orderID and userID
     * passing orderID and userID as parameters
     * 
     * return array
     */
    public static function getOrderByOrderID($orderID, $userID)
    {
        $order = Order::where('order_id', $orderID)->where('user_id', $userID)->first();

        return $order;
    }


    function uniqiD($limit)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $limit);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}