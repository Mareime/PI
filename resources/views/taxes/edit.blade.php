{{-- @if (Session::has('user_id')) --}}
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center">Modifier le Pourcentage des Taxes</h2>

    <form action="{{ route('taxes.update', $taxes) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Nom de la taxe -->
        <div class="row mb-3">
            <label for="nom" class="col-md-3 col-form-label text-md-end">Nom de la Taxe</label>
            <div class="col-md-6">
                <input 
                    type="text" 
                    name="nom" 
                    id="nom" 
                    class="form-control" 
                    value="{{ old('nom', $taxes->nom) }}" 
                    required
                    placeholder="Exemple : TVA"
                >
            </div>
        </div>

        <!-- Pourcentage de la taxe -->
        <div class="row mb-3">
            <label for="pourcentage" class="col-md-3 col-form-label text-md-end">Pourcentage de la Taxe</label>
            <div class="col-md-6">
                <input 
                    type="number" 
                    name="pourcentage" 
                    id="pourcentage" 
                    class="form-control" 
                    value="{{ old('pourcentage', $taxes->pourcentage) }}" 
                    required
                    placeholder="Exemple : 20"
                >
            </div>
        </div>

        <!-- Boutons -->
        <div class="row mb-3">
            <div class="col-md-6 offset-md-3">
                <button type="submit" class="btn btn-primary">Mettre à Jour</button>
                <a href="{{ route('taxes.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </div>
    </form>
</div>
@endsection
{{-- @else
<script>
    window.location.href = "{{ route('connexion.form') }}";
</script>
@endif --}}
