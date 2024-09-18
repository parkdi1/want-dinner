<?php
session_start();
require_once 'db_connect.php';

$response = ['success' => false, 'message' => '', 'debug' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    
    $response['debug'] .= "실행된 쿼리: " . $sql . "\n";
    
    try {
        $result = $conn->query($sql);
        
        if ($result) {
            $response['debug'] .= "쿼리 실행 성공. 반환된 행 수: " . $result->num_rows . "\n";
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION['username'] = $row['username'];
                $response['success'] = true;
                $response['message'] = "로그인 성공!";
                
                $response['debug'] .= "로그인한 사용자: " . $row['username'] . "\n";
                
                // 모든 반환된 행의 정보를 디버그에 추가
                $result->data_seek(0); // 결과 포인터를 처음으로 되돌림
                while ($row = $result->fetch_assoc()) {
                    $response['debug'] .= "사용자 ID: " . $row['id'] . ", 사용자명: " . $row['username'] . "\n";
                }
            } else {
                $response['message'] = "로그인 실패. 일치하는 사용자가 없습니다.";
            }
        } else {
            $response['message'] = "쿼리 실행 실패";
            $response['debug'] .= "SQL 오류: " . $conn->error . "\n";
        }
    } catch (mysqli_sql_exception $e) {
        $response['message'] = "SQL 오류: " . $e->getMessage();
        $response['debug'] .= "예외 발생: " . $e->getMessage() . "\n";
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>