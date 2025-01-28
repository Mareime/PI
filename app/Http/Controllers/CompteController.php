<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compte;
use App\Exports\ComptesExport;
use App\Imports\ComptesImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Routing\Controller;
class CompteController extends Controller
{
     // if (auth()->guest()) {
        //     return redirect()->route('login');
        // }

    public function index()
    {
<<<<<<< HEAD
        // var_dump(auth()->guest());
        if (auth()->guest()) {
            return redirect()->route('login');
        }

=======
        if (auth()->guest()) {
            return redirect()->route('login');
        }
>>>>>>> master
        $comptes = Compte::all();
        return view('compte.index', compact('comptes'));
    }

    public function create()
    {
        return view('compte.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|string|max:50',  // Assurez-vous que le champ 'num_compt' est validé
            'type_compte' => 'required|string|max:255',
            'solde' => 'required|numeric',
            'date_creation' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        Compte::create($request->all());
        return redirect()->route('compte.index')->with('success', 'Compte créé avec succès.');
    }

    public function edit(Compte $compte)
    {
        return view('compte.edit', compact('compte'));
    }

    public function update(Request $request, Compte $compte)
    {
        $request->validate([
            'numero' => 'required|string|max:50',
            'type_compte' => 'required|string|max:255',
            'solde' => 'required|numeric',
            'date_creation' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);
        
        $compte->update($request->all());
        return redirect()->route('compte.index')->with('success', 'Compte mis à jour avec succès.');
    }

    public function destroy(Compte $compte)
    {
        $compte->delete();
        return redirect()->route('compte.index')->with('success', 'Compte supprimé avec succès.');
    }

    public function export()
    {
        return Excel::download(new ComptesExport, 'compt.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048',
        ]);

        try {
            Excel::import(new ComptesImport, $request->file('file'));
            return redirect()->route('compte.index')->with('success', 'Comptes importés avec succès!');
        } catch (\Exception $e) {
            return redirect()->route('compte.index')->with('error', 'Erreur lors de l\'importation : ' . $e->getMessage());
        }
    }
}
