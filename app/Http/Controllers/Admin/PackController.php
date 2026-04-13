<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\VotePack;
use Illuminate\Http\Request;

class PackController extends Controller
{
    public function index()
    {
        $packs = VotePack::orderBy('price_fcfa')->get();
        return view('admin.packs.index', compact('packs'));
    }

    public function update(Request $request, VotePack $pack)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:80',
            'price_fcfa'  => 'required|integer|min:1',
            'votes_count' => 'required|integer|min:1',
        ]);

        $pack->update($data);
        return back()->with('success', 'Pack mis à jour.');
    }

    public function toggleActive(VotePack $pack)
    {
        $pack->update(['is_active' => !$pack->is_active]);
        $status = $pack->is_active ? 'activé' : 'désactivé';
        return back()->with('success', "Pack {$status}.");
    }
}