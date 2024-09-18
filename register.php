<?php
require_once 'db_connect.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    
    // 중복 ID 체크
    $check_sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows > 0) {
        $error_message = "이미 사용 중인 아이디입니다.";
    } else {
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<script>
                alert('회원가입 성공!');
                window.location.href='index.php';
            </script>";
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입</title>
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
        h2 {
            color: #202124;
            font-size: 24px;
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <h2>회원가입</h2>
        <?php
        if (!empty($error_message)) {
            echo "<p class='error'>$error_message</p>";
        }
        ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="text" name="username" placeholder="아이디" required>
            <input type="password" name="password" placeholder="비밀번호" required>
            <input type="submit" value="가입하기">
        </form>
    </div>
    <script>
    <?php
    if (!empty($error_message)) {
        echo "alert('$error_message');";
    }
    ?>
    </script>
</body>
</html>