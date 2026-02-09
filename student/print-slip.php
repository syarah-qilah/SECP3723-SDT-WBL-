<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// 1. SECURITY: Ensure user is a registered Student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student' || !isset($_SESSION['key_id'])) {
    die("Access Denied. Student privileges required.");
}

$matric_no = $_SESSION['key_id'];
$current_semester = get_current_semester();
$date_printed = date('d/m/Y h:i A');

// 2. FETCH STUDENT & USER DETAILS
// Join Student and User tables to get names, program, faculty, etc.
$stu_sql = "SELECT s.*, u.name, u.faculty 
            FROM Student s 
            JOIN User u ON s.username = u.username 
            WHERE s.matricno = '$matric_no'";
$stu_result = mysqli_query($conn, $stu_sql);

if (!$stu_row = mysqli_fetch_assoc($stu_result)) {
    die("Student registration record not found.");
}

// 3. FETCH APPROVED COURSES
// Only get courses with 'Approved' status for the slip
$course_sql = "SELECT r.*, c.c_name, c.c_credit, c.section 
               FROM Registration r 
               JOIN Course c ON r.c_code = c.c_code 
               WHERE r.matricno = '$matric_no' AND r.regisStat = 'Approved'
               ORDER BY r.c_code ASC";
$course_result = mysqli_query($conn, $course_sql);

// Calculate Total Credits
$total_credits = 0;
$courses = [];
while ($c_row = mysqli_fetch_assoc($course_result)) {
    $total_credits += $c_row['c_credit'];
    $courses[] = $c_row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Registration Slip - <?php echo $matric_no; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* --- PRINTER-FRIENDLY CSS --- */
        body {
            font-family: 'Roboto', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #fff; /* White background for printing */
        }

        .slip-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #000; /* Outer border like the example */
            padding: 20px;
        }

        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 80px; /* Adjust logo size */
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: 700;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 11px;
        }

        /* Title Bar */
        .slip-title {
            background: #e0e0e0; /* Light gray background */
            text-align: center;
            font-weight: 700;
            padding: 5px;
            border: 1px solid #000;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        /* Data Tables */
        .info-table, .course-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td, .course-table th, .course-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        .info-field {
            font-weight: 700;
            width: 140px;
            background: #f9f9f9; /* Slight background for labels */
        }
        
        /* Course Table Headers */
        .course-table th {
            background: #e0e0e0;
            text-align: left;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Totals Section */
        .totals-section {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 20px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        /* Declaration & Footer */
        .declaration {
            font-size: 11px;
            text-align: justify;
            margin-bottom: 40px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }
        .signature-box {
            width: 40%;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .doc-id {
            text-align: right;
            font-size: 10px;
            margin-bottom: 10px;
        }

        /* Print-specific rules to hide on-screen elements */
        @media print {
            @page { margin: 2cm; }
            body { -webkit-print-color-adjust: exact; } /* Force background colors */
            .no-print { display: none !important; }
        }

        /* A temporary "Back" button for on-screen viewing */
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <a href="my-courses.php" class="back-btn no-print">
        <i class="fas fa-arrow-left"></i> Back to My Courses
    </a>

    <div class="slip-container">
        
        <div class="doc-id">SMS/STD-REG/<?php echo date('Y'); ?>-1</div>

        <div class="header">
            <img src="../assets/img/logo.png" alt="University Logo">
            <h1>Say Say University</h1>
            <p>Academic Affairs Division</p>
        </div>

        <div class="slip-title">
            COURSE REGISTRATION SLIP - <?php echo strtoupper($current_semester); ?>
        </div>

        <table class="info-table">
            <tr>
                <td class="info-field">Student ID</td>
                <td><?php echo $stu_row['matricno']; ?></td>
                <td class="info-field">Programme</td>
                <td><?php echo $stu_row['program']; ?></td>
            </tr>
            <tr>
                <td class="info-field">Name</td>
                <td><?php echo strtoupper($stu_row['name']); ?></td>
                <td class="info-field">Faculty</td>
                <td><?php echo $stu_row['faculty']; ?></td>
            </tr>
            <tr>
                 <td class="info-field">Year / Part</td>
                <td><?php echo $stu_row['year']; ?></td>
                <td class="info-field">Status</td>
                <td>Active</td> </tr>
        </table>

        <table class="course-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th class="text-center">Section</th>
                    <th class="text-center">Credit</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($courses) > 0): ?>
                    <?php foreach ($courses as $index => $course): ?>
                        <tr>
                            <td class="text-center"><?php echo $index + 1; ?></td>
                            <td><strong><?php echo $course['c_code']; ?></strong></td>
                            <td><?php echo strtoupper($course['c_name']); ?></td>
                            <td class="text-center"><?php echo $course['section']; ?></td>
                            <td class="text-center"><?php echo $course['c_credit']; ?>.0</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 20px;">No approved courses found for this semester.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="totals-section">
            <div class="totals-row">
                <div><strong>Total Credit Units:</strong> <?php echo number_format($total_credits, 1); ?></div>
                <div><strong>Date Printed:</strong> <?php echo $date_printed; ?></div>
            </div>
            <div class="totals-row">
                 <div><strong>Status:</strong> <?php echo ($total_credits > 0) ? 'Validated - Officially Registered' : 'Pending Registration'; ?></div>
            </div>
        </div>

        <div class="declaration">
            <p>I hereby declare that the courses listed above are correct and I have registered for them in accordance with the University's academic regulations. I understand that it is my responsibility to ensure the accuracy of this registration.</p>
        </div>

        <div class="signatures">
            <div class="signature-box">
                <p><strong>Student's Signature</strong></p>
                <br><br>
                <p><?php echo strtoupper($stu_row['name']); ?></p>
            </div>
            <div class="signature-box" style="text-align: right;">
                 <p><strong>Academic Advisor / Faculty Approval</strong></p>
            </div>
        </div>

    </div>

    <script>
        window.onload = function() {
            // Uncomment the line below to auto-print on page load
            // window.print();
        }
    </script>

</body>
</html>