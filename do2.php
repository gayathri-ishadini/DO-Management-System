<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Employee Registration Form</title>
  

  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet" />

  <style>
    form {
      background-color: #C6F5DF;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #C6F5F3;
      padding: 30px;
    }

    h2 {
      color: white;
      margin-bottom: 20px;
      background-color: #4a90e2;
      padding: 15px 25px;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }

    h3 {
      color: #444;
      margin-bottom: 20px;
    }

    .form-section {
      background: #fff;
      border-radius: 8px;
      padding: 25px 30px;
      margin-bottom: 30px;
      box-shadow: 0 1px 5px rgba(0,0,0,0.08);
    }

    label {
      font-weight: 600;
      color: #555;
    }

    input.form-control, select.form-select {
      background-color: #f9f9f9;
      border: 1px solid #ddd;
      transition: border-color 0.3s ease;
    }
    input.form-control:focus, select.form-select:focus {
      border-color: #a0d468;
      box-shadow: none;
      background-color: #fff;
    }

    .btn-add {
      background-color: #9ad1f5;
      color: #1a73e8;
      font-weight: 600;
      border: none;
      margin-bottom: 15px;
      transition: background-color 0.3s ease;
      cursor: pointer;
    }
    .btn-add:hover {
      background-color: #7cb9e8;
      color: #fff;
    }

    .btn-submit {
      background-color: #ED82B0;
      border: none;
      font-weight: 600;
      padding: 12px 30px;
      font-size: 18px;
      transition: background-color 0.3s ease;
      cursor: pointer;
    }
    .btn-submit:hover {
      background-color: #E872A4;
    }

    .dynamic-group {
      border: 1px solid #e1e8f0;
      padding: 15px;
      border-radius: 6px;
      margin-bottom: 15px;
      background-color: #f8fbff;
    }

    .dynamic-group .form-label {
      font-weight: 600;
      color: #666;
    }

    
  #comment-section {
  width: 100%;
  max-width: 600px;
  margin: 40px auto;
  background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  transition: 0.3s ease-in-out;
}

#comment-section:hover {
  transform: scale(1.02);
}

/* ====== Label Styling ====== */
#comment-section label {
  display: block;
  font-size: 18px;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 10px;
  text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

/* ====== Textarea Styling ====== */
#feedback {
  width: 100%;
  height: 150px;
  padding: 12px 15px;
  font-size: 16px;
  border-radius: 10px;
  border: 2px solid #9c27b0;
  background-color: #fff;
  resize: vertical;
  transition: all 0.3s ease;
  font-family: 'Segoe UI', sans-serif;
}

/* ====== Focus Effect ====== */
#feedback:focus {
  border-color: #3f51b5;
  background-color: #f5f7ff;
  outline: none;
  box-shadow: 0 0 10px rgba(63,81,181,0.3);
}

/* ====== Placeholder Styling ====== */
#feedback::placeholder {
  color: #999;
  font-style: italic;
}
</style>

  <script>
    // Add generic input field (for addresses, phones, whatsapps)
    function addField(containerId, inputName) {
      const container = document.getElementById(containerId);
      const input = document.createElement('input');
      input.type = 'text';
      input.name = inputName + '[]';
      input.placeholder = "Enter " + inputName.slice(0, -1);
      input.className = 'form-control mb-2';
      container.appendChild(input);
    }

    // Add school attachment group (school_name, start_date, end_date)
    function addSchool() {
      const container = document.getElementById('schools');

      const div = document.createElement('div');
      div.className = 'dynamic-group mb-2';

      div.innerHTML = `
        <input type="text" name="school_name[]" placeholder="School Name" class="form-control mb-1" required />
        <input type="date" name="school_start[]" class="form-control mb-1" required /> to
        <input type="date" name="school_end[]" class="form-control mb-1" required />
      `;

      container.appendChild(div);
    }
  </script>
</head>
<body>

<?php

// // ========== DATABASE CONNECTION ==========
// $host = "localhost";
// $user = "root";
// $pass = "";
// $dbname = "education";

// $conn = new mysqli($host, $user, $pass, $dbname);

// if ($conn->connect_error) {
//     die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
// }

// // ========== HANDLE FORM SUBMISSION ==========
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $file_number = $_POST['file_number'];
//     $employee_name = $_POST['employee_name'];
//     $nic = $_POST['nic'];
//     $dob = $_POST['date_of_birth'];
//     $gender = $_POST['gender'];
//     $trainee_date = $_POST['date_of_trainee_training_appointment'];
//     $release_date = $_POST['date_of_release_from_divisional_secretariat'];
//     $appointment_date = $_POST['date_of_appointment'];
//     $assume_duties_date = $_POST['date_of_assuming_duties_in_zone'];
//     $eff_test_date = $_POST['date_of_passing_efficiency_test'];
//     $tamil_release_date = $_POST['date_of_tamil_release'];
//     $confirm_date = $_POST['date_of_appointment_confirmed'];

//     // NEW FIELDS
//    /*
//  $date_assigned_to_school = $_POST['date_assigned_to_school'];
//     $assigned_school_name = $_POST['assigned_school_name'];
//     $assigned_period = $_POST['assigned_period'];*/

//     // Insert main employee data
//     $stmt = $conn->prepare("INSERT INTO Employees
//         (file_number, employee_name, nic, date_of_birth, gender,
//         date_of_trainee_training_appointment, date_of_release_from_divisional_secretariat,
//         date_of_appointment, date_of_assuming_duties_in_zone, date_of_passing_efficiency_test,
//         date_of_tamil_release, date_of_appointment_confirmed)
//         VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
//     $stmt->bind_param("ssssssssssss", $file_number, $employee_name, $nic,
//         $dob, $gender, $trainee_date, $release_date, $appointment_date,
//         $assume_duties_date, $eff_test_date, $tamil_release_date, $confirm_date);
//     $stmt->execute();

//     // Insert addresses
//     if (!empty($_POST['addresses'])) {
//         $addrStmt = $conn->prepare("INSERT INTO Employeeaddresses (file_number, address) VALUES (?, ?)");
//         foreach ($_POST['addresses'] as $address) {
//             $address = trim($address);
//             if ($address != "") {
//                 $addrStmt->bind_param("ss", $file_number, $address);
//                 $addrStmt->execute();
//             }
//         }
//     }

//     // Insert phones
//     if (!empty($_POST['phones'])) {
//         $phoneStmt = $conn->prepare("INSERT INTO Employeetelephones (file_number, phone_number) VALUES (?, ?)");
//         foreach ($_POST['phones'] as $phone) {
//             $phone = trim($phone);
//             if ($phone != "") {
//                 $phoneStmt->bind_param("ss", $file_number, $phone);
//                 $phoneStmt->execute();
//             }
//         }
//     }

//     // Insert WhatsApp numbers
//     if (!empty($_POST['whatsapps'])) {
//         $waStmt = $conn->prepare("INSERT INTO EmployeewhatsApp (file_number, whatsapp_number) VALUES (?, ?)");
//         foreach ($_POST['whatsapps'] as $whatsapp) {
//             $whatsapp = trim($whatsapp);
//             if ($whatsapp != "") {
//                 $waStmt->bind_param("ss", $file_number, $whatsapp);
//                 $waStmt->execute();
//             }
//         }
//     }

//     // Insert Assigned School details
//    /* $date_assigned_to_school = trim($date_assigned_to_school);
//     $assigned_school_name = trim($assigned_school_name);
//     $assigned_period = trim($assigned_period);

//     if ($date_assigned_to_school !== "" || $assigned_school_name !== "" || $assigned_period !== "") {
//         $assignedStmt = $conn->prepare("INSERT INTO Employeeattachments (file_number, date_of_attachment, school_name, period) VALUES (?, ?, ?, ?)");
//         $assignedStmt->bind_param("ssss", $file_number, $date_assigned_to_school, $assigned_school_name, $assigned_period);
//         $assignedStmt->execute();
//     }*/

//     // Insert School Attachments (multiple)
//     if (!empty($_POST['school_name']) && !empty($_POST['school_start']) && !empty($_POST['school_end'])) {
//         $schoolStmt = $conn->prepare("INSERT INTO employeeschoolattachments (file_number, school_name, start_date, end_date) VALUES (?, ?, ?, ?)");
//         $count = count($_POST['school_name']);
//         for ($i = 0; $i < $count; $i++) {
//             $school_name = trim($_POST['school_name'][$i]);
//             $school_start = trim($_POST['school_start'][$i]);
//             $school_end = trim($_POST['school_end'][$i]);

//             if ($school_name === "" || $school_start === "" || $school_end === "") {
//                 continue;
//             }

//             $schoolStmt->bind_param("ssss", $file_number, $school_name, $school_start, $school_end);
//             $schoolStmt->execute();
//         }
//     }

//     echo "<p style='color:green; font-weight:600; text-align:center;'>දත්ත සාර්ථකව ඇතුළත් කරන ලදී!</p>";
// }

// ?>

<?php
// ========== DATABASE CONNECTION ==========
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "education";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
}

// ========== HANDLE FORM SUBMISSION ==========
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $file_number = $_POST['file_number'];
    $employee_name = $_POST['employee_name'];
    $nic = $_POST['nic'];
    $dob = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $trainee_date = $_POST['date_of_trainee_training_appointment'];
    $release_date = $_POST['date_of_release_from_divisional_secretariat'];
    $appointment_date = $_POST['date_of_appointment'];
    $assume_duties_date = $_POST['date_of_assuming_duties_in_zone'];
    $eff_test_date = $_POST['date_of_passing_efficiency_test'];
    $tamil_release_date = $_POST['date_of_tamil_release'];
    $confirm_date = $_POST['date_of_appointment_confirmed'];
    $comments = trim($_POST['feedback']); // ← Using form name “feedback”, database column “comments”

    // Insert main employee data + comments
    $stmt = $conn->prepare("INSERT INTO Employees
        (file_number, employee_name, nic, date_of_birth, gender,
        date_of_trainee_training_appointment, date_of_release_from_divisional_secretariat,
        date_of_appointment, date_of_assuming_duties_in_zone, date_of_passing_efficiency_test,
        date_of_tamil_release, date_of_appointment_confirmed, comments)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");

    $stmt->bind_param("sssssssssssss", $file_number, $employee_name, $nic,
        $dob, $gender, $trainee_date, $release_date, $appointment_date,
        $assume_duties_date, $eff_test_date, $tamil_release_date, $confirm_date, $comments);

    if ($stmt->execute()) {
        echo "<p style='color:green; font-weight:600; text-align:center;'>දත්ත සහ අදහස් සාර්ථකව සුරැක්ෂණය කරන ලදී!</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>Error: " . $stmt->error . "</p>";
    }

    // Insert addresses
    if (!empty($_POST['addresses'])) {
        $addrStmt = $conn->prepare("INSERT INTO Employeeaddresses (file_number, address) VALUES (?, ?)");
        foreach ($_POST['addresses'] as $address) {
            $address = trim($address);
            if ($address != "") {
                $addrStmt->bind_param("ss", $file_number, $address);
                $addrStmt->execute();
            }
        }
    }

    // Insert phones
    if (!empty($_POST['phones'])) {
        $phoneStmt = $conn->prepare("INSERT INTO Employeetelephones (file_number, phone_number) VALUES (?, ?)");
        foreach ($_POST['phones'] as $phone) {
            $phone = trim($phone);
            if ($phone != "") {
                $phoneStmt->bind_param("ss", $file_number, $phone);
                $phoneStmt->execute();
            }
        }
    }

    // Insert WhatsApp numbers
    if (!empty($_POST['whatsapps'])) {
        $waStmt = $conn->prepare("INSERT INTO EmployeewhatsApp (file_number, whatsapp_number) VALUES (?, ?)");
        foreach ($_POST['whatsapps'] as $whatsapp) {
            $whatsapp = trim($whatsapp);
            if ($whatsapp != "") {
                $waStmt->bind_param("ss", $file_number, $whatsapp);
                $waStmt->execute();
            }
        }
    }

    // // Insert School Attachments (multiple)
    // if (!empty($_POST['school_name']) && !empty($_POST['school_start']) && !empty($_POST['school_end'])) {
    //     $schoolStmt = $conn->prepare("INSERT INTO employeeschoolattachments (file_number, school_name, start_date, end_date) VALUES (?, ?, ?, ?)");
    //     $count = count($_POST['school_name']);
    //     for ($i = 0; $i < $count; $i++) {
    //         $school_name = trim($_POST['school_name'][$i]);
    //         $school_start = trim($_POST['school_start'][$i]);
    //         $school_end = trim($_POST['school_end'][$i]);
    //         if ($school_name === "" || $school_start === "" || $school_end === "") continue;
    //         $schoolStmt->bind_param("ssss", $file_number, $school_name, $school_start, $school_end);
    //         $schoolStmt->execute();
    //     }
    // }

    // Insert School Attachments (multiple)
if (!empty($_POST['school_name']) && !empty($_POST['school_start']) && !empty($_POST['school_end'])) {
    // include comments column too
    $schoolStmt = $conn->prepare("INSERT INTO employeeschoolattachments 
        (file_number, school_name, start_date, end_date, comments) 
        VALUES (?, ?, ?, ?, ?)");

    if (!$schoolStmt) {
        die("<p style='color:red;'>School Insert Prepare Failed: " . $conn->error . "</p>");
    }

    $count = count($_POST['school_name']);
    for ($i = 0; $i < $count; $i++) {
        $school_name = trim($_POST['school_name'][$i]);
        $school_start = trim($_POST['school_start'][$i]);
        $school_end = trim($_POST['school_end'][$i]);
        $comments = trim($_POST['feedback']); // ← same comment from main form

        if ($school_name === "" || $school_start === "" || $school_end === "") continue;

        $schoolStmt->bind_param("sssss", $file_number, $school_name, $school_start, $school_end, $comments);
        $schoolStmt->execute();
    }
}

}
?>



<h2>Employee Registration Form</h2>
    <button onclick="window.location.href='do_view_table2.php'" class="btn btn-secondary">⬅ Back</button>

<form method="post" action="" class="mx-auto" style="max-width: 720px;">

  <div class="form-section">
    <h3>Main Details</h3>
    <div class="mb-3 row">
      <label class="col-sm-4 col-form-label text-end">ලිපිගොනු අංකය <span class="text-danger">*</span></label>
      <div class="col-sm-8"><input type="text" name="file_number" class="form-control" required /></div>
    </div>

    <div class="mb-3 row">
      <label class="col-sm-4 col-form-label text-end">සේවකයාගේ නම<span class="text-danger">*</span></label>
      <div class="col-sm-8"><input type="text" name="employee_name" class="form-control" required /></div>
    </div>

    <div class="mb-3 row">
      <label class="col-sm-4 col-form-label text-end">ජාතික හැඳුනුම්පත් අංකය</label>
      <div class="col-sm-8"><input type="text" name="nic" class="form-control" /></div>
    </div>

    <div class="mb-3 row">
      <label class="col-sm-4 col-form-label text-end">උපන් දිනය</label>
      <div class="col-sm-8"><input type="date" name="date_of_birth" class="form-control" /></div>
    </div>

    <div class="mb-3 row">
      <label class="col-sm-4 col-form-label text-end">ස්ත්‍රී/පුරුෂ භාවය</label>
      <div class="col-sm-8">
        <select class="form-select" name="gender">
          <option value="">--තෝරන්න--</option>
          <option value="Male">පුරුෂ</option>
          <option value="Female">ස්ත්‍රී</option>
        </select>
      </div>
    </div>
  </div>

  <div class="form-section">
    <h3>Addresses</h3>
    <div id="addresses">
      <input type="text" name="addresses[]" placeholder="Enter address" class="form-control mb-2" />
    </div>
    <button type="button" class="btn btn-add" onclick="addField('addresses','addresses')">+ Add Address</button><br>
  </div>

  <div class="form-section">
    <h3>Telephone Numbers</h3>
    <div id="phones">
      <input type="text" name="phones[]" placeholder="Enter phone number" class="form-control mb-2" />
    </div>
    <button type="button" class="btn btn-add" onclick="addField('phones','phones')">+ Add Phone</button><br>
  </div>

  <div class="form-section">
    <h3>WhatsApp Numbers</h3>
    <div id="whatsapps">
      <input type="text" name="whatsapps[]" placeholder="Enter WhatsApp number" class="form-control mb-2" />
    </div>
    <button type="button" class="btn btn-add" onclick="addField('whatsapps','whatsapps')">+ Add WhatsApp</button><br>
  </div>

  <div class="form-section">
    <h3>Other Dates</h3>

    <div class="mb-3 row">
      <label class="col-sm-5 col-form-label text-end">අභ්‍යාසලබී පූණු පත්වීම් දිනය</label>
      <div class="col-sm-7"><input type="date" name="date_of_trainee_training_appointment" class="form-control" /></div>
    </div>

    <div class="mb-3 row">
      <label class="col-sm-5 col-form-label text-end">ප්‍රාදේශීය ලේකම් කාර්යාලයෙන් නිදහස් වූ දිනය</label>
      <div class="col-sm-7"><input type="date" name="date_of_release_from_divisional_secretariat" class="form-control" /></div>
    </div>

    <div class="mb-3 row">
      <label class="col-sm-5 col-form-label text-end">පත්වීම් දිනය</label>
      <div class="col-sm-7"><input type="date" name="date_of_appointment" class="form-control" /></div>
    </div>

    <div class="mb-3 row">
      <label class="col-sm-5 col-form-label text-end">කලාපයේ වැඩ භාරගත් දිනය</label>
      <div class="col-sm-7"><input type="date" name="date_of_assuming_duties_in_zone" class="form-control" /></div>
    </div>

    <div class="mb-3 row">
      <label class="col-sm-5 col-form-label text-end">කාර්යක්ෂමතා කඩඉම් සමත් දිනය</label>
      <div class="col-sm-7"><input type="date" name="date_of_passing_efficiency_test" class="form-control" /></div>
    </div> 

    <div class="mb-3 row">
      <label class="col-sm-5 col-form-label text-end">දෙමළ නිදහස් දිනය</label>
      <div class="col-sm-7"><input type="date" name="date_of_tamil_release" class="form-control" /></div>
    </div>

    <div class="mb-3 row">
      <label class="col-sm-5 col-form-label text-end">පත්වීම ස්ථීර වූ ඇති දිනය</label>
      <div class="col-sm-7"><input type="date" name="date_of_appointment_confirmed" class="form-control" /></div>
    </div>
  </div>
<!--
  <div class="form-section">
    <h3>අනියුක්ත වූ පාසල පිළිබඳ තොරතුරු:</h3>
    <div class="mb-3 row">
      <label class="col-sm-4 col-form-label text-end">ආරම්භක දිනය</label>
      <div class="col-sm-8"><input type="date" name="date_assigned_to_school" class="form-control" /></div>
    </div>

    <div class="mb-3 row">
      <label class="col-sm-4 col-form-label text-end">පාසල</label>
      <div class="col-sm-8"><input type="text" name="assigned_school_name" class="form-control" /></div>
    </div>

    <div class="mb-3 row">
      <label class="col-sm-4 col-form-label text-end">කාලය</label>
      <div class="col-sm-8"><input type="text" name="assigned_period" class="form-control" placeholder="e.g., YYYY.MM.DD-YYYY.MM.DD" /></div>
    </div>
  </div>
-->
  <div class="form-section">
    <h3>අනියුක්ත පාසල</h3>
    <div id="schools">
      <div class="dynamic-group mb-2">
        <input type="text" name="school_name[]" placeholder="පාසලේ නම" class="form-control mb-1" required />
        <input type="date" name="school_start[]" class="form-control mb-1" required /> to
        <input type="date" name="school_end[]" class="form-control mb-1" required />
      </div>
    </div>
    <button type="button" class="btn btn-add" onclick="addSchool()">+ Add School</button>

    <div id="comment-section">
      <div class="dynamic-group mb-2">
        <label for="feedback">අදහස් දක්වන්න</label><br>
        <textarea id="feedback" name="feedback" rows="6" cols="50" placeholder="Type your comments here..."></textarea>

        
      </div>
    </div>

  </div>

  <div class="text-center mt-4">
    <button type="submit" class="btn btn-submit">Save Employee</button>
	 
  </div>
</form>
  
<script src="js/bootstrap.bundle.min.js"> </script> 
 
</body>
</html>