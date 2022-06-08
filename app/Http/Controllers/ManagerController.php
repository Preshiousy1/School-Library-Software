<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookRequest;
use App\Models\Manager;
use App\Models\Student;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ManagerController extends Controller
{
    /**
     * Create a new ManagerController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:manager,admin', ['except' => ['login']]);
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
        if (!$token = auth('manager')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // dd($token);
        return $this->createNewToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('manager')->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth('manager')->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth('manager')->user());
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
            'expires_in' => auth('manager')->factory()->getTTL() * 60 * 60,
            'manager_user' => auth('manager')->user()
        ]);
    }


    /**
     * get all books or by conditions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBooks(Request $request)
    {
        if ($request->has('available') && $request->has('borrowed')) {
            return response()->json(Book::where('is_available', $request['available'])->where('is_borrowed', $request['borrowed'])->get());
        }
        if ($request->has('available')) {
            return response()->json(Book::where('is_available', $request['available'])->get());
        }
        if ($request->has('borrowed')) {
            return response()->json(Book::where('is_borrowed', $request['borrowed'])->get());
        }
        return response()->json(Book::all());
    }

    /**
     * get all students or by conditions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudents(Request $request)
    {

        if ($request->has('suspended')) {
            return response()->json(Student::where('is_suspended', $request['suspended'])->get());
        }

        return response()->json(Student::all());
    }

    /**
     * get all booksrequests if pending.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookRequests()
    {

        return response()->json(BookRequest::where('is_pending', true)->get());
    }

    /**
     * get all booksrequests if pending.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setBookRequestApproval(Request $request, $request_id)
    {

        $validator = Validator::make($request->all(), [
            'approved' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $book_request = BookRequest::find($request_id);
        if (!$book_request) {
            return response()->json([
                'status' => 0,
                'output' => "This book request does not exist in library",
            ]);
        }

        if ($book_request->is_accepted != null || !$book_request->is_pending) {
            return response()->json([
                'status' => 0,
                'output' => 'This book request has already been treated.',
            ]);
        }

        try {

            $updated = $book_request->update([
                'is_pending' => false,
                'is_accepted' => $request['approved']
            ]);

            $book = $book_request->book;  //This request's book

            // If the request was actually approved then the book is borrowed
            if (($request['approved'] == true || $request['approved'] == 1) && $updated) {

                // Update The book's status to borrowed
                $book->update([
                    'is_borrowed' => true
                ]);

                // set other pending requests on this book to disapproved
                $other_requests = $book->pending_requests;
                foreach ($other_requests as $other_request) {
                    $other_request->update([
                        'is_pending' => false,
                        'is_accepted' => false
                    ]);
                }
            }

            return response()->json([
                'status' => 1,
                'output' => "Approval set sucessfully"
            ], 201);
        } catch (Error $er) {

            return response()->json([
                'status' => 0,
                'output' => "An error occured $er"
            ], 201);
        }
    }


    /**
     * get all booksrequests if pending.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setStudentSuspendStatus(Request $request, $student_id)
    {

        $validator = Validator::make($request->all(), [
            'suspended' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $student = Student::find($student_id);
        if (!$student) {
            return response()->json([
                'status' => 0,
                'output' => "This student does not exist in library",
            ]);
        }

        try {
            $updated = $student->update([
                'is_suspended' => $request['suspended']
            ]);

            if ($updated && $request['suspended']) {
                return response()->json([
                    'status' => 1,
                    'output' => "This student has been SUSPENDED"
                ], 201);
            } elseif ($updated && !$request['suspended']) {
                return response()->json([
                    'status' => 1,
                    'output' => "This student has been UNSUSPENDED"
                ], 201);
            }
        } catch (Error $er) {

            return response()->json([
                'status' => 0,
                'output' => "An error occured $er"
            ], 201);
        }
    }

    /**
     * set book availability.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setBookAvailability(Request $request, $book_id)
    {
        $validator = Validator::make($request->all(), [
            'available' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $book = Book::find($book_id);
        if (!$book) {
            return response()->json([
                'status' => 0,
                'output' => "This book does not exist in library",
            ]);
        }

        if (count($book->accepted_requests) > 0 || $book->is_borrowed) {
            return response()->json([
                'status' => 0,
                'output' => 'This book is currently borrowed.',
            ]);
        }
        if (count($book->pending_requests) > 0) {
            return response()->json([
                'status' => 0,
                'output' => 'This book has pending requests.',
            ]);
        }

        $book->is_available = $request['available'];
        $book->save();

        return response()->json([
            'status' => 1,
            'output' => "Availablity set sucessfully"
        ], 201);
    }
}
