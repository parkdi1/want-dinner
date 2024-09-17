<?php
$servername = "133.186.209.239";
$username = "admin";
$password = "admin";
$dbname = "dinner";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}

// UTF-8 인코딩 설정
$conn->set_charset("utf8mb4");
?>