<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Paiement;
use App\Models\Compte;
use App\Models\Beneficiaire;
use App\Models\PaiementTaxe;
use App\Models\Taxe;
use Illuminate\Http\Request;
use App\Exports\PaiementExport;
use App\Imports\PaiementImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Kwn\NumberToWords\NumberToWords;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Validator;
class PaiementController extends Controller
{
    public function index()
    {
        if (auth()->guest()) {
            return redirect()->route('login');
        }
        $paiements = Paiement::all();
        return view('paiements.index', compact('paiements'));
    }

    public function create()
    {
        $comptes = Compte::all(); 
        $beneficiaires = Beneficiaire::all(); 
        return view('paiements.create', compact('comptes', 'beneficiaires'));
    }

    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'montant' => 'required|numeric',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|in:carte,virement,cheque,espèces',
            'id_compte' => 'required|exists:compte,id',
            'id_beneficiaire' => 'required|exists:beneficiaire,id',
            'status' => 'required|in:en attente,réussi,échoué',
            'motif_de_la_depence' => 'required|string',
            'impulsion' => 'required|in:TVA,IMF,loyer,Exonéré',
        ]);
    
        // Démarrer une transaction pour garantir l'intégrité des données
        DB::beginTransaction();
    
        try {
            $annee = date('Y', strtotime($request->date_paiement));
    
            // Sélectionner et verrouiller la ligne correspondante dans la table `compteur`
            $compteur = DB::table('compteur')
                ->where('annee', $annee)
                ->lockForUpdate() // Verrouiller la ligne pour éviter les conflits
                ->first();
    
            // Si l'année n'existe pas dans la table `compteur`, la créer avec un compteur initialisé à 1
            if (!$compteur) {
                $id_compt = 1;
                DB::table('compteur')->insert([
                    'annee' => $annee,
                    'compteur' => $id_compt,
                ]);
            } else {
                // Incrémenter le compteur
                $id_compt = $compteur->compteur + 1;
            }
    
            // Insérer le paiement avec le nouvel `id_compt`
            Paiement::create([
                'montant' => $request->montant,
                'date_paiement' => $request->date_paiement,
                'mode_paiement' => $request->mode_paiement,
                'id_compte' => $request->id_compte,
                'id_beneficiaire' => $request->id_beneficiaire,
                'status' => $request->status,
                'motif_de_la_depence' => $request->motif_de_la_depence,
                'impulsion' => $request->impulsion,
                
            ]);
    
            // Mettre à jour le compteur dans la table `compteur`
            DB::table('compteur')
                ->where('annee', $annee)
                ->update(['compteur' => $id_compt]);
    
            // Valider la transaction
            DB::commit();
    
            return redirect()->route('paiements.index')->with('success', 'Paiement ajouté avec succès!');
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'ajout du paiement: ' . $e->getMessage());
        }
    }
  
    public function edit($id)
    {

        $paiement = Paiement::where('id', $id)->firstOrFail();
        $comptes = Compte::all();
        $beneficiaires = Beneficiaire::all();

        return view('paiements.edit', compact('paiement', 'comptes', 'beneficiaires'));
}

    public function update(Request $request, $id)
    {
            $request->validate([
                'montant' => 'required|numeric',
                'mode_paiement' => 'required|in:carte,virement,cheque,espèces',
                'id_compte' => 'required|exists:compte,id',
                'id_beneficiaire' => 'required|exists:beneficiaire,id',
                'status' => 'required|in:en attente,réussi,échoué',
                'motif_de_la_depence' => 'required|string',
                'impulsion' => 'required|in:TVA,IMF,loyer,Exonéré',
            ]);
            $paiement = Paiement::where('id', $id)->firstOrFail();
            $paiement->update($request->all());
        
            return redirect()->route('paiements.index')->with('success', 'Paiement mis à jour avec succès.');
        

    }
    public function destroy($id)
    {
        try {
            $paiement = Paiement::where('id', $id)->firstOrFail();
            $paiement->delete();
    
            return redirect()->route('paiements.index')->with('success', 'Paiement supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('paiements.index')->with('error', 'Une erreur s\'est produite lors de la suppression du paiement.');
        }
    }


    // public function export()
    // {

    //     return Excel::download(new PaiementExport, 'paiements.xlsx');
    // }
   
    public function import(Request $request)
    {
    $request->validate([
        'file' => 'required|mimes:xlsx,csv',
    ]);

    Excel::import(new PaiementImport, $request->file('file'));

    return back()->with('success', 'Les paiements ont été importés avec succès.');
    }

function convertirMontantEnLettres($montant) {
        $montant = number_format($montant, 2, '.', ''); // Assurez-vous que le montant est bien formaté avec deux décimales
    
        $chiffres = [
            0 => 'zéro', 1 => 'un', 2 => 'deux', 3 => 'trois', 4 => 'quatre', 5 => 'cinq', 6 => 'six', 
            7 => 'sept', 8 => 'huit', 9 => 'neuf', 10 => 'dix', 11 => 'onze', 12 => 'douze', 13 => 'treize', 
            14 => 'quatorze', 15 => 'quinze', 16 => 'seize', 17 => 'dix-sept', 18 => 'dix-huit', 19 => 'dix-neuf', 
            20 => 'vingt', 30 => 'trente', 40 => 'quarante', 50 => 'cinquante', 60 => 'soixante', 
            70 => 'soixante-dix', 80 => 'quatre-vingts', 90 => 'quatre-vingt-dix'
        ];
    
        // Conversion de la partie entière
        $entier = floor($montant);
        $decimales = round(($montant - $entier) * 100); // On prend les deux premières décimales
    
        // Conversion des centaines, milliers, etc.
        if ($entier == 0) {
            return $chiffres[0];
        }
    
        $lettres = '';
    
        // Des milliers et centaines
        if ($entier >= 1000) {
            $milliers = floor($entier / 1000);
            $lettres .= $this->convertirMontantEnLettres($milliers) . ' mille ';
            $entier %= 1000;
        }
    
        if ($entier >= 100) {
            $centaines = floor($entier / 100);
            if ($centaines > 1) {
                $lettres .= $chiffres[$centaines] . ' cent ';
            } else {
                $lettres .= 'cent ';
            }
            $entier %= 100;
        }
    
        // Les dizaines et unités
        if ($entier >= 20) {
            $dizaines = floor($entier / 10) * 10;
            $lettres .= $chiffres[$dizaines] . ' ';
            $entier %= 10;
        }
    
        if ($entier > 0) {
            $lettres .= $chiffres[$entier];
        }
    
        // Conversion de la partie décimale (centimes)
        if ($decimales > 0) {
            $lettres .= ' et ' . $decimales . '/100';
        }
    
        return ucfirst(trim($lettres)); // Capitalize the first letter for better formatting
    }
    function afficherDeuxPremiersChiffres($nombre) {
    $nombreStr = (string)$nombre;
    if (strlen($nombreStr) >= 2) {
        return substr($nombreStr, 0, 2);
    } else {
        return $nombreStr;
    }
}

function afficherAnneeActuelle() {
    // Utiliser la fonction date() pour obtenir l'année actuelle
    return date('Y');
}
    public function show($id)
    {
        $comptes = Compte::all(); 
        $beneficiaires = Beneficiaire::all();
        $paiement = Paiement::findOrFail($id);
        $taxes = Taxe::all();
        
        $tva = $imf = $pl = $cf = $irf = 0;

        foreach ($taxes as $taxe) {
            if ($taxe->nom == 'TVA') {
                $tva = $taxe->pourcentage / 100;
            } elseif ($taxe->nom == 'IMF') {
                $imf = $taxe->pourcentage / 100;
            } elseif ($taxe->nom == 'PL') {
                $pl = $taxe->pourcentage / 100;
            } elseif ($taxe->nom == 'CF') {
                $cf = $taxe->pourcentage / 100;
            } elseif ($taxe->nom == 'IRF') {
                $irf = $taxe->pourcentage / 100;
            }
        }

        if($paiement->impulsion == 'TVA'){

          // Création du document Word
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();
    $TCC = $paiement->montant;
    $HT = round($TCC / (1 + $tva), 2);
    $calc_tva = round(($tva * $TCC) / (1 + $tva), 1);
    $calc_imf = round($HT * $imf, 2);
    $net = round($TCC - ($calc_tva + $calc_imf), 1);
    

    // En-tête
    $section->addText("République Islamique de Mauritanie", ['bold' => true], ['align' => Jc::CENTER]);
    $section->addText("Honneur – Fraternité – Justice", ['bold' => true], ['align' => Jc::CENTER]);
    $section->addText("MINISTERE DE L'ENSEIGNEMENT SUPERIEUR", ['bold' => true], ['align' => Jc::CENTER]);
    $section->addText("ET DE LA RECHERCHE SCIENTIFIQUE", ['bold' => true], ['align' => Jc::CENTER]);
    $section->addText("INSTITUT SUPÉRIEUR NUMÉRIQUE", ['bold' => true], ['align' => Jc::CENTER]);
    $section->addText("Titre de paiement N° " . $paiement->id . "/" .$this->afficherAnneeActuelle(), ['bold' => true, 'underline' => 'single'], ['align' => Jc::CENTER]);

    // Corps
    $section->addText("Imputation budgétaire : Compte Principale :"." " .$this->afficherDeuxPremiersChiffres($paiement->compte->numero)." ". "SOUS Compte "." ". $paiement->compte->numero, ['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("Bénéficiaire : " . $paiement->beneficiaire->nom." ". $paiement->beneficiaire->prenom, ['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("Montant (en chiffres) : " . number_format($paiement->montant, 0, ',', ' ') . " MRU", ['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("Montant (en lettres) : " . $this->convertirMontantEnLettres($paiement->montant) . " MRU", ['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("Mode de paiement : "." ".$paiement->mode_paiement, null, ['align' => Jc::LEFT]);
    $section->addText("Montant brut : " . $this->convertirMontantEnLettres($paiement->montant) . " MRU", ['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("Motive de la dépense : "." " .$paiement->motif_de_la_depence, ['bold' => true], ['align' => Jc::LEFT]);

    // Taxes
    $section->addText("TVA : ". number_format($calc_tva, 0, ',', ' ') . " MRU", ['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("IMF : " . number_format($calc_imf, 0, ',', ' ') . " MRU ", ['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("ITS : " , null, ['align' => Jc::LEFT]);
    $section->addText("CNAM : ", null, ['align' => Jc::LEFT]);
    $section->addText("NET : " . number_format($net, 0, ',', ' ') . " MRU", ['bold' => true], ['align' => Jc::LEFT]);

    // Date et signatures
    $section->addTextBreak(1);
    $date = Carbon::parse($paiement->date_paiement)->format('d/m/Y');
    $section->addText("La date : " . $date, ['bold' => true], ['align' => Jc::RIGHT]);
    $section->addText("La Comptable", null, ['align' => Jc::LEFT]);
    $section->addText("Le Directeur", null, ['align' => Jc::RIGHT]);

    // Enregistrement et téléchargement du fichier
    $fileName = 'paiement_' . $paiement->id . '.docx';
    $filePath = storage_path('app/public/' . $fileName);
    $phpWord->save($filePath, 'Word2007');

    return response()->file($filePath, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'Content-Disposition' => 'inline; filename="' . $fileName . '"'
    ])->deleteFileAfterSend(true);
}
        elseif($paiement->impulsion == 'IMF'){

            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            $TTC = $paiement->montant;
            $HT = round($TTC / (1 + $tva), 2);
            $calc_imf = $HT * $imf;
            $net = $TTC - $calc_imf;
 // En-tête
 $section->addText("République Islamique de Mauritanie", ['bold' => true], ['align' => Jc::CENTER]);
 $section->addText("Honneur – Fraternité – Justice", ['bold' => true], ['align' => Jc::CENTER]);
 $section->addText("MINISTERE DE L'ENSEIGNEMENT SUPERIEUR", ['bold' => true], ['align' => Jc::CENTER]);
 $section->addText("ET DE LA RECHERCHE SCIENTIFIQUE", ['bold' => true], ['align' => Jc::CENTER]);
 $section->addText("INSTITUT SUPÉRIEUR NUMÉRIQUE", ['bold' => true], ['align' => Jc::CENTER]);
 $section->addText("Titre de paiement N° " . $paiement->id . "/" .$paiement->annee.$this->afficherAnneeActuelle(), ['bold' => true, 'underline' => 'single'], ['align' => Jc::CENTER]);

 // Corps
 $section->addText("Imputation budgétaire : Compte Principale :"." " .$this->afficherDeuxPremiersChiffres($paiement->compte->numero)." ". "SOUS Compte "." ". $paiement->compte->numero, ['bold' => true], ['align' => Jc::LEFT]);
 $section->addText("Bénéficiaire : " . $paiement->beneficiaire->nom." ". $paiement->beneficiaire->prenom, ['bold' => true], ['align' => Jc::LEFT]);
 $section->addText("Montant (en chiffres) : " . number_format($paiement->montant, 0, ',', ' ') . " MRU", ['bold' => true], ['align' => Jc::LEFT]);
 $section->addText("Montant (en lettres) : " . $this->convertirMontantEnLettres($paiement->montant) . " MRU", ['bold' => true], ['align' => Jc::LEFT]);
 $section->addText("Mode de paiement : "." ".$paiement->mode_paiement, null, ['align' => Jc::LEFT]);
 $section->addText("Montant brut : " . $this->convertirMontantEnLettres($paiement->montant) . " MRU", ['bold' => true], ['align' => Jc::LEFT]);
 $section->addText("Motive de la dépense : "." " .$paiement->motif_de_la_depence, ['bold' => true], ['align' => Jc::LEFT]);

    // Taxes
    $section->addText("TVA : ", null, ['align' => Jc::LEFT]);
    $section->addText("IMF : " . number_format($calc_imf, 0, ',', ' ') . " MRU ", ['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("ITS : " , null, ['align' => Jc::LEFT]);
    $section->addText("CNAM : ", null, ['align' => Jc::LEFT]);
    $section->addText("NET : " . number_format($net, 0, ',', ' ') . " MRU", ['bold' => true], ['align' => Jc::LEFT]);

    // Date et signatures
    $section->addTextBreak(1);
    $date = Carbon::parse($paiement->date_paiement)->format('d/m/Y');
    $section->addText("La date : " . $date, ['bold' => true], ['align' => Jc::RIGHT]);
    $section->addText("La Comptable", null, ['align' => Jc::LEFT]);
    $section->addText("Le Directeur", null, ['align' => Jc::RIGHT]);

    // Enregistrement et téléchargement du fichier
    $fileName = 'paiement_' . $paiement->id . '.docx';
    $filePath = storage_path('app/public/' . $fileName);
    $phpWord->save($filePath, 'Word2007');

    return response()->file($filePath, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'Content-Disposition' => 'inline; filename="' . $fileName . '"'
    ])->deleteFileAfterSend(true);
}

        elseif($paiement->impulsion == 'Loyer'){
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            $calc_pl = ($paiement->montant) * $pl;
            $calc_cf = ($paiement->montant) * $cf;
            $calc_irf = ($paiement->montant) * $irf;
            $net = ($paiement->montant) - ($calc_pl + $calc_cf + $calc_irf);

  // En-tête
  $section->addText("République Islamique de Mauritanie", ['bold' => true], ['align' => Jc::CENTER]);
  $section->addText("Honneur – Fraternité – Justice", ['bold' => true], ['align' => Jc::CENTER]);
  $section->addText("MINISTERE DE L'ENSEIGNEMENT SUPERIEUR", ['bold' => true], ['align' => Jc::CENTER]);
  $section->addText("ET DE LA RECHERCHE SCIENTIFIQUE", ['bold' => true], ['align' => Jc::CENTER]);
  $section->addText("INSTITUT SUPÉRIEUR NUMÉRIQUE", ['bold' => true], ['align' => Jc::CENTER]);
  $section->addText("Titre de paiement N° " . $paiement->id . "/" .$paiement->annee.$this->afficherAnneeActuelle(), ['bold' => true, 'underline' => 'single'], ['align' => Jc::CENTER]);

  // Corps
  $section->addText("Imputation budgétaire : Compte Principale :"." " .$this->afficherDeuxPremiersChiffres($paiement->compte->numero)." ". "SOUS Compte "." ". $paiement->compte->numero, ['bold' => true], ['align' => Jc::LEFT]);
  $section->addText("Bénéficiaire : " . $paiement->beneficiaire->nom." ". $paiement->beneficiaire->prenom, ['bold' => true], ['align' => Jc::LEFT]);
  $section->addText("Montant (en chiffres) : " . number_format($paiement->montant, 0, ',', ' ') . " MRU", ['bold' => true], ['align' => Jc::LEFT]);
  $section->addText("Montant (en lettres) : " . $this->convertirMontantEnLettres($paiement->montant) . " MRU", ['bold' => true], ['align' => Jc::LEFT]);
  $section->addText("Mode de paiement : "." ".$paiement->mode_paiement, null, ['align' => Jc::LEFT]);
  $section->addText("Montant brut : " . $this->convertirMontantEnLettres($paiement->montant) . " MRU", ['bold' => true], ['align' => Jc::LEFT]);
  $section->addText("Motive de la dépense : "." " .$paiement->motif_de_la_depence, ['bold' => true], ['align' => Jc::LEFT]);

    // Taxes
    $section->addText("TVA : ", null, ['align' => Jc::LEFT]);
    $section->addText("PL : " . number_format($calc_pl, 0, ',', ' ') . " MRU ", ['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("CF : " . number_format($calc_cf, 0, ',', ' ') . " MRU ", ['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("IRF : ". number_format($calc_irf, 0, ',', ' ') . " MRU ",['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("NET : " . number_format($net, 0, ',', ' ') . " MRU", ['bold' => true], ['align' => Jc::LEFT]);

    // Date et signatures
    $section->addTextBreak(1);
    $date = Carbon::parse($paiement->date_paiement)->format('d/m/Y');
    $section->addText("La date : " . $date, ['bold' => true], ['align' => Jc::RIGHT]);
    $section->addText("La Comptable", null, ['align' => Jc::LEFT]);
    $section->addText("Le Directeur", null, ['align' => Jc::RIGHT]);

    // Enregistrement et téléchargement du fichier
    $fileName = 'paiement_' . $paiement->id . '.docx';
    $filePath = storage_path('app/public/' . $fileName);
    $phpWord->save($filePath, 'Word2007');

    return response()->file($filePath, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'Content-Disposition' => 'inline; filename="' . $fileName . '"'
    ])->deleteFileAfterSend(true);
}
        else{
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            
           
    // En-tête
    $section->addText("République Islamique de Mauritanie", ['bold' => true], ['align' => Jc::CENTER]);
    $section->addText("Honneur – Fraternité – Justice", ['bold' => true], ['align' => Jc::CENTER]);
    $section->addText("MINISTERE DE L'ENSEIGNEMENT SUPERIEUR", null, ['align' => Jc::CENTER]);
    $section->addText("ET DE LA RECHERCHE SCIENTIFIQUE", null, ['align' => Jc::CENTER]);
    $section->addText("INSTITUT SUPÉRIEUR NUMÉRIQUE", ['bold' => true], ['align' => Jc::CENTER]);
    $section->addText("Titre de paiement N° " . $paiement->id . "/" .$paiement->annee.$this->afficherAnneeActuelle(), ['bold' => true, 'underline' => 'single'], ['align' => Jc::CENTER]);

    // Corps
    $section->addText("Imputation budgétaire : Compte Principale :"." " .$this->afficherDeuxPremiersChiffres($paiement->compte->numero)." ". "SOUS Compte "." ". $paiement->compte->numero, null, ['align' => Jc::LEFT]);
    $section->addText("Bénéficiaire : " . $paiement->beneficiaire->nom, null, ['align' => Jc::LEFT]);
    $section->addText("Montant (en chiffres) : " . number_format($paiement->montant, 0, ',', ' ') . " MRU", ['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("Montant (en lettres) : " . $this->convertirMontantEnLettres($paiement->montant) . " MRU", ['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("Mode de paiement : Virement bancaire", null, ['align' => Jc::LEFT]);
    $section->addText("Montant brut : " . $this->convertirMontantEnLettres($paiement->montant) . " MRU", ['bold' => true], ['align' => Jc::LEFT]);

    // Taxes
    $section->addText("TVA : ", ['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("IMF : " , ['bold' => true], ['align' => Jc::LEFT]);
    $section->addText("ITS : " , null, ['align' => Jc::LEFT]);
    $section->addText("CNAM : ", null, ['align' => Jc::LEFT]);
    $section->addText("NET : " . number_format($paiement->montant, 0, ',', ' ') . " MRU", ['bold' => true], ['align' => Jc::LEFT]);

    // Date et signatures
    $section->addTextBreak(1);
    $date = Carbon::parse($paiement->date_paiement)->format('d/m/Y');
    $section->addText("La date : " . $date, ['bold' => true], ['align' => Jc::RIGHT]);
    $section->addText("La Comptable", null, ['align' => Jc::LEFT]);
    $section->addText("Le Directeur", null, ['align' => Jc::RIGHT]);

    // Enregistrement et téléchargement du fichier
    $fileName = 'paiement_' . $paiement->id . '.docx';
    $filePath = storage_path('app/public/' . $fileName);
    $phpWord->save($filePath, 'Word2007');

    return response()->file($filePath, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'Content-Disposition' => 'inline; filename="' . $fileName . '"'
    ])->deleteFileAfterSend(true);
}
        

    }

    public function showLastIdPerYear()
    {
        // Récupérer les derniers paiements par année
        $paiements = DB::table('paiement')
            ->select(DB::raw('YEAR(date_paiement) as year'), DB::raw('MAX(id) as last_id'))
            ->groupBy(DB::raw('YEAR(date_paiement)'))
            ->get();
    
        // Calculer la prochaine ID pour chaque année
        $paiements = $paiements->map(function ($paiement) {
            $paiement->next_id = $paiement->last_id + 1;
            return $paiement;
        });
    
        return view('paiements.updateNextId', compact('paiements'));
    }
    

    // public function updateNextId(Request $request)
    // {
    //     // Validation des données
    //     $request->validate([
    //         'year' => 'required|integer',
    //         'next_id' => 'required|integer|min:1',
    //     ]);
    
    //     // Récupérer les valeurs du formulaire
    //     $year = $request->input('year');
    //     $newNextId = $request->input('next_id');
    
    //     // Vérifier que la nouvelle ID est supérieure à la dernière ID pour cette année
    //     $currentMaxId = DB::table('paiement')
    //         ->whereYear('date_paiement', $year)
    //         ->max('id') ?? 0;
    
    //     if ($newNextId <= $currentMaxId) {
    //         return redirect()->route('paiement.showLastIdPerYear')
    //             ->with('error', "La nouvelle ID doit être supérieure à l'ID maximum actuel pour l'année $year ($currentMaxId).");
    //     }
    
    //     try {
    //         // Mettre à jour la prochaine ID dans la table
    //         DB::statement("ALTER TABLE paiement AUTO_INCREMENT = $newNextId");
    
    //         return redirect()->route('paiement.showLastIdPerYear')
    //             ->with('success', "La prochaine ID pour l'année $year a été mise à jour à $newNextId.");
    //     } catch (\Exception $e) {
    //         return redirect()->route('paiement.showLastIdPerYear')
    //             ->with('error', "Une erreur est survenue lors de la mise à jour de la prochaine ID : " . $e->getMessage());
    //     }
    // }
}