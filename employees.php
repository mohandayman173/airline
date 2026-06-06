<?php
require_once "DB.PHP";
$method = $_SERVER['REQUEST_METHOD'];
try {
    if ($method == "GET" && isset($_GET['name'])) {
        $name = $_GET['name'];
        $stmt = $conn->prepare("
            SELECT * FROM Employee 
            WHERE EmployeeName = ?");
        $stmt->execute([$name]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode([
                "message" => "No employees here" ]);
        }
        exit;
    }
    if ($method == "POST") {

        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data["EmployeeName"]) || empty($data["AirlineID"])) {
            http_response_code(422);
            echo json_encode([
                "message" => "EmployeeName and AirlineID are required" ]);
            exit;
        }
      $stmt = $conn->prepare("INSERT INTO employee (EmployeeName, BirthDate, Gender, Position, AirlineID) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$data["EmployeeName"],$data["BirthDate"] ?? null,$data["Gender"] ?? null,$data["Position"] ?? null,$data["AirlineID"]
]);
        echo json_encode([
            "message" => "Employee created success"
        ]);
        exit;
    }
}
 catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => $e->getMessage()
    ]);
}