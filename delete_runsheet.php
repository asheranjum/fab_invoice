<?php
require 'config/database.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input) {
        $runsheetNumber = mysqli_real_escape_string($conn, $input['runsheet_number'] ?? '');
        $runsheetDate = mysqli_real_escape_string($conn, $input['runsheet_date'] ?? '');

        if ($runsheetNumber && $runsheetDate) {
            $sqlDelete = "DELETE FROM invoice_items WHERE runsheet_number=? AND runsheet_date=?";
            $stmt = $conn->prepare($sqlDelete);
            $stmt->bind_param("ss", $runsheetNumber, $runsheetDate);

            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Runsheet and linked items deleted successfully'];
            } else {
                $response = ['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)];
            }
        } else {
            $response = ['success' => false, 'message' => 'Missing runsheet number or date'];
        }
    }
}

echo json_encode($response);
mysqli_close($conn);
?>