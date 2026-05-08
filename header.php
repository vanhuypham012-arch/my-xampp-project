<?php
// header.php
// File này giả định session_start() đã được gọi ở đầu file PHP bao gồm nó.

// Xác định đường dẫn gốc cho các liên kết
$base_path = '';
// Kiểm tra xem script hiện tại có nằm trong thư mục 'html' hay không
if (strpos($_SERVER['PHP_SELF'], '/html/') !== false) {
    $base_path = '../';
    $html_path = '';
} else {
    $base_path = '';
    $html_path = 'html/';
}

// Kiểm tra xem có phải trang chủ không
$is_home = (basename($_SERVER['PHP_SELF']) == 'index.php');
// Kiểm tra xem có phải trang quản trị không
$is_admin_page = (basename($_SERVER['PHP_SELF']) == 'admin.php');

// Bao gồm Font Awesome CSS một lần duy nhất
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">';

// Logic xử lý đa ngôn ngữ đơn giản cho Header
$lang = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'EN';
$translations = [
    'EN' => [
        'home' => 'Home', 'fashion' => 'Fashion', 'makeup' => 'Makeup', 'watch' => 'Watch',
        'admin' => 'Admin Dashboard', 'logout' => 'Logout', 'login' => 'Login', 'profile' => 'Profile'
    ],
    'VN' => [
        'home' => 'Trang Chủ', 'fashion' => 'Thời Trang', 'makeup' => 'Trang Điểm', 'watch' => 'Đồng Hồ',
        'admin' => 'Quản Trị', 'logout' => 'Đăng Xuất', 'login' => 'Đăng Nhập', 'profile' => 'Trang cá nhân'
    ]
];
$t = $translations[$lang];
?>
<header class="header <?php echo $is_home ? 'home-header' : ''; ?>">
    <div class="header-left">
        <?php if (!$is_home && !$is_admin_page): ?>
            <!-- Logo nằm bên trái cho các trang mua sắm (Fashion, Makeup, etc.) -->
            <h1 class="logo" onclick="window.location.href='<?php echo $base_path; ?>index.php'" style="font-size: 24px;">FASHION<span>CLUB</span></h1>
        <?php elseif ($is_admin_page): ?>
            <nav class="main-menu">
                <a href="<?php echo $base_path; ?>index.php" class="nav-link"><?php echo $t['home']; ?></a>
            </nav>
        <?php endif; ?>
    </div>

    <div class="header-center">
        <?php if ($is_home || $is_admin_page): ?>
            <!-- Logo nằm giữa tại trang Intro và Admin -->
            <h1 class="logo" onclick="window.location.href='<?php echo $base_path; ?>index.php'">FASHION<span>CLUB</span></h1>
        <?php else: ?>
            <!-- Menu nằm giữa cho mọi trang mua sắm -->
            <nav class="main-menu">
                <a href="<?php echo $base_path; ?>index.php" class="nav-link"><?php echo $t['home']; ?></a>
                <a href="<?php echo $html_path; ?>collection.php" class="nav-link"><?php echo $t['fashion']; ?></a>
                <a href="<?php echo $html_path; ?>makeup.php" class="nav-link"><?php echo $t['makeup']; ?></a>
                <a href="<?php echo $html_path; ?>watch.php" class="nav-link"><?php echo $t['watch']; ?></a>
            </nav>
        <?php endif; ?>
    </div>

    <div class="header-right">
        <div class="lang-switcher" onclick="toggleLanguage()" title="Switch Language" style="cursor: pointer; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 5px;">
            <i class="fa-solid fa-globe"></i> <span><?php echo $lang; ?></span>
        </div>
        <?php if (!$is_admin_page): ?>
            <!-- Hiện giỏ hàng ở tất cả các trang, trừ trang Admin -->
        <div class="cart-icon-wrapper" onclick="toggleCart()">
            <i class="fa-solid fa-bag-shopping"></i>
            <span id="cart-count">0</span>
        </div>
        <?php endif; ?>

        <div class="account-menu">
            <div class="account-trigger" id="accountTrigger" onclick="toggleAccountDropdown(event)" style="cursor: pointer; position: relative; z-index: 10001;">
                <?php if(isset($_SESSION['avatar']) && !empty($_SESSION['avatar'])): ?>
                    <img src="<?php echo $base_path; ?>img/avatars/<?php echo htmlspecialchars($_SESSION['avatar']); ?>" alt="Avatar" class="user-avatar">
                <?php else: ?>
                    <i class="fa-solid fa-circle-user"></i>
                <?php endif; ?>
            </div>
            <div id="accountDropdown" class="account-dropdown">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['role'] === 'admin'): ?>
                        <a href="<?php echo $base_path; ?>html/admin.php"><i class="fa-solid fa-user-gear"></i> <?php echo $t['admin']; ?></a>
                    <?php endif; ?>
                    <a href="<?php echo $base_path; ?>profile.php"><i class="fa-solid fa-user-pen"></i> <?php echo $t['profile']; ?></a>
                    <a href="<?php echo $base_path; ?>logout.php"><i class="fa-solid fa-right-from-bracket"></i> <?php echo $t['logout']; ?></a>
            <?php else: ?>
                    <a href="<?php echo $base_path; ?>login.php"><i class="fa-solid fa-user"></i> <?php echo $t['login']; ?></a>
            <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<script>
// Hiệu ứng tự động thêm class 'scrolled' khi cuộn trang
window.addEventListener("scroll", () => {
    const header = document.querySelector(".header");
    if (header) {
        header.classList.toggle("scrolled", window.scrollY > 50);
    }
});

function toggleLanguage() {
    const currentLang = '<?php echo $lang; ?>';
    const newLang = currentLang === 'EN' ? 'VN' : 'EN';
    document.cookie = "lang=" + newLang + "; path=/; max-age=" + (30 * 24 * 60 * 60);
    location.reload();
}

function toggleAccountDropdown(event) {
    if (event) event.stopPropagation();
    const dropdown = document.getElementById("accountDropdown");
    if (dropdown) {
        dropdown.classList.toggle("show");
    }
}

window.addEventListener("click", () => {
    const dropdown = document.getElementById("accountDropdown");
    if (dropdown && dropdown.classList.contains("show")) {
        dropdown.classList.remove("show");
    }
});

// Hàm hiển thị thông báo Toast toàn cục
window.showToast = function(message) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = `<i class="fa-solid fa-check-circle" style="margin-right: 10px;"></i> ${message}`;
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('fade-out');
        setTimeout(() => toast.remove(), 400);
    }, 2500);
};
</script>