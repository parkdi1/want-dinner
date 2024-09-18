<?php
require_once 'db_connect.php';

// 사용자 삭제 기능
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// 모든 사용자 정보 가져오기
$result = $conn->query("SELECT id, username, password FROM users");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 페이지</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .delete-btn {
            background-color: #ff4444;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }
        .delete-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>관리자 페이지  flag5: 사과</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>사용자명</th>
                <th>비밀번호</th>
                <th>작업</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['password']); ?></td>
                    <td>
                        <a href="?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('정말로 이 사용자를 삭제하시겠습니까?');">삭제</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
        // 경고 메시지
        alert("주의: 이 페이지는 보안되지 않은 관리자 페이지입니다. 실제 환경에서는 절대 사용하지 마세요!");
    </script>
</body>
</html>

<?php
$conn->close();
?>