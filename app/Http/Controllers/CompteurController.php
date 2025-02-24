<?php

namespace App\Http\Controllers;

use App\Models\Compteur;
use Illuminate\Http\Request;

class CompteurController extends Controller
{
    public function index()
    {
        $compteurs = Compteur::all();
        return view('compteurs.index', compact('compteurs'));
    }

    public function create()
    {
        return view('compteurs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'annee' => 'required|integer',
            'compteur' => 'required|integer',
        ]);

        Compteur::create($request->all());
        return redirect()->route('compteurs.index')->with('success', 'Compteur ajouté avec succès!');
    }
    public function edit($annee) {
        $compteur = Compteur::find($annee);
    
        if (!$compteur) {
            return redirect()->route('compteurs.index')->with('error', 'Compteur non trouvé.');
        }
    
        return view('compteurs.edit', compact('compteur'));
    }
    public function update(Request $request, $annee) {
        $request->validate([
            'annee' => 'required|integer',
            'compteur' => 'required|integer',
        ]);
    
        $compteur = Compteur::find($annee);
    
        if (!$compteur) {
            return redirect()->route('compteurs.index')->with('error', 'Compteur non trouvé.');
        }
    
        $compteur->update($request->all());
    
        return redirect()->route('compteurs.index')->with('success', 'Compteur modifié avec succès.');
    }

}