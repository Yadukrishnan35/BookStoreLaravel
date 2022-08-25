<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $table = "books";

    protected $fillable = [
        'name',
        'description',
        'author',
        'image',
        'Price',
        'quantity',
    ];

    public function adminOrUserVerification($currentUserId)
    {
        $adminId = User::select('id')->where([['role', '=', 'admin'], ['id', '=', $currentUserId]])->get();
        return $adminId;
    }
    public function saveBookDetails($request, $currentUser)
    {
        $book = new Book();
        
        if($request->hasFile('image')){
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $extension;
            $file->move('uploads/books', $fileName);
            $book->image = $fileName;
        }
        $book->name = $request->input('name');
        $book->description = $request->input('description');
        $book->author = $request->input('author');
        //$book->image = $request->input('image');
        $book->Price = $request->input('Price');
        $book->quantity = $request->input('quantity');
        $book->user_id = $currentUser->id;
        $book->save();

        return $book;
    }

    public function findingBook($bookId)
    {
        $book = Book::where('id', $bookId)->first();
        return $book;
    }


    public static function updateBook($request, $book)
    {
        if($request->hasFile('image')){
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $extension;
            $file->move('uploads/books', $fileName);
            $book->image = $fileName;
        }
        $book->name = $request->input('name');
        $book->description = $request->input('description');
        $book->author = $request->input('author');
        $book->Price = $request->input('Price');
        $book->save();
        return $book;
    }

    public function ascendingOrder()
    {
        return Book::select('*')->orderBy('Price')->get();
    }

    public function descendingOrder()
    {
       return Book::select('*')->orderBydesc('Price')->get();
       
    }
    
    public function getBookDetails($bookName)
    {
        return Book::select('id', 'name', 'quantity', 'author', 'Price')
            ->where('name', '=', $bookName)
            ->first();
    }

    public static function searchBook($searchKey)
    {
        $userbooks = Book::select('books.id', 'books.name', 'books.description', 'books.author', 'books.Price', 'books.quantity')
        ->Where('books.name', 'like', '%' . $searchKey . '%')
        ->orWhere('books.author', 'like', '%' . $searchKey . '%')
        ->orWhere('books.description', 'like', '%' . $searchKey . '%')
        ->get();

        return $userbooks;
    }
}
