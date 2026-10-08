<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign In - Froggy Kidz</title>
    <style>
        :root {
            --navy-900: #0F1A2E;
            --navy-800: #16233B;
            --navy-700: #1E2E4A;
            --gold-500: #C79A45;
            --gold-600: #A97F34;
            --ink: #1B2333;
            --ink-soft: #5B6472;
            --ink-faint: #8A93A3;
            --line: #E2E5EA;
            --bg: #F5F6F8;
            --white: #FFFFFF;
            --danger-bg: #FBEAE9;
            --danger-text: #A32E27;
            --danger-line: #E4A9A4;
            --success-bg: #E9F4EE;
            --success-text: #2E7D4F;
            --success-line: #A9D3BB;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg);
            min-height: 100vh;
            color: var(--ink);
        }

        .shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 5fr) minmax(0, 6fr);
        }

        /* Left: brand panel */
        .brand-panel {
            background: var(--navy-900);
            background-image:
                radial-gradient(circle at 15% 8%, rgba(199, 154, 69, 0.16), transparent 45%),
                linear-gradient(180deg, var(--navy-900) 0%, var(--navy-800) 100%);
            color: var(--white);
            padding: 56px 52px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-mark {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-mark .mark-icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: rgba(199, 154, 69, 0.14);
            border: 1px solid rgba(199, 154, 69, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-mark .mark-icon svg {
            width: 19px;
            height: 19px;
            stroke: var(--gold-500);
        }

        .brand-mark span {
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.2px;
            color: rgba(255, 255, 255, 0.92);
        }

        .brand-copy {
            max-width: 360px;
            margin-top: 64px;
        }

        .brand-copy h1 {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 34px;
            line-height: 1.28;
            font-weight: 500;
            color: var(--white);
        }

        .brand-copy p {
            margin-top: 16px;
            font-size: 15px;
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.62);
        }

        .brand-features {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .brand-features .feature {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .brand-features .feature svg {
            width: 17px;
            height: 17px;
            stroke: var(--gold-500);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .brand-features .feature p {
            margin: 0;
            font-size: 13.5px;
            line-height: 1.55;
            color: rgba(255, 255, 255, 0.72);
        }

        .brand-foot {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.4);
        }

        /* Right: form panel */
        .form-panel {
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 32px;
        }

        .form-wrap {
            width: 100%;
            max-width: 380px;
        }

        .form-head h2 {
            font-size: 24px;
            font-weight: 600;
            color: var(--ink);
        }

        .form-head p {
            margin-top: 6px;
            font-size: 14px;
            color: var(--ink-soft);
        }

        .alert {
            margin-top: 24px;
            padding: 11px 14px;
            border-radius: 8px;
            font-size: 13.5px;
            line-height: 1.5;
            border: 1px solid transparent;
        }

        .alert-danger {
            background: var(--danger-bg);
            color: var(--danger-text);
            border-color: var(--danger-line);
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success-text);
            border-color: var(--success-line);
        }

        form {
            margin-top: 28px;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
        }

        .field .control {
            position: relative;
            display: flex;
            align-items: center;
        }

        .field .control svg.leading {
            position: absolute;
            left: 13px;
            width: 17px;
            height: 17px;
            stroke: var(--ink-faint);
            pointer-events: none;
        }

        .field input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 1px solid var(--line);
            border-radius: 8px;
            font-size: 14.5px;
            font-family: inherit;
            color: var(--ink);
            background: var(--white);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .field input::placeholder {
            color: var(--ink-faint);
        }

        .field input:focus {
            outline: none;
            border-color: var(--gold-600);
            box-shadow: 0 0 0 3px rgba(199, 154, 69, 0.16);
        }

        .field .toggle-visibility {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            padding: 4px;
            cursor: pointer;
            display: flex;
            color: var(--ink-faint);
        }

        .field .toggle-visibility svg {
            width: 17px;
            height: 17px;
            stroke: currentColor;
        }

        .field .toggle-visibility:hover {
            color: var(--ink-soft);
        }

        .row-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            color: var(--ink-soft);
            cursor: pointer;
            user-select: none;
        }

        .remember input {
            width: 15px;
            height: 15px;
            accent-color: var(--gold-600);
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            padding: 12.5px;
            background: var(--navy-900);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 14.5px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .btn-submit:hover {
            background: var(--navy-700);
        }

        .switch-line {
            text-align: center;
            margin-top: 26px;
            font-size: 13.5px;
            color: var(--ink-soft);
        }

        .switch-line a {
            color: var(--gold-600);
            text-decoration: none;
            font-weight: 600;
        }

        .switch-line a:hover {
            text-decoration: underline;
        }

        @media (max-width: 880px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                padding: 36px 28px;
            }

            .brand-copy {
                margin-top: 28px;
            }

            .brand-copy h1 {
                font-size: 26px;
            }

            .brand-features {
                display: none;
            }

            .form-panel {
                padding: 36px 24px 48px;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="brand-panel">
            <div class="brand-mark">
                <span class="mark-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"/></svg>
                </span>
                <span>Froggy Kidz</span>
            </div>

            <div class="brand-copy">
                <h1>Sign in to manage your platform</h1>
                <p>One workspace to oversee accounts, review activity, and keep every record in order.</p>

                <div class="brand-features">
                    <div class="feature">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.2"/><path d="M5 20c0-3.5 3.1-6.2 7-6.2s7 2.7 7 6.2"/></svg>
                        <p>Manage user accounts and roles from a single directory</p>
                    </div>
                    <div class="feature">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v14H7l-3 3V4z"/></svg>
                        <p>Review sign-ins and account status changes as they happen</p>
                    </div>
                    <div class="feature">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="10" rx="1.5"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                        <p>Every session is authenticated and access is logged</p>
                    </div>
                </div>
            </div>

            <div class="brand-foot">&copy; {{ date('Y') }} Froggy Kidz</div>
        </div>

        <div class="form-panel">
            <div class="form-wrap">
                <div class="form-head">
                    <h2>Welcome back</h2>
                    <p>Sign in with your account credentials</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('admin.login') }}" method="POST">
                    @csrf
                    <div class="field">
                        <label for="email">Email address</label>
                        <div class="control">
                            <svg class="leading" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
                            <input type="email" id="email" name="email" placeholder="you@company.com" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="control">
                            <svg class="leading" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="10" rx="1.5"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" class="toggle-visibility" data-target="password" aria-label="Show password">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="row-options">
                        <label class="remember">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">Sign in</button>
                </form>

                <div class="switch-line">
                    Don't have an account? <a href="{{ route('admin.register') }}">Create one</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.toggle-visibility').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.target);
                var isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            });
        });
    </script>
</body>
</html>