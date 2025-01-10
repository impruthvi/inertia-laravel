<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\CreateUserRequest;
use App\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        protected UserInterface $userInterface
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        // Extract and sanitize input
        $search = $request->input('search');
        $sortArray = $request->input('sort', []);

        // Apply search filter
        $filters = [
            'search' => $search,
            'sort' => $sortArray,
        ];

        // Paginate results and preserve query parameters
        $users = $this->userInterface->get(
            select: ['name', 'email', 'created_at'],
            filters: $filters,
            paginate: true
        );

        // Return response
        return Inertia::render('Admin/Users/UsersPage', [
            'users' => $users,
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
