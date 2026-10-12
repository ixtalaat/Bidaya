<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboards) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->role === Role::Teacher) {
            $data = $this->dashboards->teacherData($user);

            return $data === null ? view('dashboard.unlinked') : view('dashboard.teacher', $data);
        }

        if ($user->role === Role::Parent) {
            $data = $this->dashboards->parentData($user);

            return $data === null ? view('dashboard.unlinked') : view('dashboard.parent', $data);
        }

        if ($user->role === Role::Student) {
            $data = $this->dashboards->studentData($user);

            return $data === null ? view('dashboard.unlinked') : view('dashboard.parent', $data);
        }

        if ($user->role === Role::Accountant) {
            return view('dashboard.accountant', $this->dashboards->accountantData());
        }

        return view('dashboard.admin', $this->dashboards->adminData());
    }
}
