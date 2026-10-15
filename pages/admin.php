<?php
/**
 * PDFtoExcel.in - Self-Hosted Analytics & Conversion Admin Dashboard
 * Secure, privacy-first analytics and conversion tracking portal
 */

session_start();

require_once __DIR__ . '/../includes/db.php';

// Master security credentials (Change here or in server environment)
define('ADMIN_PASSWORD', getenv('ADMIN_PASSWORD') ?: 'admin123');

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_auth']);
    session_destroy();
    header("Location: /admin");
    exit;
}

// Handle Login Submission
$loginError = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    if (!Database::checkRateLimit('admin_login', 20, 900)) {
        $loginError = "Too many failed login attempts. Please wait 15 minutes.";
    } else {
        $password = $_POST['admin_pass'] ?? '';
        if ($password === ADMIN_PASSWORD) {
            Database::clearRateLimit('admin_login');
            $_SESSION['admin_auth'] = true;
            header("Location: /admin");
            exit;
        } else {
            $loginError = "Invalid administrator password. Access denied.";
        }
    }
}

$isLoggedIn = !empty($_SESSION['admin_auth']);

// Range filter: default 30 days
$rangeDays = (int)($_GET['range'] ?? 30);
if (!in_array($rangeDays, [1, 7, 30, 90, 365])) {
    $rangeDays = 30;
}

if ($isLoggedIn) {
    $metrics = Database::getDashboardMetrics($rangeDays);
    $topPages = Database::getTopPages($rangeDays, 15);
    $toolFunnel = Database::getToolFunnel($rangeDays);
    $trafficSeries = Database::getDailyTrafficSeries($rangeDays <= 14 ? $rangeDays : 14);
    $recentConversions = Database::getRecentConversions(25);
    $recentInquiries = Database::getRecentInquiries(15);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics & Conversion Dashboard | PDFtoExcel.in</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #060913;
            --bg-card: rgba(15, 23, 42, 0.75);
            --bg-card-hover: rgba(30, 41, 59, 0.85);
            --bg-input: rgba(15, 23, 42, 0.9);
            --border: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(16, 185, 129, 0.3);
            --primary: #10B981;
            --primary-glow: rgba(16, 185, 129, 0.2);
            --primary-light: #34D399;
            --accent-blue: #38BDF8;
            --accent-purple: #A855F7;
            --danger: #F43F5E;
            --warning: #F59E0B;
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --text-dim: #64748B;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: var(--bg-base);
            background-image: 
                radial-gradient(at 10% 20%, rgba(16, 185, 129, 0.07) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(56, 189, 248, 0.05) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(168, 85, 247, 0.04) 0px, transparent 60%);
            background-attachment: fixed;
            color: var(--text-main);
            font-family: var(--font-sans);
            line-height: 1.5;
            min-height: 100vh;
        }

        .container { max-width: 1360px; margin: 0 auto; padding: 0 1.5rem; }

        /* Navigation Bar */
        .admin-nav {
            background: rgba(10, 15, 29, 0.8);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .admin-nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 700;
            font-size: 1.15rem;
        }
        .brand-badge {
            background: rgba(16, 185, 129, 0.12);
            color: var(--primary);
            border: 1px solid rgba(16, 185, 129, 0.3);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.55rem;
            border-radius: 9999px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        /* Time Range Filters */
        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(15, 23, 42, 0.6);
            padding: 0.3rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
        }
        .filter-btn {
            background: transparent;
            color: var(--text-muted);
            border: none;
            padding: 0.45rem 0.85rem;
            font-size: 0.82rem;
            font-weight: 600;
            border-radius: var(--radius-sm);
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .filter-btn:hover { color: var(--text-main); }
        .filter-btn.active {
            background: var(--primary);
            color: #042f1a;
            box-shadow: 0 0 12px var(--primary-glow);
        }

        .btn-logout {
            background: rgba(244, 63, 94, 0.1);
            color: var(--danger);
            border: 1px solid rgba(244, 63, 94, 0.25);
            padding: 0.45rem 0.9rem;
            font-size: 0.82rem;
            font-weight: 600;
            border-radius: var(--radius-sm);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s ease;
        }
        .btn-logout:hover {
            background: var(--danger);
            color: white;
        }

        /* Login Screen */
        .login-wrap {
            min-height: calc(100vh - 72px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .login-box {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2.75rem 2.25rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        .login-icon {
            width: 60px;
            height: 60px;
            background: rgba(16, 185, 129, 0.12);
            color: var(--primary);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }
        .login-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; }
        .login-subtitle { color: var(--text-muted); font-size: 0.88rem; margin-bottom: 2rem; }
        .form-control {
            width: 100%;
            padding: 0.85rem 1.1rem;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            color: var(--text-main);
            font-size: 0.95rem;
            font-family: inherit;
            margin-bottom: 1.25rem;
            transition: border-color 0.2s;
        }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-glow); }
        .btn-primary {
            width: 100%;
            padding: 0.85rem;
            background: var(--primary);
            color: #042f1a;
            font-weight: 700;
            font-size: 0.95rem;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary:hover { background: var(--primary-light); transform: translateY(-1px); }

        /* Dashboard Content Layout */
        .dashboard-header {
            padding: 2.5rem 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .page-title { font-size: 1.85rem; font-weight: 800; letter-spacing: -0.02em; }
        .page-desc { color: var(--text-muted); font-size: 0.92rem; }

        /* KPI Cards Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .kpi-card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, border-color 0.2s;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            border-color: var(--border-hover);
        }
        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--card-accent, var(--primary));
        }
        .kpi-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.85rem;
        }
        .kpi-label { color: var(--text-muted); font-size: 0.82rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        .kpi-icon {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-sm);
            background: rgba(255, 255, 255, 0.04);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--card-accent, var(--primary));
        }
        .kpi-value { font-size: 2.25rem; font-weight: 800; line-height: 1.1; margin-bottom: 0.5rem; }
        .kpi-meta { font-size: 0.82rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.4rem; }
        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
        }
        .badge-success { background: rgba(16, 185, 129, 0.15); color: var(--primary); border: 1px solid rgba(16, 185, 129, 0.3); }
        .badge-danger { background: rgba(244, 63, 94, 0.15); color: var(--danger); border: 1px solid rgba(244, 63, 94, 0.3); }
        .badge-blue { background: rgba(56, 189, 248, 0.15); color: var(--accent-blue); border: 1px solid rgba(56, 189, 248, 0.3); }

        /* Section Cards */
        .section-card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            margin-bottom: 2rem;
        }
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .section-title { font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 0.6rem; }

        /* Tables */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem; }
        th {
            background: rgba(15, 23, 42, 0.9);
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border);
        }
        td {
            padding: 0.95rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: var(--text-main);
        }
        tr:hover td { background: rgba(255, 255, 255, 0.02); }
        .cell-mono { font-family: var(--font-mono); font-size: 0.82rem; }

        /* Progress Bar */
        .prog-bar-bg {
            background: rgba(255, 255, 255, 0.06);
            height: 6px;
            border-radius: 999px;
            overflow: hidden;
            width: 100%;
            max-width: 140px;
        }
        .prog-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--accent-blue));
            border-radius: 999px;
        }

        /* SVG Bar Chart */
        .chart-container {
            width: 100%;
            height: 220px;
            margin: 1.5rem 0 0.5rem;
            position: relative;
        }
        .chart-svg { width: 100%; height: 100%; overflow: visible; }

        /* Grid Layout for Lower Tables */
        .grid-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        @media (max-width: 980px) {
            .grid-2col { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Admin Navigation -->
    <header class="admin-nav">
        <div class="container admin-nav-inner">
            <a href="/admin" class="brand">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" color="#10B981">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
                <span>PDFtoExcel.in</span>
                <span class="brand-badge">Command Center</span>
            </a>

            <?php if ($isLoggedIn): ?>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="filter-group">
                    <a href="/admin?range=1" class="filter-btn <?= $rangeDays === 1 ? 'active' : '' ?>">Today</a>
                    <a href="/admin?range=7" class="filter-btn <?= $rangeDays === 7 ? 'active' : '' ?>">7 Days</a>
                    <a href="/admin?range=30" class="filter-btn <?= $rangeDays === 30 ? 'active' : '' ?>">30 Days</a>
                    <a href="/admin?range=365" class="filter-btn <?= $rangeDays === 365 ? 'active' : '' ?>">All Time</a>
                </div>
                <a href="/admin?action=logout" class="btn-logout" title="Log out">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Logout
                </a>
            </div>
            <?php else: ?>
            <a href="/" target="_blank" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none;">View Live Site &rarr;</a>
            <?php endif; ?>
        </div>
    </header>

    <?php if (!$isLoggedIn): ?>
    <!-- Login Portal -->
    <main class="login-wrap">
        <div class="login-box">
            <div class="login-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
            <h1 class="login-title">Administrator Login</h1>
            <p class="login-subtitle">Enter your master passcode to access self-hosted visitor telemetry and conversion analytics.</p>

            <?php if ($loginError): ?>
            <div style="background: rgba(244, 63, 94, 0.15); color: var(--danger); border: 1px solid rgba(244, 63, 94, 0.3); border-radius: var(--radius-sm); padding: 0.75rem; font-size: 0.85rem; margin-bottom: 1.25rem;">
                <?= htmlspecialchars($loginError) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="/admin">
                <input type="password" name="admin_pass" class="form-control" placeholder="Master Passcode" required autofocus>
                <button type="submit" name="login_submit" class="btn-primary">Unlock Command Center</button>
            </form>
            <p style="margin-top: 1.5rem; font-size: 0.75rem; color: var(--text-dim);">
                100% Private Self-Hosted Telemetry &bull; Zero Third-Party Trackers
            </p>
        </div>
    </main>

    <?php else: ?>
    <!-- Dashboard Main View -->
    <main class="container" style="padding-bottom: 4rem;">
        <div class="dashboard-header">
            <div>
                <h1 class="page-title">Visitor Analytics & Conversion Health</h1>
                <p class="page-desc">Comprehensive real-time tracking of visitor traffic, page paths, tool adoption, and conversion outcomes.</p>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span class="badge-pill badge-success">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--primary); display: inline-block;"></span>
                    Telemetry Live
                </span>
                <span style="font-size: 0.82rem; color: var(--text-dim); font-family: var(--font-mono);">
                    Window: <?= $rangeDays ?> Days
                </span>
            </div>
        </div>

        <!-- 4 Core Executive KPI Cards -->
        <section class="kpi-grid">
            <!-- 1. Total Visitors -->
            <div class="kpi-card" style="--card-accent: var(--primary);">
                <div class="kpi-header">
                    <span class="kpi-label">Visitor Traffic</span>
                    <div class="kpi-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                </div>
                <div class="kpi-value"><?= number_format($metrics['unique_visitors'] ?? 0) ?></div>
                <div class="kpi-meta">
                    <span class="badge-pill badge-blue"><?= number_format($metrics['total_views'] ?? 0) ?> Total Views</span>
                    <span>Unique IP visitors</span>
                </div>
            </div>

            <!-- 2. Tool Conversions -->
            <div class="kpi-card" style="--card-accent: var(--accent-blue);">
                <div class="kpi-header">
                    <span class="kpi-label">Tool Conversions</span>
                    <div class="kpi-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                    </div>
                </div>
                <div class="kpi-value"><?= number_format($metrics['total_conversions'] ?? 0) ?></div>
                <div class="kpi-meta">
                    <span class="badge-pill badge-success"><?= $metrics['tool_adoption_rate'] ?? 0 ?>% Adoption</span>
                    <span>of page visitors use a tool</span>
                </div>
            </div>

            <!-- 3. Conversion Success Rate -->
            <div class="kpi-card" style="--card-accent: var(--accent-purple);">
                <div class="kpi-header">
                    <span class="kpi-label">Success Rate</span>
                    <div class="kpi-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                </div>
                <div class="kpi-value"><?= $metrics['success_rate'] ?? 100 ?>%</div>
                <div class="kpi-meta">
                    <span class="badge-pill badge-success"><?= number_format($metrics['successful_conversions'] ?? 0) ?> OK</span>
                    <span class="badge-pill badge-danger"><?= number_format($metrics['failed_conversions'] ?? 0) ?> Errors</span>
                </div>
            </div>

            <!-- 4. Device Distribution -->
            <div class="kpi-card" style="--card-accent: var(--warning);">
                <div class="kpi-header">
                    <span class="kpi-label">Device Channels</span>
                    <div class="kpi-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    </div>
                </div>
                <div class="kpi-value">
                    <?php
                    $desktopCount = 0; $mobileCount = 0;
                    foreach ($metrics['devices'] ?? [] as $d) {
                        if ($d['device_type'] === 'desktop') $desktopCount = $d['count'];
                        if ($d['device_type'] === 'mobile') $mobileCount = $d['count'];
                    }
                    echo ($desktopCount + $mobileCount > 0) ? round(($desktopCount / ($desktopCount + $mobileCount)) * 100) . '%' : '100%';
                    ?>
                </div>
                <div class="kpi-meta">
                    <span>Desktop Dominance &bull; <?= $mobileCount ?> Mobile</span>
                </div>
            </div>
        </section>

        <!-- Daily Traffic & Conversion Activity Chart -->
        <section class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" color="#10B981"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                        Daily Visitor & Conversion Activity (Last 14 Days)
                    </h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem;">Daily comparison between incoming page views, unique visitors, and completed tool conversions.</p>
                </div>
                <div style="display: flex; gap: 1rem; font-size: 0.8rem; align-items: center;">
                    <span style="display: flex; align-items: center; gap: 0.35rem;">
                        <span style="width: 10px; height: 10px; border-radius: 2px; background: #38BDF8; display: inline-block;"></span> Page Views
                    </span>
                    <span style="display: flex; align-items: center; gap: 0.35rem;">
                        <span style="width: 10px; height: 10px; border-radius: 2px; background: #10B981; display: inline-block;"></span> Unique Visitors
                    </span>
                    <span style="display: flex; align-items: center; gap: 0.35rem;">
                        <span style="width: 10px; height: 10px; border-radius: 2px; background: #A855F7; display: inline-block;"></span> Tool Conversions
                    </span>
                </div>
            </div>

            <!-- SVG Responsive Bar Chart -->
            <div class="chart-container">
                <?php
                $maxVal = 1;
                foreach ($trafficSeries as $pt) {
                    $maxVal = max($maxVal, $pt['views'], $pt['visitors'], $pt['conversions']);
                }
                $countPts = count($trafficSeries);
                ?>
                <svg class="chart-svg" viewBox="0 0 1000 220" preserveAspectRatio="none">
                    <!-- Grid Lines -->
                    <line x1="0" y1="180" x2="1000" y2="180" stroke="rgba(255,255,255,0.06)" stroke-width="1" />
                    <line x1="0" y1="100" x2="1000" y2="100" stroke="rgba(255,255,255,0.04)" stroke-dasharray="4" />
                    <line x1="0" y1="20" x2="1000" y2="20" stroke="rgba(255,255,255,0.04)" stroke-dasharray="4" />

                    <?php foreach ($trafficSeries as $idx => $pt): 
                        $colWidth = 1000 / max(1, $countPts);
                        $x = $idx * $colWidth + ($colWidth * 0.15);
                        $barW = ($colWidth * 0.22);

                        $vH = ($pt['views'] / $maxVal) * 150;
                        $uH = ($pt['visitors'] / $maxVal) * 150;
                        $cH = ($pt['conversions'] / $maxVal) * 150;
                    ?>
                        <!-- Views Bar -->
                        <rect x="<?= $x ?>" y="<?= 180 - $vH ?>" width="<?= $barW ?>" height="<?= max(2, $vH) ?>" rx="3" fill="#38BDF8" opacity="0.85">
                            <title><?= $pt['label'] ?>: <?= $pt['views'] ?> Page Views</title>
                        </rect>
                        <!-- Unique Visitors Bar -->
                        <rect x="<?= $x + $barW + 2 ?>" y="<?= 180 - $uH ?>" width="<?= $barW ?>" height="<?= max(2, $uH) ?>" rx="3" fill="#10B981" opacity="0.9">
                            <title><?= $pt['label'] ?>: <?= $pt['visitors'] ?> Unique Visitors</title>
                        </rect>
                        <!-- Conversions Bar -->
                        <rect x="<?= $x + ($barW * 2) + 4 ?>" y="<?= 180 - $cH ?>" width="<?= $barW ?>" height="<?= max(2, $cH) ?>" rx="3" fill="#A855F7" opacity="0.9">
                            <title><?= $pt['label'] ?>: <?= $pt['conversions'] ?> Tool Conversions</title>
                        </rect>

                        <!-- X Axis Label -->
                        <text x="<?= $x + $barW ?>" y="202" fill="#64748B" font-size="11" font-family="var(--font-mono)" text-anchor="middle"><?= $pt['label'] ?></text>
                    <?php endforeach; ?>
                </svg>
            </div>
        </section>

        <!-- 2-Column Grid: Top Visited Pages & Tool Funnel -->
        <div class="grid-2col">
            <!-- Left: Top Visited Pages -->
            <section class="section-card" style="margin-bottom: 0;">
                <div class="section-header">
                    <h2 class="section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" color="#38BDF8"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                        Top Visited Pages
                    </h2>
                    <span style="font-size: 0.78rem; color: var(--text-dim);">By Page Impressions</span>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Page Route</th>
                                <th>Views</th>
                                <th>Uniques</th>
                                <th>Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topPages)): ?>
                            <tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">No page visits recorded yet. As visitors browse the site, telemetry appears here instantly.</td></tr>
                            <?php else: 
                                $topMax = max(1, $topPages[0]['views'] ?? 1);
                                foreach ($topPages as $page): 
                                    $pct = round(($page['views'] / $topMax) * 100);
                            ?>
                            <tr>
                                <td>
                                    <a href="<?= htmlspecialchars($page['page_url']) ?>" target="_blank" style="color: var(--text-main); text-decoration: none; font-weight: 600;" class="cell-mono">
                                        <?= htmlspecialchars($page['page_url']) ?>
                                    </a>
                                </td>
                                <td><strong><?= number_format($page['views']) ?></strong></td>
                                <td style="color: var(--text-muted);"><?= number_format($page['unique_visitors']) ?></td>
                                <td>
                                    <div class="prog-bar-bg">
                                        <div class="prog-bar-fill" style="width: <?= $pct ?>%;"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Right: Tool Conversion Funnel -->
            <section class="section-card" style="margin-bottom: 0;">
                <div class="section-header">
                    <h2 class="section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" color="#10B981"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        Tool Usage & Success Rate
                    </h2>
                    <span style="font-size: 0.78rem; color: var(--text-dim);">Conversion Health</span>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Tool Name</th>
                                <th>Uses</th>
                                <th>Success</th>
                                <th>Errors</th>
                                <th>Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($toolFunnel)): ?>
                            <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">No tool conversions logged yet. Test any converter on the live site to record stats.</td></tr>
                            <?php else: foreach ($toolFunnel as $tool): 
                                $rate = $tool['total_conversions'] > 0 ? round(($tool['successes'] / $tool['total_conversions']) * 100, 1) : 100;
                            ?>
                            <tr>
                                <td>
                                    <span class="cell-mono" style="font-weight: 600; color: #38BDF8;">
                                        <?= htmlspecialchars($tool['tool_type']) ?>
                                    </span>
                                </td>
                                <td><strong><?= number_format($tool['total_conversions']) ?></strong></td>
                                <td style="color: var(--primary); font-weight: 600;"><?= number_format($tool['successes']) ?></td>
                                <td>
                                    <?php if ($tool['errors'] > 0): ?>
                                    <span class="badge-pill badge-danger"><?= $tool['errors'] ?> Fail</span>
                                    <?php else: ?>
                                    <span style="color: var(--text-dim);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge-pill <?= $rate >= 95 ? 'badge-success' : 'badge-danger' ?>">
                                        <?= $rate ?>%
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Live Conversion Activity Stream -->
        <section class="section-card" style="margin-top: 2rem;">
            <div class="section-header">
                <div>
                    <h2 class="section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" color="#A855F7"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                        Live Conversion Log Stream
                    </h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem;">Chronological stream of individual conversions showing tool choice, format, payload size, and outcome.</p>
                </div>
                <span class="badge-pill badge-blue">Last <?= count($recentConversions) ?> Events</span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Tool Type</th>
                            <th>Output Format</th>
                            <th>File Size</th>
                            <th>Outcome</th>
                            <th>Diagnostic Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentConversions)): ?>
                        <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">No conversion events recorded in this time window.</td></tr>
                        <?php else: foreach ($recentConversions as $row): 
                            $isOk = ($row['status'] === 'success' || empty($row['status']));
                        ?>
                        <tr>
                            <td class="cell-mono" style="color: var(--text-muted); font-size: 0.78rem;">
                                <?= htmlspecialchars($row['created_at']) ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($row['tool_type']) ?></strong>
                            </td>
                            <td>
                                <span class="badge-pill badge-blue uppercase">
                                    .<?= htmlspecialchars($row['format'] ?: 'xlsx') ?>
                                </span>
                            </td>
                            <td class="cell-mono" style="color: var(--text-muted);">
                                <?= $row['file_size'] > 0 ? round($row['file_size'] / 1024, 1) . ' KB' : 'Client Memory' ?>
                            </td>
                            <td>
                                <?php if ($isOk): ?>
                                <span class="badge-pill badge-success">SUCCESS</span>
                                <?php else: ?>
                                <span class="badge-pill badge-danger">ERROR</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: var(--text-dim); font-size: 0.8rem;">
                                <?= htmlspecialchars($row['error_msg'] ?: 'Clean vector execution') ?>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Customer Inquiries & Feedback Inbox -->
        <section class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" color="#10B981"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        Support Inquiries Inbox (/contact)
                    </h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem;">Direct customer inquiries, bug reports, and partnership requests submitted via the contact portal.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Sender</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentInquiries)): ?>
                        <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">No customer inquiries received yet.</td></tr>
                        <?php else: foreach ($recentInquiries as $inq): ?>
                        <tr>
                            <td class="cell-mono" style="color: var(--text-muted); font-size: 0.78rem; white-space: nowrap;">
                                <?= htmlspecialchars($inq['created_at']) ?>
                            </td>
                            <td><strong><?= htmlspecialchars($inq['name']) ?></strong></td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($inq['email']) ?>" style="color: var(--accent-blue); text-decoration: none;">
                                    <?= htmlspecialchars($inq['email']) ?>
                                </a>
                            </td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($inq['subject']) ?></td>
                            <td style="max-width: 380px; font-size: 0.82rem; color: var(--text-muted); line-height: 1.4;">
                                <?= nl2br(htmlspecialchars($inq['message'])) ?>
                            </td>
                            <td>
                                <span class="badge-pill badge-blue">
                                    <?= htmlspecialchars($inq['status'] ?? 'unread') ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <?php endif; ?>

</body>
</html>
