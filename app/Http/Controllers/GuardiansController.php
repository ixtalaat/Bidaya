<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuardianRequest;
use App\Http\Requests\UpdateGuardianRequest;
use App\Models\Guardian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuardiansController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $guardians = Guardian::query()
            ->when(
                $request->string('search')->toString(),
                fn ($query, string $term) => $query
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
            )
            ->withCount('students')
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('parents.index', ['guardians' => $guardians]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('parents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGuardianRequest $request): RedirectResponse
    {
        $guardian = Guardian::create($request->validated());

        return redirect()->route('parents.show', $guardian)->with('status', __('guardians.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Guardian $guardian): View
    {
        $guardian->load(['students' => fn ($query) => $query->orderBy('name')->orderBy('id')]);

        return view('parents.show', ['guardian' => $guardian]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guardian $guardian): View
    {
        return view('parents.edit', ['guardian' => $guardian]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGuardianRequest $request, Guardian $guardian): RedirectResponse
    {
        $guardian->update($request->validated());

        return redirect()->route('parents.show', $guardian)->with('status', __('guardians.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guardian $guardian): RedirectResponse
    {
        $guardian->delete();

        return redirect()->route('parents.index')->with('status', __('guardians.deleted'));
    }
}
