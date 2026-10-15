<?php
/**
 * PDFtoExcel.in - Database Helper Wrapper (PDO)
 * Supports SQLite (portable zero-config) and MySQL (production/XAMPP)
 */

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): ?PDO {
        if (self::$instance === null) {
            try {
                // Try SQLite by default for self-contained portability without configuration errors
                $dbDir = __DIR__ . '/../data';
                if (!is_dir($dbDir)) {
                    mkdir($dbDir, 0777, true);
                }
                $sqlitePath = $dbDir . '/app.sqlite';
                self::$instance = new PDO("sqlite:" . $sqlitePath);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // Initialize tables if not existing
                self::initSchema(self::$instance);
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                return null;
            }
        }
        return self::$instance;
    }

    private static function initSchema(PDO $pdo): void {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS conversion_stats (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                tool_type TEXT NOT NULL,
                file_size INTEGER NOT NULL,
                ip_hash TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS feedback (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                page_url TEXT NOT NULL,
                rating INTEGER NOT NULL,
                comments TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS rate_limits (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ip_hash TEXT NOT NULL,
                action TEXT NOT NULL,
                created_at INTEGER NOT NULL
            );
            CREATE INDEX IF NOT EXISTS idx_rate_limits ON rate_limits (ip_hash, action, created_at);

            CREATE TABLE IF NOT EXISTS contact_inquiries (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                subject TEXT NOT NULL,
                message TEXT NOT NULL,
                ip_hash TEXT NOT NULL,
                status TEXT DEFAULT 'unread',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE INDEX IF NOT EXISTS idx_contact_created ON contact_inquiries (created_at);

            CREATE TABLE IF NOT EXISTS page_views (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                page_url TEXT NOT NULL,
                ip_hash TEXT NOT NULL,
                referrer TEXT,
                device_type TEXT,
                user_agent TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE INDEX IF NOT EXISTS idx_page_views_created ON page_views (created_at);
            CREATE INDEX IF NOT EXISTS idx_page_views_url ON page_views (page_url);
        ");

        // Safe migration for conversion_stats columns
        try {
            $cols = $pdo->query("PRAGMA table_info(conversion_stats)")->fetchAll(PDO::FETCH_COLUMN, 1);
            if (!in_array('status', $cols)) {
                $pdo->exec("ALTER TABLE conversion_stats ADD COLUMN status TEXT DEFAULT 'success'");
            }
            if (!in_array('format', $cols)) {
                $pdo->exec("ALTER TABLE conversion_stats ADD COLUMN format TEXT DEFAULT 'xlsx'");
            }
            if (!in_array('error_msg', $cols)) {
                $pdo->exec("ALTER TABLE conversion_stats ADD COLUMN error_msg TEXT DEFAULT NULL");
            }
        } catch (Exception $e) {
            error_log("Schema migration notice: " . $e->getMessage());
        }
    }

    public static function checkRateLimit(string $action, int $maxRequests = 30, int $windowSeconds = 60): bool {
        try {
            $pdo = self::getConnection();
            if (!$pdo) return true; // Fail open to protect user conversion flow if DB offline

            $ipHash = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
            $now = time();
            $cutoff = $now - $windowSeconds;

            // Prune expired records occasionally (1 in 25 chance)
            if (random_int(1, 25) === 1) {
                $pdo->exec("DELETE FROM rate_limits WHERE created_at < " . ($now - 3600));
            }

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM rate_limits WHERE ip_hash = ? AND action = ? AND created_at >= ?");
            $stmt->execute([$ipHash, $action, $cutoff]);
            $count = (int)$stmt->fetchColumn();

            if ($count >= $maxRequests) {
                return false;
            }

            $insert = $pdo->prepare("INSERT INTO rate_limits (ip_hash, action, created_at) VALUES (?, ?, ?)");
            $insert->execute([$ipHash, $action, $now]);
            return true;
        } catch (Exception $e) {
            error_log("Rate limit check error: " . $e->getMessage());
            return true;
        }
    }

    public static function clearRateLimit(string $action): void {
        try {
            $pdo = self::getConnection();
            if (!$pdo) return;
            $ipHash = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
            $stmt = $pdo->prepare("DELETE FROM rate_limits WHERE ip_hash = ? AND action = ?");
            $stmt->execute([$ipHash, $action]);
        } catch (Exception $e) {
            error_log("Rate limit clear error: " . $e->getMessage());
        }
    }

    public static function logPageView(string $url): void {
        try {
            $pdo = self::getConnection();
            if (!$pdo) return;

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ipHash = hash('sha256', $ip);
            $referrer = isset($_SERVER['HTTP_REFERER']) ? substr(trim($_SERVER['HTTP_REFERER']), 0, 300) : 'direct';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

            $device = 'desktop';
            if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
                $device = 'tablet';
            } elseif (preg_match('/(mobile|iphone|ipod|blackberry|opera mini|iemobile|wpdesktop)/i', $ua)) {
                $device = 'mobile';
            }

            $stmt = $pdo->prepare("INSERT INTO page_views (page_url, ip_hash, referrer, device_type, user_agent) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                substr(trim($url), 0, 200),
                $ipHash,
                $referrer,
                $device,
                substr($ua, 0, 250)
            ]);
        } catch (Exception $e) {
            error_log("Failed to log page view: " . $e->getMessage());
        }
    }

    public static function logConversion(string $toolType, int $fileSize, string $format = 'xlsx', string $status = 'success', ?string $errorMsg = null): void {
        try {
            $pdo = self::getConnection();
            if ($pdo) {
                $ipHash = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
                $stmt = $pdo->prepare("INSERT INTO conversion_stats (tool_type, file_size, ip_hash, format, status, error_msg) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    substr(trim($toolType), 0, 100),
                    $fileSize,
                    $ipHash,
                    substr(trim($format), 0, 20),
                    substr(trim($status), 0, 20),
                    $errorMsg ? substr(trim($errorMsg), 0, 500) : null
                ]);
            }
        } catch (Exception $e) {
            // Fail silently so database issues never interrupt conversion UX
            error_log("Failed to log conversion: " . $e->getMessage());
        }
    }

    public static function getDashboardMetrics(int $days = 30): array {
        try {
            $pdo = self::getConnection();
            if (!$pdo) return [];

            $cutoff = date('Y-m-d H:i:s', strtotime("-$days days"));

            $pvStmt = $pdo->prepare("SELECT COUNT(*) as total_views, COUNT(DISTINCT ip_hash) as unique_visitors FROM page_views WHERE created_at >= ?");
            $pvStmt->execute([$cutoff]);
            $pv = $pvStmt->fetch() ?: ['total_views' => 0, 'unique_visitors' => 0];

            $cStmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as total_conversions,
                    SUM(CASE WHEN status = 'success' OR status IS NULL THEN 1 ELSE 0 END) as successful_conversions,
                    SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as failed_conversions
                FROM conversion_stats 
                WHERE created_at >= ?
            ");
            $cStmt->execute([$cutoff]);
            $conv = $cStmt->fetch() ?: ['total_conversions' => 0, 'successful_conversions' => 0, 'failed_conversions' => 0];

            $totalViews = (int)($pv['total_views'] ?? 0);
            $uniqueVisitors = (int)($pv['unique_visitors'] ?? 0);
            $totalConversions = (int)($conv['total_conversions'] ?? 0);
            $successes = (int)($conv['successful_conversions'] ?? 0);
            $failures = (int)($conv['failed_conversions'] ?? 0);

            $successRate = $totalConversions > 0 ? round(($successes / $totalConversions) * 100, 1) : 100.0;
            $toolAdoptionRate = $totalViews > 0 ? round(($totalConversions / $totalViews) * 100, 1) : 0.0;

            $devStmt = $pdo->prepare("SELECT device_type, COUNT(*) as count FROM page_views WHERE created_at >= ? GROUP BY device_type");
            $devStmt->execute([$cutoff]);
            $devices = $devStmt->fetchAll();

            return [
                'total_views' => $totalViews,
                'unique_visitors' => $uniqueVisitors,
                'total_conversions' => $totalConversions,
                'successful_conversions' => $successes,
                'failed_conversions' => $failures,
                'success_rate' => $successRate,
                'tool_adoption_rate' => $toolAdoptionRate,
                'devices' => $devices
            ];
        } catch (Exception $e) {
            error_log("Analytics summary query error: " . $e->getMessage());
            return [];
        }
    }

    public static function getTopPages(int $days = 30, int $limit = 15): array {
        try {
            $pdo = self::getConnection();
            if (!$pdo) return [];

            $cutoff = date('Y-m-d H:i:s', strtotime("-$days days"));
            $stmt = $pdo->prepare("
                SELECT page_url, COUNT(*) as views, COUNT(DISTINCT ip_hash) as unique_visitors 
                FROM page_views 
                WHERE created_at >= ? 
                GROUP BY page_url 
                ORDER BY views DESC 
                LIMIT ?
            ");
            $stmt->execute([$cutoff, $limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Top pages query error: " . $e->getMessage());
            return [];
        }
    }

    public static function getToolFunnel(int $days = 30): array {
        try {
            $pdo = self::getConnection();
            if (!$pdo) return [];

            $cutoff = date('Y-m-d H:i:s', strtotime("-$days days"));
            $stmt = $pdo->prepare("
                SELECT 
                    tool_type, 
                    COUNT(*) as total_conversions,
                    SUM(CASE WHEN status = 'success' OR status IS NULL THEN 1 ELSE 0 END) as successes,
                    SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as errors,
                    ROUND(AVG(file_size)) as avg_size
                FROM conversion_stats 
                WHERE created_at >= ? 
                GROUP BY tool_type 
                ORDER BY total_conversions DESC
            ");
            $stmt->execute([$cutoff]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Tool funnel query error: " . $e->getMessage());
            return [];
        }
    }

    public static function getDailyTrafficSeries(int $days = 14): array {
        try {
            $pdo = self::getConnection();
            if (!$pdo) return [];

            $cutoff = date('Y-m-d H:i:s', strtotime("-$days days"));
            
            $pvStmt = $pdo->prepare("
                SELECT substr(created_at, 1, 10) as day_date, COUNT(*) as views, COUNT(DISTINCT ip_hash) as visitors 
                FROM page_views 
                WHERE created_at >= ? 
                GROUP BY day_date 
                ORDER BY day_date ASC
            ");
            $pvStmt->execute([$cutoff]);
            $viewsByDay = [];
            foreach ($pvStmt->fetchAll() as $row) {
                $viewsByDay[$row['day_date']] = [
                    'views' => (int)$row['views'],
                    'visitors' => (int)$row['visitors']
                ];
            }

            $convStmt = $pdo->prepare("
                SELECT substr(created_at, 1, 10) as day_date, COUNT(*) as conversions 
                FROM conversion_stats 
                WHERE created_at >= ? 
                GROUP BY day_date 
                ORDER BY day_date ASC
            ");
            $convStmt->execute([$cutoff]);
            $convByDay = [];
            foreach ($convStmt->fetchAll() as $row) {
                $convByDay[$row['day_date']] = (int)$row['conversions'];
            }

            $series = [];
            for ($i = $days; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-$i days"));
                $series[] = [
                    'date' => $d,
                    'label' => date('M j', strtotime($d)),
                    'views' => $viewsByDay[$d]['views'] ?? 0,
                    'visitors' => $viewsByDay[$d]['visitors'] ?? 0,
                    'conversions' => $convByDay[$d] ?? 0
                ];
            }
            return $series;
        } catch (Exception $e) {
            error_log("Daily traffic query error: " . $e->getMessage());
            return [];
        }
    }

    public static function getRecentConversions(int $limit = 25): array {
        try {
            $pdo = self::getConnection();
            if (!$pdo) return [];

            $stmt = $pdo->prepare("SELECT id, tool_type, format, file_size, status, error_msg, created_at FROM conversion_stats ORDER BY id DESC LIMIT ?");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Recent conversions query error: " . $e->getMessage());
            return [];
        }
    }

    public static function getRecentInquiries(int $limit = 15): array {
        try {
            $pdo = self::getConnection();
            if (!$pdo) return [];

            $stmt = $pdo->prepare("SELECT id, name, email, subject, message, status, created_at FROM contact_inquiries ORDER BY id DESC LIMIT ?");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Recent inquiries query error: " . $e->getMessage());
            return [];
        }
    }

    public static function logRating(string $pageUrl, int $rating, ?string $comments = null): void {
        try {
            $pdo = self::getConnection();
            if ($pdo) {
                $stmt = $pdo->prepare("INSERT INTO feedback (page_url, rating, comments) VALUES (?, ?, ?)");
                $stmt->execute([$pageUrl, max(1, min(5, $rating)), $comments]);
            }
        } catch (Exception $e) {
            error_log("Failed to log rating: " . $e->getMessage());
        }
    }

    public static function logContactInquiry(string $name, string $email, string $subject, string $message): bool {
        try {
            $pdo = self::getConnection();
            if ($pdo) {
                $ipHash = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
                $stmt = $pdo->prepare("INSERT INTO contact_inquiries (name, email, subject, message, ip_hash) VALUES (?, ?, ?, ?, ?)");
                return $stmt->execute([
                    substr(trim($name), 0, 100),
                    substr(trim($email), 0, 150),
                    substr(trim($subject), 0, 200),
                    substr(trim($message), 0, 3000),
                    $ipHash
                ]);
            }
            return false;
        } catch (Exception $e) {
            error_log("Failed to log contact inquiry: " . $e->getMessage());
            return false;
        }
    }
}
