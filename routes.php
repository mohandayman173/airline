<?php
require_once "db.php";
$method = $_SERVER['REQUEST_METHOD'];
try {
    if ($method == "GET") {
        $stmt = $conn->prepare("SELECT * FROM Route");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }
    if ($method == "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        
        $stmt = $conn->prepare("INSERT INTO Route(Origin, Destination, Distance, Classification) VALUES (?, ?, ?, ?) ");
        $stmt->execute([
            $data["Origin"],
            $data["Destination"],
            $data["Distance"],
            $data["Classification"] ?? null
        ]);
        echo json_encode(["message" => "Route created successfully" ]);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}