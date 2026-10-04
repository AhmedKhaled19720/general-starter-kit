<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = [
            'causer_id' => $request->input('causer_id'),
            'log_name' => $request->input('log_name'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
        ];

        $activities = Activity::query()
            ->with('causer:id,name')
            ->when($filters['causer_id'], fn ($query, $id) => $query->where('causer_id', $id))
            ->when($filters['log_name'], fn ($query, $name) => $query->where('log_name', $name))
            ->when($filters['from'], fn ($query, $from) => $query->where('created_at', '>=', $from))
            ->when($filters['to'], fn ($query, $to) => $query->where('created_at', '<=', $to.' 23:59:59'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('activity/Index', [
            'activities' => $activities,
            'filters' => $filters,
            'users' => User::orderBy('name')->get(['id', 'name']),
            'logNames' => Activity::select('log_name')->distinct()->orderBy('log_name')->pluck('log_name'),
        ]);
    }
}
