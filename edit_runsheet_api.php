<?php
require 'config/database.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input) {
        $oldNumber = mysqli_real_escape_string($conn, $input['oldNumber'] ?? '');
        $oldDate = mysqli_real_escape_string($conn, $input['oldDate'] ?? '');
        $newNumber = mysqli_real_escape_string($conn, $input['newNumber'] ?? '');
        $newDate = mysqli_real_escape_string($conn, $input['newDate'] ?? '');

        // Update runsheet details for all matching items
        $sqlUpdate = "UPDATE invoice_items SET runsheet_number=?, runsheet_date=? WHERE runsheet_number=? AND runsheet_date=?";
        $stmt = $conn->prepare($sqlUpdate);
        $stmt->bind_param("ssss", $newNumber, $newDate, $oldNumber, $oldDate);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Runsheet updated successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)];
        }
    }
}

echo json_encode($response);
mysqli_close($conn);
?>