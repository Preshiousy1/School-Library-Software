<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Book;
use App\Models\Manager;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
    /**
     * Create a new StudentController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['login']]);
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
        if (!$token = auth('admin')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // dd($token);
        return $this->createNewToken($token);
    }
    /**
     * Register an Admin.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAdmin(Request $request)
    {
        $admin = auth('admin')->user();

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $admin = array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)],
            ['creator_id' => $admin->id]

        );
        $user = Admin::create($admin);
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createManager(Request $request)
    {
        $admin = auth('admin')->user();
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $manager = array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)],
            ['creator_id' => $admin->id]

        );
        $user = Manager::create($manager);
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBook(Request $request)
    {
        $admin = auth('admin')->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'author' => 'required|string',
            'category' => 'required|string',
            'year_published' => 'required|string',
            'is_available' => 'boolean',
            'is_borrowed' => 'boolean',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $book = Book::create($validator->validated());
        return response()->json([
            'message' => 'Book successfully Added',
            'book' => $book
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('admin')->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth('admin')->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth('admin')->user());
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
            'expires_in' => auth('admin')->factory()->getTTL() * 60 * 60,
            'admin_user' => auth('admin')->user()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Manager  $book
     * @return \Illuminate\Http\Response
     */
    public function deleteManager($manager_id)
    {
        try {
            $manager = Manager::find($manager_id);
            $deleted = $manager->delete();
            return  array('status' => 1, "output" => "This manager has been successfully deleted");
            //code...
        } catch (\Throwable $th) {
            return  array('status' => 0, "output" => "Manager Not Found");
        }
    }

    /**
     * Get all managers.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getManagers()
    {
        return response()->json(Manager::all());
    }

    /**
     * Get all Students.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudents()
    {
        return response()->json(Student::all());
    }
}
