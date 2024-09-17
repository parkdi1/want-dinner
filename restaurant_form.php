<?php
session_start();
require_once 'db_connect.php';

// 오류 표시 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 폼이 제출되었을 때 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $location = $conn->real_escape_string($_POST['location']);
    $notes = $conn->real_escape_string($_POST['notes']);
    
    $menu_image = "";
    if (isset($_FILES['menu_image']) && $_FILES['menu_image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["menu_image"]["name"]);
        
        // 파일 업로드 시도
        if (move_uploaded_file($_FILES["menu_image"]["tmp_name"], $target_file)) {
            $menu_image = $target_file;
            $message = "파일 " . basename($_FILES["menu_image"]["name"]) . "이(가) 업로드되었습니다.";
        } else {
            $error = "파일 업로드 중 오류가 발생했습니다. 오류 코드: " . $_FILES["menu_image"]["error"];
        }
    } else if ($_FILES['menu_image']['error'] != 4) { // 4는 파일이 선택되지 않은 경우
        $error = "파일 업로드 오류: " . $_FILES["menu_image"]["error"];
    }
    
    if (!isset($error)) {
        $sql = "INSERT INTO restaurants (name, location, menu_image, notes) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $location, $menu_image, $notes);
        
        if ($stmt->execute()) {
            $message = "식당이 성공적으로 등록되었습니다. " . (isset($message) ? $message : "");
        } else {
            $error = "데이터베이스 오류: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>식당 등록</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f1f1f1;
            color: #202124;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #1a73e8;
            font-weight: 500;
            margin-bottom: 30px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 16px;
            font-weight: 500;
            color: #5f6368;
        }
        input[type="text"], textarea {
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #dadce0;
            border-radius: 4px;
            font-size: 16px;
        }
        input[type="file"] {
            margin-top: 8px;
        }
        input[type="submit"] {
            background-color: #1a73e8;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 24px;
            font-size: 16px;
            font-weight: 500;
        }
        input[type="submit"]:hover {
            background-color: #1765cc;
        }
        .message {
            color: #0f9d58;
            text-align: center;
            margin-top: 20px;
            font-weight: 500;
        }
        .error {
            color: #d93025;
            text-align: center;
            margin-top: 20px;
            font-weight: 500;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
        }
        .button {
            background-color: #fff;
            color: #1a73e8;
            border: 1px solid #1a73e8;
            padding: 10px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #f1f8ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>식당 등록</h2>
        <?php
        if (isset($message)) {
            echo "<p class='message'>$message</p>";
        }
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <label for="name">식당 이름</label>
            <input type="text" id="name" name="name" required>
            
            <label for="location">위치</label>
            <input type="text" id="location" name="location" required>
            
            <label for="menu_image">메뉴 사진</label>
            <input type="file" id="menu_image" name="menu_image">
            
            <label for="notes">비고</label>
            <textarea id="notes" name="notes" rows="4"></textarea>
            
            <input type="submit" value="등록하기">
        </form>
        <div class="button-container">
            <a href="dashboard.php" class="button">대시보드</a>
            <a href="restaurant_list.php" class="button">식당 목록</a>
        </div>
    </div>
</body>
</html>