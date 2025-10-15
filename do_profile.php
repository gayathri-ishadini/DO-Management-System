<?php
// ===================== DATABASE CONNECTION =====================
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "education";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
}

// ===================== GET FILE NUMBER =====================
$file_number = "";
if (isset($_GET['file_number'])) {
    $file_number = trim($_GET['file_number']);
}

// ===================== WHEN FILE NUMBER IS EMPTY (SHOW SEARCH FORM) =====================
if ($file_number == "") {
    ?>
    <!DOCTYPE html>
    <html lang="si">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>🔍 සේවකයා සෙවීම</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <style>
      body { background-color: #C6F5F3; font-family: 'Segoe UI', sans-serif; padding: 40px; }
      .search-box {
        background: #fff; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 40px; max-width: 500px; margin: auto; text-align: center;
      }
      input[type=text] {
        padding: 10px; width: 80%; border-radius: 8px; border: 1px solid #ccc;
      }
      button { margin-top: 15px; }
      </style>
    </head>
    <body>
      <div class="search-box">
        <h3 class="text-primary mb-3">🔍 සේවකයාගේ ලිපිගොනු අංකය ඇතුළත් කරන්න</h3>
        <form method="GET">
          <input type="text" name="file_number" placeholder="උදා: EMP001" required>
          <br>
          <button type="submit" class="btn btn-primary mt-3">ප්‍රොෆයිලය බලන්න</button>
        </form>
      </div>
    </body>
    </html>
    <?php
    exit;
}

// ===================== GET EMPLOYEE DATA =====================
$empSql = "SELECT * FROM Employees WHERE file_number = ?";
$empStmt = $conn->prepare($empSql);
$empStmt->bind_param("s", $file_number);
$empStmt->execute();
$emp = $empStmt->get_result()->fetch_assoc();

if (!$emp) {
    die("
    <div style='text-align:center; margin-top:50px;'>
      <p style='color:red; font-size:18px;'>සේවකයා හමු නොවීය: <b>$file_number</b></p>
      <a href='employee_profile.php' class='btn btn-secondary'>🔍 නැවත සෙවීමකට යන්න</a>
    </div>");
}

// ===================== FETCH RELATED TABLE DATA =====================
function fetchAll($conn, $table, $field, $file_number)
{
    $sql = "SELECT * FROM $table WHERE $field = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $file_number);
    $stmt->execute();
    return $stmt->get_result();
}

$addresses = fetchAll($conn, "Employeeaddresses", "file_number", $file_number);
$phones = fetchAll($conn, "Employeetelephones", "file_number", $file_number);
$whatsapps = fetchAll($conn, "EmployeewhatsApp", "file_number", $file_number);
//$attachments = fetchAll($conn, "Employeeattachments", "file_number", $file_number);
$schools = fetchAll($conn, "employeeschoolattachments", "file_number", $file_number);
?>

<!DOCTYPE html>
<html lang="si">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Employee Profile - <?= htmlspecialchars($emp['employee_name']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background-color: #C6F5F3;
  font-family: 'Segoe UI', sans-serif;
  padding: 20px;
}
.profile-card {
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  padding: 30px;
  max-width: 900px;
  margin: auto;
}
.section-title {
  background-color: #4a90e2;
  color: white;
  padding: 10px 20px;
  border-radius: 6px;
  margin-bottom: 20px;
}
.table-custom th {
  background-color: #e7f3ff;
}
</style>
</head>

<body>
<div class="profile-card">
  <h2 class="text-center mb-4 text-primary">👤 EMPLOYEE PROFILE</h2>

  <!-- MAIN DETAILS -->
  <div class="section-title">ප්‍රධාන විස්තර</div>
  <table class="table table-bordered table-custom">
    <tr><th>ලිපිගොනු අංකය</th><td><?= htmlspecialchars($emp['file_number']) ?></td></tr>
    <tr><th>සේවකයාගේ නම</th><td><?= htmlspecialchars($emp['employee_name']) ?></td></tr>
    <tr><th>ජාතික හැඳුනුම්පත් අංකය</th><td><?= htmlspecialchars($emp['nic']) ?></td></tr>
    <tr><th>උපන් දිනය</th><td><?= htmlspecialchars($emp['date_of_birth']) ?></td></tr>
    <tr><th>ස්ත්‍රී/පුරුෂ භාවය</th><td><?= htmlspecialchars($emp['gender']) ?></td></tr>
  </table>

  <!-- ADDRESSES -->
  <div class="section-title">ලිපින</div>
  <ul>
    <?php if ($addresses->num_rows > 0) {
      while ($row = $addresses->fetch_assoc()) { ?>
        <li><?= htmlspecialchars($row['address']) ?></li>
    <?php } } else { echo "<li>ලිපිනයක් නොමැත</li>"; } ?>
  </ul>

  <!-- PHONE NUMBERS -->
  <div class="section-title">දුරකථන අංක</div>
  <ul>
    <?php if ($phones->num_rows > 0) {
      while ($row = $phones->fetch_assoc()) { ?>
        <li><?= htmlspecialchars($row['phone_number']) ?></li>
    <?php } } else { echo "<li>දුරකථන අංක නොමැත</li>"; } ?>
  </ul>

  <!-- WHATSAPP NUMBERS -->
  <div class="section-title">WhatsApp අංක</div>
  <ul>
    <?php if ($whatsapps->num_rows > 0) {
      while ($row = $whatsapps->fetch_assoc()) { ?>
        <li><?= htmlspecialchars($row['whatsapp_number']) ?></li>
    <?php } } else { echo "<li>WhatsApp අංක නොමැත</li>"; } ?>
  </ul>

  <!-- DATES -->
  <div class="section-title">දිනයන්</div>
  <table class="table table-bordered table-custom">
    <tr><th>අභ්‍යාසලබී පූණු පත්වීම් දිනය</th><td><?= $emp['date_of_trainee_training_appointment'] ?></td></tr>
    <tr><th>ප්‍රාදේශීය ලේකම් කාර්යාලයෙන් නිදහස් වූ දිනය</th><td><?= $emp['date_of_release_from_divisional_secretariat'] ?></td></tr>
    <tr><th>පත්වීම් දිනය</th><td><?= $emp['date_of_appointment'] ?></td></tr>
    <tr><th>කලාපයේ වැඩ භාරගත් දිනය</th><td><?= $emp['date_of_assuming_duties_in_zone'] ?></td></tr>
    <tr><th>කාර්යක්ෂමතා කඩඉම් සමත් දිනය</th><td><?= $emp['date_of_passing_efficiency_test'] ?></td></tr>
    <tr><th>දෙමළ නිදහස් දිනය</th><td><?= $emp['date_of_tamil_release'] ?></td></tr>
    <tr><th>පත්වීම ස්ථීර වූ ඇති දිනය</th><td><?= $emp['date_of_appointment_confirmed'] ?></td></tr>
  </table>

  
  <!-- SCHOOL ATTACHMENTS -->
  <div class="section-title">අනුයුක්ත පාසල් පත්කිරීම්</div>
  <table class="table table-bordered table-custom">
    <thead><tr><th>පාසලේ නම</th><th>ආරම්භක දිනය</th><th>අවසන් දිනය</th></tr></thead>
    <tbody>
    <?php if ($schools->num_rows > 0) {
      while ($row = $schools->fetch_assoc()) { ?>
        <tr>
          <td><?= htmlspecialchars($row['school_name']) ?></td>
          <td><?= htmlspecialchars($row['start_date']) ?></td>
          <td><?= htmlspecialchars($row['end_date']) ?></td>
        </tr>
    <?php } } else { echo "<tr><td colspan='3'>අතීත පාසල් පත්කිරීම් නොමැත</td></tr>"; } ?>
    </tbody>
  </table>

  <!-- ACTION BUTTONS -->
  <div class="text-center mt-4">
    <button onclick="window.location.href='do_profile.php'" class="btn btn-secondary">🔍 වෙනත් සේවකයෙකු සෙවීමට</button>
    <button onclick="window.location.href='do_view_table2.php'" class="btn btn-primary">⬅ ආපසු යන්න</button>
  </div>
</div>
</body>
</html>