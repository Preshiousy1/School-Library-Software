<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{

    /**
     * Create a new BookController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:manager,admin,student');
    }
    /**
     * Get all available books
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableBooks()
    {

        return response()->json(Book::where('is_available', true)->get());
    }

    /**
     * Search by category.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchBooksByCategory($category)
    {
        return response()->json(Book::where('category', 'like', "%{$category}%")->where('is_available', true)->get());
    }

    /**
     * Search by author.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchBooksByAuthor($author)
    {
        return response()->json(Book::where('author', 'like', "%{$author}%")->where('is_available', true)->get());
    }

    /**
     * Search by year.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchBooksByYear($year)
    {
        return response()->json(Book::where('year', 'like', "%{$year}%")->where('is_available', true)->get());
    }

    /**
     * Search by name.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchBooks(Request $request)
    {
        if ($request->has('name')) {
            return response()->json(Book::where('name', 'like', "%{$request['name']}%")->where('is_available', true)->get());
        }
        if ($request->has('author')) {
            return response()->json(Book::where('author', 'like', "%{$request['author']}%")->where('is_available', true)->get());
        }
        if ($request->has('year')) {
            return response()->json(Book::where('year_published', 'like', "%{$request['year']}%")->where('is_available', true)->get());
        }
        if ($request->has('category')) {
            return response()->json(Book::where('category', 'like', "%{$request['category']}%")->where('is_available', true)->get());
        }
        return response()->json(Book::where('is_available', true)->get());
    }
}
