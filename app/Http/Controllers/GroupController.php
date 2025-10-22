<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function __construct()
    {
        // Hanya superuser
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'superuser') {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $groups = Group::with('users')->paginate(10); // <-- paginate, bukan all()
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        // semua user bisa dipilih saat assign
        $users = User::all();
        return view('groups.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // assign users ke group
        User::whereIn('id', $request->user_ids ?? [])->update(['group_id' => $group->id]);

        return redirect()->route('groups.index')->with('success', 'Group berhasil dibuat.');
    }

    public function edit(Group $group)
    {
        $users = User::all(); // semua user bisa dipilih
        return view('groups.edit', compact('group', 'users'));
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Assign users ke group
        User::whereIn('id', $request->user_ids ?? [])->update(['group_id' => $group->id]);

        return redirect()->route('groups.index')->with('success', 'Group updated successfully.');
    }
}
