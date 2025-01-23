<?php

declare(strict_types=1);

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

final class UserController extends Controller
{
    public function __construct(
        private readonly UserInterface $userInterface
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

        return redirect()->back()->with('success', trans('messages.created', ['entity' => 'User']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        // Get user
        $user = $this->userInterface->find($id);
        $this->authorize(get_ability('edit'), $user);

        if (! $user instanceof User) {
            return response()->json(['error' => true, 'message' => trans('messages.not_found', ['entity' => 'User'])]);
        }

        // Return response in JSON
        return response()->json(['error' => false, 'data' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id): RedirectResponse
    {
        $user = $this->userInterface->find($id);

        $this->authorize(get_ability('edit'), $user);

        if (! $user instanceof User) {
            return redirect()->back()->with('error', trans('messages.not_found', ['entity' => 'User']));
        }

        $this->userInterface->update($user, $request->validated());

        return redirect()->back()->with('success', trans('messages.updated', ['entity' => 'User']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->authorize(get_ability('delete'));
        $this->userInterface->delete($id);

        return redirect()->back()->with('success', trans('messages.deleted', ['entity' => 'User']));
    }
}
