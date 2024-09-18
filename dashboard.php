<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

function getRandomRestaurants($conn) {
    $query = "SELECT * FROM restaurants ORDER BY RAND() LIMIT 3";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$restaurants = getRandomRestaurants($conn);

// AJAX 요청 처리
if (isset($_GET['action']) && $_GET['action'] == 'refresh') {
    echo json_encode(getRandomRestaurants($conn));
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>대시보드</title>
    <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=f3021abf2adca41dbeb1847c39822adf&libraries=services"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
            cursor: pointer;
            border: none;
            font-size: 16px;
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
            <div class="restaurants" id="restaurantList">
                <h2>오늘의 추천 메뉴</h2>
                <!-- 식당 목록은 JavaScript로 동적으로 생성됩니다 -->
            </div>
            <div id="map" class="map-container"></div>
        </div>
        <div class="buttons">
            <button id="refreshBtn" class="btn">재추천받기</button>
            <a href="restaurant_form.php" class="btn">식당 등록</a>
            <a href="restaurant_list.php" class="btn">식당 목록</a>
            <a href="logout.php" class="btn">로그아웃</a>
        </div>
    </div>

    <script>
        var map;
        var markers = [];

        function initMap() {
            var mapContainer = document.getElementById('map');
            var mapOption = { 
                center: new kakao.maps.LatLng(33.450701, 126.570667),
                level: 3
            };
            
            map = new kakao.maps.Map(mapContainer, mapOption);
        }

        function clearMarkers() {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
            markers = [];
        }

        function addMarker(restaurant) {
            var geocoder = new kakao.maps.services.Geocoder();
            geocoder.addressSearch(restaurant.location, function(result, status) {
                if (status === kakao.maps.services.Status.OK) {
                    var coords = new kakao.maps.LatLng(result[0].y, result[0].x);
                    var marker = new kakao.maps.Marker({
                        map: map,
                        position: coords
                    });
                    var infowindow = new kakao.maps.InfoWindow({
                        content: '<div style="width:150px;text-align:center;padding:6px 0;">' + restaurant.name + '</div>'
                    });
                    infowindow.open(map, marker);
                    markers.push(marker);
                    map.setCenter(coords);
                }
            });
        }

        function updateRestaurantList(restaurants) {
            var list = $('#restaurantList');
            list.html('<h2>오늘의 추천 메뉴</h2>');
            restaurants.forEach(function(restaurant) {
                var item = $('<div class="restaurant-item">');
                item.append('<h3>' + restaurant.name + '</h3>');
                item.append('<p>위치: ' + restaurant.location + '</p>');
                if (restaurant.notes) {
                    item.append('<p>비고: ' + restaurant.notes + '</p>');
                }
                if (restaurant.menu_image) {
                    item.append('<img src="' + restaurant.menu_image + '" alt="메뉴 이미지">');
                }
                list.append(item);
                addMarker(restaurant);
            });
        }

        $(document).ready(function() {
            initMap();
            updateRestaurantList(<?php echo json_encode($restaurants); ?>);

            $('#refreshBtn').click(function() {
                $.ajax({
                    url: 'dashboard.php',
                    method: 'GET',
                    data: { action: 'refresh' },
                    dataType: 'json',
                    success: function(data) {
                        clearMarkers();
                        updateRestaurantList(data);
                    }
                });
            });
        });
    </script>
</body>
</html>