<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CRM Register</title>
    <style>
        :root {
            --panel-bg: rgba(255, 255, 255, 0.062);
            --panel-border: rgba(255, 255, 255, 0.534);
            --text-main: #f8fafc;
            --input-bg: rgba(255, 255, 255, 0.12);
            --input-border: rgba(255, 255, 255, 0.22);
            --button-bg: linear-gradient(135deg, #0f766e, #14b8a6);
            --shadow: 0 24px 60px rgba(0, 0, 0, 0.493);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Tahoma, Arial, sans-serif;
            background:
                linear-gradient(rgba(15, 23, 42, 0.699), rgba(15, 23, 42, 0.808)),
                url('images/crm.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            color: var(--text-main);
            padding: 24px 24px 24px 7vw;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(20, 184, 166, 0.18), transparent 30%),
                radial-gradient(circle at bottom left, rgba(59, 130, 246, 0.18), transparent 35%);
            pointer-events: none;
        }

        .register-card {
            width: min(530px, 100%);
            padding: 64px 40px;
            border-radius: 24px;
            background: var(--panel-bg);
            border: 1px solid var(--panel-border);
            box-shadow: var(--shadow);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(20px);
            position: relative;
            z-index: 1;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            opacity: 0.9;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .back-link:hover {
            opacity: 1;
            transform: translateX(-2px);
        }

        h1 {
            font-size: 31px;
            margin: 0 0 24px;
            text-align: center;
        }

        .status {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        .error-list {
            margin: 0 0 18px;
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(239, 68, 68, 0.18);
            border: 1px solid rgba(248, 113, 113, 0.4);
            color: #fee2e2;
        }

        .field {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #e2e8f0;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid var(--input-border);
            background: var(--input-bg);
            color: #ffffff;
            outline: none;
            transition: 0.2s ease;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.342);
        }

        input:focus {
            border-color: rgba(255, 255, 255, 0.42);
            background: rgba(255, 255, 255, 0.18);
        }

        button {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
            background: var(--button-bg);
            color: #fff;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
            box-shadow: 0 16px 30px rgba(20, 184, 165, 0.712);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 34px rgba(20, 184, 166, 0.3);
        }

        @media (max-width: 520px) {
            body {
                padding: 16px;
                justify-content: center;
            }

            .register-card {
                padding: 28px 20px;
                border-radius: 20px;
            }

            h1 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <form class="register-card" action="{{ route('register.submit') }}" method="POST">
        @csrf
        <a class="back-link" href="{{ route('login') }}">
            <span>&larr;</span>
        </a>
        <h1>SIGN UP</h1>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-list">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="field">
            <label for="name">Nom complet</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Votre nom" required>
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="name@example.com" required>
        </div>

        <div class="field">
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" placeholder="********" required>
        </div>

        <div class="field">
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="********" required>
        </div>

        <button type="submit">Créer un compte</button>
    </form>
</body>
</html>
