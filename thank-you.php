<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank You | Logan Express Care</title>
    <meta http-equiv="refresh" content="5;url=single-page.php">
    <style>
        :root {
            --navy: #253873;
            --gold: #E2A76F;
            --bg: #f7f9fc;
            --surface: #ffffff;
            --text: #1a1f36;
            --muted: #4a5568;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #eef2f9 100%);
            color: var(--text);
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 620px;
            background: var(--surface);
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(37, 56, 115, 0.12);
            padding: 40px 28px;
            text-align: center;
        }

        h1 {
            margin: 0 0 12px;
            color: var(--navy);
            font-size: 2rem;
            line-height: 1.2;
        }

        p {
            margin: 0 0 14px;
            color: var(--muted);
            line-height: 1.7;
        }

        .countdown {
            display: inline-block;
            margin-top: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #f5e6d6;
            color: var(--navy);
            font-weight: 600;
        }

        .link {
            display: inline-block;
            margin-top: 18px;
            color: var(--gold);
            font-weight: 600;
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Thank you for your request</h1>
        <p>Your consultation request was submitted successfully.</p>
        <p>We will redirect you back shortly.</p>
        <div class="countdown">Redirecting in <span id="seconds">5</span> seconds</div>
        <br>
        <a class="link" href="single-page.php">Go back now</a>
    </main>

    <script>
        let seconds = 5;
        const secondsEl = document.getElementById('seconds');

        const timer = setInterval(() => {
            seconds -= 1;
            secondsEl.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(timer);
                window.location.href = 'index-old.php';
            }
        }, 1000);
    </script>
</body>
</html>
