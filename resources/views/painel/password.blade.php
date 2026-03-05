<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel protegido</title>
    @vite(['resources/js/panel-password.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            width: 100%;
            height: 100%;
            background: #0f0f0f;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, sans-serif;
            color: #e5e5e5;
        }

        .card {
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            border-radius: 12px;
            padding: 40px 36px;
            width: 100%;
            max-width: 360px;
        }

        .icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #2a2a2a;
            margin: 0 auto 20px;
        }

        .icon svg {
            width: 24px;
            height: 24px;
            color: #f59e0b;
        }

        h1 {
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        p {
            text-align: center;
            font-size: 13px;
            color: #777;
            margin-bottom: 28px;
        }

        label {
            display: block;
            font-size: 13px;
            color: #aaa;
            margin-bottom: 6px;
        }

        input[type="password"] {
            width: 100%;
            background: #111;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 15px;
            color: #e5e5e5;
            outline: none;
            transition: border-color 0.15s;
        }

        input[type="password"]:focus {
            border-color: #f59e0b;
        }

        input[type="password"].error {
            border-color: #ef4444;
        }

        .error-msg {
            font-size: 12px;
            color: #ef4444;
            margin-top: 6px;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 11px;
            background: #f59e0b;
            color: #0f0f0f;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s;
        }

        button:hover { background: #d97706; }
    </style>
</head>
<body>
    <div id="panel-password" data-hash="{{ $hash }}"></div>
    <div class="card">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
            </svg>
        </div>

        <h1>Painel protegido</h1>
        <p>Insira a senha para acessar este painel.</p>

        <form method="POST" action="/painel/{{ $hash }}/unlock">
            @csrf
            <label for="password">Senha</label>
            <input
                id="password"
                type="password"
                name="password"
                autofocus
                autocomplete="current-password"
                class="{{ $errors->has('password') ? 'error' : '' }}"
            >
            @error('password')
                <div class="error-msg">{{ $message }}</div>
            @enderror
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
