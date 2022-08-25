<?php

namespace App\Http\Controllers;

use App\Exceptions\BookStoreException;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class BookController extends Controller
{

    public function addingBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:5,1000',
            'author' => 'required|string|between:5,300',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'image' => 'required|string|between:5,1000',
            'Price' => 'required|integer',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            if ($currentUser) {
                $book = new Book();
                $adminId = $book->adminOrUserVerification($currentUser->id);
                
                if (count($adminId) == 0) {
                    throw new bookstoreexception("NOT AN ADMIN", 404);
                }

                $bookDetails = Book::where('name', $request->name)->first();
                if ($bookDetails) {
                    throw new bookstoreexception("Book is already exist in there", 401);
                }

                $book->saveBookDetails($request, $currentUser)->save();
            } else {
                Log::error('Invalid User');
                throw new bookstoreexception("Invalid authorization token", 404);
            }

            Cache::remember('books', 3600, function () {
                return DB::table('books')->get();
            });

            Log::info('book created', ['admin_id' => $book->user_id]);

            return response()->json([
                'message' => 'Book created successfully'
            ], 201);
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

    public function updateBookById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'name' => 'required|string|min:3|max:50',
                'description' => 'required|string|min:5|max:1000',
                'author' => 'required|string|min:5|max:50',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,tiff|max:2048',
                
                'Price' => 'required|integer',
                
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $currentUser = JWTAuth::parseToken()->authenticate();
            if (!$currentUser) {
                Log::error('Invalid Authorization Token');
                throw new BookStoreException('Invalid Authorization Token', 401);
            }
            $book = new Book();
            $adminId = $book->adminOrUserVerification($currentUser->id);
            if (count($adminId) == 0) {
                return response()->json(['message' => 'NOT AN ADMIN'], 404);
            }

            $bookData =  $book->findingBook($request->id, $currentUser->id);
            if (!$bookData) {
                Log::error('Book Not Found');
                throw new BookStoreException('Book Not Found', 404);
            }

            $book = Book::updateBook($request, $bookData);
            Cache::forget('books');
            if ($book) {
                Log::info('Book Updated Successfully', ['AdminID' => $currentUser->id]);
                return response()->json([
                    'message' => 'Book Updated Successfully'
                ], 201);
            }
        } catch (BookStoreException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    public function deleteBookById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $currentUser = JWTAuth::parseToken()->authenticate();
            if (!$currentUser) {
                Log::error('Invalid Authorization Token');
                throw new BookStoreException('Invalid Authorization Token', 401);
            }

            $book = new Book();
            $adminId = $book->adminOrUserVerification($currentUser->id);
            if (count($adminId) == 0) {
                return response()->json(['message' => 'NOT AN ADMIN'], 404);
            }

            $bookDetails = $book->findingBook($request->id, $currentUser->id);
            if (!$bookDetails) {
                Log::error('Book Not Found');
                throw new BookStoreException('Book Not Found', 404);
            }

            if ($bookDetails->delete()) {
                Log::info('Book Deleted Successfully', ['AdminID' => $currentUser->id, 'BookID' => $request->id]);
                Cache::forget('books');
                return response()->json([
                    'message' => 'Book Deleted Sucessfully'
                ], 200);
            }
        } catch (BookStoreException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    public function addQuantityToExistingBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            if (!$currentUser) {
                Log::error('Invalid User');
                throw new BookStoreException("Invalid authorization token", 404);
            }
            $book = new Book();
            $adminId = $book->adminOrUserVerification($currentUser->id);
            if (!$adminId) {
                return response()->json(['message' => 'NOT AN ADMIN'], 404);
            }

            $bookDetails = $book->findingBook($request->id);
            if (!$bookDetails) {
                throw new BookStoreException("Couldnot found a book with that given id", 404);
            }

            $bookDetails->quantity += $request->quantity;
            $bookDetails->save();
            Cache::forget('books');

            return response()->json([
                'status' => 201,
                'message' => 'Book Quantity updated Successfully'
            ], 201);
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

    public function displayAllBooks()
    {
        try {
            $book = Book::get();

            if ($book == []) {
                throw new BookStoreException("Books are not there", 404);
            }
            return response()->json([
                'message' => 'Display All books are :',
                'books' => $book

            ], 201);
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

    public function sortPriceLowToHigh()
    {
        $currentUser = JWTAuth::parseToken()->authenticate();
        $book = new Book();
        if ($currentUser) {
            $bookDetails = $book->ascendingOrder();
        }
        if ($bookDetails == []) {
            return response()->json(['message' => 'Books not found'], 404);
        }
        return response()->json([
            'message' => 'Sorting Books are Low to high',
            'books' => $bookDetails
        ], 201);

        // $books = Book::select('*')->orderBy('Price')->get();
        // return $books;

    }

    public function sortPriceHighToLow()
    {
        $currentUser = JWTAuth::parseToken()->authenticate();
        $book = new Book();
        if ($currentUser) {
            $bookDetails = $book->descendingOrder();
        }
        if ($bookDetails == []) {
            return response()->json(['message' => 'Books not found'], 404);
        }
        return response()->json([
            'message' => 'Sorting Books are High to Low',
            'books' => $bookDetails
        ], 201);
    }

    public function searchBookByKeyword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            $searchKey = $request->input('search');
            $currentUser = JWTAuth::parseToken()->authenticate();

            if ($currentUser) {
                $userbooks = new Book();
                Log::info('Search is Successfull');
                return response()->json([
                    'message' => 'Search done Successfully',
                    'books' => $userbooks->searchBook($searchKey)
                ], 201);
                if ($userbooks == '[]') {
                    Log::error('No Book Found');
                    throw new BookStoreException("No Book Found For This Search Key ", 404);
                }
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }
}
