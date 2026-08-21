<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Report - Password Required</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: linear-gradient(135deg, #0b0b39 0%, #001a3b 40%, #0f2460 70%, #1e3a8a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Decorative circles */
        body::before {
            content: '';
            position: absolute; top: -100px; left: -100px;
            width: 400px; height: 400px; border-radius: 50%;
            background: rgba(255,255,255,0.03);
        }
        body::after {
            content: '';
            position: absolute; bottom: -150px; right: -100px;
            width: 500px; height: 500px; border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        /* Floating blobs */
        .blob-1 { position: absolute; top: 20%; right: 15%; width: 200px; height: 200px; border-radius: 50%; background: radial-gradient(circle, rgba(59,130,246,0.12), transparent); animation: float 6s ease-in-out infinite; }
        .blob-2 { position: absolute; bottom: 25%; left: 10%; width: 150px; height: 150px; border-radius: 50%; background: radial-gradient(circle, rgba(139,92,246,0.1), transparent); animation: float 8s ease-in-out infinite reverse; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }

        /* Card */
        .pw-card {
            background: #fff;
            border-radius: 24px;
            width: 100%; max-width: 420px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.4);
            overflow: hidden;
            position: relative; z-index: 1;
            animation: slideUp 0.6s ease both;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px) scale(0.96); } to { opacity: 1; transform: translateY(0) scale(1); } }

        /* Card Header */
        .pw-card-header {
            background: linear-gradient(135deg, #001a3b 0%, #1e3a8a 100%);
            padding: 36px 32px 28px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .pw-card-header::after {
            content: ''; position: absolute; bottom: -40px; right: -40px;
            width: 140px; height: 140px; border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }

        .pw-lock-icon {
            width: 68px; height: 68px;
            background: rgba(255,255,255,0.15);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            animation: lockBounce 2s ease-in-out infinite;
        }
        @keyframes lockBounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
        .pw-lock-icon svg { width: 32px; height: 32px; color: #fff; fill: none; stroke: #fff; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        .pw-card-header h2 { color: #fff; font-size: 1.2rem; font-weight: 800; margin-bottom: 6px; position: relative; z-index: 1; }
        .pw-card-header p  { color: rgba(255,255,255,0.7); font-size: 0.84rem; position: relative; z-index: 1; }

        .pw-project-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,0.12); border-radius: 8px;
            padding: 5px 14px; font-size: 0.75rem; font-weight: 700;
            color: rgba(255,255,255,0.85); margin-top: 12px;
            backdrop-filter: blur(4px); position: relative; z-index: 1;
        }

        /* Card Body */
        .pw-card-body { padding: 32px; }

        /* Alert */
        .pw-alert {
            background: #fee2e2; border: 1px solid #fecaca; border-radius: 10px;
            padding: 12px 16px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
            font-size: 0.84rem; color: #b91c1c; font-weight: 600;
        }
        .pw-alert svg { width: 18px; height: 18px; flex-shrink: 0; fill: none; stroke: #b91c1c; stroke-width: 2; }

        /* Form */
        .pw-label {
            display: block; font-size: 0.8rem; font-weight: 700; color: #374151;
            text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px;
        }
        .pw-input-wrap { position: relative; margin-bottom: 24px; }
        .pw-input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); }
        .pw-input-icon svg { width: 18px; height: 18px; color: #94a3b8; fill: none; stroke: #94a3b8; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .pw-input {
            width: 100%; border: 2px solid #e8edf5; border-radius: 12px;
            padding: 13px 14px 13px 44px; font-size: 0.9rem; color: #1e293b;
            background: #fafbfd; outline: none; transition: all 0.2s;
            font-family: 'Inter', sans-serif; letter-spacing: 0.08em;
        }
        .pw-input:focus { border-color: #001a3b; background: #fff; box-shadow: 0 0 0 4px rgba(0,26,59,0.08); }

        .pw-toggle-btn {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; padding: 2px;
            color: #94a3b8; transition: color 0.2s;
        }
        .pw-toggle-btn:hover { color: #001a3b; }
        .pw-toggle-btn svg { width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        .pw-submit {
            width: 100%;
            background: linear-gradient(135deg, #001a3b, #1e3a8a);
            color: #fff; border: none; border-radius: 12px;
            padding: 14px; font-size: 0.92rem; font-weight: 800;
            cursor: pointer; letter-spacing: 0.02em;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(0,26,59,0.3);
        }
        .pw-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0,26,59,0.4); }
        .pw-submit:active { transform: translateY(0); }
        .pw-submit svg { width: 18px; height: 18px; fill: none; stroke: #fff; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

        .pw-footer { text-align: center; margin-top: 20px; font-size: 0.76rem; color: #94a3b8; }
        .pw-footer span { display: flex; align-items: center; justify-content: center; gap: 5px; }
        .pw-footer svg { width: 14px; height: 14px; fill: none; stroke: #94a3b8; stroke-width: 2; }
    </style>
</head>
<body>
    <div class="blob-1"></div>
    <div class="blob-2"></div>

    <div class="pw-card">
        <div class="pw-card-header">
            <div class="pw-lock-icon">
                <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
            <h2>Secure Project Report</h2>
            <p>Enter the password to access this report</p>
            <div class="pw-project-badge">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                {{ $project->project_name }}
            </div>
        </div>

        <div class="pw-card-body">
            @if(session('error'))
            <div class="pw-alert">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                {{ session('error') }}
            </div>
            @endif

            <form action="{{ route('project.public.verify', $token) }}" method="POST">
                @csrf
                <label class="pw-label">Password</label>
                <div class="pw-input-wrap">
                    <div class="pw-input-icon">
                        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <input type="password" name="password" id="passwordInput" class="pw-input" placeholder="Enter your password" required autocomplete="current-password">
                    <button type="button" class="pw-toggle-btn" onclick="togglePassword()" id="toggleBtn">
                        <svg id="eyeIcon" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>

                <button type="submit" class="pw-submit">
                    <svg viewBox="0 0 24 24"><polyline points="9 10 4 15 9 20"></polyline><path d="M20 4v7a4 4 0 0 1-4 4H4"></path></svg>
                    Access Report
                </button>
            </form>

            <div class="pw-footer">
                <span>
                    <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    This report is password protected and confidential
                </span>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            var input = document.getElementById('passwordInput');
            var btn   = document.getElementById('toggleBtn');
            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
            } else {
                input.type = 'password';
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            }
        }
    </script>
</body>
</html>
