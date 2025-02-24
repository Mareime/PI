@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modifier le compteur</h1>

    <form action="{{ route('compteurs.update', $compteur->aneee) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="annee">Année</label>
            <input type="text" class="form-control" id="annee" name="annee" value="{{ $compteur->annee }}" required>
        </div>
        <div class="form-group">
            <label for="compteur">Compteur</label>
            <input type="text" class="form-control" id="compteur" name="compteur" value="{{ $compteur->compteur }}" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Mettre à jour</button>
    </form>
</div>
@endsection
