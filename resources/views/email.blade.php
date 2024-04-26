<!-- resources/views/emails/reply.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Reply</title>
    <style>
        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
            margin-bottom: 20px; /* Khoảng cách giữa hình ảnh và nội dung */
        }
        p {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Email Reply</h1>
        </div>
        <div class="content">
            <p>{{ $subject }}</p>
            <p>{{ $body }}</p>
            <img src="https://tse2.mm.bing.net/th?id=OIP.TjdCpRJlenzhaPq3Xyog1gHaEc&pid=Api&P=0&h=180" alt="">
        </div>
        <div class="footer">
            <p>Sincerely,</p>
            <p>BitStorm team</p>
        </div>
    </div>
</body>
</html>
