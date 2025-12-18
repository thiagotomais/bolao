<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: #e5e7eb;
        }

        header {
            background: #020617;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 1.1rem;
            margin: 0;
        }

        nav a {
            margin-right: 1rem;
            color: #22c55e;
            text-decoration: none;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        main {
            padding: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th, td {
            border: 1px solid #334155;
            padding: .75rem;
            text-align: left;
        }

        th {
            background: #020617;
        }

        button {
            background: #22c55e;
            border: none;
            padding: .5rem 1rem;
            cursor: pointer;
            font-weight: bold;
        }

        .muted {
            color: #94a3b8;
        }
    </style>
</head>
<body>

<header>
    <h1>Painel Admin</h1>

    <nav>
        <a href="{{ route('admin.participants') }}">Participantes</a>
        <a href="{{ route('admin.simulate') }}">Simulação</a>
        <a href="{{ route('admin.games') }}">Jogos</a>

        <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
            @csrf
            <button type="submit">Sair</button>
        </form>
    </nav>
</header>

<main>
    @yield('content')
</main>

</body>
</html>
