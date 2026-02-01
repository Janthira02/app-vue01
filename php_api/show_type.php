<?php
include 'condb.php';
header("Content-Type: application/json; charset=UTF-8");

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // ✅ ดึงข้อมูลพนักงานทั้งหมด
    if ($method === "GET") {
        $stmt = $conn->prepare("SELECT * FROM type ORDER BY type_id DESC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $result]);
    }

    // ✅ เพิ่มข้อมูลพนักงาน
    elseif ($method === "POST") {
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
        } else {
            $data = $_POST;
        }

        if (empty($data["type_name"])) {
            echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบ"]);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO type (type_id, type_name) VALUES (:type_id, :type_name)");
        $stmt->bindParam(":type_id", $data["type_id"]);
        $stmt->bindParam(":type_name", $data["type_name"]);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "เพิ่มข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถเพิ่มข้อมูลได้"]);
        }
    }

    // ✅ แก้ไขข้อมูลพนักงาน
    elseif ($method === "PUT") {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["type_id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบค่า type_id"]);
            exit;
        }

        $type_id = intval($data["type_id"]);

        $sql = "UPDATE type
                SET type_name = :type_name
                WHERE type_id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":type_name", $data["type_name"]);
        $stmt->bindParam(":id", $type_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "แก้ไขข้อมูลเรียบร้อย"]); }
         else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถแก้ไขข้อมูลได้"]);
        
    }
    }

    // ✅ ลบข้อมูล
    elseif ($method === "DELETE") {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["type_id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบค่า type_id"]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM type WHERE type_id = :id");
        $stmt->bindParam(":id", $data["type_id"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "ลบข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถลบข้อมูลได้"]);
        }
    }

    else {
        echo json_encode(["success" => false, "message" => "Method ไม่ถูกต้อง"]);
    }
}
   catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }

?>