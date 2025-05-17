<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang thông báo</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            position: relative;
            min-height: 100vh;
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .alert {
            font-size: 16px;
        }
        .container {
            margin-top: 100px;
        }

        /* Hình ảnh mờ ở dưới */
        .background-image {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 200px; /* Chiều cao của hình ảnh */
            background-image: url('https://via.placeholder.com/1500x600'); /* Thay thế với link hình ảnh bạn muốn */
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            opacity: 0.4; /* Điều chỉnh độ mờ của hình ảnh */
        }

        /* Đảm bảo nội dung không bị che khuất */
        .content-wrapper {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>

<div class="background-image"></div> <!-- Hình ảnh mờ dưới cùng -->

<div class="container content-wrapper">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header text-center">
                    <h4>Thông báo kích hoạt mật khẩu</h4>
                </div>
                <div class="card-body">
                    @if($status == 'success')
                        <div class="alert alert-success" role="alert">
                            <strong>Thành công!</strong> Mật khẩu của bạn đã được kích hoạt thành công.
                        </div>
                    @elseif($status == 'failed')
                        <div class="alert alert-danger" role="alert">
                            <strong>Thất bại!</strong> Có lỗi xảy ra, không thể kích hoạt mật khẩu.
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <strong>Thông tin:</strong> Trạng thái mật khẩu chưa xác định.
                        </div>
                    @endif
                    {{-- <div class="text-center">
                        <a href="/" class="btn btn-primary">Quay lại trang chủ</a>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
