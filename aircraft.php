<?php
require_once "DB.PHP";
$method = $_SERVER['REQUEST_METHOD'];
try {
    if ($method == "GET") {
        $stmt = $conn->prepare("
            SELECT * FROM aircraft");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        exit;
    }
    if ($method == "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("INSERT INTO aircraft(Model, Capacity, AirlineID, CrewID) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data["Model"],
            $data["Capacity"],
            $data["AirlineID"],
            $data["CrewID"] ?? null ]);
        echo json_encode(["message" => "Aircraft created successfully" ]);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => $e->getMessage()
    ]);
}