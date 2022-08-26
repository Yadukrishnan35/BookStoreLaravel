<?php

namespace App\Http\Controllers;

use App\Exceptions\BookStoreException;
use App\Models\Book;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class CartController extends Controller
{
    public function addBookToCartByBookId(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'book_id' => 'required|integer',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->tojson(), 400);
        }

        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            $cart = new Cart();
            $book = new Book();
            $user = new User();

            $userId = $user->userVerification($currentUser->id);

            if (count($userId) == 0) {
                return response()->json(['message' => 'NOT AN USER'], 404);
            }

            if ($currentUser) {
                $book_id = $request->input('book_id');
                $book_existance = $book->findingbook($book_id);

                if (!$book_existance) {

                    return response()->json([
                        'message' => 'Book not found',
                        'status' => 404
                    ], 404);
                }


                $book_cart = $cart->bookCart($book_id, $currentUser->id);

                if ($book_cart) {

                    return response()->json([
                        'status' => 'Book already added to the cart'
                    ], 401);
                }
                $cart->book_id = $request->get('book_id');

                if ($currentUser->carts()->save($cart)) {
                    Cache::remember('carts', 3600, function () {
                        return DB::table('carts')->get();
                    });

                    return response()->json([
                        'message' => 'Book Added to Cart Sucessfully'
                    ], 201);
                }
                return response()->json([
                    'message ' => 'Book Cannot Added to Cart '
                ], 405);
            } else {
                Log::error('Invalid User');
                throw new BookStoreException("Invalid Authorization Token", 404);
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }


    public function deleteBookByCartId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->tojson(), 400);
        }

        try {
            $id = $request->input('id');
            $currentUser = JWTAuth::parseToken()->authenticate();
            $user = new User();
            $userId = $user->userVerification($currentUser->id);
            if (count($userId) == 0) {
                return response()->json([
                    'status' => 404,
                    'message' => 'NOT AN USER'
                ], 404);
            }

            if (!$currentUser) {
                Log::error("Invalid User");
                throw new BookStoreException("Invalid authorization token", 404);
            }

            $book = $currentUser->carts()->find($id);
            if (!$book) {
                Log::error('Book not found', ['id' => $request->id]);
                return response()->json([
                    'message' => 'Book not found in CART'
                ], 404);
            }

            if ($book->delete()) {
                Log::info('book deleted', ['user_id' => $currentUser, 'book_id' => $request->id]);
                Cache::forget('carts');
                return response()->json([
                    'message' => 'Book deleted succesfully from Cart'
                ], 201);
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }


    public function getAllBooksInCart()
    {
        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            $user = new User();
            $userId = $user->userVerification($currentUser->id);
            if (count($userId) == 0) {
                return response()->json([
                    'message' => 'NOT AN USER'
                ], 404);
            }
            if ($currentUser) {

                $books = new Cart();
                Log::info('All Book Present in Cart are Fetched');
                return response()->json([
                    'message' => 'All Books Present in Cart',
                    'Cart' => $books->getAllBooks($currentUser)
                ], 201);
                if ($books == []) {
                    Log::error('Book not found');
                    return response()->json([
                        'message' => 'Books not found'
                    ], 404);
                }
            } else {
                Log::error('Invalid User');
                throw new BookStoreException("Invalid Authorization token", 404);
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

    public function increamentBookQuantityInCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'book_quantity'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            $cart = new Cart();
            $user = new User();
            $userId = $user->userVerification($currentUser->id);
            if (count($userId) == 0) {
                return response()->json(['message' => 'NOT AN USER'], 404);
            }
            if (!$currentUser) {
                Log::error('Invalid User');
                throw new BookStoreException("Invalid authorization token", 404);
            }
            $cart = Cart::find($request->id);

            if (!$cart) {
                return response()->json([
                    'message' => 'Item Not found with this id'
                ], 404);
            }
            $cart->book_quantity += $request->book_quantity;
            $cart->save();
            Log::info('Book Quantity increament Successfully to the bookstore cart');
            return response()->json([
                'message' => 'Book Quantity increament success'
            ], 201);
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

    public function decrementBookQuantityInCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'book_quantity'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            $cart = new Cart();
            $user = new User();
            $userId = $user->userVerification($currentUser->id);
            if (count($userId) == 0) {
                return response()->json(['message' => 'NOT AN USER'], 404);
            }
            if (!$currentUser) {
                Log::error('Invalid User');
                throw new BookStoreException("Invalid authorization token", 404);
            }
            $cart = Cart::find($request->id);

            if (!$cart) {
                return response()->json([
                    'message' => 'Item Not found with this id'
                ], 404);
            }
            $cart->book_quantity -= $request->book_quantity;
            $cart->save();
            if ($cart->book_quantity == 0) {
                $cart->delete();
                return response()->json([
                    'message' => 'Book Successfully remove from cart (Empty)'
                ], 201);
            }
            Log::info('Book Quantity decreament Successfully from the bookstore cart');
            return response()->json([
                'message' => 'Book Quantity Decreament Success'
            ], 201);
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }
}
