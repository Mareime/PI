@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Liste des Taxes</h2>

    <!-- Message de succès -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- <div class="mb-3 text-end">
        <a href="{{ route('taxes.create') }}" class="btn btn-warning btn-sm">Ajouter une Taxe</a>
        <a href="{{ route('taxes.export') }}" class="btn btn-primary btn-sm">Exporter</a>
    </div> --}}
    <div class="mb-3">
        <form action="{{ route('taxes.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- <div class="input-group">
                <input type="file" name="file" class="form-control" required>
                <button type="submit" class="btn btn-success">Importer</button>
            </div> --}}
        </form>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Nom du taxe</th>
                <th>Pourcentage du taxe</th>
                {{-- <th>Actions</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($taxes as $taxe)
            <tr>
                <td>{{ $taxe->nom }}</td>
                <td>{{ $taxe->pourcentage }}</td>
                {{-- <td>
                    <a href="{{ route('taxes.edit', $taxe->id) }}" class="btn btn-warning btn-sm">Modifier</a>

                    <!-- Bouton Supprimer -->
                    <form action="{{ route('taxes.destroy', $taxe->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette taxe ?')">
                            Supprimer
                        </button>
                    </form>
                </td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
