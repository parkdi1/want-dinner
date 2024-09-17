// register.php
<?php
$conn = new mysqli("localhost", "username", "password", "database_name");
$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $password = $conn->real_escape_string($_POST['password']);
    
    $sql = "INSERT INTO dinner (id, pw) VALUES ('$user_id', '$password')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('회원가입 성공!'); window.location.href='index.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
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
            font-family: Arial, sans-serif;
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
        }
        input[type="submit"]:hover {
            background-color: #287ae6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>회원가입</h2>
        <form action="register.php" method="post">
            <input type="text" name="user_id" placeholder="아이디" required>
            <input type="password" name="password" placeholder="비밀번호" required>
            <input type="submit" value="가입하기">
        </form>
    </div>
</body>
</html>