<?php
session_start();
$error = "";
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login | JOBER</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4e54c8;
            --primary-light: #7c3aed;
            --bg-glass: rgba(255, 255, 255, 0.9);
            --text-main: #1e293b;
            --text-muted: #64748b;
            --error: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
            background-color: #4e54c8;
            overflow: hidden;
            position: relative;
        }

        /* Home Button Styling */
        .home-button {
            position: absolute;
            top: 30px;
            left: 30px;
            text-decoration: none;
            color: white;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 10;
        }

        .home-button:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-5px);
        }

        .login-card {
            background: var(--bg-glass);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: cardEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: scale(0.9) translateY(30px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .login-card h1 {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-main);
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: -1px;
        }

        .subtitle {
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 30px;
        }

        .error-banner {
            background: #fef2f2;
            color: var(--error);
            padding: 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #fee2e2;
        }

        .input-group { margin-bottom: 20px; }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
            margin-left: 4px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            color: var(--text-muted);
            font-size: 16px;
            transition: 0.3s;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            background: #f8fafc;
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 15px;
            color: var(--text-main);
            transition: all 0.3s ease;
        }

        .input-wrapper input:focus {
            background: #fff;
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(78, 84, 200, 0.1);
        }

        .input-wrapper input:focus + i { color: var(--primary); }

        .forgot-pass { text-align: right; margin-top: -10px; margin-bottom: 25px; }

        .forgot-pass a {
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
            transition: 0.3s;
        }

        .forgot-pass a:hover { color: var(--primary-light); text-decoration: underline; }

        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 10px 15px -3px rgba(78, 84, 200, 0.3);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(78, 84, 200, 0.4);
            filter: brightness(1.1);
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: var(--text-muted);
            border-top: 1px solid rgba(0,0,0,0.05);
            padding-top: 20px;
        }

        .footer-text a { color: var(--primary); text-decoration: none; font-weight: 700; }
        .footer-text a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<a href="homePage.php" class="home-button">
    <i class="fa-solid fa-house"></i> Back to Home
</a>

<div class="login-card">
    <h1>Welcome Back</h1>
    <p class="subtitle">Enter your credentials to access your account</p>

    <?php if($error != ""): ?>
        <div class="error-banner">
            <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="../Back_End/user_login.php" method="post">
        
        <div class="input-group">
            <label>Aadhar Card Number</label>
            <div class="input-wrapper">
                <i class="fa-regular fa-id-card"></i>
                <input 
                    type="text" 
                    name="aadhar" 
                    placeholder="12-digit Aadhar number" 
                    maxlength="12" 
                    required
                >
            </div>
        </div>

        <div class="input-group">
            <label>Password</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-lock"></i>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="••••••••" 
                    required
                >
            </div>
        </div>

        <div class="forgot-pass">
            <a href="#">Forgot password?</a>
        </div>

        <button type="submit" class="login-btn">
            Sign In <i class="fa-solid fa-arrow-right-to-bracket"></i>
        </button>

    </form>

    <div class="footer-text">
        Don't have an account? <a href="register_user_page.php">Create Account</a>
    </div>
</div>

</body>
</html>