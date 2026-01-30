<?php
require "../Back_End/db.php";

// 1. Fetch Latest Jobs
$jobs = mysqli_query($conn, "
    SELECT j.*, c.company_name, c.company_photo
    FROM jobs j
    JOIN companies c ON j.company_id = c.company_id
    ORDER BY j.created_at DESC
    LIMIT 6
");

// 2. Fetch Top Recruiters (Companies with most jobs)
$top_recruiters = mysqli_query($conn, "
    SELECT c.company_id, c.company_name, c.company_photo, COUNT(j.job_id) as job_count
    FROM companies c
    LEFT JOIN jobs j ON c.company_id = j.company_id
    GROUP BY c.company_id
    HAVING job_count > 0
    ORDER BY job_count DESC
    LIMIT 4
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOBER | Premium Career Portal</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-soft: #eff6ff;
            --secondary: #f97316;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-body: #f8fafc;
            --white: #ffffff;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* --- Navigation --- */
        header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1250px;
            margin: 0 auto;
            padding: 0 20px;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 26px;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            letter-spacing: -1px;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 15px;
            transition: 0.3s;
        }

        .nav-links a:hover { 
            color: var(--primary); 
        }

        .nav-auth { 
            display: flex; 
            gap: 12px; 
        }

        .btn {
            padding: 10px 22px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-outline { 
            border: 1.5px solid var(--primary); 
            color: var(--primary); 
            background: transparent; 
        }
        .btn-outline:hover {
             background: var(--primary-soft); 
            }
        .btn-primary { 
            background: var(--primary); 
            color: white; 
        }
        .btn-primary:hover { 
            background: #1d4ed8; 
            transform: translateY(-2px); 
        }
        .btn-secondary { 
            background: var(--secondary); 
            color: white; 
        }

        /* --- Hero Section --- */
        .hero {
            padding: 100px 20px 140px;
            text-align: center;
            background: radial-gradient(circle at top right, #e0e7ff, transparent), 
                        radial-gradient(circle at bottom left, #fff7ed, transparent);
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            color: #1e293b;
        }

        .hero p {
            font-size: 1.2rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto 40px;
        }

        /* --- Professional Search Bar --- */
        .search-container {
            max-width: 850px;
            margin: 0 auto;
            background: var(--white);
            padding: 8px;
            border-radius: 100px;
            display: flex;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
            border: 1px solid var(--border);
        }

        .search-container input, .search-container select {
            border: none;
            padding: 15px 25px;
            outline: none;
            font-size: 15px;
            background: transparent;
        }

        .search-container input { flex: 2; border-right: 1px solid var(--border); }
        .search-container select { flex: 1; color: var(--text-muted); }

        .search-btn {
            background: var(--primary);
            color: white;
            padding: 0 40px;
            border-radius: 100px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        /* --- Stats Panel --- */
        .stats-wrapper {
            max-width: 1100px;
            margin: -60px auto 60px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 0 20px;
        }

        .stat-card {
            background: var(--white);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            border: 1px solid var(--border);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.02);
        }

        .stat-card h3 { font-size: 28px; color: var(--primary); margin-bottom: 5px; }
        .stat-card p { font-size: 14px; color: var(--text-muted); font-weight: 600; }

        /* --- Top Recruiters Section --- */
        .section-container { max-width: 1250px; margin: 60px auto; padding: 0 20px; }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 40px;
        }

        .recruiters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
        }

        .recruiter-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.4s ease;
            position: relative;
        }

        .recruiter-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
            box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        }

        .recruiter-card img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            background: var(--bg-body);
            border-radius: 15px;
            padding: 10px;
            margin-bottom: 20px;
        }

        .recruiter-card h4 { 
            font-size: 18px; 
            margin-bottom: 8px; 
        }

        .job-count-badge {
            background: var(--primary-soft);
            color: var(--primary);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
        }

        .verified-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #10b981;
        }

        /* --- Job Feed Grid --- */
        .job-feed {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 25px;
        }

        .job-card {
            background: var(--white);
            border: 1px solid var(--border);
            padding: 25px;
            border-radius: 16px;
            transition: 0.3s;
            text-decoration: none;
            color: inherit;
        }

        .job-card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border-color: #cbd5e1;
        }

        .job-card-top { 
            display: flex; 
            gap: 15px; 
            margin-bottom: 20px; 
        }
        .job-card-top img { 
            width: 55px; 
            height: 55px; 
            border-radius: 12px; 
            object-fit: cover; 
        }

        .job-meta { 
            display: flex; 
            gap: 15px; 
            color: var(--text-muted); 
            font-size: 14px; 
            margin-bottom: 20px; 
        }
        .job-meta i { 
            color: var(--primary); 
        }

        .tag-row { 
            display: flex; 
            gap: 8px; 
        }
        .tag { 
            background: #f1f5f9; 
            padding: 5px 12px; 
            border-radius: 6px; 
            font-size: 12px; 
            font-weight: 600; 
            color: #475569; 
        }

        /* --- Mobile Responsive --- */
        @media (max-width: 768px) {
            .hero h1 { 
                font-size: 2.2rem; 
            }
            .nav-links { 
                display: none; 
            }
            .search-container { 
                flex-direction: column; 
                border-radius: 20px; 
                padding: 15px; 
            }
            .search-container input { 
                border-right: none; 
                border-bottom: 1px solid var(--border); 
                width: 100%; 
            }
            .search-btn { 
                height: 50px; 
                margin-top: 10px; 
                width: 100%; 
            }
            .job-feed { 
                grid-template-columns: 1fr; 
            }
        }
    </style>
</head>
<body>

<header>
    <div class="nav-container">
        <a href="#" class="logo">JOBER</a>
        <ul class="nav-links">
            <li><a href="#">Find Jobs</a></li>
            <li><a href="#">Companies</a></li>
            <li><a href="#">Resources</a></li>
        </ul>
        <div class="nav-auth">
            <a href="user_login_page.php" class="btn btn-outline">Login</a>
            <a href="register_user_page.php" class="btn btn-primary">Sign Up</a>
            <a href="homePage_recruit.php" class="btn btn-secondary">Employers</a>
        </div>
    </div>
</header>

<section class="hero">
    <h1>Find the career <br><span style="color: var(--primary);">you deserve.</span></h1>
    <p>Connecting the best talent with top companies worldwide. Your next big opportunity is just a search away.</p>
    
    <div class="search-container">
        <input type="text" placeholder="Job title, skills or company">
        <select>
            <option>Experience</option>
            <option>Fresher (0 years)</option>
            <option>Intermediate (1-3 yrs)</option>
            <option>Expert (5+ yrs)</option>
        </select>
        <button class="search-btn">Find Jobs</button>
    </div>
</section>

<div class="stats-wrapper">
    <div class="stat-card">
        <h3>25k+</h3>
        <p>Live Jobs</p>
    </div>
    <div class="stat-card">
        <h3>8k+</h3>
        <p>Companies</p>
    </div>
    <div class="stat-card">
        <h3>150+</h3>
        <p>New Daily Roles</p>
    </div>
    <div class="stat-card">
        <h3>12M</h3>
        <p>Job Seekers</p>
    </div>
</div>

<div class="section-container">
    <div class="section-header">
        <div>
            <h2 style="font-size: 30px;">Top Hiring Companies</h2>
            <p style="color: var(--text-muted);">Work with the world's leading brands and startups.</p>
        </div>
        <a href="#" class="btn btn-outline" style="padding: 8px 16px;">View All</a>
    </div>

    <div class="recruiters-grid">
        <?php while($rec = mysqli_fetch_assoc($top_recruiters)) { ?>
            <div class="recruiter-card">
                <i class="fa-solid fa-certificate verified-icon"></i>
                <img src="../uploads/company_photos/<?php echo $rec['company_photo'] ?: 'default.png'; ?>" alt="logo">
                <h4><?php echo htmlspecialchars($rec['company_name']); ?></h4>
                <div style="margin-top: 15px;">
                    <span class="job-count-badge"><?php echo $rec['job_count']; ?> Open Positions</span>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="section-container">
    <div class="section-header">
        <h2 style="font-size: 30px;">Latest Opportunities</h2>
    </div>

    <div class="job-feed">
        <?php while($job = mysqli_fetch_assoc($jobs)) { ?>
            <a href="user_login_page.php?id=<?php echo $job['job_id']; ?>" class="job-card">
                <div class="job-card-top">
                    <img src="../uploads/company_photos/<?php echo $job['company_photo'] ?: 'default.png'; ?>">
                    <div>
                        <h4 style="font-size: 18px; font-weight: 700;"><?php echo htmlspecialchars($job['job_title']); ?></h4>
                        <p style="color: var(--text-muted); font-size: 14px;"><?php echo htmlspecialchars($job['company_name']); ?></p>
                    </div>
                </div>

                <div class="job-meta">
                    <span><i class="fa-solid fa-location-dot"></i> <?php echo $job['location']; ?></span>
                    <span><i class="fa-solid fa-wallet"></i> ₹<?php echo $job['salary_min']; ?> - <?php echo $job['salary_max']; ?></span>
                </div>

                <div class="tag-row">
                    <span class="tag"><?php echo $job['experience_required']; ?></span>
                    <span class="tag">Full-Time</span>
                    <span class="tag" style="background: #fff7ed; color: #c2410c;">Urgent</span>
                </div>
            </a>
        <?php } ?>
    </div>
    
    <div style="text-align: center; margin-top: 50px;">
        <button class="btn btn-primary" style="padding: 15px 40px;">Browse All Jobs</button>
    </div>
</div>

<footer style="background: #0f172a; color: #94a3b8; padding: 60px 20px; text-align: center;">
    <h2 style="color: white; margin-bottom: 20px;">JOBER</h2>
    <p>The #1 Job Board for Modern Professionals.</p>
    <div style="margin: 20px 0; font-size: 20px;">
        <i class="fa-brands fa-linkedin" style="margin: 0 10px;"></i>
        <i class="fa-brands fa-twitter" style="margin: 0 10px;"></i>
        <i class="fa-brands fa-github" style="margin: 0 10px;"></i>
    </div>
    <hr style="border: 0; border-top: 1px solid #1e293b; margin: 30px 0;">
    <p style="font-size: 13px;">&copy; 2024 JOBER Inc. All rights reserved.</p>
</footer>

</body>
</html>