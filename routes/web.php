<?php

use App\Http\Controllers\CompteController;
use App\Http\Controllers\beneficiaireController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\CompteurController;

use App\Http\Controllers\TaxeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\controleruser;
// Route to display the login form (GET request)
// Affiche le formulaire de connexion
Route::get('', [controleruser::class, 'showLoginForm'])->name('login');
Route::get('/logout', [controleruser::class, 'logout'])->name('logout');
Route::post('/login', [controleruser::class, 'login'])->name('login.post');
// Route::middleware(['auth'])->group(function () {

Route::get('/home', function () {
    return view('connexion.home');
})->middleware('auth');

Route::get('compt/export', [CompteController::class, 'export'])->name('compt.export');
Route::get('beneficiaire/export', [beneficiaireController::class, 'export'])->name('beneficiaire.export');
Route::get('paiements/export', [PaiementController::class, 'export'])->name('paiements.export');

// Importation des comptes
// Exporter et importer les comptes
Route::get('comptes/export', [CompteController::class, 'export'])->name('compte.export');
Route::post('comptes/import', [CompteController::class, 'import'])->name('compte.import');
Route::post('beneficiaire/import', [beneficiaireController::class, 'import'])->name('beneficiaire.import');
Route::get('paiements/import', [PaiementController::class, 'import'])->name('paiements.import');

Route::get('beneficiaire/export', [beneficiaireController::class, 'export'])->name('beneficiaire.export');

// Importation des comptes
Route::post('comptes/import', [CompteController::class, 'import'])->name('compte.import');
Route::post('beneficiaire/import', [beneficiaireController::class, 'import'])->name('beneficiaire.import');

Route::get('/comptes', [CompteController::class,'index'])->name('compte.index'); 
Route::get('/comptes/create', [CompteController::class, 'create'])->name('compte.create');
Route::post('/comptes', [CompteController::class, 'store'])->name('compte.store');
Route::get('/comptes/{compte}/edit', [CompteController::class, 'edit'])->name('compte.edit');
Route::put('/comptes/{compte}', [CompteController::class, 'update'])->name('compte.update');
Route::delete('/comptes/{compte}', [CompteController::class, 'destroy'])->name('compte.destroy');
// pour la table beneficaire
Route::get('/beneficiaires', [beneficiaireController::class, 'index'])->name('beneficiaire.index');
Route::get('/beneficiaires/create', [beneficiaireController::class, 'create'])->name('beneficiaire.create');
Route::post('/beneficiaires', [beneficiaireController::class, 'store'])->name('beneficiaire.store');
Route::get('/beneficiaires/{beneficiaire}/edit', [beneficiaireController::class, 'edit'])->name('beneficiaire.edit');
Route::put('/beneficiaires/{beneficiaire}', [beneficiaireController::class, 'update'])->name('beneficiaire.update');
Route::delete('/beneficiaires/{beneficiaire}', [beneficiaireController::class, 'destroy'])->name('beneficiaire.destroy');

// Routes pour les paiements
Route::resource('paiements', PaiementController::class);
Route::get('paiements',[PaiementController::class,'index'])->name('paiements.index');
Route::get('paiements/create',[PaiementController::class,'create'])->name('paiement.create');
Route::post('/paiements',[PaiementController::class,'store'])->name('paiements.store');
Route::delete('paiements/{id}', [PaiementController::class, 'destroy'])->name('paiements.destroy1');
Route::get('paiements/{id}/edit', [PaiementController::class, 'edit'])->name('paiements.edit1');
Route::put('paiements/{id}', [PaiementController::class, 'update'])->name('paiements.update1');

Route::resource('taxes', TaxeController::class)->except(['show']);

// Routes personnalisées pour import/export
Route::post('/taxes/import', [TaxeController::class, 'import'])->name('taxes.import'); 
Route::get('/taxes/export', [TaxeController::class, 'export'])->name('taxes.export'); 
// });
Route::get('/accueil', function () {
    return view('connexion.home');
});
// id par annee
Route::get('/paiements-next-id', [PaiementController::class,'showLastIdPerYear'])->name('paiement.NextId');
Route::post('/paiement/update-next-id', [PaiementController::class, 'updateNextId'])->name('paiement.updateNextId');
// users
Route::get('/users', [controleruser::class, 'index'])->name('users.index');
Route::get('/users/create', [controleruser::class, 'create'])->name('users.create');
Route::post('/users', [controleruser::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [controleruser::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [controleruser::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [controleruser::class, 'destroy'])->name('users.destroy');
// route for compteure
Route::get('compteurs', [CompteurController::class, 'index'])->name('compteurs.index'); 
Route::get('compteurs/create', [CompteurController::class, 'create'])->name('compteurs.create');  
Route::post('compteurs', [CompteurController::class, 'store'])->name('compteurs.store');  
// commit