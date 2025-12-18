<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background: #0f172a;
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        form {
            background: #020617;
            padding: 2rem;
            border-radius: 8px;
            width: 100%;
            max-width: 320px;
        }
        input, button {
            width: 100%;
            padding: .75rem;
            margin-top: .75rem;
        }
        button {
            background: #22c55e;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        .error {
            color: #f87171;
            font-size: .9rem;
        }
    </style>
</head>
<body>

<form method="POST" action="{{ route('admin.login.submit') }}">
    @csrf

    <h2>Admin</h2>

    <input type="password" name="password" placeholder="Senha do admin" required>

    @error('password')
        <div class="error">{{ $message }}</div>
    @enderror

    <button type="submit">Entrar</button>
</form>

</body>
</html>
