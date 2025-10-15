<?php
// index.php

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

// ================= VARIABLES =================
$edit_data = null;
$addresses = [];
$telephones = [];
$whatsapp = [];
$sclattach = [];
$esclattach = [];
$searched_value = $_GET['file_number'] ?? '';

// ================= HANDLE REFRESH =================
if (isset($_GET['refresh'])) {
    header("Location: do_view_table2.php");
    exit;
}

// ================= HANDLE SEARCH BY FILE NUMBER =================

  if (!empty($searched_value)) {
    $search = trim($searched_value);

    // Prepare and execute the main employee data search
    $stmt = $conn->prepare("SELECT * FROM employees WHERE file_number = ? OR nic = ?");
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
    $stmt->close();



    if ($edit_data) {
		$file_number = $edit_data['file_number'];
        // Addresses
        $stmt = $conn->prepare("SELECT address FROM employeeaddresses WHERE file_number = ?");
        $stmt->bind_param("s", $file_number);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) $addresses[] = $row['address'];
        $stmt->close();

        // Telephones
        $stmt1 = $conn->prepare("SELECT phone_number FROM employeetelephones WHERE file_number = ?");
        $stmt1->bind_param("s", $file_number);
        $stmt1->execute();
        $res1 = $stmt1->get_result();
        while ($row = $res1->fetch_assoc()) $telephones[] = $row['phone_number'];
        $stmt1->close();

        // WhatsApp
        $stmt2 = $conn->prepare("SELECT whatsapp_number FROM employeewhatsapp WHERE file_number = ?");
        $stmt2->bind_param("s", $file_number);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        while ($row = $res2->fetch_assoc()) $whatsapp[] = $row['whatsapp_number'];
        $stmt2->close();

        // Attachments (school etc.)
       /*
	   $stmt3 = $conn->prepare("SELECT date_of_attachment, school_name, period FROM employeeattachments WHERE file_number = ?");
        $stmt3->bind_param("s", $file_number);
        $stmt3->execute();
        $res3 = $stmt3->get_result();
        while ($row = $res3->fetch_assoc()) {
            $sclattach[] = [
                'date_of_attachment' => $row['date_of_attachment'],
                'school_name' => $row['school_name'],
                'period' => $row['period']
            ];
        }
        $stmt3->close();
*/
        $stmt4 = $conn->prepare("SELECT school_name, start_date, end_date FROM employeeschoolattachments WHERE file_number = ?");
        $stmt4->bind_param("s", $file_number);
        $stmt4->execute();
        $res4 = $stmt4->get_result();
        while ($row = $res4->fetch_assoc()) {
            $esclattach[] = [
                'school_name' => $row['school_name'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date']
            ];
        }
        $stmt4->close();
    }
}

// ================= FETCH TABLE DATA =================
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
$result_all = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8" />
    <title>සේවකයින් කළමනාකරණය</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { background-color: #f9fafc; }
        .card { border-radius: 12px; }
        .info-table td { padding: 8px 12px; vertical-align: middle; }
        .info-table th { background-color: #e9ecef; width: 250px; text-align: left; }
        .highlight-row { background-color: #212529 !important; color: #fff !important; font-weight: bold; }
    </style>
</head>
<body class="p-4">

    <h2 class="mb-4 text-center text-primary fw-bold">සේවකයින් කළමනාකරණය</h2>

    <!-- Search Form + Refresh Button -->
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-5">
            <input type="text" name="file_number" class="form-control form-control-lg"
       placeholder="ලිපිගොනු අංකය හෝ NIC ඇතුළත් කරන්න"
       value="<?= h($searched_value) ?>">

        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-success btn-lg w-100">Search</button>
        </div>
		<!--
        <div class="col-md-2">
            <a href="index.php" class="btn btn-secondary btn-lg w-100">සියල්ල පෙන්වන්න</a>
        </div>
		-->
        <div class="col-md-2">
            <button type="submit" name="refresh" value="1" class="btn btn-primary btn-lg w-100">Refresh</button>
        </div>
    </form>
	
	
	
	
	
	<!-- Employee Detail View -->
    <?php if ($edit_data): ?>
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-info text-white">
                ලිපිගොනු අංකය: <?= h($edit_data['file_number']) ?> සඳහා විස්තර
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>සේවකයාගේ නම</label>
                        <input type="text" class="form-control" readonly value="<?= h($edit_data['employee_name']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label>ජාතික හැඳුනුම්පත් අංකය</label>
                        <input type="text" class="form-control" readonly value="<?= h($edit_data['nic']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>උපන් දිනය</label>
                        <input type="text" class="form-control" readonly value="<?= h($edit_data['date_of_birth']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>ස්ත්‍රී/පුරුෂ භාවය</label>
                        <input type="text" class="form-control" readonly value="<?= h($edit_data['gender']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>ලිපිනය</label>
                      <!--  <input type="text" class="form-control" readonly value="<?= !empty($addresses) ? implode(' / ', array_map('h', $addresses)) : 'ලිපිනයක් නොමැත' ?>"> -->
                    
					  <?= !empty($addresses) ? '<ul class="list-group">' . implode('', array_map(fn($a) => "<li class='list-group-item'>" . h($a) . "</li>", $addresses)) . '</ul>' : '<input type="text" class="form-control" readonly value="ලිපිනයක් නොමැත">' ?>
					
					</div>
                    <div class="col-md-4">
                        <label>දුරකථන අංකය</label>
                        <!-- <input type="text" class="form-control" readonly value="<?= !empty($telephones) ? implode(' / ', array_map('h', $telephones)) : 'දුරකථන අංකය නොමැත' ?>">  -->
						
						<?= !empty($telephones) ? '<ul class="list-group">' . implode('', array_map(fn($t) => "<li class='list-group-item'>" . h($t) . "</li>", $telephones)) . '</ul>' : '<input type="text" class="form-control" readonly value="දුරකථන අංකයක් නොමැත">' ?>
						
                    </div>
                    <div class="col-md-4">
                        <label>Whatsapp අංකය</label>
                       <!-- <input type="text" class="form-control" readonly value="<?= !empty($whatsapp) ? implode(' / ', array_map('h', $whatsapp)) : 'WhatsApp අංකය නොමැත' ?>"> -->
					   
					   <?= !empty($whatsapp) ? '<ul class="list-group">' . implode('', array_map(fn($w) => "<li class='list-group-item'>" . h($w) . "</li>", $whatsapp)) . '</ul>' : '<input type="text" class="form-control" readonly value="Whatsapp අංකයක් නොමැත">' ?>
					   
                    </div>
                    <div class="col-md-4">
                        <label>අභ්‍යාසලබී පූණු පත්වීම් දිනය</label>
                        <input type="text" class="form-control" readonly value="<?= h($edit_data['date_of_trainee_training_appointment']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>ප්‍රාදේශීය ලේකම් කාර්යාලයෙන් නිදහස් වූ දිනය</label>
                        <input type="text" class="form-control" readonly value="<?= h($edit_data['date_of_release_from_divisional_secretariat']) ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label>කලාපයේ වැඩ භාරගත් දිනය</label>
                        <input type="text" class="form-control" readonly value="<?= h($edit_data['date_of_assuming_duties_in_zone']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>කාර්යක්ෂමතා කඩඉම් සමත් දිනය</label>
                        <input type="text" class="form-control" readonly value="<?= h($edit_data['date_of_passing_efficiency_test']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>දෙමළ නිදහස් දිනය</label>
                        <input type="text" class="form-control" readonly value="<?= h($edit_data['date_of_tamil_release']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>පත්වීම ස්ථීර වූ දිනය</label>
                        <input type="text" class="form-control" readonly value="<?= h($edit_data['date_of_appointment_confirmed']) ?>">
                    </div>
                <!--    <div class="col-md-12">
                        <label>අනියුක්ත වූ පාසල් තොරතුරු</label>
                         <?php if (!empty($sclattach)): ?>
                                <ul class="list-group">
                                    <?php foreach ($sclattach as $sa): ?>
                                        <li class="list-group-item">📅 <?= h($sa['date_of_attachment']) ?> | 🏫 <?= h($sa['school_name']) ?> | කාලය: <?= h($sa['period']) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                තොරතුරු නොමැත
                            <?php endif; ?>
                    </div> -->
					<div class="col-md-12">
                        <label>අනියුක්ත වූ පාසල(ආරම්භක / අවසන් දිනය)</label>
                         <?php if (!empty($esclattach)): ?>
                                <ul class="list-group">
                                    <?php foreach ($esclattach as $esa): ?>
                                        <li class="list-group-item">🏫 <?= h($esa['school_name']) ?> | ආරම්භක: <?= h($esa['start_date']) ?> | අවසන්: <?= h($esa['end_date']) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                තොරතුරු නොමැත
                            <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif (isset($_GET['file_number'])): ?>
        <div class="alert alert-danger">මෙම ලිපිගොනු අංකයට අදාල සේවකයෙකු නොමැත.</div>
    <?php endif; ?>

	
	 <a href="do2.php" class="btn btn-success btn-sm me-1">ADD</a>

	<a href="update.php?file_number=<?= h($edit_data['file_number']) ?>" class="btn btn-warning btn-sm me-1">UPDATE</a> 
    <a href="delete.php?file_number=<?= h($edit_data['file_number']) ?>" class="btn btn-danger btn-sm me-1" onclick="return confirm('Are you want to delete this record?');">DELETE</a>
	  <a href="do_profile.php?file_number=<?= h($edit_data['file_number']) ?>" class="btn btn-info btn-sm me-1">View Profile</a>
	 <a href="view_all.php" class="btn btn-secondary btn-sm">VIEW ALL</a>

	
	
	
	
	
	

    <!---Employee Details 
    <?php if ($edit_data): ?>
    <div class="card mb-5 shadow">
        <div class="card-header bg-info text-white fw-bold">
            ලිපිගොනු අංකය: <?= h($edit_data['file_number']) ?> සඳහා සේවක විස්තර
        </div>
        <div class="card-body">
            <table class="table table-bordered info-table">
                <tbody>
                    <tr><th>සේවකයාගේ නම</th><td><?= h($edit_data['employee_name']) ?></td>
                    <tr><th>ජාතික හැඳුනුම්පත් අංකය</th><td><?= h($edit_data['nic']) ?></td></tr>
                    <tr><th>උපන් දිනය</th><td><?= h($edit_data['date_of_birth']) ?></td></tr>
                    <tr><th>ස්ත්‍රී/පුරුෂ භාවය</th><td><?= h($edit_data['gender']) ?></td></tr>
                    <tr><th>ලිපිනය</th><td><?= !empty($addresses) ? implode(' / ', array_map('h', $addresses)) : 'ලිපිනයක් නොමැත' ?></td></tr>
                    <tr><th>දුරකථන අංකය</th><td><?= !empty($telephones) ? implode(' / ', array_map('h', $telephones)) : 'දුරකථන අංකය නොමැත' ?></td></tr>
                    <tr><th>WhatsApp අංකය</th><td><?= !empty($whatsapp) ? implode(' / ', array_map('h', $whatsapp)) : 'WhatsApp අංකය නොමැත' ?></td></tr>
                    <tr><th>අභ්‍යාසලබී පූණු පත්වීම් දිනය</th><td><?= h($edit_data['date_of_trainee_training_appointment']) ?></td></tr>
                    <tr><th>ප්‍රාදේශීය ලේකම් කාර්යාලයෙන් නිදහස් වූ දිනය</th><td><?= h($edit_data['date_of_release_from_divisional_secretariat']) ?></td></tr>
                    <tr><th>කලාපයේ වැඩ භාරගත් දිනය</th><td><?= h($edit_data['date_of_assuming_duties_in_zone']) ?></td></tr>
                    <tr><th>කාර්යක්ෂමතා කඩඉම් සමත් දිනය</th><td><?= h($edit_data['date_of_passing_efficiency_test']) ?></td></tr>
                    <tr><th>දෙමළ නිදහස් දිනය</th><td><?= h($edit_data['date_of_tamil_release']) ?></td></tr>
                    <tr><th>පත්වීම ස්ථීර වූ දිනය</th><td><?= h($edit_data['date_of_appointment_confirmed']) ?></td></tr>

                    

                    <tr><th>අනියුක්ත පාසල (ආරම්භක / අවසන් දිනය)</th>
                        <td>
                            <?php if (!empty($esclattach)): ?>
                                <ul class="list-group">
                                    <?php foreach ($esclattach as $esa): ?>
                                        <li class="list-group-item">🏫 <?= h($esa['school_name']) ?> | ආරම්භක: <?= h($esa['start_date']) ?> | අවසන්: <?= h($esa['end_date']) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                තොරතුරු නොමැත
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php elseif (!empty($searched_file_number)): ?>
        <div class="alert alert-danger">මෙම ලිපිගොනු අංකයට අදාල සේවකයෙකු නොමැත.</div>
    <?php endif; ?>
-->
    <!-- Employee Table -->
    <div class="table-responsive mt-5" style="max-height: 600px; overflow-y: auto;">
        <table class="table table-bordered table-striped table-hover align-middle text-center">
            <thead class="table-light sticky-top bg-white" style="top: 0; z-index: 1020;">
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
            <?php if ($result_all->num_rows === 0): ?>
                <tr><td colspan="10" class="text-center">දත්ත නොමැත</td></tr>
            <?php else: ?>
                <?php while ($row = $result_all->fetch_assoc()):
                    $highlight = ($searched_value && ($row['file_number'] === $searched_value || $row['nic']===$searched_value)) ? 'highlight-row' : '';
                ?>
                    <tr class="<?= $highlight ?>">
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
                                }
                            ?>
                        </td>
						<td>
                    <a href="update.php?file_number=<?= h($row['file_number']) ?>" class="btn btn-warning btn-sm">UPDATE</a> <br> <br>
                    <a href="delete.php?file_number=<?= h($row['file_number']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you want to delete this record?');">DELETE</a>
                </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script src ="js/bootstrap.bundle.min.js"></script>
</body>
</html>
