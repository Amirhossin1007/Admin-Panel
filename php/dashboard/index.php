<?php

// Route: admin/activities/index.php

require_once __DIR__ . '/../login/auth_check.php';

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" id="viewport-meta">
<title>Panel | Activities</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2300f2ff%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><circle cx=%2212%22 cy=%2212%22 r=%223%22></circle><path d=%22M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1.51 1 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z%22></path></svg>">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&family=Vazirmatn:wght@100;300;400;700&display=swap" rel="stylesheet">
<style>
    html.lenis { height: auto; }
    .lenis.lenis-smooth { scroll-behavior: auto !important; }
    .lenis.lenis-smooth [data-lenis-prevent] { overscroll-behavior: contain; }
    .lenis.lenis-stopped { overflow: hidden; }
    .lenis.lenis-scrolling iframe { pointer-events: none; }
</style>
<link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
<script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
<div class="ambient-bg">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="noise-overlay"></div>
</div>

<div class="overlay" id="overlay" onclick="toggleDrawer()"></div>

<div class="drawer" id="drawer" data-lenis-prevent>
    <div class="drawer-header">
        <h2 class="font-num" style="letter-spacing:2px; color:#fff;">MENU</h2>
        <div class="close-btn" onclick="toggleDrawer()">
            <i data-lucide="x" size="24"></i>
        </div>
    </div>
    
    <div class="drawer-divider"></div>

    <a href="" class="drawer-link active"><i data-lucide="layout-dashboard"></i> داشبورد اصلی</a>
    
    <a href="../login/logout.php" class="drawer-link" id="logout-link" style="color: var(--danger); margin-top: auto;"><i data-lucide="log-out"></i> خروج</a>
</div>

<div class="dashboard-wrapper">
    <div class="header-section">
        <div class="header-controls">
            <div class="menu-trigger" onclick="toggleDrawer()">
                <i data-lucide="menu"></i>
            </div>
        </div>
        <div class="brand">
            <i data-lucide="settings" class="brand-icon"></i>
            <div class="brand-text">
                <h1 class="font-num">DASHBOARD</h1>
                <span>Management Panel</span>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="coming-soon">Coming Soon</div>
    </div>
</div>

<footer class="main-footer-bar">
    Designed by <a href="https://amirhossin1007.ir" target="_blank" rel="noopener noreferrer">Amirhossin1007.ir</a> - Jan 2026
</footer>

<script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
<script src="script.js?v=<?php echo time(); ?>"></script>
</body>
</html>