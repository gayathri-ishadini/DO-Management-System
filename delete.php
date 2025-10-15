<?php
// delete.php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "education";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$file_number = $_GET['file_number'] ?? '';

if ($file_number) {
    // Delete from all related tables first to maintain foreign key constraints
    $tables = [
        'employeeaddresses',
        'employeetelephones',
        'employeewhatsapp',
        'employeeattachments',
        'employeeschoolattachments',
        'employees'
    ];

    foreach ($tables as $table) {
        $stmt = $conn->prepare("DELETE FROM $table WHERE file_number = ?");
        $stmt->bind_param("s", $file_number);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: do_view_table2.php");
    exit;
} else {
    echo "ලිපිගොනු අංකය ලබා දිය නොහැක.";
}
?>
