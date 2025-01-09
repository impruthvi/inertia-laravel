<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        // Extract and sanitize input
        $search = $request->input('search');
        $sortArray = $request->input('sort', []);

        // Define allowed fields for sorting
        $allowedSortFields = ['name', 'email', 'created_at'];
        $allowedSortDirections = ['asc', 'desc'];

        // Start building the query
        $query = User::query();

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Apply sorting
        foreach ($sortArray as $field => $direction) {
            if (in_array($field, $allowedSortFields) && in_array(strtolower($direction), $allowedSortDirections)) {
                $query->orderBy($field, $direction);
            }
        }

        // Paginate results and preserve query parameters
        $users = $query->paginate(10)->appends($request->all());

        // Return response
        return Inertia::render('Admin/Users/UsersPage', [
            'users' => $users,
            'sort' => $sortArray,
        ]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request)
    {
        $validated = $request->validated();

        // create user
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => generatePassword(User::USER_DEFAULT_PASSWORD),
        ]);

        return redirect()->back()->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
