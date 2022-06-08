<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Create a new StudentController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:student', ['except' => ['login', 'register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        // dd($validator->validated());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (!$token = auth('student')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if (auth('student')->attempt($validator->validated()) && auth('student')->user()->is_suspended) {
            return response()->json(['error' => 'Your Account Has been Suspended, Contact Admin.'], 401);
        }

        // dd($token);
        return $this->createNewToken($token);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $student = array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)],

        );
        $user = Student::create($student);
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth('student')->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth('student')->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('student')->factory()->getTTL() * 60 * 60,
            'user' => auth('student')->user()
        ]);
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function requestBook($book_id)
    {

        $student = auth('student')->user();
        $book = Book::find($book_id);

        // return $student->pending_requests;

        if (!$book) {
            return response()->json([
                'status' => 0,
                'output' => 'This book does not exist',
            ]);
        }
        if (!$book->is_available || $book->is_borrowed) {
            return response()->json([
                'status' => 0,
                'output' => 'This book is not available for borrowing',
            ]);
        }
        if (count($student->accepted_requests) > 0) {
            return response()->json([
                'status' => 0,
                'output' => 'You have a borrowed book, you cannot request for more books until you return.',
                'book' => $student->accepted_requests
            ]);
        }
        if (count($student->pending_requests) > 0) {
            return response()->json([
                'status' => 0,
                'output' => 'You have a pending borrow request, you cannot request for more books.',
                'book' => $student->pending_requests

            ]);
        }
        $book_request = $student->book_requests()->create([
            'book_id' => $book->id
        ]);

        return response()->json([
            'status' => 1,
            'output' => 'Book request successful',
            'request' => $book_request
        ]);
    }

    /**
     * get all student's borrowed books, should be just one though.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBorrowedBooks()
    {

        $student = auth('student')->user();
        return response()->json($student->accepted_requests);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function returnBook($request_id)
    {

        $student = auth('student')->user();
        $book_request = BookRequest::find($request_id);

        // return $student->pending_requests;

        if (!$book_request) {
            return response()->json([
                'status' => 0,
                'output' => 'This book request does not exist',
            ]);
        }
        if ($book_request->user_id != $student->id) {
            return response()->json([
                'status' => 0,
                'output' => 'You did not borrow this book, therefore cannot return it',
            ]);
        }
        if ($book_request->is_returned == true) {
            return response()->json([
                'status' => 0,
                'output' => 'This book request has already been processed',
            ]);
        }

        if (!$book_request->is_accepted) {
            return response()->json([
                'status' => 0,
                'output' => 'This book request was rejected',
            ]);
        }

        $book_request->is_returned = true;
        $book_request->book()->update(['is_borrowed' => false]);
        $book_request->save();

        return response()->json([
            'status' => 1,
            'output' => 'Book returned successfully',
            'request' => $book_request
        ]);
    }
}
