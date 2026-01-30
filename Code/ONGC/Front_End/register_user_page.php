<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOBER | Premium Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4e54c8;
            --primary-hover: #3f44a1;
            --accent: #ff9f43;
            --glass: rgba(255, 255, 255, 0.9);
            --text-dark: #1e293b;
            --text-light: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f1f5f9;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
            background-color: #4e54c8;
            padding: 40px 20px;
            overflow-x: hidden;
        }

        /* The Main Container */
        .register_user_page {
            width: 100%;
            max-width: 550px;
            perspective: 1000px;
        }

        .register_new_user_form {
            background: var(--glass);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: formAppear 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes formAppear {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .register_new_user_form_title {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-dark);
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .form-subtitle {
            text-align: center;
            color: var(--text-light);
            font-size: 14px;
            margin-bottom: 30px;
        }

        /* Dynamic Input Groups */
        .register_new_user_form_group {
            position: relative;
            margin-bottom: 24px;
        }

        .register_new_user_form_group label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            color: var(--text-dark);
            margin-bottom: 8px;
            transition: 0.3s;
        }

        .register_new_user_form_input {
            width: 100%;
            padding: 12px 16px;
            background: #f8fafc;
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 15px;
            color: var(--text-dark);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .register_new_user_form_input:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(78, 84, 200, 0.1);
            outline: none;
        }

        /* Radio Styling */
        .register_new_user_form_radio {
            display: flex;
            background: #f1f5f9;
            padding: 6px;
            border-radius: 12px;
            gap: 5px;
        }

        .register_new_user_form_radio label {
            flex: 1;
            text-align: center;
            margin: 0;
            padding: 10px;
            cursor: pointer;
            border-radius: 8px;
            transition: 0.3s;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .register_new_user_form_radio input { display: none; }

        .register_new_user_form_radio label:has(input:checked) {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(78, 84, 200, 0.3);
        }

        /* Submit Button with Ripple Effect */
        .register_new_user_form_button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), #7c3aed);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .register_new_user_form_button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(78, 84, 200, 0.4);
            filter: brightness(1.1);
        }

        .register_new_user_form_button:active {
            transform: translateY(0);
        }

        /* Bottom Section */
        .register_user_page_already_user {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(0,0,0,0.05);
            text-align: center;
        }

        .login_link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
            margin-left: 5px;
            transition: 0.3s;
        }

        .login_link:hover {
            color: var(--accent);
            text-decoration: underline;
        }

        /* Pulse Animation for file upload */
        input[type="file"]::file-selector-button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 15px;
            transition: 0.3s;
        }

        input[type="file"]::file-selector-button:hover {
            background: var(--accent);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .register_new_user_form { padding: 30px 20px; }
            .register_new_user_form_title { font-size: 22px; }
        }
    </style>
</head>

<body>
    <div class="register_user_page">
        <div class="register_new_user_form">
            <h1 class="register_new_user_form_title">Create Account</h1>
            <p class="form-subtitle">Join the JOBER community today.</p>

            <form action="../Back_End/user_register.php" method="post" enctype="multipart/form-data">

                <div class="register_new_user_form_group">
                    <label><i class="fa-solid fa-user"></i> Full Name</label>
                    <input type="text" name="full_name" class="register_new_user_form_input" placeholder="John Doe" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="register_new_user_form_group">
                        <label><i class="fa-solid fa-image"></i> Profile Photo</label>
                        <input type="file" name="photo" class="register_new_user_form_input" accept="image/*" required>
                    </div>
                    <div class="register_new_user_form_group">
                        <label><i class="fa-solid fa-calendar"></i> Date of Birth</label>
                        <input type="date" name="dob" class="register_new_user_form_input" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="register_new_user_form_group">
                        <label><i class="fa-solid fa-id-card"></i> Aadhar Number</label>
                        <input type="text" name="aadhar" class="register_new_user_form_input" placeholder="1234..." maxlength="12" required>
                    </div>
                    <div class="register_new_user_form_group">
                        <label><i class="fa-solid fa-phone"></i> Mobile Number</label>
                        <input type="text" name="mobile" class="register_new_user_form_input" placeholder="9876..." maxlength="10" required>
                    </div>
                </div>

                <div class="register_new_user_form_group">
                    <label>Gender</label>
                    <div class="register_new_user_form_radio">
                        <label><input type="radio" name="gender" value="Male" checked> Male</label>
                        <label><input type="radio" name="gender" value="Female"> Female</label>
                        <label><input type="radio" name="gender" value="Other"> Other</label>
                    </div>
                </div>

                <div class="register_new_user_form_group">
                    <label><i class="fa-solid fa-briefcase"></i> Experience Level</label>
                    <select name="experience" class="register_new_user_form_input" required>
                        <option value="">Select your experience</option>
                        <option>Fresher</option>
                        <option>1 Year</option>
                        <option>2 Years</option>
                        <option>3+ Years</option>
                    </select>
                </div>

                <div class="register_new_user_form_group">
                    <label><i class="fa-solid fa-lock"></i> Secure Password</label>
                    <input type="password" name="password" class="register_new_user_form_input" placeholder="••••••••" required>
                </div>

                <button type="submit" class="register_new_user_form_button">
                    Complete Registration <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
                </button>

            </form>

            <div class="register_user_page_already_user">
                <p>Already a member?<a href="user_login_page.php" class="login_link">Login Here</a></p>
            </div>
        </div>
    </div>
</body>
</html>