<?php
// ================= DATABASE CONNECTION =================
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "education";

$conn = new mysqli($host, $user, $pass, $dbname);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$file_number = $_GET['file_number'] ?? '';
$employee = null;
$addresses = [];
$phones = [];
$whatsapps = [];

$school_attachments = [];

if ($file_number) {
    // Get employee data
    $stmt = $conn->prepare("SELECT * FROM employees WHERE file_number = ?");
    $stmt->bind_param("s", $file_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();
    $stmt->close();

    if ($employee) {
        // Get addresses
        $stmt = $conn->prepare("SELECT address FROM employeeaddresses WHERE file_number = ?");
        $stmt->bind_param("s", $file_number);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) $addresses[] = $r['address'];
        $stmt->close();

        // Get phones
        $stmt = $conn->prepare("SELECT phone_number FROM employeetelephones WHERE file_number = ?");
        $stmt->bind_param("s", $file_number);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) $phones[] = $r['phone_number'];
        $stmt->close();

        // Get whatsapps
        $stmt = $conn->prepare("SELECT whatsapp_number FROM employeewhatsapp WHERE file_number = ?");
        $stmt->bind_param("s", $file_number);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) $whatsapps[] = $r['whatsapp_number'];
        $stmt->close();

        

        // Get school attachments history
        $stmt = $conn->prepare("SELECT school_name, start_date, end_date FROM employeeschoolattachments WHERE file_number = ?");
        $stmt->bind_param("s", $file_number);
        $stmt->execute();
        $res = $stmt->get_result();
        $school_attachments = [];
        while ($row = $res->fetch_assoc()) {
            $school_attachments[] = $row;
        }
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic employee info
    $name = $_POST['employee_name'];
    $nic = $_POST['nic'];
    $dob = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $addresses = $_POST['address'] ?? [];
    $phones = $_POST['phone_number'] ?? [];
    $whatsapps = $_POST['whatsapp_number'] ?? [];

    $date_trainee = $_POST['date_of_trainee_training_appointment'];
    $date_release = $_POST['date_of_release_from_divisional_secretariat'];
    $date_zone = $_POST['date_of_assuming_duties_in_zone'];
    $date_efficiency = $_POST['date_of_passing_efficiency_test'];
    $date_tamil = $_POST['date_of_tamil_release'];
    $date_confirmed = $_POST['date_of_appointment_confirmed'];

   

    // School attachment history
    $school_names = $_POST['school_name'] ?? [];
    $school_start_dates = $_POST['school_start_date'] ?? [];
    $school_end_dates = $_POST['school_end_date'] ?? [];

    // Update employees table
    $stmt = $conn->prepare("UPDATE employees SET employee_name=?, nic=?, date_of_birth=?, gender=?, date_of_trainee_training_appointment=?, date_of_release_from_divisional_secretariat=?, date_of_assuming_duties_in_zone=?, date_of_passing_efficiency_test=?, date_of_tamil_release=?, date_of_appointment_confirmed=? WHERE file_number=?");
    $stmt->bind_param("sssssssssss", $name, $nic, $dob, $gender, $date_trainee, $date_release, $date_zone, $date_efficiency, $date_tamil, $date_confirmed, $file_number);
    $stmt->execute();
    $stmt->close();

    // Clear old related data
    $conn->query("DELETE FROM employeeaddresses WHERE file_number = '$file_number'");
    $conn->query("DELETE FROM employeetelephones WHERE file_number = '$file_number'");
    $conn->query("DELETE FROM employeewhatsapp WHERE file_number = '$file_number'");
   
    $conn->query("DELETE FROM employeeschoolattachments WHERE file_number = '$file_number'");

    // Insert new addresses
    $stmtA = $conn->prepare("INSERT INTO employeeaddresses (file_number, address) VALUES (?, ?)");
    foreach ($addresses as $a) {
        $a = trim($a);
        if ($a !== '') {
            $stmtA->bind_param("ss", $file_number, $a);
            $stmtA->execute();
        }
    }
    $stmtA->close();

    // Insert new phones
    $stmtP = $conn->prepare("INSERT INTO employeetelephones (file_number, phone_number) VALUES (?, ?)");
    foreach ($phones as $p) {
        $p = trim($p);
        if ($p !== '') {
            $stmtP->bind_param("ss", $file_number, $p);
            $stmtP->execute();
        }
    }
    $stmtP->close();

    // Insert new whatsapps
    $stmtW = $conn->prepare("INSERT INTO employeewhatsapp (file_number, whatsapp_number) VALUES (?, ?)");
    foreach ($whatsapps as $w) {
        $w = trim($w);
        if ($w !== '') {
            $stmtW->bind_param("ss", $file_number, $w);
            $stmtW->execute();
        }
    }
    $stmtW->close();

    

    // Insert school attachment history
    $stmt = $conn->prepare("INSERT INTO employeeschoolattachments (file_number, school_name, start_date, end_date) VALUES (?, ?, ?, ?)");
    for ($i = 0; $i < count($school_names); $i++) {
        $sname = trim($school_names[$i]);
        $sstart = $school_start_dates[$i] ?? '';
        $send = $school_end_dates[$i] ?? '';
        if ($sname !== '') {
            $stmt->bind_param("ssss", $file_number, $sname, $sstart, $send);
            $stmt->execute();
        }
    }
    $stmt->close();

    header("Location: do_view_table2.php?file_number=" . urlencode($file_number));
    exit;
}
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <title>සේවකයාගේ විස්තර යාවත්කාලීන කිරීම</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>.multi-input input { margin-bottom: 8px; }</style>
</head>
<body class="p-4 bg-light">
<div class="container">
    <h2 class="mb-4 text-primary text-center fw-bold">සේවකයාගේ විස්තර යාවත්කාලීන කිරීම</h2>
    <?php if ($employee): ?>
    <form method="post">
        <div class="row g-3">
            <!-- Basic Fields -->
            <div class="col-md-6"><label>සේවක නම</label><input type="text" name="employee_name" class="form-control" value="<?= h($employee['employee_name']) ?>" required></div>
            <div class="col-md-6"><label>ජා. හැ. අංකය</label><input type="text" name="nic" class="form-control" value="<?= h($employee['nic']) ?>" required></div>
            <div class="col-md-4"><label>උපන් දිනය</label><input type="date" name="date_of_birth" class="form-control" value="<?= h($employee['date_of_birth']) ?>"></div>
            <div class="col-md-4"><label>ස්ත්‍රී/පුරුෂ භාවය</label>
                <select name="gender" class="form-control">
                    <option value="පුරුෂ" <?= $employee['gender'] === 'පුරුෂ' ? 'selected' : '' ?>>පුරුෂ</option>
                    <option value="ස්ත්‍රී" <?= $employee['gender'] === 'ස්ත්‍රී' ? 'selected' : '' ?>>ස්ත්‍රී</option>
                </select>
            </div>

            <!-- Multi Field Inputs (address, phones, whatsapp) -->
            <div class="col-md-4 multi-input"><label>ලිපිනය(න්)</label><div id="addressFields"><?php foreach ($addresses ?: [''] as $a): ?><input type="text" name="address[]" class="form-control" value="<?= h($a) ?>"><?php endforeach; ?></div>
                <button type="button" class="btn btn-sm btn-outline-info mt-2" onclick="addField('addressFields','address[]')">+ ලිපිනයක්</button></div>

            <div class="col-md-4 multi-input"><label>දුරකථන</label><div id="phoneFields"><?php foreach ($phones ?: [''] as $p): ?><input type="text" name="phone_number[]" class="form-control" value="<?= h($p) ?>"><?php endforeach; ?></div>
                <button type="button" class="btn btn-sm btn-outline-info mt-2" onclick="addField('phoneFields','phone_number[]')">+ අංකයක්</button></div>

            <div class="col-md-4 multi-input"><label>WhatsApp</label><div id="whatsappFields"><?php foreach ($whatsapps ?: [''] as $w): ?><input type="text" name="whatsapp_number[]" class="form-control" value="<?= h($w) ?>"><?php endforeach; ?></div>
                <button type="button" class="btn btn-sm btn-outline-info mt-2" onclick="addField('whatsappFields','whatsapp_number[]')">+ WhatsApp</button></div>

            <!-- Date Fields -->
            <div class="col-md-4"><label>අභ්‍යාසලාභී පත්වීම් දිනය</label><input type="date" name="date_of_trainee_training_appointment" class="form-control" value="<?= h($employee['date_of_trainee_training_appointment']) ?>"></div>
            <div class="col-md-4"><label>ප්‍රා. ලේ. කාර්යාලයෙන් නිදහස් වූ දිනය</label><input type="date" name="date_of_release_from_divisional_secretariat" class="form-control" value="<?= h($employee['date_of_release_from_divisional_secretariat']) ?>"></div>
            <div class="col-md-4"><label>කලාපයේ වැඩ භාර දිනය</label><input type="date" name="date_of_assuming_duties_in_zone" class="form-control" value="<?= h($employee['date_of_assuming_duties_in_zone']) ?>"></div>
            <div class="col-md-4"><label>කාර්යක්ෂමතා සමත් දිනය</label><input type="date" name="date_of_passing_efficiency_test" class="form-control" value="<?= h($employee['date_of_passing_efficiency_test']) ?>"></div>
            <div class="col-md-4"><label>දෙමළ නිදහස් දිනය</label><input type="date" name="date_of_tamil_release" class="form-control" value="<?= h($employee['date_of_tamil_release']) ?>"></div>
            <div class="col-md-4"><label>පත්වීම ස්ථීර වූ දිනය</label><input type="date" name="date_of_appointment_confirmed" class="form-control" value="<?= h($employee['date_of_appointment_confirmed']) ?>"></div>

           

            <!-- School Attachment History -->
            <div class="col-12 mt-4"><h5>අනියුක්ත වූ පාසල(ආරම්භක / අවසන් දිනය)</h5></div>
            <div class="col-md-4 multi-input" id="schoolHistoryFields">
                <?php
                if ($school_attachments) {
                    foreach ($school_attachments as $sch) {
                        ?>
                        <input type="text" name="school_name[]" placeholder="පාසලේ නම" class="form-control mb-2" value="<?= h($sch['school_name']) ?>">
                        <input type="date" name="school_start_date[]" class="form-control mb-2" value="<?= h($sch['start_date']) ?>">
                        <input type="date" name="school_end_date[]" class="form-control mb-4" value="<?= h($sch['end_date']) ?>">
                        <?php
                    }
                } else {
                    // show empty fields for adding
                    for ($i=0; $i<2; $i++) {
                        ?>
                        <input type="text" name="school_name[]" placeholder="පාසලේ නම" class="form-control mb-2">
                        <input type="date" name="school_start_date[]" class="form-control mb-2">
                        <input type="date" name="school_end_date[]" class="form-control mb-4">
                        <?php
                    }
                }
                ?>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addSchoolHistoryFields()">+  එක් කරන්න</button>
            </div>

        </div>

        <div class="mt-4">
             <button type="submit" class="btn btn-success btn-lg">Update</button>
                <a href="do_view_table2.php?file_number=<?= h($file_number) ?>" class="btn btn-secondary btn-lg">Back</a>
        </div>
    </form>
    <?php else: ?>
        <p class="alert alert-warning">මෙම file_number සඳහා දත්ත හමුවුණේ නැත.</p>
    <?php endif; ?>
</div>

<script>
function addField(containerId, inputName) {
    const container = document.getElementById(containerId);
    const input = document.createElement('input');
    input.type = 'text';
    input.name = inputName;
    input.className = 'form-control mt-2';
    container.appendChild(input);
}

function addSchoolHistoryFields() {
    const container = document.getElementById('schoolHistoryFields');

    const schoolName = document.createElement('input');
    schoolName.type = 'text';
    schoolName.name = 'school_name[]';
    schoolName.placeholder = 'පාසලේ නම';
    schoolName.className = 'form-control mb-2';

    const startDate = document.createElement('input');
    startDate.type = 'date';
    startDate.name = 'school_start_date[]';
    startDate.className = 'form-control mb-2';

    const endDate = document.createElement('input');
    endDate.type = 'date';
    endDate.name = 'school_end_date[]';
    endDate.className = 'form-control mb-4';

    container.appendChild(schoolName);
    container.appendChild(startDate);
    container.appendChild(endDate);
}
</script>
</body>
</html>
