<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// 랜덤으로 4개의 식당 선택
$query = "SELECT * FROM restaurants ORDER BY RAND() LIMIT 4";
$result = $conn->query($query);
$restaurants = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>대시보드</title>
    <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=f3021abf2adca41dbeb1847c39822adf&libraries=services"></script>
    <style>
        body, html { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            height: 100%; 
            overflow: hidden;
        }
        .container { 
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }
        h1 { 
            color: #1a73e8; 
            margin-bottom: 20px; 
        }
        .content { 
            display: flex; 
            justify-content: space-between; 
            flex-grow: 1;
            overflow: hidden;
        }
        .restaurants, .map-container { 
            width: 48%; 
            height: 100%;
            overflow-y: auto;
        }
        .restaurant-item { 
            margin-bottom: 10px; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
        }
        .restaurant-item img { 
            max-width: 100%; 
            max-height: 100px; 
            object-fit: cover;
        }
        .buttons { 
            margin-top: 20px; 
            text-align: center; 
        }
        .btn { 
            display: inline-block; 
            padding: 10px 20px; 
            margin: 0 10px; 
            background-color: #1a73e8; 
            color: white; 
            text-decoration: none; 
            border-radius: 4px; 
        }
        .btn:hover { 
            background-color: #1565c0; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>안녕하세요, <?php echo htmlspecialchars($_SESSION['username']); ?>님!</h1>
        <div class="content">
            <div class="restaurants">
                <h2>오늘의 추천 메뉴</h2>
                <?php foreach ($restaurants as $restaurant): ?>
                    <div class="restaurant-item">
                        <h3><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                        <p>위치: <?php echo htmlspecialchars($restaurant['location']); ?></p>
                        <?php if (!empty($restaurant['notes'])): ?>
                        <p>비고: <?php echo htmlspecialchars($restaurant['notes']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($restaurant['menu_image'])): ?>
                            <img src="<?php echo htmlspecialchars($restaurant['menu_image']); ?>" alt="메뉴 이미지">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="map" class="map-container"></div>
        </div>
        <div class="buttons">
            <a href="dashboard.php" class="btn">재추천받기</a>
            <a href="restaurant_form.php" class="btn">식당 등록</a>
            <a href="restaurant_list.php" class="btn">식당 목록</a>
            <a href="logout.php" class="btn">로그아웃</a>
        </div>
    </div>

    <script>
        var mapContainer = document.getElementById('map'),
            mapOption = { 
                center: new kakao.maps.LatLng(33.450701, 126.570667),
                level: 3
            };
        
        var map = new kakao.maps.Map(mapContainer, mapOption);
        var geocoder = new kakao.maps.services.Geocoder();

        <?php foreach ($restaurants as $restaurant): ?>
            geocoder.addressSearch('<?php echo $restaurant['location']; ?>', function(result, status) {
                if (status === kakao.maps.services.Status.OK) {
                    var coords = new kakao.maps.LatLng(result[0].y, result[0].x);
                    var marker = new kakao.maps.Marker({
                        map: map,
                        position: coords
                    });
                    var infowindow = new kakao.maps.InfoWindow({
                        content: '<div style="width:150px;text-align:center;padding:6px 0;"><?php echo $restaurant['name']; ?></div>'
                    });
                    infowindow.open(map, marker);
                    map.setCenter(coords);
                }
            });
        <?php endforeach; ?>
    </script>
</body>
</html>