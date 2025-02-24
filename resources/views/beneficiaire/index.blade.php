@extends('layouts.app')

@section('content')

    <div class="container">
        <h2 class="mb-4">Liste des Bénéficiaires</h2>
        {{-- <a href="{{ route('beneficiaire.create') }}" class="btn btn-success mb-3">Créer un nouveau Bénéficiaire</a> --}}

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Conteneur avec alignement à droite pour les boutons Exporter et Importer -->
        {{-- <div class="mb-3 d-flex justify-content-end">
            <a href="{{ route('beneficiaire.export') }}" class="btn btn-primary mr-2">Exporter</a>

            <!-- Bouton d'importation avec une modale -->
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#importModal">
                Importer
            </button>            
        </div> --}}
         <!-- Modal pour l'importation -->
         {{-- <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Importer un fichier Excel (XLSX) ou CSV</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('beneficiaire.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="file">Sélectionner un fichier à importer</label>
                                <input type="file" name="file" id="file" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-success">Importer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> --}}

        <!-- Tableau des bénéficiaires -->
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Adresse</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Type de Bénéficiaire</th>
                    {{-- <th>Actions</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach($beneficiaires as $beneficiaire)
                <tr>
                    <td>{{ $beneficiaire->nom }}</td>
                    <td>{{ $beneficiaire->prenom }}</td>
                    <td>{{ $beneficiaire->adresse }}</td>
                    <td>{{ $beneficiaire->telephone }}</td>
                    <td>{{ $beneficiaire->email }}</td>
                    <td>{{ $beneficiaire->type_beneficiaire }}</td>
                    {{-- <td>
                        <a href="{{ route('beneficiaire.edit', $beneficiaire) }}" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="{{ route('beneficiaire.destroy', $beneficiaire) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce bénéficiaire ?');">
                            @csrf
                            @method('DELETE') <!-- Ceci est essentiel pour indiquer DELETE -->
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                        
                        </form>
                    </td> --}}
                </tr>
                @endforeach
            </tbody>
        </table>
@endsection
