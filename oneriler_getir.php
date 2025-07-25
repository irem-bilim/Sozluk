<?php
header('Content-Type: application/json; charset=utf8mb4');
mb_internal_encoding("UTF-8");
error_reporting(E_ALL);
ini_set('display_errors',1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sozluk";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Veritabanı bağlantısı başarısız: " . $conn->connect_error]);
    exit();
}
$conn->set_charset("utf8mb4");
$conn->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_turkish_ci'");
$conn->query("SET collation_connection = 'utf8mb4_turkish_ci'");

$kelimeParcasi = isset($_GET['kelime']) ? $_GET['kelime'] : '';//kullanıcının arama kutusuna yazdığı kelime parçasını alıyor
//$kelimeParcasi = mb_strtoupper($kelimeParcasi, 'UTF-8'); // Öneriler için de büyük harfe çevir
$oneriler = [];

if (!empty($kelimeParcasi)) {
    // Sadece kelime başlangıcına göre arayabiliriz veya kelime içinde geçenleri de gösterebiliriz
    // 'name LIKE ?' -> kelimenin o parça ile başlaması için: $searchTerm = $kelimeParcasi . "%";
    $stmt = $conn->prepare("SELECT name FROM dictionary WHERE name LIKE ? COLLATE utf8mb4_turkish_ci LIMIT 10");
    $searchTerm = $kelimeParcasi . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $oneriler[] = $row['name']; 
        }
    }
    $stmt->close();
}

echo json_encode($oneriler);
$conn->close();
?>