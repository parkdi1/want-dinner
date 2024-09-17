// login.php
<?php
session_start();
$conn = new mysqli("localhost", "username", "password", "database_name");
$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $password = $conn->real_escape_string($_POST['password']);
    
    $sql = "SELECT * FROM dinner WHERE id = '$user_id' AND pw = '$password'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $_SESSION['user_id'] = $user_id;
        header("Location: index.php");
    } else {
        echo "로그인 실패. 다시 시도해주세요.";
    }
}
$conn->close();
?>