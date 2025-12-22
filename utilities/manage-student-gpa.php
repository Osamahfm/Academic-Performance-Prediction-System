<?php
/**
 * Utility to manage student GPA and attendance
 * This is essential for KNN predictions to work
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

// Ensure autoloader is registered
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Database;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Student GPA & Attendance - EduPredict</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c5aa0;
            margin-bottom: 30px;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn-primary {
            background: #2c5aa0;
            color: white;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
            position: relative;
        }
        .close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            cursor: pointer;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-low { background: #28a745; color: white; }
        .badge-medium { background: #ffc107; color: #333; }
        .badge-high { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Manage Student GPA & Attendance</h1>

        <div class="info">
            <p><strong>Why This Matters:</strong></p>
            <p>KNN predictions require student GPA and attendance data. Without this data, predictions cannot be made.</p>
            <p><strong>GPA Range:</strong> 0.00 - 4.00 | <strong>Attendance Range:</strong> 0% - 100%</p>
        </div>

<?php
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_gpa'])) {
        $studentId = $_POST['student_id'] ?? null;
        $gpa = floatval($_POST['gpa'] ?? 0);
        $attendance = floatval($_POST['attendance_rate'] ?? 0);
        
        // Validate ranges
        if ($gpa < 0 || $gpa > 4.0) {
            echo "<div class='error'>❌ GPA must be between 0.00 and 4.00</div>";
        } elseif ($attendance < 0 || $attendance > 100) {
            echo "<div class='error'>❌ Attendance must be between 0% and 100%</div>";
        } elseif ($studentId) {
            // Calculate risk level based on GPA
            $riskLevel = 'medium';
            if ($gpa < 2.0) {
                $riskLevel = 'high';
            } elseif ($gpa >= 3.0) {
                $riskLevel = 'low';
            }
            
            $sql = "UPDATE students SET gpa = :gpa, attendance_rate = :attendance, risk_level = :risk WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':gpa' => $gpa,
                ':attendance' => $attendance,
                ':risk' => $riskLevel,
                ':id' => $studentId
            ]);
            
            // Trigger prediction update
            try {
                // Ensure KNNPredictor is loaded
                require_once __DIR__ . '/../app/core/ML/KNNPredictor.php';
                require_once __DIR__ . '/../app/services/PredictionService.php';
                $predictionService = new \App\Services\PredictionService();
                $predictionService->predictPerformance($studentId);
                echo "<div class='success'>✅ Student data updated and prediction refreshed!</div>";
            } catch (Exception $e) {
                echo "<div class='success'>✅ Student data updated! (Prediction update skipped: " . htmlspecialchars($e->getMessage()) . ")</div>";
            }
        }
    }
    
    // Handle bulk update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_update'])) {
        $gpa = floatval($_POST['bulk_gpa'] ?? 0);
        $attendance = floatval($_POST['bulk_attendance'] ?? 0);
        $updateAll = isset($_POST['update_all']) && $_POST['update_all'] === '1';
        
        if ($gpa >= 0 && $gpa <= 4.0 && $attendance >= 0 && $attendance <= 100) {
            $riskLevel = 'medium';
            if ($gpa < 2.0) {
                $riskLevel = 'high';
            } elseif ($gpa >= 3.0) {
                $riskLevel = 'low';
            }
            
            if ($updateAll) {
                // Update ALL students
                $sql = "UPDATE students SET gpa = :gpa, attendance_rate = :attendance, risk_level = :risk";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':gpa' => $gpa,
                    ':attendance' => $attendance,
                    ':risk' => $riskLevel
                ]);
                $count = $stmt->rowCount();
                echo "<div class='success'>✅ Updated ALL {$count} students with GPA {$gpa} and attendance {$attendance}%!</div>";
            } else {
                // Update only students without GPA/attendance
                $sql = "UPDATE students SET gpa = :gpa, attendance_rate = :attendance, risk_level = :risk 
                        WHERE (gpa IS NULL OR gpa = 0) AND (attendance_rate IS NULL OR attendance_rate = 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':gpa' => $gpa,
                    ':attendance' => $attendance,
                    ':risk' => $riskLevel
                ]);
                $count = $stmt->rowCount();
                echo "<div class='success'>✅ Updated {$count} students (without GPA/attendance) with default values!</div>";
            }
        } else {
            echo "<div class='error'>❌ Invalid GPA or attendance values</div>";
        }
    }
    
    // Get all students
    $sql = "SELECT s.id, s.student_id, s.gpa, s.attendance_rate, s.risk_level, u.name, u.email
            FROM students s
            LEFT JOIN users u ON s.user_id = u.id
            ORDER BY u.name";
    $students = $pdo->query($sql)->fetchAll();
    
    // Statistics
    $studentsWithData = 0;
    $studentsWithoutData = 0;
    foreach ($students as $student) {
        if ($student['gpa'] !== null && $student['gpa'] > 0 && 
            $student['attendance_rate'] !== null && $student['attendance_rate'] > 0) {
            $studentsWithData++;
        } else {
            $studentsWithoutData++;
        }
    }
    
    echo "<div class='info'>";
    echo "<h3>📈 Statistics</h3>";
    echo "<p><strong>Total Students:</strong> " . count($students) . "</p>";
    echo "<p><strong>With GPA & Attendance:</strong> {$studentsWithData} ✅</p>";
    echo "<p><strong>Without GPA & Attendance:</strong> {$studentsWithoutData} ⚠️</p>";
    echo "</div>";
    
    // Bulk update form
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;'>";
    echo "<h3>⚡ Quick Bulk Update</h3>";
    echo "<form method='POST'>";
    echo "<div style='display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 15px;'>";
    echo "<div class='form-group'>";
    echo "<label>GPA (0.00 - 4.00) *</label>";
    echo "<input type='number' name='bulk_gpa' step='0.01' min='0' max='4' value='2.5' required>";
    echo "</div>";
    echo "<div class='form-group'>";
    echo "<label>Attendance (0% - 100%) *</label>";
    echo "<input type='number' name='bulk_attendance' step='0.1' min='0' max='100' value='75' required>";
    echo "</div>";
    echo "<div class='form-group'>";
    echo "<label>Update Option</label>";
    echo "<select name='update_all' style='width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<option value='0'>Only Empty Records (NULL/0)</option>";
    echo "<option value='1'>ALL Students (Overwrite Existing)</option>";
    echo "</select>";
    echo "</div>";
    echo "<div class='form-group'>";
    echo "<label>&nbsp;</label>";
    echo "<button type='submit' name='bulk_update' class='btn btn-success' style='width: 100%; padding: 10px;'>🚀 Bulk Update</button>";
    echo "</div>";
    echo "</div>";
    echo "<small style='color: #666; display: block; margin-top: 10px;'><strong>Note:</strong> 'Only Empty Records' updates students with NULL or 0 GPA/attendance. 'ALL Students' will overwrite existing values for everyone.</small>";
    echo "</form>";
    echo "</div>";
    
    // Students table
    echo "<h2>All Students</h2>";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Name</th>";
    echo "<th>Student ID</th>";
    echo "<th>GPA</th>";
    echo "<th>Attendance</th>";
    echo "<th>Risk Level</th>";
    echo "<th>Actions</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    
    foreach ($students as $student) {
        $gpa = $student['gpa'] !== null ? number_format($student['gpa'], 2) : '<span style="color: #dc3545;">Not Set</span>';
        $attendance = $student['attendance_rate'] !== null ? number_format($student['attendance_rate'], 1) . '%' : '<span style="color: #dc3545;">Not Set</span>';
        $riskLevel = $student['risk_level'] ?? 'medium';
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($student['name'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($student['student_id'] ?? 'N/A') . "</td>";
        echo "<td>{$gpa}</td>";
        echo "<td>{$attendance}</td>";
        echo "<td><span class='badge badge-{$riskLevel}'>" . ucfirst($riskLevel) . "</span></td>";
        echo "<td>";
        echo "<button onclick=\"editStudent(" . htmlspecialchars(json_encode($student)) . ")\" class='btn btn-primary'>Edit</button>";
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</tbody>";
    echo "</table>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

        <!-- Edit Modal -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Edit Student GPA & Attendance</h2>
                <form method="POST" id="editForm">
                    <input type="hidden" id="editStudentId" name="student_id">
                    <div class="form-group">
                        <label>GPA (0.00 - 4.00) *</label>
                        <input type="number" id="editGpa" name="gpa" step="0.01" min="0" max="4" required>
                    </div>
                    <div class="form-group">
                        <label>Attendance Rate (0% - 100%) *</label>
                        <input type="number" id="editAttendance" name="attendance_rate" step="0.1" min="0" max="100" required>
                    </div>
                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" name="update_gpa" class="btn btn-success" style="flex: 1;">Update</button>
                        <button type="button" onclick="closeModal()" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;">
            <a href="/projecty/public/dashboard/admin" class="btn btn-primary">← Back to Dashboard</a>
            <a href="/projecty/public/predictions" class="btn btn-success">Run Predictions →</a>
        </div>
    </div>

    <script>
        function editStudent(student) {
            document.getElementById('editModal').style.display = 'block';
            document.getElementById('editStudentId').value = student.id;
            document.getElementById('editGpa').value = student.gpa || '';
            document.getElementById('editAttendance').value = student.attendance_rate || '';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>

