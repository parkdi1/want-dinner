<?php
session_start();
require_once 'db_connect.php';

// 로그인 확인 및 사용자 이름 가져오기
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
$username = $_SESSION['username'];

// 삭제 기능
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM restaurants WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// 식당 목록 가져오기
$result = $conn->query("SELECT * FROM restaurants");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>식당 목록</title>
    <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=f3021abf2adca41dbeb1847c39822adf&libraries=services"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f1f1f1;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1a73e8;
            margin-bottom: 10px;
        }
        .sub-title {
            color: #5f6368;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-right: 10px;
            background-color: #1a73e8;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #1765cc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: top;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #5f6368;
        }
        .map {
            width: 300px;
            height: 200px;
        }
        .delete-btn {
            display: inline-block;
            background-color: #ea4335;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .delete-btn:hover {
            background-color: #d33828;
        }
        .restaurant-name {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .restaurant-image {
            width: 200px;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>식당 목록</h1>
        <p class="sub-title">안녕하세요, <?php echo htmlspecialchars($username); ?>님!</p>
        <a href="dashboard.php" class="btn">대시보드</a>
        <a href="restaurant_form.php" class="btn">새 식당 등록</a>
        <a href="index.php" class="btn">로그아웃</a>
        
        <table>
            <thead>
                <tr>
                    <th>식당 이름</th>
                    <th>위치</th>
                    <th>메뉴 사진</th>
                    <th>지도</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="restaurant-name"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td>
                            <?php if (!empty($row['menu_image'])): ?>
                                <img src="<?php echo htmlspecialchars($row['menu_image']); ?>" alt="메뉴 이미지" class="restaurant-image">
                            <?php else: ?>
                                <p>이미지 없음</p>
                            <?php endif; ?>
                        </td>
                        <td><div id="map_<?php echo $row['id']; ?>" class="map"></div></td>
                        <td><a href="?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('정말로 삭제하시겠습니까?');">삭제</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
    <?php 
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()): 
    ?>
    (function() {
        var mapContainer = document.getElementById('map_<?php echo $row['id']; ?>'),
            mapOption = {
                center: new kakao.maps.LatLng(33.450701, 126.570667),
                level: 3
            };

        var map = new kakao.maps.Map(mapContainer, mapOption);
        var geocoder = new kakao.maps.services.Geocoder();

        geocoder.addressSearch('<?php echo $row['location']; ?>', function(result, status) {
            if (status === kakao.maps.services.Status.OK) {
                var coords = new kakao.maps.LatLng(result[0].y, result[0].x);

                var marker = new kakao.maps.Marker({
                    map: map,
                    position: coords
                });

                var infowindow = new kakao.maps.InfoWindow({
                    content: '<div style="width:150px;text-align:center;padding:6px 0;"><?php echo htmlspecialchars($row['name']); ?></div>'
                });
                infowindow.open(map, marker);

                map.setCenter(coords);
            }
        });
    })();
    <?php endwhile; ?>
    </script>
</body>
</html>
<?php
$conn->close();
?>