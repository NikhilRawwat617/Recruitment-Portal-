<?php


session_start();
require "../Back_End/db.php"; 

// --- 1. Session Check  ---
if (!isset($_SESSION['company_id'])) {
    header("Location: homePage_recruit.php");
    exit;
}

$company_id    = $_SESSION['company_id'];
$company_name  = $_SESSION['company_name'];
$company_photo = $_SESSION['company_photo'];


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// --- 2. HELPER FUNCTIONS ---
function sanitize($conn, $input) {
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($input)));
}

function getStats($conn, $cid) {
    $stats = [];
    $stats['total_jobs'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM jobs WHERE company_id=$cid"))['c'];
    $stats['active_jobs'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM jobs WHERE company_id=$cid AND status='active'"))['c'];
    $stats['total_applicants'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM applications a JOIN jobs j ON a.job_id=j.job_id WHERE j.company_id=$cid"))['c'];
    return $stats;
}

// --- 3. POST REQUEST  ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
 
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security Token Mismatch");
    }

    // A. Add Job
    if (isset($_POST['add_job'])) {
        $stmt = mysqli_prepare($conn, "INSERT INTO jobs (company_id, job_title, job_description, experience_required, location, salary_min, salary_max, salary_currency, status) VALUES (?,?,?,?,?,?,?,?, 'active')");
        mysqli_stmt_bind_param($stmt, "issssdds", $company_id, $_POST['job_title'], $_POST['job_description'], $_POST['experience'], $_POST['location'], $_POST['salary_min'], $_POST['salary_max'], $_POST['currency']);
        mysqli_stmt_execute($stmt);
        $_SESSION['flash_msg'] = "Job Posted Successfully!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // B. Toggle Job Status
    if (isset($_POST['toggle_job_status'])) {
        $job_id = (int)$_POST['job_id'];
        $new_status = sanitize($conn, $_POST['new_status']);
        mysqli_query($conn, "UPDATE jobs SET status='$new_status' WHERE job_id=$job_id AND company_id=$company_id");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // C. Update Application Status
    if (isset($_POST['update_app_status'])) {
        $app_id = (int)$_POST['application_id'];
        $status = sanitize($conn, $_POST['status']);
        mysqli_query($conn, "UPDATE applications a JOIN jobs j ON a.job_id=j.job_id SET a.status='$status' WHERE a.application_id=$app_id AND j.company_id=$company_id");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // D. Update Company Profile
    if (isset($_POST['update_profile'])) {
        $name = sanitize($conn, $_POST['company_name']);
        if (!empty($_FILES['company_photo']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['company_photo']['name'], PATHINFO_EXTENSION));
            if(in_array($ext, $allowed)){
                $file = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", $_FILES['company_photo']['name']);
                move_uploaded_file($_FILES['company_photo']['tmp_name'], "../uploads/company_photos/$file");
                mysqli_query($conn, "UPDATE companies SET company_name='$name', company_photo='$file' WHERE company_id=$company_id");
                $_SESSION['company_photo'] = $file;
            }
        } else {
            mysqli_query($conn, "UPDATE companies SET company_name='$name' WHERE company_id=$company_id");
        }
        $_SESSION['company_name'] = $name;
        $_SESSION['flash_msg'] = "Profile Updated!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    
    // E. Export to CV
    if (isset($_POST['export_csv'])) {
        $jid = (int)$_POST['job_id'];
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="applicants_job_'.$jid.'.csv"');
        $output = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($output, array('Name', 'Mobile', 'Experience', 'Gender', 'DOB', 'Status', 'Applied Date'));
        
        // Fetch specific columns based on your table
        $q = mysqli_query($conn, "SELECT u.full_name, u.mobile, u.experience, u.gender, u.dob, a.status, a.applied_at 
                                  FROM applications a 
                                  JOIN user_info u ON a.user_id=u.user_id 
                                  WHERE a.job_id=$jid");
        
        while ($row = mysqli_fetch_assoc($q)) fputcsv($output, $row);
        fclose($output);
        exit;
    }
}

// --- 4. DATA FETCH ---
$stats = getStats($conn, $company_id);
$jobs_query = mysqli_query($conn, "SELECT * FROM jobs WHERE company_id=$company_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruiter Hub | <?= htmlspecialchars($company_name) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --bg: #0f172a;
            --sidebar-bg: #1e293b;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body {
            background-color: var(--bg);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            padding: 2rem;
            position: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .brand { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            margin-bottom: 3rem; 
            font-size: 1.25rem; 
            font-weight: 700; 
            color: white;
        }
        
        .nav-links { 
            list-style: none; 
            flex: 1; 
        }
        .nav-item {
            padding: 12px 16px;
            margin-bottom: 8px;
            border-radius: 8px;
            cursor: pointer;
            color: var(--text-muted);
            transition: 0.2s;
            display: flex; align-items: center; gap: 12px;
            font-weight: 500;
        }
        .nav-item:hover, .nav-item.active { 
            background: var(--primary); 
            color: white; }
        
        .profile-mini {
            border-top: 1px solid var(--border);
            padding-top: 1.5rem;
            display: flex; align-items: center; gap: 10px;
        }
        .profile-mini img { 
            width: 40px; 
            height: 40px; 
            border-radius: 10px; 
            object-fit: cover; }

        /* --- Main Content --- */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 2.5rem;
        }

        /* --- Stats Grid --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 3rem;
        }
        .stat-card {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
        }
        .stat-val { 
            font-size: 2rem; 
            font-weight: 700; 
            color: white; 
        }
        .stat-label { 
            color: var(--text-muted); 
            font-size: 0.9rem; 
        }
        .stat-icon { 
            width: 50px; 
            height: 50px; 
            border-radius: 12px; 
            background: rgba(99, 102, 241, 0.1); 
            color: var(--primary);
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.2rem;
        }

        /* --- Tabs --- */
        .tab-content { 
            display: none; 
            animation: fadeIn 0.4s ease; 
        }
        .tab-content.active { 
            display: block; 
        }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* --- Job Cards --- */
        .job-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .job-header {
             display: flex; 
             justify-content: space-between; 
             align-items: flex-start; 
             margin-bottom: 1rem; 
        }
        .job-meta { 
            display: flex; 
            gap: 20px; 
            color: var(--text-muted); 
            font-size: 0.9rem; 
            margin-bottom: 1.5rem; 
        }
        
        .badge { 
            padding: 4px 10px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
        }

        .badge.active {
             background: rgba(16, 185, 129, 0.2); 
             color: var(--success); 
        }

        .badge.cancelled {
             background: rgba(239, 68, 68, 0.2); 
             color: var(--danger); 
        }

        .badge.Accepted { 
                background: var(--success); color: white; 
        }

        .badge.Applied { 
                background: var(--warning); color: black; 
        }

        /* --- Applicants --- */
        .applicant-list {
            margin-top: 1.5rem;
            background: rgba(0,0,0,0.2);
            border-radius: 12px;
            overflow: hidden;
        }
        .applicant-row {
            padding: 1rem;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid var(--border);
        }
        .applicant-row:last-child { border-bottom: none; }
        
        /* --- Forms & Inputs --- */
        input, select, textarea {
            width: 100%; 
            padding: 12px; 
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border); 
            border-radius: 8px; 
            color: white; 
            margin-bottom: 15px;
        }
        input:focus { outline: none; border-color: var(--primary); }

        /* --- Buttons --- */
        .btn { padding: 10px 20px;
             border-radius: 8px; border: none; 
             cursor: pointer; font-weight: 600;
             font-size: 0.9rem; transition: 0.2s;
             display: inline-flex; align-items: center; 
            gap: 8px; 
        }
        .btn-primary { 
            background: var(--primary); 
            color: white; 
        }
        .btn-primary:hover { 
            background: var(--primary-dark); 
        }
        .btn-outline { 
            background: transparent; 
            border: 1px solid var(--border); 
            color: var(--text-muted); 
        }
        .btn-outline:hover { 
            border-color: var(--text-main); 
            color: var(--text-main); 
        }
        .btn-sm { 
            padding: 6px 12px; 
            font-size: 0.8rem; 
        }
        .btn-danger { 
            background: rgba(239, 68, 68, 0.15); 
            color: var(--danger); 
        }

        /* --- Modals --- */
        .modal {
            display: none; 
            position: fixed; 
            inset: 0; 
            background: rgba(0,0,0,0.8); 
            backdrop-filter: blur(5px);
            z-index: 1000; 
            align-items: center; 
            justify-content: center;
        }
        .modal-content {
            background: var(--sidebar-bg); 
            width: 100%; 
            max-width: 600px;
            padding: 2rem; 
            border-radius: 20px; 
            border: 1px solid var(--border);
            max-height: 90vh; 
            overflow-y: auto;
        }

        /* --- Utilities --- */
        .toast { 
            position: fixed; 
            top: 20px; right: 20px; 
            background: var(--success); 
            color: white; padding: 1rem; 
            border-radius: 10px; z-index: 2000; 
            animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }

    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="brand">
            <i class="fa fa-cube" style="color: var(--primary);"></i> JOBERS
        </div>
        
        <ul class="nav-links">
            <li class="nav-item active" onclick="switchTab('dashboard', this)">
                <i class="fa fa-chart-pie"></i> Overview
            </li>
            <li class="nav-item" onclick="switchTab('jobs', this)">
                <i class="fa fa-briefcase"></i> Manage Jobs
            </li>
            <li class="nav-item" onclick="switchTab('profile', this)">
                <i class="fa fa-building"></i> Company Profile
            </li>
            <li class="nav-item" onclick="openModal('passModal')">
                <i class="fa fa-shield-halved"></i> Security
            </li>
        </ul>

        <div class="profile-mini">
            <img src="../uploads/company_photos/<?= !empty($company_photo) ? $company_photo : 'default.png' ?>">
            <div style="flex: 1;">
                <h4 style="font-size: 0.9rem;"><?= htmlspecialchars($company_name) ?></h4>
                <a href="../Back_End/logout.php" style="color: var(--danger); font-size: 0.8rem; text-decoration: none;">Logout</a>
            </div>
        </div>
    </aside>

    <main class="main-content">
        
        <?php if(isset($_SESSION['flash_msg'])): ?>
            <div class="toast"><?= $_SESSION['flash_msg'] ?></div>
            <?php unset($_SESSION['flash_msg']); ?>
        <?php endif; ?>

        <div id="dashboard" class="tab-content active">
            <header style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1>Hello, Recruiter 👋</h1>
                    <p style="color: var(--text-muted);">Here's what's happening with your hiring pipeline today.</p>
                </div>
                <button class="btn btn-primary" onclick="openModal('addJobModal')"><i class="fa fa-plus"></i> Post New Job</button>
            </header>

            <div class="stats-grid">
                <div class="stat-card">
                    <div>
                        <div class="stat-val"><?= $stats['total_jobs'] ?></div>
                        <div class="stat-label">Total Jobs Posted</div>
                    </div>
                    <div class="stat-icon"><i class="fa fa-briefcase"></i></div>
                </div>
                <div class="stat-card">
                    <div>
                        <div class="stat-val"><?= $stats['active_jobs'] ?></div>
                        <div class="stat-label">Active Listings</div>
                    </div>
                    <div class="stat-icon" style="color: var(--success); background: rgba(16, 185, 129, 0.1);"><i class="fa fa-check-circle"></i></div>
                </div>
                <div class="stat-card">
                    <div>
                        <div class="stat-val"><?= $stats['total_applicants'] ?></div>
                        <div class="stat-label">Total Candidates</div>
                    </div>
                    <div class="stat-icon" style="color: var(--warning); background: rgba(245, 158, 11, 0.1);"><i class="fa fa-users"></i></div>
                </div>
            </div>

            <h3 style="margin-bottom: 1rem;">Recent Activity</h3>
            <div style="color: var(--text-muted); font-style: italic;">Select "Manage Jobs" to view full details.</div>
        </div>

        <div id="jobs" class="tab-content">
            <header style="margin-bottom: 2rem; display: flex; justify-content: space-between;">
                <h1>Job Listings</h1>
                <input type="text" placeholder="Search jobs..." style="width: 250px; margin-bottom: 0;" onkeyup="filterJobs(this.value)">
            </header>

            <?php if(mysqli_num_rows($jobs_query) == 0): ?>
                <div style="text-align: center; padding: 4rem; border: 2px dashed var(--border); border-radius: 16px;">
                    <i class="fa fa-folder-open" style="font-size: 3rem; color: var(--border); margin-bottom: 1rem;"></i>
                    <p>No jobs posted yet.</p>
                </div>
            <?php endif; ?>

            <?php 
            mysqli_data_seek($jobs_query, 0);
            while($j = mysqli_fetch_assoc($jobs_query)): 
            ?>
                <div class="job-card" data-title="<?= strtolower($j['job_title']) ?>">
                    <div class="job-header">
                        <div>
                            <h2><?= htmlspecialchars($j['job_title']) ?></h2>
                            <span class="badge <?= $j['status'] ?>"><?= $j['status'] ?></span>
                        </div>
                        <div>
                            <button class="btn btn-outline btn-sm" onclick="openModal('desc-<?= $j['job_id'] ?>')">Description</button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="job_id" value="<?= $j['job_id'] ?>">
                                <input type="hidden" name="new_status" value="<?= $j['status'] == 'active' ? 'cancelled' : 'active' ?>">
                                <button name="toggle_job_status" class="btn btn-sm <?= $j['status']=='active' ? 'btn-danger' : 'btn-primary' ?>">
                                    <?= $j['status']=='active' ? 'Close Job' : 'Re-open' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="job-meta">
                        <span><i class="fa fa-map-marker-alt"></i> <?= htmlspecialchars($j['location']) ?></span>
                        <span><i class="fa fa-money-bill"></i> <?= htmlspecialchars($j['salary_currency']) ?> <?= $j['salary_min'] ?> - <?= $j['salary_max'] ?></span>
                        <span><i class="fa fa-clock"></i> <?= htmlspecialchars($j['experience_required']) ?></span>
                    </div>

                    <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <h4><i class="fa fa-users"></i> Applicants</h4>
                            <form method="POST">
                                <input type="hidden" name="job_id" value="<?= $j['job_id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <button name="export_csv" class="btn btn-sm btn-outline"><i class="fa fa-download"></i> Export CSV</button>
                            </form>
                        </div>

                        <div class="applicant-list">
                            <?php
                            $aq = mysqli_query($conn, "SELECT a.*, u.* FROM applications a JOIN user_info u ON a.user_id=u.user_id WHERE a.job_id={$j['job_id']}");
                            if(mysqli_num_rows($aq) == 0) echo "<div style='padding:1rem; color:var(--text-muted); text-align:center;'>No applicants yet.</div>";
                            
                            while($a = mysqli_fetch_assoc($aq)):
                                // Updated JSON to match your table schema
                                $uJson = htmlspecialchars(json_encode([
                                    'name' => $a['full_name'], 
                                    'photo' => $a['photo'], 
                                    'dob' => $a['dob'],
                                    'gender' => $a['gender'],
                                    'mobile' => $a['mobile'], 
                                    'exp' => $a['experience'], 
                                    'info' => $a['other_info']
                                ]), ENT_QUOTES, 'UTF-8');
                            ?>
                                <div class="applicant-row">
                                    <div style="display: flex; gap: 12px; align-items: center;">
                                        <img src="../uploads/user_photos/<?= $a['photo'] ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                        <div>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($a['full_name']) ?></div>
                                            <span class="badge <?= $a['status'] ?>" style="font-size: 0.65rem;"><?= $a['status'] ?></span>
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn btn-sm btn-outline" onclick='viewCandidate(<?= $uJson ?>)'>View Details</button>
                                        <?php if($a['status'] != 'Accepted'): ?>
                                        <form method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="application_id" value="<?= $a['application_id'] ?>">
                                            <input type="hidden" name="status" value="Accepted">
                                            <button name="update_app_status" class="btn btn-sm btn-primary" style="background: var(--success);"><i class="fa fa-check"></i></button>
                                        </form>
                                        <form method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="application_id" value="<?= $a['application_id'] ?>">
                                            <input type="hidden" name="status" value="Rejected">
                                            <button name="update_app_status" class="btn btn-sm btn-danger"><i class="fa fa-times"></i></button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>

                <div class="modal" id="desc-<?= $j['job_id'] ?>">
                    <div class="modal-content">
                        <h2>Job Description</h2>
                        <div style="margin: 20px 0; color: var(--text-muted); line-height: 1.6;">
                            <?= nl2br(htmlspecialchars($j['job_description'])) ?>
                        </div>
                        <button class="btn btn-outline" onclick="closeModal('desc-<?= $j['job_id'] ?>')" style="width:100%">Close</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div id="profile" class="tab-content">
            <header><h1>Company Profile</h1></header>
            <div style="max-width: 600px; background: var(--card-bg); padding: 2rem; border-radius: 16px; border: 1px solid var(--border);">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <img src="../uploads/company_photos/<?= !empty($company_photo) ? $company_photo : 'default.png' ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary);">
                        <div style="margin-top: 10px; color: var(--text-muted);">Current Logo</div>
                    </div>
                    
                    <label>Company Name</label>
                    <input type="text" name="company_name" value="<?= htmlspecialchars($company_name) ?>" required>
                    
                    <label>Update Logo</label>
                    <input type="file" name="company_photo" accept="image/*">
                    
                    <button name="update_profile" class="btn btn-primary" style="width: 100%;">Save Changes</button>
                </form>
            </div>
        </div>

    </main>

    <div class="modal" id="addJobModal">
        <div class="modal-content">
            <h2 style="margin-bottom: 20px;">Post a New Job</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <input name="job_title" placeholder="Job Title (e.g. Senior Developer)" required>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <input name="location" placeholder="Location" required>
                    <select name="experience">
                        <option>Fresher</option>
                        <option>1-2 Years</option>
                        <option>3-5 Years</option>
                        <option>5+ Years</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <select name="currency" style="width: 80px;"><option>INR</option><option>USD</option></select>
                    <input type="number" name="salary_min" placeholder="Min Salary" required>
                    <input type="number" name="salary_max" placeholder="Max Salary" required>
                </div>

                <textarea name="job_description" rows="5" placeholder="Job Description & Requirements..." required></textarea>
                
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button name="add_job" class="btn btn-primary" style="flex: 1;">Post Job</button>
                    <button type="button" class="btn btn-outline" onclick="closeModal('addJobModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="candidateModal">
        <div class="modal-content">
            <div style="text-align: center;">
                <img id="c-photo" src="" style="width: 100px; height: 100px; border-radius: 50%; border: 2px solid var(--primary); object-fit: cover;">
                <h2 id="c-name" style="margin-top: 10px;"></h2>
                <div id="c-exp" style="color: var(--primary); font-weight: 600;"></div>
            </div>
            
            <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-top: 1.5rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                    <p><strong><i class="fa fa-phone"></i> Mobile:</strong> <br><span id="c-mobile" style="color:var(--text-muted)"></span></p>
                    <p><strong><i class="fa fa-venus-mars"></i> Gender:</strong> <br><span id="c-gender" style="color:var(--text-muted)"></span></p>
                    <p><strong><i class="fa fa-birthday-cake"></i> DOB:</strong> <br><span id="c-dob" style="color:var(--text-muted)"></span></p>
                </div>
                
                <hr style="border: 0; border-top: 1px solid var(--border); margin: 15px 0;">
                
                <p><strong><i class="fa fa-info-circle"></i> Other Information:</strong></p>
                <p id="c-info" style="color: var(--text-muted); line-height: 1.5; margin-top: 5px;"></p>
            </div>
            <button class="btn btn-outline" onclick="closeModal('candidateModal')" style="width: 100%; margin-top: 20px;">Close</button>
        </div>
    </div>

    <div class="modal" id="passModal">
        <div class="modal-content">
            <h2>Change Password</h2>
            <form id="passwordForm">
                <input type="password" name="current_password" placeholder="Current Password" required>
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Update</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('passModal')" style="width: 100%; margin-top: 10px;">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        // --- Tab Logic ---
        function switchTab(tabId, element) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            if(element) element.classList.add('active');
        }

        // --- Modal Logic ---
        function openModal(id) { document.getElementById(id).style.display = 'flex'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
        
        // Close modal on outside click
        window.onclick = function(e) { if(e.target.classList.contains('modal')) e.target.style.display = 'none'; }

        // --- Dynamic Candidate Modal (UPDATED) ---
        function viewCandidate(data) {
            document.getElementById('c-photo').src = "../uploads/user_photos/" + data.photo;
            document.getElementById('c-name').textContent = data.name;
            document.getElementById('c-mobile').textContent = data.mobile;
            document.getElementById('c-exp').textContent = data.exp;
            document.getElementById('c-dob').textContent = data.dob;
            document.getElementById('c-gender').textContent = data.gender;
            document.getElementById('c-info').textContent = data.info || "No other info provided.";
            openModal('candidateModal');
        }

        // --- Filter Jobs ---
        function filterJobs(text) {
            text = text.toLowerCase();
            document.querySelectorAll('.job-card').forEach(card => {
                const title = card.getAttribute('data-title');
                card.style.display = title.includes(text) ? 'block' : 'none';
            });
        }

        // --- AJAX Password Change ---
        document.getElementById('passwordForm').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('change_password', '1');
            try {
                // Ensure the path to your PHP script is correct
                const res = await fetch('../Back_End/change_password_recruiter.php', { method: 'POST', body: formData });
                const json = await res.json();
                alert(json.message); 
                if(json.status === 'success') {
                    this.reset();
                    closeModal('passModal');
                }
            } catch(err) {
                console.error(err);
                alert("Password update request sent.");
            }
        };

        // Auto-hide toast
        setTimeout(() => {
            const toast = document.querySelector('.toast');
            if(toast) toast.style.display = 'none';
        }, 3000);
    </script>
</body>
</html>