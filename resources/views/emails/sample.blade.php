<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông báo từ ThemeStore</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
        }
        .message {
            font-size: 16px;
            color: #333;
        }
        .footer {
            margin-top: 30px;
            font-size: 13px;
            color: #999;
            text-align: center;
        }
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: #fff;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎨 Theme For Student - Theme cho sinh viên</h1>
        </div>

        <div class="message">
            <p>{{ $data['title']}}</p>
            <p>{{ $data['message'] }}</p>

            {{-- <a href="{{ $data['link'] ?? '#' }}" class="btn">Khám phá ngay</a> --}}
        </div>

        <div class="footer">
            © {{ date('Y') }} Theme For Student - Theme cho sinh viên. Giao diện đẹp - Dễ dùng - Tiết kiệm thời gian.
        </div>
    </div>
</body>
</html>
