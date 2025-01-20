<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\CreateUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
        $this->authorize(get_ability('access'));

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
            select: ['id', 'name', 'email', 'created_at'],
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
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request): RedirectResponse
    {
        $this->authorize(get_ability('add'));

        $validated = $request->validated();

        $this->userInterface->create([
            ...$validated,
            'password' => generatePassword(User::USER_DEFAULT_PASSWORD),
        ]);

        return redirect()->back()->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        // Get user
        $user = $this->userInterface->find($id);
        $this->authorize(get_ability('edit'), $user);

        // Return response in JSON
        return response()->json(['error' => false, 'data' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id):RedirectResponse
    {
        $user = $this->userInterface->find($id);

        $this->authorize(get_ability('edit'), $user);

        $this->userInterface->update($id, $request->validated());

        return redirect()->back()->with('success', 'User Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->authorize(get_ability('delete'));
        $this->userInterface->delete($id);

        return redirect()->back()->with('success', 'User deleted successfully');
    }
}
