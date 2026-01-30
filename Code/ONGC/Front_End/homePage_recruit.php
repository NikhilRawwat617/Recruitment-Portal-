<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruiter | JOBER</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-glow: rgba(99, 102, 241, 0.4);
            --bg: #0f172a;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
            --error-bg: rgba(244, 63, 94, 0.1);
            --error-text: #f43f5e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0, transparent 50%), 
                radial-gradient(at 100% 100%, rgba(6, 182, 212, 0.1) 0, transparent 50%);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* --- NAVBAR --- */
        .recruit_home_page_nav_bar {
            padding: 15px 5%;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav h2 {
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(to right, #fff, var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 15px;
            align-items: center;
        }

        nav button {
            padding: 10px 22px;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            background: var(--glass);
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        nav button:hover {
            background: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 0 20px var(--primary-glow);
            transform: translateY(-2px);
        }

        /* --- MAIN LAYOUT --- */
        .recruit_home_page {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 8% 60px;
            min-height: 100vh;
            gap: 60px;
        }

        .recruit_home_page_top {
            flex: 1;
            max-width: 600px;
        }

        .tagline {
            color: var(--primary);
            font-weight: 800;
            letter-spacing: 4px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            line-height: 1.1;
            font-weight: 800;
            margin-bottom: 30px;
            color: #ffffff;
        }

        .subtitle {
            font-size: 1.1rem;
            color: var(--text-dim);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .subtitle i {
            color: var(--primary);
            width: 25px;
        }

        /* --- AUTH CARDS --- */
        .recuriter_login_wrapper {
            flex: 0 0 450px;
            position: relative;
        }

        .card {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card_tagline {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .card_title {
            font-size: 1.75rem;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }

        /* --- FORM ELEMENTS --- */
        .input_group {
            position: relative;
            margin-bottom: 25px;
        }

        .input_group input {
            width: 100%;
            padding: 12px 0;
            background: transparent;
            border: none;
            border-bottom: 1px solid var(--glass-border);
            color: white;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }

        .input_group label {
            position: absolute;
            top: 12px;
            left: 0;
            color: var(--text-dim);
            pointer-events: none;
            transition: 0.3s ease all;
        }

        .input_group input:focus ~ label,
        .input_group input:valid ~ label,
        .input_group input:not(:placeholder-shown) ~ label {
            top: -12px;
            font-size: 0.8rem;
            color: var(--primary);
            font-weight: 600;
        }

        .input_group input:focus {
            border-bottom-color: var(--primary);
        }

        input[type="file"] {
            padding-top: 25px;
            font-size: 0.8rem;
        }

        .auth_btn {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: none;
            background: var(--primary);
            color: white;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s ease;
            margin-top: 10px;
        }

        .auth_btn:hover {
            background: #4f46e5;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
            transform: translateY(-1px);
        }

        .card_footer {
            text-align: center;
            margin-top: 20px;
            color: var(--text-dim);
            font-size: 0.9rem;
        }

        .card_footer span {
            color: var(--primary);
            cursor: pointer;
            font-weight: 700;
            margin-left: 5px;
        }

        .terms {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            color: var(--text-dim);
        }

        .error_msg {
            color: var(--error-text);
            background: var(--error-bg);
            border: 1px solid rgba(244, 63, 94, 0.2);
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            font-size: 0.85rem;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .hidden { display: none; }

        @media (max-width: 968px) {
            .recruit_home_page {
                flex-direction: column;
                text-align: center;
                padding-top: 100px;
            }
            .subtitle { justify-content: center; }
            .recuriter_login_wrapper { width: 100%; max-width: 450px; }
        }
    </style>
</head>
<body>

<div class="app_wrapper">

    <div class="recruit_home_page_nav_bar">
        <nav>
            <ul>
                <li><h2>JOBER</h2></li>
            </ul>
            <ul>
                <li><button id="Register_button_homepage_recuriter" type="button">Register</button></li>
                <li><button onclick="location.href='homePage.php'" type="button">For Job Seekers</button></li>
            </ul>
        </nav>
    </div>

    <div class="recruit_home_page">

        <div class="recruit_home_page_top">
            <h3 class="tagline">TALENT DECODED</h3>
            <h1 class="title">Bringing Employers and Professionals Together</h1>
            <h4 class="subtitle"><i class="fa-regular fa-user"></i> 10+ Crore Profiles</h4>
            <h4 class="subtitle"><i class="fa-solid fa-arrow-trend-up"></i> Simplified Hiring</h4>
        </div>

        <div class="recuriter_login_wrapper">

            <div class="card" id="loginCard">
                <h3 class="card_tagline">WELCOME BACK</h3>
                <h2 class="card_title">Login</h2>

                <?php
                if(isset($_SESSION['login_error'])){
                    echo "<div class='error_msg'><i class='fa-solid fa-circle-exclamation'></i> ".$_SESSION['login_error']."</div>";
                    unset($_SESSION['login_error']);
                }
                ?>

                <form action="../Back_End/recruiter_login.php" method="POST">
                    <div class="input_group">
                        <input type="email" name="email" placeholder=" " required>
                        <label>Email</label>
                    </div>

                    <div class="input_group">
                        <input type="password" name="password" placeholder=" " required>
                        <label>Password</label>
                    </div>

                    <button class="auth_btn" type="submit" name="login">Login</button>
                    <p class="card_footer">Don’t have an account? <span id="openRegister">Register</span></p>
                </form>
            </div>

            <div class="card hidden" id="registerCard">
                <h3 class="card_tagline">CREATE ACCOUNT</h3>
                <h2 class="card_title">Recruiter Registration</h2>

                <form action="../Back_End/company_new_account_form.php" method="POST" enctype="multipart/form-data">
                    <div class="input_group">
                        <input type="text" name="company_name" placeholder=" " required>
                        <label>Company Name</label>
                    </div>

                    <div class="input_group">
                        <input type="text" name="recruiter_name" placeholder=" " required>
                        <label>Recruiter Name</label>
                    </div>

                    <div class="input_group">
                        <input type="email" name="email" placeholder=" " required>
                        <label>Email</label>
                    </div>

                    <div class="input_group">
                        <input type="tel" name="phone" placeholder=" " required>
                        <label>Phone Number</label>
                    </div>

                    <div class="input_group">
                        <input type="url" name="website" placeholder=" ">
                        <label>Company Website</label>
                    </div>

                    <div class="input_group">
                        <input type="file" name="company_photo" accept="image/*" required>
                        <label>Company Logo / Photo</label>
                    </div>

                    <div class="input_group">
                        <input type="password" name="password" placeholder=" " required>
                        <label>Password</label>
                    </div>

                    <div class="input_group">
                        <input type="password" name="confirm_password" placeholder=" " required>
                        <label>Confirm Password</label>
                    </div>

                    <div class="terms">
                        <input type="checkbox" required>
                        <span>I agree to Terms & Conditions</span>
                    </div>

                    <button class="auth_btn" type="submit">Register</button>
                    <p class="card_footer">Already have an account? <span id="backToLogin">Login</span></p>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
const loginCard = document.getElementById("loginCard");
const registerCard = document.getElementById("registerCard");
const openRegister = document.getElementById("openRegister");
const backToLogin = document.getElementById("backToLogin");
const topRegisterBtn = document.getElementById("Register_button_homepage_recuriter");

function showRegister() { 
    loginCard.classList.add("hidden"); 
    registerCard.classList.remove("hidden"); 
}
function showLogin() { 
    registerCard.classList.add("hidden"); 
    loginCard.classList.remove("hidden"); 
}

openRegister.onclick = showRegister;
backToLogin.onclick = showLogin;
topRegisterBtn.onclick = showRegister;
</script>
</body>
</html>