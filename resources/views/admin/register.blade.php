<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Account - Froggy Kidz</title>
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

        .brand-steps {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .brand-steps .step {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .brand-steps .step .num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 1px solid rgba(199, 154, 69, 0.5);
            color: var(--gold-500);
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-steps .step p {
            margin: 0;
            font-size: 13.5px;
            line-height: 1.55;
            color: rgba(255, 255, 255, 0.72);
            padding-top: 3px;
        }

        .brand-foot {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.4);
        }

        .form-panel {
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 32px;
        }

        .form-wrap {
            width: 100%;
            max-width: 400px;
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
            border: 1px solid var(--danger-line);
            background: var(--danger-bg);
            color: var(--danger-text);
        }

        .alert ul {
            list-style: none;
        }

        .alert li + li {
            margin-top: 3px;
        }

        form {
            margin-top: 26px;
        }

        .field {
            margin-bottom: 18px;
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

        .field.has-error input {
            border-color: var(--danger-line);
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

        .field .error-message {
            display: block;
            margin-top: 6px;
            font-size: 12.5px;
            color: var(--danger-text);
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
            margin-top: 6px;
            transition: background 0.15s ease;
        }

        .btn-submit:hover {
            background: var(--navy-700);
        }

        .switch-line {
            text-align: center;
            margin-top: 24px;
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

        .terms {
            margin-top: 18px;
            text-align: center;
            font-size: 12.5px;
            line-height: 1.6;
            color: var(--ink-faint);
        }

        .terms a {
            color: var(--ink-soft);
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

            .brand-steps {
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
                <h1>Create your account</h1>
                <p>Set up access in under a minute. You'll be able to sign in right away.</p>

                <div class="brand-steps">
                    <div class="step">
                        <span class="num">1</span>
                        <p>Tell us your name and how to reach you</p>
                    </div>
                    <div class="step">
                        <span class="num">2</span>
                        <p>Choose a password to protect your account</p>
                    </div>
                    <div class="step">
                        <span class="num">3</span>
                        <p>You're signed in immediately, no waiting</p>
                    </div>
                </div>
            </div>

            <div class="brand-foot">&copy; {{ date('Y') }} Froggy Kidz</div>
        </div>

        <div class="form-panel">
            <div class="form-wrap">
                <div class="form-head">
                    <h2>Create account</h2>
                    <p>Fill in your details to get started</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('admin.register') }}" method="POST">
                    @csrf
                    <div class="field @error('name') has-error @enderror">
                        <label for="name">Full name</label>
                        <div class="control">
                            <svg class="leading" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.2"/><path d="M5 20c0-3.5 3.1-6.2 7-6.2s7 2.7 7 6.2"/></svg>
                            <input type="text" id="name" name="name" placeholder="Enter your full name" value="{{ old('name') }}" required autofocus>
                        </div>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field @error('email') has-error @enderror">
                        <label for="email">Email address</label>
                        <div class="control">
                            <svg class="leading" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
                            <input type="email" id="email" name="email" placeholder="you@company.com" value="{{ old('email') }}" required>
                        </div>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field @error('phone') has-error @enderror">
                        <label for="phone">Phone number</label>
                        <div class="control">
                            <svg class="leading" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h4l1.5 4-2 1.5a12 12 0 0 0 6 6l1.5-2 4 1.5v4c0 1-1 2-2 2C10.5 20 4 13.5 4 5c0-1 1-2 2-2z"/></svg>
                            <input type="text" id="phone" name="phone" placeholder="Enter your phone number" value="{{ old('phone') }}">
                        </div>
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field @error('password') has-error @enderror">
                        <label for="password">Password</label>
                        <div class="control">
                            <svg class="leading" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="10" rx="1.5"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                            <input type="password" id="password" name="password" placeholder="At least 6 characters" required>
                            <button type="button" class="toggle-visibility" data-target="password" aria-label="Show password">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm password</label>
                        <div class="control">
                            <svg class="leading" viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter your password" required>
                            <button type="button" class="toggle-visibility" data-target="password_confirmation" aria-label="Show password">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Create account</button>
                </form>

                <div class="switch-line">
                    Already have an account? <a href="{{ route('admin.login') }}">Sign in</a>
                </div>

                <div class="terms">
                    By creating an account, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
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