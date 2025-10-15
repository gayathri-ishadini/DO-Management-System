<?php
// view_all.php

// ================= DATABASE CONNECTION =================
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "education";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ================= SANITIZE FUNCTION =================
function h($str) {
    if (is_array($str)) return '';
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// ================= FETCH ALL EMPLOYEE DATA =================
$sql = "SELECT 
            e.*, 
            GROUP_CONCAT(DISTINCT ea.address SEPARATOR '/ ') AS addresses, 
            GROUP_CONCAT(DISTINCT et.phone_number SEPARATOR '/ ') AS phone_numbers,
            GROUP_CONCAT(DISTINCT ew.whatsapp_number SEPARATOR '/ ') AS whatsapp_numbers,
			
            GROUP_CONCAT(DISTINCT esa.school_name SEPARATOR '/ ') AS school_name,
            GROUP_CONCAT(DISTINCT esa.start_date SEPARATOR '/ ') AS start_date,
            GROUP_CONCAT(DISTINCT esa.end_date SEPARATOR '/ ') AS end_date
        FROM employees e
        LEFT JOIN employeeaddresses ea ON e.file_number = ea.file_number
        LEFT JOIN employeetelephones et ON e.file_number = et.file_number
        LEFT JOIN employeewhatsapp ew ON e.file_number = ew.file_number
        
        LEFT JOIN employeeschoolattachments esa ON e.file_number = esa.file_number
        GROUP BY e.employee_id
        ORDER BY e.employee_id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8" />
    <title>සියලු සේවකයින්</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { background-color: #f9fafc; }
        .card { border-radius: 12px; }
        .table thead th { background-color: #e9ecef; }
        .sticky-top { position: sticky; top: 0; z-index: 1020; }
    </style>
</head>
<body class="p-4">

    <h2 class="mb-4 text-center text-primary fw-bold">සියලු සේවකයින්ගේ විස්තර</h2>

    <div class="mb-4 text-center">
        <a href="do_view_table2.php" class="btn btn-secondary btn-lg">⬅ ප්‍රධාන පිටුවට මාරුවන්න</a>
		
    </div>

    <div class="table-responsive" style="max-height: 700px; overflow-y: auto;">
        <table class="table table-bordered table-striped table-hover align-middle text-center">
            <thead class="sticky-top bg-white">
                <tr>
                    <th>ලිපිගොනු අංකය</th>
                    <th>නම</th>
                    <th>හැඳුනුම්පත්</th>
                    <th>උපන් දිනය</th>
                    <th>ස්ත්‍රී/පුරුෂ</th>
                    <th>ලිපිනය</th>
                    <th>දුරකථන</th>
                    <th>WhatsApp</th>
                    <th>අභ්‍යාසලබී පූණු පත්වීම් දිනය</th>
                    <th>ප්‍රාදේශීය ලේකම් කාර්යාලයෙන් නිදහස් වූ දිනය</th>
                    <th>කලාපයේ වැඩ භාරගත් දිනය</th>
                    <th>කාර්යක්ෂමතා කඩඉම් සමත් දිනය</th>
                    <th>දෙමළ නිදහස් දිනය</th>
                    <th>පත්වීම ස්ථීර වූ දිනය</th>
                    <th>අනියුක්ත පාසල (ආරම්භක / අවසන් දිනය)</th>
                    <th>ක්‍රියාමාර්ග</th>
                </tr>
            </thead>
           <tbody>
          <?php if ($result->num_rows === 0): ?>
                <tr><td colspan="17" class="text-center">දත්ත නොමැත</td></tr>
            <?php else: ?>
			
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= h($row['file_number']) ?></td>
                        <td><?= h($row['employee_name']) ?></td>
                        <td><?= h($row['nic']) ?></td>
                        <td><?= h($row['date_of_birth']) ?></td>
                        <td><?= h($row['gender']) ?></td>
                        <td><?= h($row['addresses'] ?? '') ?></td>
                        <td><?= h($row['phone_numbers'] ?? '') ?></td>
                        <td><?= h($row['whatsapp_numbers'] ?? '') ?></td>
                        <td><?= h($row['date_of_trainee_training_appointment']) ?></td>
                        <td><?= h($row['date_of_release_from_divisional_secretariat']) ?></td>
                        <td><?= h($row['date_of_assuming_duties_in_zone']) ?></td>
                        <td><?= h($row['date_of_passing_efficiency_test']) ?></td>
                        <td><?= h($row['date_of_tamil_release']) ?></td>
                        <td><?= h($row['date_of_appointment_confirmed']) ?></td>
                     
                        <td>
                            <?php
                                if (!empty($row['school_name'])) {
                                    $schools2 = explode('/', $row['school_name']);
                                    $starts = explode('/', $row['start_date']);
                                    $ends = explode('/', $row['end_date']);
                                    $list2 = [];
                                    for($i=0; $i<count($schools2); $i++){
                                        $s = h(trim($schools2[$i]));
                                        $st = h(trim($starts[$i] ?? ''));
                                        $en = h(trim($ends[$i] ?? ''));
                                        $list2[] = "🏫 $s ($st - $en)";
                                    }
                                    echo implode('<br>', $list2);
                                } else {
                                    echo 'තොරතුරු නොමැත';
                                }
                            ?>
                        </td>
                        <td>
                            <a href="update.php?file_number=<?= h($row['file_number']) ?>" class="btn btn-warning btn-sm">UPDATE</a> <br><br>
                            <a href="delete.php?file_number=<?= h($row['file_number']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you want to delete this record?');">DELETE</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
