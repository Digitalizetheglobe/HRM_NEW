<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#5c59e8">
    <title>Offline — Digitalize The Globe</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #f4f6fb;
            color: #1e293b;
        }
        .card {
            width: 90%;
            max-width: 420px;
            background: #fff;
            border-radius: 16px;
            padding: 40px 32px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(92, 89, 232, 0.12);
        }
        .icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: rgba(92, 89, 232, 0.12);
            color: #5c59e8;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        h1 {
            margin: 0 0 8px;
            font-size: 1.45rem;
        }
        p {
            margin: 0 0 24px;
            color: #64748b;
            line-height: 1.5;
        }
        button {
            border: 0;
            background: #5c59e8;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 12px 22px;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover { background: #4a47d1; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="1" y1="1" x2="23" y2="23"></line>
                <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"></path>
                <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"></path>
                <path d="M10.71 5.05A16 16 0 0 1 22.56 9"></path>
                <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"></path>
                <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                <line x1="12" y1="20" x2="12.01" y2="20"></line>
            </svg>
        </div>
        <h1>You are offline</h1>
        <p>Please check your internet connection and try again.</p>
        <button type="button" onclick="window.location.reload()">Try again</button>
    </div>
</body>
</html>
