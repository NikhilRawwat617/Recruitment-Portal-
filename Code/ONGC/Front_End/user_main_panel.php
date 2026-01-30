<?php
session_start();
require "../Back_End/db.php";


if (isset($_SESSION['msg'])): ?>
    <div id="flash-msg" class="toast-msg">
        <?= htmlspecialchars($_SESSION['msg']); ?>
    </div>
<?php
    unset($_SESSION['msg']);
endif;

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login_page.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$jobs = mysqli_query($conn,"
    SELECT j.*, c.company_name, c.company_photo
    FROM jobs j
    JOIN companies c ON j.company_id=c.company_id
    ORDER BY j.created_at DESC
");

$applied = [];
$r = mysqli_query($conn,"SELECT job_id,status FROM applications WHERE user_id=$user_id");
while($row=mysqli_fetch_assoc($r)){
    $applied[$row['job_id']]=$row['status'];
}

$applied_jobs = mysqli_query($conn,"
    SELECT j.job_title, c.company_name, c.company_photo, a.status, a.applied_at
    FROM applications a
    JOIN jobs j ON a.job_id=j.job_id
    JOIN companies c ON j.company_id=c.company_id
    WHERE a.user_id=$user_id
    ORDER BY a.applied_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | JOBER</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

:root {
    --primary: #2563eb;
    --primary-hover: #1e40af;
    --success: #22c55e;
    --danger: #ef4444;
    --warning: #facc15;
    --bg-dark: #020617;
    --bg-light: #0f172a;
    --glass: rgba(255, 255, 255, 0.05);
    --glass-border: rgba(255, 255, 255, 0.1);
    --text-main: #ffffff;
    --text-dim: rgba(255, 255, 255, 0.6);
    --sidebar-width: 260px;
}

* { 
    margin:0; 
    padding:0; 
    box-sizing:border-box; 
    font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif; 
}

body {
    background: radial-gradient(circle at top right, var(--bg-light), var(--bg-dark));
    color: var(--text-main);
    min-height: 100vh;
    overflow: hidden; /* Main wrapper handles scroll */
}

/* ===== LAYOUT ===== */
.wrapper { display: flex; height: 100vh; overflow: hidden; }

/* ===== SIDEBAR ===== */
.sidebar {
    width: var(--sidebar-width);
    background: rgba(15, 23, 42, 0.8);
    backdrop-filter: blur(20px);
    border-right: 1px solid var(--glass-border);
    padding: 30px 20px;
    display: flex;
    flex-direction: column;
    transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1000;
}

.sidebar h2 {
    font-size: 24px;
    letter-spacing: 2px;
    text-align: center;
    margin-bottom: 40px;
    color: var(--primary);
    font-weight: 800;
}

.sidebar-menu { 
    list-style: none; 
    flex: 1; 
}

.sidebar li {
    padding: 14px 18px;
    margin-bottom: 8px;
    border-radius: 14px;
    cursor: pointer;
    transition: 0.3s;
    display: flex;
    align-items: center;
    gap: 15px;
    color: var(--text-dim);
    font-weight: 500;
}

.sidebar li i { 
    font-size: 18px; 
    width: 25px; 
}

.sidebar li:hover {
    background: var(--glass);
    color: var(--text-main);
    transform: translateX(5px);
}

.sidebar li.active {
    background: var(--primary);
    color: white;
    box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
}

.sidebar a { 
    color: inherit; 
    text-decoration: none; 
    display: flex; 
    align-items: center; 
    gap: 15px; 
    width: 100%; 
}

/* ===== MAIN CONTENT ===== */
.main {
    flex: 1;
    padding: 30px;
    overflow-y: auto;
    scroll-behavior: smooth;
}

/* ===== SEARCH BAR ===== */
.search-container {
    position: sticky;
    top: 0;
    z-index: 100;
    margin-bottom: 30px;
    animation: fadeInDown 0.6s ease;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 15px;
    background: rgba(255, 255, 255, 0.07);
    backdrop-filter: blur(10px);
    padding: 16px 25px;
    border-radius: 18px;
    border: 1px solid var(--glass-border);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    transition: 0.3s;
}

.search-box:focus-within {
    border-color: var(--primary);
    background: rgba(255, 255, 255, 0.1);
}

.search-box input {
    background: transparent;
    border: none;
    outline: none;
    color: white;
    width: 100%;
    font-size: 16px;
}

/* ===== JOB CARDS ===== */
.panel { 
    display: none; 
    animation: fadeInUp 0.5s ease forwards; 
}
.panel.active { 
    display: block; 
}

.jobs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
}

.card {
    background: var(--glass);
    border: 1px solid var(--glass-border);
    backdrop-filter: blur(12px);
    border-radius: 24px;
    padding: 25px;
    transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
}

.card:hover {
    transform: translateY(-10px);
    background: rgba(255, 255, 255, 0.08);
    border-color: var(--primary);
}

.card img {
    width: 55px; height: 55px;
    border-radius: 14px;
    margin-bottom: 15px;
    background: #fff;
    padding: 5px;
    object-fit: contain;
}

.card h3 { 
    font-size: 1.2rem; 
    margin-bottom: 5px; 
}
.card p { 
    color: var(--text-dim); 
    font-size: 0.9rem; 
    margin-bottom: 15px; 
}

/* ===== BUTTONS ===== */
.btn {
    width: 100%;
    padding: 12px;
    border-radius: 14px;
    border: none;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 10px;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-view { 
    background: var(--glass); 
    color: white; 
    border: 1px solid var(--glass-border); 
}
.btn-view:hover { 
    background: rgba(255,255,255,0.15); 
}

.btn-apply { 
    background: var(--primary); 
    color: white; 
}
.btn-apply:hover { 
    background: var(--primary-hover); 
    transform: scale(1.02); 
}

.btn.applied { 
    background: var(--success) !important; 
    cursor: default; 
}

/* ===== APPLIED JOBS LIST ===== */
.applied-grid { 
    display: flex; 
    flex-direction: column; 
    gap: 15px; 
}

.applied-card {
    background: var(--glass);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: 0.3s;
}

.status {
    margin-left: auto;
    padding: 6px 16px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
}
.status.pending { 
    background: rgba(250, 204, 21, 0.2); 
    color: var(--warning); 
}
.status.accepted { 
    background: rgba(34, 197, 94, 0.2); 
    color: var(--success); 
}
.status.rejected { 
    background: rgba(239, 68, 68, 0.2); 
    color: var(--danger); 
}

/* ===== MODAL ===== */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(8px);
    z-index: 2000;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-box {
    background: var(--bg-dark);
    border: 1px solid var(--glass-border);
    width: 100%;
    max-width: 550px;
    padding: 40px;
    border-radius: 30px;
    position: relative;
    box-shadow: 0 25px 50px rgba(0,0,0,0.5);
    animation: modalZoom 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* ===== TOAST MESSAGE ===== */
.toast-msg {
    position: fixed;
    top: 20px;
    right: 20px;
    background: var(--success);
    color: white;
    padding: 15px 25px;
    border-radius: 12px;
    z-index: 3000;
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    animation: slideLeft 0.5s ease forwards;
}

/* ===== ANIMATIONS ===== */
@keyframes fadeInUp { 
    from {
        opacity: 0; 
        transform: translateY(30px); 
    } to { 
        opacity: 1; 
        transform: translateY(0); 
    } 
}
@keyframes fadeInDown { 
    from { 
        opacity: 0; 
        transform: translateY(-30px); 
    } to { 
        opacity: 1; 
        transform: translateY(0); 
    } 
}
@keyframes modalZoom { 
    from { 
        opacity: 0; 
        transform: scale(0.8); 
    } to { 
        opacity: 1; 
        transform: scale(1); 
    } 
}
@keyframes slideLeft { 
    from { 
        transform: translateX(100%); 
        opacity: 0; 
    } to { 
        transform: translateX(0); 
        opacity: 1; 
    } 
}

/* ===== RESPONSIVE ===== */
@media (max-width: 900px) {
    .wrapper { 
        flex-direction: column; 
    }
    .sidebar {
        width: 100%;
        height: auto;
        padding: 15px;
        flex-direction: row;
        justify-content: space-around;
        border-right: none;
        border-bottom: 1px solid var(--glass-border);
    }
    .sidebar h2 { 
        display: none; 
    }
    .sidebar-menu { 
        display: flex; 
        gap: 10px; width: 100%; 
        justify-content: space-around; 
    }
    .sidebar li { 
        margin-bottom: 0; 
        padding: 10px; 
        font-size: 12px; 
        flex-direction: column; 
        gap: 5px; 
    }
    .sidebar li i { 
        font-size: 20px; 
        text-align: center; 
        width: auto; 
    }
}

input[type="password"] {
    background: var(--glass);
    border: 1px solid var(--glass-border);
    padding: 15px;
    border-radius: 12px;
    color: white;
    margin-bottom: 15px;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Flash message auto-hide
    const msg = document.getElementById("flash-msg");
    if (msg) {
        setTimeout(() => {
            msg.style.opacity = "0";
            setTimeout(() => msg.remove(), 500);
        }, 3000);
    }
});

function showPanel(id, el) {
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    document.querySelectorAll('.sidebar li').forEach(l => l.classList.remove('active'));
    el.classList.add('active');
}

function openModal(id) {
    document.getElementById('m' + id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById('m' + id).style.display = 'none';
    document.body.style.overflow = 'auto';
}

function filterJobs() {
    const input = document.getElementById('jobSearch').value.toLowerCase();
    const cards = document.querySelectorAll('.card');
    let visible = 0;

    cards.forEach(card => {
        const text = card.dataset.title + card.dataset.company + card.dataset.location + card.dataset.desc;
        if (text.includes(input)) {
            card.style.display = "block";
            visible++;
        } else {
            card.style.display = "none";
        }
    });

    document.getElementById('noResult').style.display = visible === 0 ? "block" : "none";
}
</script>
</head>

<body>
<div class="wrapper">

    <nav class="sidebar">
        <h2>JOBER</h2>
        <ul class="sidebar-menu">
            <li class="active" onclick="showPanel('jobs',this)"><i class="fa-solid fa-briefcase"></i> <span>Jobs</span></li>
            <li onclick="showPanel('applied',this)"><i class="fa-solid fa-circle-check"></i> <span>Applied</span></li>
            <li onclick="showPanel('password',this)"><i class="fa-solid fa-shield-halved"></i> <span>Security</span></li>
            <li style="margin-top: auto; color: var(--danger)">
                <a href="../Back_End/user_logout.php"><i class="fa-solid fa-power-off"></i> <span>Logout</span></a>
            </li>
        </ul>
    </nav>

    <main class="main">

        <div class="search-container">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="jobSearch" placeholder="Search by title, company, skills..." onkeyup="filterJobs()">
            </div>
            <p id="noResult" style="display:none; text-align:center; color:var(--danger); margin-top:20px;">No matching jobs found.</p>
        </div>

        <div id="jobs" class="panel active">
            <div class="jobs-grid">
                <?php while($j=mysqli_fetch_assoc($jobs)){ ?>
                    <div class="card" 
                         data-title="<?= strtolower($j['job_title']) ?>" 
                         data-company="<?= strtolower($j['company_name']) ?>" 
                         data-location="<?= strtolower($j['location']) ?>" 
                         data-desc="<?= strtolower($j['job_description']) ?>">
                        
                        <img src="../uploads/company_photos/<?= $j['company_photo'] ?: 'default.png' ?>" alt="logo">
                        <h3><?= htmlspecialchars($j['job_title']) ?></h3>
                        <p><i class="fa-solid fa-building"></i> <?= htmlspecialchars($j['company_name']) ?></p>
                        
                        <button class="btn btn-view" onclick="openModal(<?= $j['job_id'] ?>)">Details</button>

                        <?php if(isset($applied[$j['job_id']])){ ?>
                            <button class="btn applied"><i class="fa-solid fa-check"></i> Applied</button>
                        <?php } else { ?>
                            <form method="post" action="../Back_End/apply_job.php">
                                <input type="hidden" name="job_id" value="<?= $j['job_id'] ?>">
                                <button class="btn btn-apply">Apply Now</button>
                            </form>
                        <?php } ?>
                    </div>

                    <div class="modal" id="m<?= $j['job_id'] ?>" onclick="closeModal(<?= $j['job_id'] ?>)">
                        <div class="modal-box" onclick="event.stopPropagation()">
                            <span class="close" style="position:absolute; right:20px; top:20px; cursor:pointer; font-size:24px;" onclick="closeModal(<?= $j['job_id'] ?>)">&times;</span>
                            <h2 style="color:var(--primary)"><?= htmlspecialchars($j['job_title']) ?></h2>
                            <p style="margin-top:20px; line-height:1.6; color:var(--text-dim)"><?= nl2br(htmlspecialchars($j['job_description'])) ?></p>
                            <div style="margin-top:25px; padding-top:20px; border-top:1px solid var(--glass-border)">
                                <p><strong><i class="fa-solid fa-location-dot"></i> Location:</strong> <?= $j['location'] ?></p>
                                <p><strong><i class="fa-solid fa-money-bill-wave"></i> Salary:</strong> ₹<?= number_format($j['salary_min']) ?> - ₹<?= number_format($j['salary_max']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div id="applied" class="panel">
            <h2 style="margin-bottom:25px">Your Applications</h2>
            <div class="applied-grid">
                <?php while($a=mysqli_fetch_assoc($applied_jobs)){ ?>
                    <div class="applied-card">
                        <img src="../uploads/company_photos/<?= $a['company_photo'] ?: 'default.png' ?>">
                        <div>
                            <h3><?= htmlspecialchars($a['job_title']) ?></h3>
                            <p><?= htmlspecialchars($a['company_name']) ?></p>
                            <small style="color:var(--text-dim)"><?= date("d M Y", strtotime($a['applied_at'])) ?></small>
                        </div>
                        <span class="status <?= strtolower($a['status']) ?>"><?= ucfirst($a['status']) ?></span>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div id="password" class="panel">
            <div class="modal-box" style="max-width:450px; margin: 0 auto;">
                <h2>Security Settings</h2>
                <p style="color:var(--text-dim); margin-bottom:25px">Update your account password</p>
                <form method="post" action="../Back_End/change_password.php">
                    <input type="password" name="current" placeholder="Current Password" required style="width:100%">
                    <input type="password" name="new" placeholder="New Password" required style="width:100%">
                    <button class="btn btn-apply">Update Password</button>
                </form>
            </div>
        </div>

    </main>
</div>
</body>
</html>