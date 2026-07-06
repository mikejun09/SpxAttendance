<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — SPX Rider Attendance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f1117;
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .bg-grid {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(249,115,22,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(249,115,22,.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .glow {
            position: fixed;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(249,115,22,.12) 0%, transparent 70%);
            top: -100px; left: -100px;
            pointer-events: none;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #161b27;
            border: 1px solid #2a3450;
            border-radius: 20px;
            padding: 40px;
            position: relative;
            z-index: 1;
            box-shadow: 0 24px 64px rgba(0,0,0,.6);
        }

        .logo-wrap {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #f97316, #c2510a);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px;
            margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(249,115,22,.4);
        }

        .logo-title { font-size: 24px; font-weight: 800; }
        .logo-title span { color: #f97316; }
        .logo-sub { font-size: 13px; color: #64748b; margin-top: 4px; }

        h1 { font-size: 20px; font-weight: 700; margin-bottom: 6px; }
        .subtitle { font-size: 13px; color: #64748b; margin-bottom: 28px; }

        .form-group { margin-bottom: 18px; }

        label { display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 6px; }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            background: #0f1117;
            border: 1px solid #2a3450;
            border-radius: 10px;
            padding: 12px 16px;
            color: #f1f5f9;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color .2s, box-shadow .2s;
        }

        input:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249,115,22,.15);
        }

        .form-check {
            display: flex; align-items: center; gap: 10px;
            cursor: pointer; margin-bottom: 20px;
        }

        input[type="checkbox"] {
            width: 18px; height: 18px;
            accent-color: #f97316;
        }

        .form-check label { margin: 0; cursor: pointer; font-size: 13px; }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #f97316, #c2510a);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all .2s;
            box-shadow: 0 4px 16px rgba(249,115,22,.4);
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(249,115,22,.5);
        }

        .alert-error {
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.3);
            color: #ef4444;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-status {
            background: rgba(34,197,94,.1);
            border: 1px solid rgba(34,197,94,.3);
            color: #22c55e;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .demo-creds {
            margin-top: 24px;
            padding: 16px;
            background: rgba(249,115,22,.05);
            border: 1px dashed rgba(249,115,22,.2);
            border-radius: 10px;
            font-size: 12px;
            color: #64748b;
            text-align: center;
        }

        .demo-creds strong { color: #f97316; }
    </style>
</head>
<body>
<div class="bg-grid"></div>
<div class="glow"></div>

<div class="login-card">
    <div class="logo-wrap">
        <div class="logo-icon">🚴</div>
        <div class="logo-title"><span>SPX</span> Riders</div>
        <div class="logo-sub">Attendance & Payroll System</div>
    </div>

    <h1>Welcome back</h1>
    <p class="subtitle">Sign in to your account to continue</p>

    @if (session('status'))
        <div class="alert-status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email"><i class="fa-solid fa-envelope" style="margin-right:6px;"></i> Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="admin@spx.com">
        </div>

        <div class="form-group">
            <label for="password"><i class="fa-solid fa-lock" style="margin-right:6px;"></i> Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
        </div>

        <div class="form-check">
            <input type="checkbox" id="remember_me" name="remember">
            <label for="remember_me">Remember me</label>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-right-to-bracket"></i> Sign In
        </button>
    </form>

    <div class="demo-creds">
        <strong>Demo Credentials</strong><br>
        Admin: <strong>admin@spx.com</strong> / <strong>admin123</strong><br>
        Rider: <strong>juan@spx.com</strong> / <strong>rider123</strong>
    </div>
</div>
</body>
</html>
