<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(): View
    {
        return view('chirps.index', [
            'chirps' => Chirp::with('user')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->chirps()->count() > 10) {
            return redirect('/chirps')->with('error', 'Vous avez atteint la limite de 10 chirps.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);
 
        $request->user()->chirps()->create($validated);

 
        return redirect(route('chirps.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chirp $chirp): View
    {
        Gate::authorize('update', $chirp);
 
        return view('chirps.edit', [
            'chirp' => $chirp,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chirp $chirp): RedirectResponse
    {
        Gate::authorize('update', $chirp);
 
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);
 
        $chirp->update($validated);
 
        return redirect(route('chirps.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp): RedirectResponse
    {
        Gate::authorize('delete', $chirp);
 
        $chirp->delete();
 
        return redirect(route('chirps.index'));
    }

    public function like(Chirp $chirp, Request $request)
    {
        $user = $request->user();

        if ($chirp->likes()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->withErrors(['error' => 'Vous avez déjà liké ce chirp.']);
        }

        $chirp->likes()->attach($user);

        return redirect()->back()->with('success', 'Chirp liké avec succès !');
    }

    public function unlike(Chirp $chirp, Request $request)
    {
        $user = $request->user();

        $chirp->likes()->detach($user);

        return redirect()->back()->with('success', 'Like retiré avec succès !');
    }

}
