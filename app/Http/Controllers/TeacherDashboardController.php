<?php

namespace App\Http\Controllers;

use App\Services\TeacherDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherdashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(TeacherDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function show(Request $request)
    {
        $teacher = Auth::user();

        // 1. Fetch the data from the Service
        $stats = $this->dashboardService->getDashboardStats($teacher);
        $classrooms = $this->dashboardService->getPaginatedClassrooms($teacher, $request->input('search_class'));
        $recentQuizzes = $this->dashboardService->getRecentQuizzes($teacher);

        // 2. Merge the top-level stats with our paginated collections and send to view
        return view('teacher.teacher-dashboard', array_merge($stats, [
            'classrooms'    => $classrooms,
            'recentQuizzes' => $recentQuizzes,
        ]));
    }
}
