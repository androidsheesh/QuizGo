<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\StoreTeacherRequest;
use App\Models\User;
use App\Services\TeacherAccountService;

class AdminController extends Controller
{
    public function __construct(private TeacherAccountService $teacherAccounts)
    {
    }

    public function dashboard()
    {
        $teachers = User::where('role', Role::Teacher->value)->orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('teachers'));
    }

    public function storeTeacher(StoreTeacherRequest $request)
    {
        $this->teacherAccounts->create($request->validated());

        // Credentials are flashed once so the admin can share them; the plain password is never stored
        return redirect()->route('admin.dashboard')
            ->with('success', 'Teacher account created successfully.')
            ->with('new_teacher_credentials', [
                'email' => $request->validated('email'),
                'password' => $request->validated('password'),
            ]);
    }

    public function deleteTeacher(User $teacher)
    {
        abort_unless($teacher->isTeacher(), 400, 'Can only delete teachers.');

        $teacher->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Teacher account deleted successfully.');
    }
}
