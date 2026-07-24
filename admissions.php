<?php
// index.php - All-in-one Admission Form, CSV Tracker, and Receipt Generator for Matugga Hills SS

$submitted = false;
$app_id = "";
$submission_date = "";

// ==========================================
// 1. BACKEND PROCESSING (Triggers on POST)
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $submitted = true;

    // Sanitize General Information
    $student_name        = htmlspecialchars($_POST['student_name'] ?? '');
    $dob                 = htmlspecialchars($_POST['dob'] ?? '');
    $gender              = htmlspecialchars($_POST['gender'] ?? '');
    $target_class        = htmlspecialchars($_POST['target_class'] ?? '');
    $prev_school         = htmlspecialchars($_POST['prev_school'] ?? '');
    
    $parent_name         = htmlspecialchars($_POST['parent_name'] ?? '');
    $relationship        = htmlspecialchars($_POST['relationship'] ?? '');
    $primary_phone       = htmlspecialchars($_POST['primary_phone'] ?? '');
    $alt_phone           = htmlspecialchars($_POST['alt_phone'] ?? '');
    $email               = htmlspecialchars($_POST['email'] ?? '');
    $residential_address = htmlspecialchars($_POST['residential_address'] ?? '');

    // Collect Dynamic Class-Specific Information
    $academic_details    = "N/A";
    
    if ($target_class === "Senior One") {
        $ple_agg = htmlspecialchars($_POST['ple_aggregate'] ?? '');
        $m_math  = htmlspecialchars($_POST['s1_math'] ?? '');
        $m_eng   = htmlspecialchars($_POST['s1_english'] ?? '');
        $m_sci   = htmlspecialchars($_POST['s1_science'] ?? '');
        $m_sst   = htmlspecialchars($_POST['s1_sst'] ?? '');
        $academic_details = "PLE Agg: $ple_agg | Math: $m_math, Eng: $m_eng, Sci: $m_sci, SST: $m_sst";
    } elseif ($target_class === "Senior Two" || $target_class === "Senior Three") {
        $prev_comp = htmlspecialchars($_POST['prev_class_completed'] ?? '');
        $s_math    = htmlspecialchars($_POST['s23_math'] ?? '');
        $s_eng     = htmlspecialchars($_POST['s23_english'] ?? '');
        $s_phys    = htmlspecialchars($_POST['s23_physics'] ?? '');
        $s_chem    = htmlspecialchars($_POST['s23_chemistry'] ?? '');
        $s_bio     = htmlspecialchars($_POST['s23_biology'] ?? '');
        $s_hum     = htmlspecialchars($_POST['s23_humanities'] ?? '');
        $academic_details = "Last Class: $prev_comp | Math: $s_math, Eng: $s_eng, Phys: $s_phys, Chem: $s_chem, Bio: $s_bio, Hum: $s_hum";
    } elseif ($target_class === "Senior Five") {
        $uce_res   = htmlspecialchars($_POST['uce_results'] ?? '');
        $comb      = htmlspecialchars($_POST['desired_combination'] ?? '');
        $o_math    = htmlspecialchars($_POST['s5_math'] ?? '');
        $o_eng     = htmlspecialchars($_POST['s5_english'] ?? '');
        $o_phys    = htmlspecialchars($_POST['s5_physics'] ?? '');
        $o_chem    = htmlspecialchars($_POST['s5_chemistry'] ?? '');
        $o_bio     = htmlspecialchars($_POST['s5_biology'] ?? '');
        $academic_details = "UCE Res: $uce_res | Combo: $comb | Math: $o_math, Eng: $o_eng, Phys: $o_phys, Chem: $o_chem, Bio: $o_bio";
    }

    // Handle File Upload
    $uploaded_file_name = "No File Uploaded";
    if (isset($_FILES['transcript']) && $_FILES['transcript']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_extension = pathinfo($_FILES['transcript']['name'], PATHINFO_EXTENSION);
        $uploaded_file_name = "APP_" . date("Ymd_His") . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $student_name) . "." . $file_extension;
        move_uploaded_file($_FILES['transcript']['tmp_name'], $upload_dir . $uploaded_file_name);
    }

    // Generate Application ID & Timestamp
    $app_id          = "MHSS-" . date("Ymd-His");
    $submission_date = date("Y-m-d H:i:s");

    // Save to CSV Tracker
    $csv_file = "admissions_tracker.csv";
    $is_new_file = !file_exists($csv_file);
    $file_handle = fopen($csv_file, "a");

    if ($is_new_file) {
        fputcsv($file_handle, [
            "App ID", "Submission Date", "Student Name", "DOB", "Gender", 
            "Target Class", "Previous School", "Academic Details / Scores", 
            "Parent Name", "Relationship", "Primary Phone", "Alt Phone", 
            "Email", "Residential Address", "Uploaded File"
        ]);
    }

    fputcsv($file_handle, [
        $app_id, $submission_date, $student_name, $dob, $gender, 
        $target_class, $prev_school, $academic_details, 
        $parent_name, $relationship, $primary_phone, $alt_phone, 
        $email, $residential_address, $uploaded_file_name
    ]);
    fclose($file_handle);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matugga Hills SS - Online Admission Portal</title>
    <style>
        :root { --primary: #004080; --secondary: #274e13; --bg: #f4f7f6; --text: #333; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background-color: var(--bg); color: var(--text); line-height: 1.6; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 3px solid var(--primary); padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { color: var(--primary); font-size: 26px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { color: #666; font-size: 15px; }
        
        /* Form Styles */
        fieldset { border: 1px solid #ccc; border-radius: 6px; padding: 20px; margin-bottom: 20px; background: #fafafa; }
        legend { font-weight: bold; color: var(--primary); padding: 0 10px; font-size: 17px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: 600; margin-bottom: 5px; font-size: 14px; }
        input[type="text"], input[type="date"], input[type="email"], input[type="tel"], select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        input:focus, select:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 5px rgba(0,64,128,0.2); }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        .dynamic-section { display: none; margin-top: 15px; padding-top: 15px; border-top: 1px dashed #bbb; }
        .btn-submit { background-color: var(--primary); color: white; border: none; padding: 12px 25px; font-size: 16px; font-weight: bold; border-radius: 4px; cursor: pointer; width: 100%; transition: background 0.3s; }
        .btn-submit:hover { background-color: #002b55; }
        
        /* Receipt Styles */
        .badge-success { background: #e2f0d9; color: var(--secondary); padding: 15px; border-radius: 6px; text-align: center; font-weight: bold; margin-bottom: 25px; font-size: 16px; border: 1px solid #c9e2b3; }
        .receipt-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .receipt-table th, .receipt-table td { padding: 10px 15px; border: 1px solid #ddd; text-align: left; font-size: 14px; }
        .receipt-table th { background-color: #f4f6f9; color: var(--primary); width: 35%; }
        .btn-container { text-align: center; margin-top: 25px; }
        .btn { display: inline-block; background-color: var(--primary); color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 15px; text-decoration: none; font-weight: bold; margin: 5px; }
        .btn-print { background-color: var(--secondary); }

        @media (max-width: 600px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .container { padding: 15px; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Matugga Hills Secondary School</h1>
        <p>Online Student Admission & Registration Portal</p>
    </div>

    <?php if ($submitted): ?>
        <!-- ========================================== -->
        <!-- 2. VIEW: SUCCESS RECEIPT                   -->
        <!-- ========================================== -->
        <div class="badge-success">
            ✔ Application Submitted Successfully! <br> Tracking ID: <strong><?php echo $app_id; ?></strong>
        </div>

        <table class="receipt-table">
            <tr><th>Tracking ID</th><td><?php echo $app_id; ?></td></tr>
            <tr><th>Submission Date</th><td><?php echo $submission_date; ?></td></tr>
            <tr><th>Student Name</th><td><?php echo $student_name; ?></td></tr>
            <tr><th>Target Class</th><td><?php echo $target_class; ?></td></tr>
            <tr><th>Previous School</th><td><?php echo $prev_school; ?></td></tr>
            <tr><th>Academic / Scores Info</th><td><?php echo $academic_details; ?></td></tr>
            <tr><th>Parent / Guardian</th><td><?php echo $parent_name . " (" . $relationship . ")"; ?></td></tr>
            <tr><th>Contact Phone</th><td><?php echo $primary_phone; ?></td></tr>
            <tr><th>Email Address</th><td><?php echo $email; ?></td></tr>
            <tr><th>Uploaded File</th><td><?php echo $uploaded_file_name; ?></td></tr>
        </table>

        <div class="btn-container">
            <button onclick="window.print()" class="btn btn-print">🖨 Print / Save as PDF</button>
            <a href="index.php" class="btn">⬅ Submit Another Application</a>
        </div>

    <?php else: ?>
        <!-- ========================================== -->
        <!-- 3. VIEW: APPLICATION FORM                  -->
        <!-- ========================================== -->
        <form action="index.php" method="POST" enctype="multipart/form-data">
            
            <!-- Student Bio Details -->
            <fieldset>
                <legend>1. Student Information</legend>
                <div class="form-group">
                    <label for="student_name">Full Name of Student *</label>
                    <input type="text" id="student_name" name="student_name" required placeholder="e.g., Mukasa John">
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="dob">Date of Birth *</label>
                        <input type="date" id="dob" name="dob" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender *</label>
                        <select id="gender" name="gender" required>
                            <option value="">-- Select Gender --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="prev_school">Previous School Attended *</label>
                    <input type="text" id="prev_school" name="prev_school" required placeholder="Name of previous primary or secondary school">
                </div>
            </fieldset>

            <!-- Academic & Class Selection -->
            <fieldset>
                <legend>2. Academic & Class Requirements</legend>
                <div class="form-group">
                    <label for="target_class">Class Applying For *</label>
                    <select id="target_class" name="target_class" required onchange="toggleAcademicSections()">
                        <option value="">-- Select Target Class --</option>
                        <option value="Senior One">Senior One (S.1)</option>
                        <option value="Senior Two">Senior Two (S.2)</option>
                        <option value="Senior Three">Senior Three (S.3)</option>
                        <option value="Senior Five">Senior Five (S.5)</option>
                    </select>
                </div>

                <!-- SENIOR ONE DYNAMIC SECTION -->
                <div id="sec_s1" class="dynamic-section">
                    <h4 style="margin-bottom: 10px; color: var(--primary);">Senior One - PLE Results Breakdown</h4>
                    <div class="form-group">
                        <label>PLE Total Aggregate:</label>
                        <input type="text" name="ple_aggregate" placeholder="e.g., 8">
                    </div>
                    <label>Subject Grades (Distinction, Credit, Pass):</label>
                    <div class="grid-2" style="margin-top: 5px;">
                        <input type="text" name="s1_math" placeholder="Mathematics Grade (e.g., D1)">
                        <input type="text" name="s1_english" placeholder="English Grade (e.g., D2)">
                        <input type="text" name="s1_science" placeholder="Integrated Science (e.g., C3)">
                        <input type="text" name="s1_sst" placeholder="Social Studies (e.g., C4)">
                    </div>
                </div>

                <!-- SENIOR 2 & 3 DYNAMIC SECTION -->
                <div id="sec_s23" class="dynamic-section">
                    <h4 style="margin-bottom: 10px; color: var(--primary);">Senior 2 / Senior 3 - Previous Academic Performance</h4>
                    <div class="form-group">
                        <label>Previous Class Completed:</label>
                        <input type="text" name="prev_class_completed" placeholder="e.g., Senior One">
                    </div>
                    <label>Average Term Grades / Scores from Last Report Card:</label>
                    <div class="grid-3" style="margin-top: 5px;">
                        <input type="text" name="s23_math" placeholder="Math">
                        <input type="text" name="s23_english" placeholder="English">
                        <input type="text" name="s23_physics" placeholder="Physics">
                        <input type="text" name="s23_chemistry" placeholder="Chemistry">
                        <input type="text" name="s23_biology" placeholder="Biology">
                        <input type="text" name="s23_humanities" placeholder="History / Geo">
                    </div>
                </div>

                <!-- SENIOR FIVE DYNAMIC SECTION -->
                <div id="sec_s5" class="dynamic-section">
                    <h4 style="margin-bottom: 10px; color: var(--primary);">Senior Five - UCE Results & Subject Combinations</h4>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>UCE Aggregate / Division:</label>
                            <input type="text" name="uce_results" placeholder="e.g., Division 1 (Agg 18)">
                        </div>
                        <div class="form-group">
                            <label>Desired Subject Combination:</label>
                            <input type="text" name="desired_combination" placeholder="e.g., PCM/ICT or HGL/Sub-Math">
                        </div>
                    </div>
                    <label>Core UCE Grades in Relevant Subjects:</label>
                    <div class="grid-3" style="margin-top: 5px;">
                        <input type="text" name="s5_math" placeholder="Math Grade">
                        <input type="text" name="s5_english" placeholder="English Grade">
                        <input type="text" name="s5_physics" placeholder="Physics Grade">
                        <input type="text" name="s5_chemistry" placeholder="Chemistry Grade">
                        <input type="text" name="s5_biology" placeholder="Biology Grade">
                    </div>
                </div>

                <!-- FILE UPLOAD -->
                <div class="form-group" style="margin-top: 20px;">
                    <label for="transcript">Upload Previous Report Card or Result Slip (PDF/Image):</label>
                    <input type="file" id="transcript" name="transcript" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </fieldset>

            <!-- Parent / Guardian Information -->
            <fieldset>
                <legend>3. Parent / Guardian Contact Details</legend>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="parent_name">Parent/Guardian Name *</label>
                        <input type="text" id="parent_name" name="parent_name" required placeholder="Full Name">
                    </div>
                    <div class="form-group">
                        <label for="relationship">Relationship to Student *</label>
                        <select id="relationship" name="relationship" required>
                            <option value="">-- Select Relationship --</option>
                            <option value="Father">Father</option>
                            <option value="Mother">Mother</option>
                            <option value="Guardian">Guardian</option>
                            <option value="Sponsor">Sponsor</option>
                        </select>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="primary_phone">Primary Phone Number *</label>
                        <input type="tel" id="primary_phone" name="primary_phone" required placeholder="e.g., 0770000000">
                    </div>
                    <div class="form-group">
                        <label for="alt_phone">Alternative Phone Number</label>
                        <input type="tel" id="alt_phone" name="alt_phone" placeholder="Optional">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email Address (Optional)</label>
                    <input type="email" id="email" name="email" placeholder="for sending confirmation notifications">
                </div>
                <div class="form-group">
                    <label for="residential_address">Physical / Residential Address *</label>
                    <textarea id="residential_address" name="residential_address" rows="2" required placeholder="Town, District, Village"></textarea>
                </div>
            </fieldset>

            <button type="submit" class="btn-submit">Submit Application</button>
        </form>
    <?php endif; ?>

</div>

<!-- JavaScript for Dynamic Section Toggling -->
<script>
function toggleAcademicSections() {
    var selectedClass = document.getElementById("target_class").value;
    
    // Hide all dynamic sections initially
    document.getElementById("sec_s1").style.display = "none";
    document.getElementById("sec_s23").style.display = "none";
    document.getElementById("sec_s5").style.display = "none";

    // Reveal specific section based on class selection
    if (selectedClass === "Senior One") {
        document.getElementById("sec_s1").style.display = "block";
    } else if (selectedClass === "Senior Two" || selectedClass === "Senior Three") {
        document.getElementById("sec_s23").style.display = "block";
    } else if (selectedClass === "Senior Five") {
        document.getElementById("sec_s5").style.display = "block";
    }
}
</script>

</body>
</html>