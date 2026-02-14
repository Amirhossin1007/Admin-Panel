<?php
require_once __DIR__ . '/../../config.php';

// اتصال به دیتابیس
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset("utf8mb4");
$conn->query("SET time_zone = '+03:30'");

// دریافت لیست سوالات برای منو (آخرین سوالات اول)
$menu_items = [];
$sql_menu = "SELECT id, created_at FROM nmone ORDER BY id DESC LIMIT 50";
$res_menu = $conn->query($sql_menu);
if ($res_menu) {
    while ($row = $res_menu->fetch_assoc()) {
        $menu_items[] = $row;
    }
}

// دریافت جزئیات سوال انتخاب شده
$selected_log = null;
$current_id = isset($_GET['id']) ? (int)$_GET['id'] : (count($menu_items) > 0 ? $menu_items[0]['id'] : 0);

if ($current_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM nmone WHERE id = ?");
    $stmt->bind_param("i", $current_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $selected_log = $res->fetch_assoc();
    $stmt->close();
}

// توابع کمکی برای تمیزکاری و فرمت‌دهی
function formatText($text) {
    // 1. امنیت: تبدیل کاراکترهای خطرناک
    $text = htmlspecialchars((string)$text);
    
    // 2. بولد کردن: تبدیل **متن** به <b>متن</b>
    $text = preg_replace('/\*\*(.*?)\*\*/s', '<b class="bold-highlight">$1</b>', $text);
    
    // 3. تبدیل خطوط جدید به <br>
    return nl2br($text);
}

function parseRagDetails($json) {
    $data = json_decode($json, true);
    if (!is_array($data)) return [];
    return $data;
}

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Debug Ai</title>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #121212;
            --menu-bg: #1e1e1e;
            --card-bg: #252525;
            --text-main: #e0e0e0;
            --text-dim: #a0a0a0;
            --accent: #4caf50;
            --border: #333;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; outline: none; }
        
        body {
            font-family: 'Vazirmatn', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            overflow: hidden;
            font-size: 16px; 
        }

        /* کلاس مخصوص برای بولد کردن */
        .bold-highlight {
            font-weight: 700;
            color: #ffffff; /* سفید خالص برای تمایز */
            text-shadow: 0 0 1px rgba(255,255,255,0.2);
        }

        /* دکمه منوی موبایل */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 100;
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        /* منوی سمت چپ */
        .sidebar {
            width: 280px;
            background-color: var(--menu-bg);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow-y: auto;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            font-weight: 700;
            text-align: center;
            color: var(--accent);
        }

        .menu-list {
            list-style: none;
            padding: 10px;
        }

        .menu-item {
            display: block;
            padding: 15px;
            margin-bottom: 5px;
            background: rgba(255,255,255,0.03);
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-dim);
            transition: 0.2s;
            border: 1px solid transparent;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(76, 175, 80, 0.1);
            color: #fff;
            border-color: var(--accent);
        }

        .menu-item span {
            display: block;
            font-size: 0.8rem;
            opacity: 0.6;
            margin-top: 3px;
        }

        /* محتوای اصلی سمت راست */
        .main-content {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            height: 100%;
            padding-top: 60px; /* فضای خالی برای دکمه موبایل */
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding-bottom: 50px;
        }

        .section-box {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-number {
            background: rgba(255,255,255,0.1);
            width: 25px; height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: #fff;
        }

        .text-content {
            line-height: 1.8;
            color: #ddd;
            white-space: pre-wrap; 
        }

        .pattern-item {
            background: rgba(0,0,0,0.2);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-right: 3px solid #555;
        }

        .pattern-item.selected {
            border-right-color: var(--accent);
            background: rgba(76, 175, 80, 0.05);
        }

        .pattern-label {
            font-size: 0.8rem;
            opacity: 0.5;
            margin-bottom: 5px;
            display: block;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-success { background: rgba(76, 175, 80, 0.2); color: #4caf50; }
        .status-fail { background: rgba(244, 67, 54, 0.2); color: #f44336; }

        .approved-box {
            background: rgba(76, 175, 80, 0.15);
            color: #4caf50;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid rgba(76, 175, 80, 0.3);
            margin-bottom: 15px;
        }

        /* ریسپانسیو موبایل */
        @media (max-width: 768px) {
            .mobile-menu-btn { display: block; }
            
            .sidebar {
                position: fixed;
                top: 0; left: 0;
                z-index: 99;
                transform: translateX(-100%);
                width: 80%;
                box-shadow: 5px 0 15px rgba(0,0,0,0.5);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.7);
                z-index: 98;
            }
            .overlay.active { display: block; }
            
            .main-content {
                padding: 15px;
                padding-top: 60px;
            }
        }
    </style>
</head>
<body>

    <!-- دکمه منو موبایل -->
    <button class="mobile-menu-btn" onclick="toggleMenu()">☰</button>
    
    <!-- لایه تاریک پشت منو -->
    <div class="overlay" id="overlay" onclick="toggleMenu()"></div>

    <!-- منوی سمت چپ -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">سوالات</div>
        <div class="menu-list">
            <?php foreach ($menu_items as $item): ?>
                <?php 
                    $activeClass = ($item['id'] == $current_id) ? 'active' : '';
                    $date = $item['created_at']; 
                ?>
                <a href="?id=<?php echo $item['id']; ?>" class="menu-item <?php echo $activeClass; ?>">
                    سوال شماره #<?php echo $item['id']; ?>
                    <span><?php echo $date; ?></span>
                </a>
            <?php endforeach; ?>
            
            <?php if (empty($menu_items)): ?>
                <div style="text-align:center; padding:20px; color:#666;">هیچ داده‌ای یافت نشد</div>
            <?php endif; ?>
        </div>
    </aside>

    <!-- محتوای اصلی -->
    <main class="main-content">
        <div class="container">
            <?php if ($selected_log): ?>
                
                <div style="margin-bottom: 20px; text-align:center;">
                    <?php if(!empty($selected_log['error_msg'])): ?>
                    <?php endif; ?>
                </div>

                <!-- باکس ۱: سوال -->
                <div class="section-box">
                    <div class="section-title">
                        <span class="section-number">1</span>
                        سوال کاربر
                    </div>
                    <div class="text-content"><?php echo formatText($selected_log['question_text']); ?></div>
                </div>

                <!-- باکس ۲: هشتگ -->
                <div class="section-box">
                    <div class="section-title">
                        <span class="section-number">2</span>
                        هشتگ
                    </div>
                    <div class="text-content" style="color:var(--accent); font-weight:bold;">
                        <?php echo htmlspecialchars($selected_log['detected_category']); ?>
                    </div>
                </div>

                <!-- باکس ۳: ۵ الگوی پیدا شده -->
                <div class="section-box">
                    <div class="section-title">
                        <span class="section-number">3</span>
                        ۵ سوال/جواب یافت شده برای استفاده به عنوان الگو
                    </div>
                    <div class="text-content">
                        <?php 
                            $rag_details = parseRagDetails($selected_log['rag_details']);
                            $selected_ids = explode(',', $selected_log['rag_selected_ids'] ?? '');
                            
                            if (empty($rag_details)) {
                                echo "الگویی یافت نشد.";
                            } else {
                                foreach ($rag_details as $index => $rag) {
                                    $num = $index + 1;
                                    echo "<div class='pattern-item'>";
                                    echo "<span class='pattern-label'>الگوی شماره #$num (شناسه دیتابیس: {$rag['id']})</span>";
                                    echo "<div><b>س:</b> " . formatText($rag['question']) . "</div>";
                                    echo "<div style='margin-top:5px;'><b>ج:</b> " . formatText($rag['answer']) . "</div>";
                                    echo "</div>";
                                }
                            }
                        ?>
                    </div>
                </div>

                <!-- باکس ۴: الگوهای انتخاب شده -->
                <div class="section-box">
                    <div class="section-title">
                        <span class="section-number">4</span>
                        الگوهای نزدیک به سوال جدید
                    </div>
                    <div class="text-content">
                        <?php 
                            if (empty($selected_log['rag_selected_ids'])) {
                                echo "هیچ الگویی توسط هوش مصنوعی انتخاب نشد (احتمالاً none).";
                            } else {
                                foreach ($rag_details as $index => $rag) {
                                    if (in_array($index, $selected_ids) || in_array((string)$index, $selected_ids)) {
                                        echo "<div class='pattern-item selected'>";
                                        echo "<div><b>س:</b> " . formatText($rag['question']) . "</div>";
                                        echo "<div style='margin-top:5px;'><b>ج:</b> " . formatText($rag['answer']) . "</div>";
                                        echo "</div>";
                                    }
                                }
                            }
                        ?>
                    </div>
                </div>

                <!-- باکس ۵: پاسخ اولیه -->
                <div class="section-box">
                    <div class="section-title">
                        <span class="section-number">5</span>
                        پاسخ هوش مصنوعی
                    </div>
                    <div class="text-content"><?php echo formatText($selected_log['draft_answer']); ?></div>
                </div>

                <!-- باکس ۶: منتقد -->
                <div class="section-box">
                    <div class="section-title">
                        <span class="section-number">6</span>
                        نظر ناظر کیفی
                    </div>
                    
                    <?php 
                        $critique = $selected_log['critique_response'];
                        if (strpos($critique, 'APPROVED') !== false): 
                    ?>
                        <div class="approved-box">پاسخ تایید شد ✅</div>
                    <?php else: ?>
                        <div class="pattern-item" style="border-right-color: #ff9800;">
                            <span class="pattern-label">نظر منتقد (اصلاحات):</span>
                            <?php echo formatText($critique); ?>
                        </div>
                    <?php endif; ?>


            <?php else: ?>
                <div style="text-align:center; padding-top:100px; color:#666;">
                    لطفاً یک سوال را از منو انتخاب کنید.
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }
    </script>
</body>
</html>