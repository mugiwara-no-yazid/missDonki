<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    public function index()
    {
        $candidates = Candidate::orderBy('number')->get();
        return view('admin.candidates.index', compact('candidates'));
    }

    public function create()
    {
        return view('admin.candidates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:100',
            'number' => 'required|integer|min:1|unique:candidates,number',
            'bio'    => 'nullable|string|max:500',
            'photo'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('candidates', 'public');
        }

        unset($data['photo']);
        Candidate::create($data);

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidate ajoutée avec succès.');
    }

    public function edit(Candidate $candidate)
    {
        return view('admin.candidates.edit', compact('candidate'));
    }

    public function update(Request $request, Candidate $candidate)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:100',
            'number' => 'required|integer|min:1|unique:candidates,number,' . $candidate->id,
            'bio'    => 'nullable|string|max:500',
            'photo'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($candidate->photo_path) {
                Storage::disk('public')->delete($candidate->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('candidates', 'public');
        }

        unset($data['photo']);
        $candidate->update($data);

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidate mise à jour.');
    }

    public function toggleActive(Candidate $candidate)
    {
        $candidate->update(['is_active' => !$candidate->is_active]);
        $status = $candidate->is_active ? 'activée' : 'désactivée';
        return back()->with('success', "Candidate {$status}.");
    }

    public function destroy(Candidate $candidate)
    {
        if ($candidate->total_votes > 0) {
            return back()->with('error', 'Impossible de supprimer une candidate ayant des votes.');
        }

        if ($candidate->photo_path) {
            Storage::disk('public')->delete($candidate->photo_path);
        }

        $candidate->delete();
        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidate supprimée.');
    }
}