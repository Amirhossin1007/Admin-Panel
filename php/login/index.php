<?php

// Route: panel/login/index.php

session_start();

$bot_version = '1.0.0';
$php_version = phpversion();

$admins_file = __DIR__ . '/admins.json';
$max_attempts = 3;
$lockout_duration = 300;

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: ../dashboard");
    exit;
}

$is_locked_out = false;
$remaining_lockout = 0;

if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $max_attempts) {
    if (isset($_SESSION['lockout_time'])) {
        $elapsed = time() - $_SESSION['lockout_time'];
        if ($elapsed < $lockout_duration) {
            $is_locked_out = true;
            $remaining_lockout = $lockout_duration - $elapsed;
        } else {
            $_SESSION['login_attempts'] = 0;
            unset($_SESSION['lockout_time']);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_locked_out) {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    $authenticated = false;
    $needs_update = false;

    if (!file_exists($admins_file)) {
        if (!empty($user) && !empty($pass)) {
            $new_admins = [
                [
                    "username" => $user,
                    "password_hash" => password_hash($pass, PASSWORD_BCRYPT)
                ]
            ];
            
            if (file_put_contents($admins_file, json_encode($new_admins, JSON_PRETTY_PRINT))) {
                $authenticated = true;
            } else {
                $_SESSION['flash_error'] = "خطا در ایجاد فایل تنظیمات ادمین. لطفاً دسترسی‌ها را بررسی کنید.";
            }
        }
    } else {
        $admins = json_decode(file_get_contents($admins_file), true);
        if (is_array($admins)) {
            foreach ($admins as $key => $admin) {
                if ($admin['username'] === $user) {
                    if (!password_get_info($admin['password_hash'])['algo']) {
                        if ($admin['password_hash'] === $pass) {
                            $authenticated = true;
                            $admins[$key]['password_hash'] = password_hash($pass, PASSWORD_BCRYPT);
                            $needs_update = true;
                        }
                    } else {
                        if (password_verify($pass, $admin['password_hash'])) {
                            $authenticated = true;
                        }
                    }
                    break;
                }
            }
        }
        
        if ($authenticated && $needs_update) {
            file_put_contents($admins_file, json_encode($admins, JSON_PRETTY_PRINT));
        }
    }

    if ($authenticated) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $user;
        $_SESSION['login_attempts'] = 0;
        
        $_SESSION['last_activity'] = time();

        unset($_SESSION['lockout_time']);
        unset($_SESSION['flash_error']);

        if (isset($_SESSION['redirect_url']) && !empty($_SESSION['redirect_url'])) {
            $target_url = $_SESSION['redirect_url'];
            unset($_SESSION['redirect_url']); 
            header("Location: " . $target_url);
        } else {
            header("Location: ../dashboard");
        }
        exit;
    } else {
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        
        if ($_SESSION['login_attempts'] >= $max_attempts) {
            $_SESSION['lockout_time'] = time();
            $_SESSION['flash_error'] = "نام کاربری یا رمز عبور اشتباه است.";
        } else {
            $_SESSION['flash_error'] = "نام کاربری یا رمز عبور اشتباه است.";
        }
        
        header("Location: ./");
        exit;
    }
}

$error_msg = '';
if (isset($_SESSION['flash_error'])) {
    $error_msg = $_SESSION['flash_error'];
    if (!$is_locked_out) {
        unset($_SESSION['flash_error']);
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel | Login</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2300f2ff%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><circle cx=%2212%22 cy=%2212%22 r=%223%22></circle><path d=%22M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z%22></path></svg>">
    
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&family=Vazirmatn:wght@100;300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="ambient-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
    </div>

    <div class="login-page-wrapper">
        <div class="login-container">
            <div class="login-card">
                <div class="brand-section anim-up delay-1">
                    <i data-lucide="shield-check" class="brand-icon"></i>
                    <h1>LOGIN</h1>
                    <p>MANAGEMENT PANEL</p>
                </div>

                <?php if ($is_locked_out): ?>
                    <div class="lockout-box anim-up delay-2">
                        <i data-lucide="lock" style="margin-bottom:10px;"></i>
                        <div style="direction:rtl;">دسترسی شما موقتاً محدود شده است.</div>
                        <span id="lockout-timer" class="lockout-timer" data-time="<?php echo $remaining_lockout; ?>">
                            <?php echo gmdate("i:s", $remaining_lockout); ?>
                        </span>
                    </div>
                <?php else: ?>
                    <?php if ($error_msg): ?>
                        <div class="error-box anim-up delay-2">
                            <i data-lucide="alert-circle" size="18"></i>
                            <span><?php echo $error_msg; ?></span>
                        </div>
                    <?php endif; ?>

                    <form id="loginForm" class="login-form" method="POST" action="">
                        <div class="input-group anim-up delay-2">
                            <input type="text" name="username" id="usernameInput" class="glass-input" placeholder="نام کاربری" required autofocus autocomplete="off">
                            <i data-lucide="user" class="input-icon"></i>
                            <div class="input-glow"></div>
                        </div>
                        
                        <div class="input-group anim-up delay-3">
                            <input type="password" name="password" id="passwordInput" class="glass-input" placeholder="رمز عبور" required>
                            <i data-lucide="key" class="input-icon"></i>
                            
                            <div id="togglePasswordBtn" class="toggle-password-wrapper">
                                <i data-lucide="eye" id="icon-eye-open"></i>
                                <i data-lucide="eye-off" id="icon-eye-closed" style="display:none;"></i>
                            </div>
                            <div class="input-glow"></div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn-login anim-up delay-4">
                            <span>ورود به سیستم</span>
                            <div class="spinner"></div>
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="login-footer anim-up delay-4">
                <div class="footer-item">
                    <span>System:</span> 
                    <span class="text-green">PHP <?php echo $php_version; ?></span>
                </div>
                <div class="footer-divider"></div>
                <div class="footer-item">
                    <span>Version:</span> 
                    <span class="text-white"><?php echo $bot_version; ?></span>
                </div>
            </div>
        </div>

        <footer class="main-footer-bar">
            Designed by <a href="https://amirhossin1007.ir" target="_blank" rel="noopener noreferrer">Amirhossin1007.ir</a> - Jan 2026
        </footer>
    </div>

    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>
</html>