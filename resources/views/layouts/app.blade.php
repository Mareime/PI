<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Comptes</title>

    <!-- CSS Bootstrap -->
   <!-- Bootstrap CSS (5.1.3) -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                <!-- Utilisez asset() pour charger l'image -->
                <img src="{{ asset('storage/images/logo.png') }}" alt="Logo" width="100" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('compte.index') }}">Comptes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('beneficiaire.index') }}">Bénéficiaires</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('paiements.index') }}">Paiements</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('taxes.index') }}">Taxes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('compteurs.index') }}">Année/id</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('logout') }}">Déconnexion</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link active" href="{{ route('users.index') }}">Les utilisateures</a>
                    </li> --}}
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container mt-4">
        @yield('content')
    </div>

    <!-- jQuery et Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
