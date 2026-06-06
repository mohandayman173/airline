<?php
require_once "DB.PHP";
$method = $_SERVER["REQUEST_METHOD"];
if ($method == "GET" && !isset($_GET["transaction"])) {
    $stmt = $conn->prepare("SELECT* FROM Transaction ");
    $stmt->execute();
    echo json_encode(
        $stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}
if ($method == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $conn->beginTransaction();
    $stmt = $conn->prepare("SELECT CurrentBalance FROM Airline WHERE AirlineID = ?");
    $stmt->execute([ $data["AirlineID"]]);
    $airline = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$airline) {
        $conn->rollBack();
        http_response_code(404);
        echo json_encode([
            "message" => "Airline not found"
        ]);

        exit;
    }
    $balance = $airline["CurrentBalance"];
    if ($data["TransactionType"] == "Sell") {
        $newBalance = $balance + $data["Amount"];
    } else {
        $newBalance = $balance - $data["Amount"];
    }
    $stmt = $conn->prepare("INSERT INTO Transaction( TransactionType,Amount,Description,TransactionDate,AirlineID) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $data["TransactionType"],
        $data["Amount"],
        $data["Description"] ?? null,
        date("Y-m-d"),
        $data["AirlineID"]]);
    $stmt = $conn->prepare(" UPDATE Airline SET CurrentBalance = ? WHERE AirlineID = ?");
    $stmt->execute([
        $newBalance,
        $data["AirlineID"]]);
    $conn->commit();
    echo json_encode([
        "message" => "Transaction completed successfully",
        "NewBalance" => $newBalance
    ]);
    exit;
}