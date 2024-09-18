<?php
session_start();

// 이미 로그인한 사용자는 대시보드로 리다이렉트
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f1f1f1;
        }
        .container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h1, h2 {
            color: #202124;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 28px;
        }
        h2 {
            font-size: 18px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #dadce0;
            border-radius: 4px;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #1a73e8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
        }
        input[type="submit"]:hover {
            background-color: #287ae6;
        }
        .error {
            color: #d93025;
            font-size: 14px;
            margin-top: 10px;
        }
        a {
            color: #1a73e8;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        p {
            margin-top: 20px;
            font-size: 14px;
            text-align: center;
        }
        #current-time {
            text-align: center;
            font-size: 14px;
            color: #5f6368;
            margin-top: 20px;
        }
        #debug-info {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
        }
        #debug-info pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>저녁 뭐먹지?</h1>
        <h2>테스트페이지 입니다 실제 ID/PW을 넣지 마세요</h2>
        <h2>로그인</h2>
        <form id="login-form">
            <input type="text" name="username" placeholder="아이디" required>
            <input type="password" name="password" placeholder="비밀번호" required>
            <input type="submit" value="로그인">
        </form>
        <p>계정이 없으신가요? <a href="register.php">회원가입</a></p>
        <div id="current-time"></div>
        <div id="debug-info"></div>
    </div>

    <script>
        function updateTime() {
            const now = new Date();
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric', 
                weekday: 'long',
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit', 
                hour12: false 
            };
            const formattedTime = now.toLocaleString('ko-KR', options);
            document.getElementById('current-time').textContent = formattedTime;
        }

        updateTime();
        setInterval(updateTime, 1000);

        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            fetch('login_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('debug-info').innerHTML = '<pre>' + data.debug + '</pre>';
                if (data.success) {
                    alert(data.message);
                    window.location.href = 'dashboard.php';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>