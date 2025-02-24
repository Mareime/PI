@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Liste des compteurs</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- <a href="{{ route('compteurs.create') }}" class="btn btn-dark mb-3">Ajouter un compteur</a> --}}

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Année</th>
                <th>Compteur</th>
                {{-- <th>Actions</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($compteurs as $compteur)
                <tr>
                    <td>{{ $compteur->annee }}</td>
                    <td>{{ $compteur->compteur }}</td>
                    {{-- <td>
                        <a href="{{ route("compteurs.edite",$compteur->annee)}}" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </td> --}}
                    
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
