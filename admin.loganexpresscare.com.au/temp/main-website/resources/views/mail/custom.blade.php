<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f8;
            padding: 20px;
            color: #333;
        }
        .container {
            background-color: #ffffff;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
        .content {
            padding: 20px 0;
        }
        .footer {
            font-size: 12px;
            color: #999;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .btn {
            display: inline-block;
            background-color: #007BFF;
            color: #fff !important;
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>{{ $companyName }}</h2>
    </div>

    <div class="content">
        {!! $content !!}
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} {{ $companyName }}.
    </div>
</div>
</body>
</html>
