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
        ");
    }

    public static function logConversion(string $toolType, int $fileSize): void {
        try {
            $pdo = self::getConnection();
            if ($pdo) {
                $ipHash = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
                $stmt = $pdo->prepare("INSERT INTO conversion_stats (tool_type, file_size, ip_hash) VALUES (?, ?, ?)");
                $stmt->execute([$toolType, $fileSize, $ipHash]);
            }
        } catch (Exception $e) {
            // Fail silently so database issues never interrupt conversion UX
            error_log("Failed to log conversion: " . $e->getMessage());
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
}
