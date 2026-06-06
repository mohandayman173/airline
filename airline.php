<?php
require_once "DB.PHP";
$path = $_SERVER["PATH_INFO"] ?? null;
$method = $_SERVER["REQUEST_METHOD"];
$id = $_GET["id"] ?? null;
try {
    if ($method == "GET" && !$id) {
        $stmt = $conn->prepare("SELECT * FROM airline");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }
    if ($method == "GET" && $id) {
        $stmt = $conn->prepare("SELECT * FROM Airline WHERE AirlineID = ?");
        $stmt->execute([$id]);

        $airline = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($airline) {
            echo json_encode($airline);
        } else {
            http_response_code(404);
            echo json_encode([ "message" => "Airline not found" ]);
        }
        exit;
    }
if($method=="POST"){
     $data = json_decode(file_get_contents("php://input"), true);
     if(!isset($data["airline_name"])){
        http_response_code(400);
        echo json_encode(["message" => "airline_name is requeire please enter the name"]);
        exit;
     }
           $stmt = $conn->prepare("
    INSERT INTO Airline
    (AirlineName, Address, ContactPerson, PhoneNumber, CurrentBalance)
    VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data["airline_name"],
               $data["address"] ??null,
               $data["contact_person"]?? null,
               $data["phone_number"]?? null,
               $data["current_balance"]?? 0
            ]);
             echo json_encode(["message" =>"Airline created success"]);
       exit;           
}
if ($method=="PATCH") {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$id) { http_response_code(400);
        echo json_encode([ "message" => "ID is required" ]);
        exit;
    }
    $stmt = $conn->prepare("UPDATE Airline SET AirlineName = ?, Address = ?, ContactPerson = ?, PhoneNumber = ?, CurrentBalance = ? WHERE AirlineID = ?");
;
    $stmt->execute([
        $data['AirlineName'],
        $data['Address'] ?? null,
        $data['ContactPerson'] ?? null,
        $data['PhoneNumber'] ?? null,
        $data['CurrentBalance'] ?? 0,
        $id ]);
http_response_code(200);
    echo json_encode([
        "message" => "Airline updated successfully" ]);
    exit;
}
if ($method == "DELETE") {
    if (!$id) {
        http_response_code(400);
         echo json_encode(["message" => "ID is required"]);
        exit;
    }
    $stmt = $conn->prepare(
        "DELETE FROM Airline WHERE AirlineID = ?"
    );
    $stmt->execute([$id]);
    http_response_code(200);
    echo json_encode([
        "message" => "Airline deleted successfully"
    ]);
    exit;
}
}
 catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "something wrong  on the server please try again later" => $e->getMessage()
    ]);
}


