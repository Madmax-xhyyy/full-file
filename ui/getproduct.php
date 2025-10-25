<?php
include_once 'connectdb.php';

header('Content-Type: application/json; charset=utf-8');

// Validate id
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product id']);
    exit;
}

try {
    // Enable PDO exceptions for debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch product info (no servicetype/additionalfee here anymore)
    $stmt = $pdo->prepare('
        SELECT pid, product, category, saleprice, stock, brand, expirydate, description, image 
        FROM tbl_product 
        WHERE pid = :id 
        LIMIT 1
    ');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Add safe defaults for service and fee (so frontend won’t crash)
        $row['servicetype'] = 'Pick up'; // Default service
        $row['additionalfee'] = 0;       // Default fee

        echo json_encode($row);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
?>
