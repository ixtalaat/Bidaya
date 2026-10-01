<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogsController extends Controller
{
    public function index(Request $request): View
    {
        $logs = AuditLog::query()
            ->when(
                $request->string('action')->toString(),
                fn (Builder $query, string $action) => $query->where('action', $action)
            )
            ->when(
                $request->string('date_from')->toString(),
                fn (Builder $query, string $from) => $query->whereDate('created_at', '>=', $from)
            )
            ->when(
                $request->string('date_to')->toString(),
                fn (Builder $query, string $to) => $query->whereDate('created_at', '<=', $to)
            )
            ->with('user')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('audit-logs.index', [
            'logs' => $logs,
            'actions' => AuditLog::distinct()->orderBy('action')->pluck('action')->all(),
        ]);
    }

    public function show(AuditLog $auditLog): View
    {
        $auditLog->load('user');

        return view('audit-logs.show', ['log' => $auditLog]);
    }
}
