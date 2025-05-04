<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query();

        // Soft deletes
        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        // Zoekfunctie
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        // Sortering
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');

        if (in_array($sort, ['id', 'name', 'email']) && in_array($direction, ['asc', 'desc'])) {
            if ($sort === 'name') {
                $query->orderByRaw("LOWER(name) $direction");
            } elseif ($sort === 'email') {
                $query->orderByRaw("LOWER(email) $direction");
            } else {
                $query->orderBy($sort, $direction);
            }
        }

        // Render view
        return Inertia::render('Users/Index', [
            'users' => $query->paginate(10)->withQueryString(),
            'showTrashed' => $request->boolean('trashed'),
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }






    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()->route('users.index');
    }



    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
        ]);

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.index', ['trashed' => true]);
    }

    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return redirect()->route('users.index', ['trashed' => true]);
    }
}
