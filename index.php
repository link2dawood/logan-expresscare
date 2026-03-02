<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logan Express - Maintenance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(-45deg, #1e3c72, #2a5298, #1c92d2, #134e5e);
            background-size: 400% 400%;
            animation: gradientBG 12s ease infinite;
            font-family: 'Segoe UI', sans-serif;
            color: #fff;
            text-align: center;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            max-width: 600px;
            padding: 40px;
        }

        h1 {
            font-size: 48px;
            margin-bottom: 15px;
            letter-spacing: 2px;
        }

        h2 {
            font-weight: 300;
            margin-bottom: 20px;
        }

        p {
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255,255,255,0.3);
            border-top: 5px solid #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .footer {
            margin-top: 40px;
            font-size: 14px;
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Logan Express Care</h1>
        <h2>We're Updating Our Website</h2>
        <p>Our team is working on some improvements.<br>We’ll be back shortly.</p>

        <div class="loader"></div>

        <div class="footer">
            © 2026 Logan Express. All Rights Reserved.
        </div>
    </div>
</body>
</html>