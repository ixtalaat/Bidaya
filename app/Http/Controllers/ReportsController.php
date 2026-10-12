<?php

namespace App\Http\Controllers;

use App\Services\ReportsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function __construct(private ReportsService $reports) {}

    public function financial(Request $request): View
    {
        $from = $request->string('from')->toString() ?: today()->startOfMonth()->toDateString();
        $to = $request->string('to')->toString() ?: today()->toDateString();

        return view('reports.financial', [
            'from' => $from,
            'to' => $to,
            ...$this->reports->financial($from, $to),
        ]);
    }

    public function attendance(Request $request): View
    {
        $groupId = $request->integer('group_id');
        $from = $request->string('from')->toString() ?: today()->subDays(30)->toDateString();
        $to = $request->string('to')->toString() ?: today()->toDateString();

        return view('reports.attendance', [
            'allGroups' => $this->reports->groupOptions(),
            'groupId' => $groupId,
            'from' => $from,
            'to' => $to,
            'rows' => $groupId ? $this->reports->attendance($groupId, $from, $to) : [],
        ]);
    }

    public function progress(Request $request): View
    {
        $groupId = $request->integer('group_id');

        return view('reports.progress', [
            'allGroups' => $this->reports->groupOptions(),
            'groupId' => $groupId,
            'rows' => $groupId ? $this->reports->progress($groupId) : [],
        ]);
    }

    public function courses(): View
    {
        return view('reports.courses', ['rows' => $this->reports->courses()]);
    }
}
