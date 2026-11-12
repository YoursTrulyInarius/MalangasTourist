<?php
// app/models/Spot.php

class Spot {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        require_once __DIR__ . '/Review.php';
        Review::ensureModerationSchema($this->db);
        self::ensureMonitoringSchema($this->db);
        self::ensurePhotoUpdateSchema($this->db);
    }

    public static function ensurePhotoUpdateSchema($db) {
        $db->exec("CREATE TABLE IF NOT EXISTS spot_photo_updates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            spot_id INT NOT NULL,
            status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
            submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            reviewed_at DATETIME NULL,
            reviewed_by INT NULL,
            rejection_reason TEXT NULL,
            FOREIGN KEY (spot_id) REFERENCES spots(id) ON DELETE CASCADE,
            FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX (spot_id, status)
        )");
        $db->exec("CREATE TABLE IF NOT EXISTS spot_photo_update_files (
            id INT AUTO_INCREMENT PRIMARY KEY,
            update_id INT NOT NULL,
            file_path VARCHAR(255) NOT NULL,
            is_cover BOOLEAN NOT NULL DEFAULT FALSE,
            FOREIGN KEY (update_id) REFERENCES spot_photo_updates(id) ON DELETE CASCADE
        )");
    }

    public static function ensureMonitoringSchema($db) {
        $spotColumns = $db->query('SHOW COLUMNS FROM spots')->fetchAll(PDO::FETCH_COLUMN);
        $spotColumns = array_map('strval', $spotColumns);

        $monitorColumns = [
            'monitor_status' => "ALTER TABLE spots ADD COLUMN monitor_status ENUM('normal','warning','under_renovation','improved') NOT NULL DEFAULT 'normal'",
            'monitor_note' => "ALTER TABLE spots ADD COLUMN monitor_note TEXT NULL",
            'warning_sent_at' => "ALTER TABLE spots ADD COLUMN warning_sent_at DATETIME NULL",
            'improvement_deadline' => "ALTER TABLE spots ADD COLUMN improvement_deadline DATETIME NULL",
            'monitor_last_triggered_at' => "ALTER TABLE spots ADD COLUMN monitor_last_triggered_at DATETIME NULL",
            'renovation_note' => "ALTER TABLE spots ADD COLUMN renovation_note TEXT NULL",
            'renovation_proof_path' => "ALTER TABLE spots ADD COLUMN renovation_proof_path TEXT NULL",
            'monitor_submission_status' => "ALTER TABLE spots ADD COLUMN monitor_submission_status ENUM('pending','approved','declined','none') NOT NULL DEFAULT 'none'",
            'monitor_submission_reason' => "ALTER TABLE spots ADD COLUMN monitor_submission_reason TEXT NULL",
            'monitor_decline_reason' => "ALTER TABLE spots ADD COLUMN monitor_decline_reason TEXT NULL",
            'monitor_reviewed_at' => "ALTER TABLE spots ADD COLUMN monitor_reviewed_at DATETIME NULL",
            'monitor_submitted_at' => "ALTER TABLE spots ADD COLUMN monitor_submitted_at DATETIME NULL",
        ];

        foreach ($monitorColumns as $column => $sql) {
            if (!in_array($column, $spotColumns, true)) {
                $db->exec($sql);
            }
        }

        $userColumns = $db->query('SHOW COLUMNS FROM users')->fetchAll(PDO::FETCH_COLUMN);
        $userColumns = array_map('strval', $userColumns);
        if (!in_array('last_monitoring_read_at', $userColumns, true)) {
            $db->exec("ALTER TABLE users ADD COLUMN last_monitoring_read_at DATETIME NULL");
        }

        $db->exec("ALTER TABLE spots MODIFY COLUMN renovation_proof_path TEXT NULL");
        $db->exec("ALTER TABLE spot_monitoring MODIFY COLUMN proof_path TEXT NULL");

        $db->exec("CREATE TABLE IF NOT EXISTS spot_monitoring (
            id INT AUTO_INCREMENT PRIMARY KEY,
            spot_id INT NOT NULL,
            record_type ENUM('warning','update') NOT NULL,
            status ENUM('active','acknowledged','in_progress','completed') NOT NULL DEFAULT 'active',
            message TEXT NULL,
            proof_path TEXT NULL,
            deadline_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (spot_id) REFERENCES spots(id) ON DELETE CASCADE
        )");
    }

    public static function markMonitoringReadForUser($userId) {
        $userId = (int)$userId;
        if ($userId < 1) {
            return;
        }
        $db = Database::getInstance()->getConnection();
        self::ensureMonitoringSchema($db);
        $stmt = $db->prepare("UPDATE users SET last_monitoring_read_at = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
        if (isset($_SESSION['user']) && (int)($_SESSION['user']['id'] ?? 0) === $userId) {
            $_SESSION['user']['last_monitoring_read_at'] = date('Y-m-d H:i:s');
        }
    }

    public function getApproved() {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as category_name, c.icon as category_icon, 
                   (SELECT file_path FROM spot_photos sp WHERE sp.spot_id = s.id AND sp.is_cover = 1 LIMIT 1) as cover_photo,
                   (SELECT COALESCE(AVG(rating), 0) FROM reviews r WHERE r.spot_id = s.id AND r.status = 'approved') as avg_rating,
                   (SELECT COUNT(id) FROM reviews r WHERE r.spot_id = s.id AND r.status = 'approved') as review_count
            FROM spots s
            JOIN categories c ON s.category_id = c.id
            WHERE s.status = 'approved'
            ORDER BY review_count DESC, avg_rating DESC, s.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAll($search = '', $applicantType = '') {
        $query = "
            SELECT s.*, c.name as category_name, u.name as owner_name, u.applicant_type
            FROM spots s
            JOIN categories c ON s.category_id = c.id
            LEFT JOIN users u ON s.owner_user_id = u.id
        ";

        $conditions = [];
        $params = [];

        if ($search !== '') {
            $conditions[] = "LOWER(s.name) LIKE ?";
            $params[] = '%' . strtolower($search) . '%';
        }

        if ($applicantType === 'private' || $applicantType === 'public') {
            $conditions[] = "u.applicant_type = ?";
            $params[] = $applicantType;
        }

        if (!empty($conditions)) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $query .= "\n            ORDER BY s.created_at DESC\n        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as category_name, c.icon as category_icon,
                   u.name as owner_name, u.email as owner_email, u.phone as owner_phone,
                   (SELECT COALESCE(AVG(rating), 0) FROM reviews r WHERE r.spot_id = s.id AND r.status = 'approved') as avg_rating,
                   (SELECT COUNT(id) FROM reviews r WHERE r.spot_id = s.id AND r.status = 'approved') as review_count
            FROM spots s
            JOIN categories c ON s.category_id = c.id
            LEFT JOIN users u ON s.owner_user_id = u.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createOfficial($data) {
        $stmt = $this->db->prepare("
            INSERT INTO spots (
                owner_user_id, name, category_id, description, barangay, 
                latitude, longitude, contact_number, fb_link, hours, 
                price_range, status, source
            ) VALUES (
                NULL, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, 
                ?, 'approved', 'lgu'
            )
        ");
        
        $stmt->execute([
            $data['name'], $data['category_id'], $data['description'], $data['barangay'],
            $data['latitude'], $data['longitude'], $data['contact_number'], $data['fb_link'], $data['hours'],
            $data['price_range']
        ]);
        
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE spots SET
                name = ?, category_id = ?, description = ?, barangay = ?,
                latitude = ?, longitude = ?, contact_number = ?, fb_link = ?,
                hours = ?, price_range = ?, operational_status = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['name'], $data['category_id'], $data['description'], $data['barangay'],
            $data['latitude'], $data['longitude'], $data['contact_number'], $data['fb_link'],
            $data['hours'], $data['price_range'], $data['operational_status'],
            $id
        ]);
    }

    public function getMonitoringList() {
        $stmt = $this->db->query("SELECT s.*, c.name AS category_name, u.name AS owner_name, u.email AS owner_email, u.applicant_type,
                (SELECT ROUND(AVG(r.rating), 1) FROM reviews r WHERE r.spot_id = s.id AND r.status = 'approved') AS avg_rating,
                (SELECT COUNT(*) FROM reviews r WHERE r.spot_id = s.id AND r.status = 'approved') AS review_count
            FROM spots s
            JOIN categories c ON c.id = s.category_id
            LEFT JOIN users u ON u.id = s.owner_user_id
            WHERE s.status = 'approved'
            ORDER BY CASE s.monitor_status WHEN 'warning' THEN 0 WHEN 'under_renovation' THEN 1 WHEN 'improved' THEN 2 ELSE 3 END, COALESCE(s.warning_sent_at, s.created_at) DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function expireMonitoringListings() {
        $stmt = $this->db->prepare("UPDATE spots
            SET status = 'rejected',
                rejection_reason = 'No valid improvement update was submitted within the 3-month monitoring period. The listing was deactivated.',
                monitor_submission_status = 'declined',
                monitor_decline_reason = 'No valid update submitted before the improvement deadline.'
            WHERE status = 'approved'
              AND monitor_status IN ('warning','under_renovation')
              AND improvement_deadline IS NOT NULL
              AND improvement_deadline < NOW()
              AND (monitor_submission_status IS NULL OR monitor_submission_status IN ('none','declined'))");
        return $stmt->execute();
    }

    public function getMonitoringForOwner($ownerId) {
        $stmt = $this->db->prepare("SELECT s.*, c.name AS category_name,
                (SELECT ROUND(AVG(r.rating), 1) FROM reviews r WHERE r.spot_id = s.id AND r.status = 'approved') AS avg_rating,
                (SELECT COUNT(*) FROM reviews r WHERE r.spot_id = s.id AND r.status = 'approved') AS review_count
            FROM spots s
            JOIN categories c ON c.id = s.category_id
            WHERE s.owner_user_id = ?
            ORDER BY s.created_at DESC");
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonitoringDetail($spotId) {
        $stmt = $this->db->prepare("SELECT s.*, c.name AS category_name, u.name AS owner_name, u.email AS owner_email,
                (SELECT ROUND(AVG(r.rating), 1) FROM reviews r WHERE r.spot_id = s.id AND r.status = 'approved') AS avg_rating,
                (SELECT COUNT(*) FROM reviews r WHERE r.spot_id = s.id AND r.status = 'approved') AS review_count
            FROM spots s
            JOIN categories c ON c.id = s.category_id
            LEFT JOIN users u ON u.id = s.owner_user_id
            WHERE s.id = ? AND s.status = 'approved'
            LIMIT 1");
        $stmt->execute([$spotId]);
        $spot = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$spot) {
            return null;
        }

        $history = $this->db->prepare("SELECT record_type, status, message, proof_path, deadline_at, created_at
            FROM spot_monitoring
            WHERE spot_id = ?
            ORDER BY created_at DESC");
        $history->execute([$spotId]);
        $spot['history'] = $history->fetchAll(PDO::FETCH_ASSOC);

        return $spot;
    }

    public function flagLowRatingSpot($spotId, $message = null) {
        $message = $message ?: 'This listing has received several low ratings and needs review and improvement.';
        $deadline = date('Y-m-d H:i:s', strtotime('+3 months'));

        $stmt = $this->db->prepare("UPDATE spots SET monitor_status = 'warning', monitor_note = ?, warning_sent_at = NOW(), improvement_deadline = ?, monitor_last_triggered_at = NOW() WHERE id = ?");
        $stmt->execute([$message, $deadline, $spotId]);

        $this->logMonitoringEvent($spotId, 'warning', $message, null, $deadline, 'active');
        return $stmt->rowCount() > 0;
    }

    public function markMonitoringUpdate($spotId, $ownerId, $status, $note, $proofPath = null) {
        $status = in_array($status, ['under_renovation', 'improved'], true) ? $status : 'under_renovation';
        $opStatus = ($status === 'improved') ? 'open' : 'maintenance';
        $stmt = $this->db->prepare("UPDATE spots SET
                monitor_status = ?,
                operational_status = ?,
                renovation_note = ?,
                renovation_proof_path = ?,
                monitor_note = ?,
                monitor_submission_status = 'pending',
                monitor_submission_reason = ?,
                monitor_decline_reason = NULL,
                monitor_reviewed_at = NULL,
                monitor_submitted_at = NOW(),
                improvement_deadline = COALESCE(improvement_deadline, DATE_ADD(NOW(), INTERVAL 3 MONTH))
            WHERE id = ? AND owner_user_id = ?");
        $stmt->execute([$status, $opStatus, $note, $proofPath, $note, $note, $spotId, $ownerId]);

        if ($stmt->rowCount() > 0) {
            $this->logMonitoringEvent($spotId, 'update', $note, $proofPath, null, $status === 'improved' ? 'completed' : 'in_progress');
            return true;
        }

        return false;
    }

    public function reviewMonitoringSubmission($spotId, $decision, $reason, $reviewerId) {
        $decision = in_array($decision, ['approved', 'declined'], true) ? $decision : 'declined';
        $reason = trim((string)$reason);

        if ($decision === 'approved') {
            $approvedMessage = $reason !== '' ? $reason : 'Owner submitted a valid improvement update and it was verified by the LGU.';
            $stmt = $this->db->prepare("UPDATE spots SET
                    monitor_status = 'normal',
                    operational_status = 'open',
                    monitor_submission_status = 'approved',
                    monitor_submission_reason = ?,
                    monitor_decline_reason = NULL,
                    monitor_reviewed_at = NOW(),
                    reviewed_by = ?,
                    improvement_deadline = NULL,
                    warning_sent_at = NULL,
                    monitor_note = COALESCE(CONCAT(monitor_note, '\n\nApproved: ', ?), ?),
                    renovation_note = NULL,
                    renovation_proof_path = NULL
                WHERE id = ?");
            $ok = $stmt->execute([$approvedMessage, $reviewerId, $approvedMessage, $approvedMessage, $spotId]);
            $this->logMonitoringEvent($spotId, 'update', $approvedMessage, null, null, 'completed');
            return $ok && $stmt->rowCount() > 0;
        }

        if ($reason === '') {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE spots SET
                monitor_status = 'warning',
                monitor_submission_status = 'declined',
                monitor_submission_reason = ?,
                monitor_decline_reason = ?,
                monitor_reviewed_at = NOW(),
                reviewed_by = ?,
                monitor_note = COALESCE(CONCAT(monitor_note, '\n\nDeclined: ', ?), ?),
                improvement_deadline = COALESCE(improvement_deadline, DATE_ADD(NOW(), INTERVAL 3 MONTH))
            WHERE id = ?");
        $note = 'Declined: ' . $reason;
        $ok = $stmt->execute([$reason, $reason, $reviewerId, $reason, $note, $spotId]);
        $this->logMonitoringEvent($spotId, 'update', $reason, null, null, 'acknowledged');
        return $ok && $stmt->rowCount() > 0;
    }

    public function logMonitoringEvent($spotId, $recordType, $message, $proofPath = null, $deadline = null, $status = 'active') {
        $stmt = $this->db->prepare("INSERT INTO spot_monitoring (spot_id, record_type, status, message, proof_path, deadline_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$spotId, $recordType, $status, $message, $proofPath, $deadline]);
    }

    public function evaluateSpotRatingMonitoring($spotId) {
        $stmt = $this->db->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS review_count FROM reviews WHERE spot_id = ? AND status = 'approved'");
        $stmt->execute([$spotId]);
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$summary) {
            return false;
        }

        $avgRating = (float)($summary['avg_rating'] ?? 0);
        $reviewCount = (int)($summary['review_count'] ?? 0);

        if ($reviewCount >= 3 && $avgRating <= 2.5) {
            $this->flagLowRatingSpot($spotId, 'This listing has received multiple low ratings and requires review and improvement.');
            return true;
        }

        if ($reviewCount >= 3 && $avgRating >= 3.5) {
            $this->db->prepare("UPDATE spots SET monitor_status = 'normal', monitor_note = NULL, renovation_note = NULL, renovation_proof_path = NULL WHERE id = ? AND monitor_status IN ('warning', 'under_renovation', 'improved')")->execute([$spotId]);
        }

        return false;
    }

    public function sendOwnerWarning($spotId, $note = null) {
        $stmt = $this->db->prepare("SELECT s.id, s.name, s.monitor_status, s.warning_sent_at, s.improvement_deadline, u.email, u.name AS owner_name
            FROM spots s
            LEFT JOIN users u ON u.id = s.owner_user_id
            WHERE s.id = ?");
        $stmt->execute([$spotId]);
        $spot = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$spot || empty($spot['email'])) {
            return false;
        }

        $customNote = trim((string)($note ?? ''));
        $warningNote = $customNote !== '' ? $customNote : 'Admin warning: this tourist spot requires renovation or service improvements based on recent ratings and reviews.';
        $deadline = date('Y-m-d', strtotime('+3 months'));
        $stmt = $this->db->prepare("UPDATE spots SET monitor_status = 'warning', warning_sent_at = NOW(), improvement_deadline = ?, monitor_note = ?, monitor_submission_status = 'none' WHERE id = ?");
        $stmt->execute([$deadline . ' 00:00:00', $warningNote, $spotId]);

        $this->logMonitoringEvent($spotId, 'warning', $warningNote, null, $deadline . ' 00:00:00', 'acknowledged');
        return true;
    }

    public function updateStatusAndCoords($id, $data) {
        $spot = $this->findById($id);
        $requestedStatus = $data['operational_status'] ?? 'open';

        if (($spot['monitor_status'] ?? 'normal') === 'under_renovation' && $requestedStatus === 'open') {
            $requestedStatus = 'maintenance';
        }

        // Resets to pending if lat/lng changed (re-approval required)
        $stmt = $this->db->prepare("
            UPDATE spots SET
                description = ?, barangay = ?, contact_number = ?, fb_link = ?,
                hours = ?, price_range = ?, operational_status = ?,
                latitude = ?, longitude = ?,
                status = CASE WHEN (latitude != ? OR longitude != ?) THEN 'pending' ELSE status END
            WHERE id = ? AND owner_user_id = ?
        ");
        return $stmt->execute([
            $data['description'], $data['barangay'], $data['contact_number'], $data['fb_link'],
            $data['hours'], $data['price_range'], $requestedStatus,
            $data['latitude'], $data['longitude'],
            $data['latitude'], $data['longitude'],
            $id, $data['owner_user_id']
        ]);
    }

    public function getPendingPhotoUpdate($spotId) {
        $stmt = $this->db->prepare("SELECT * FROM spot_photo_updates WHERE spot_id = ? AND status = 'pending' ORDER BY submitted_at DESC LIMIT 1");
        $stmt->execute([$spotId]);
        $update = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$update) {
            return null;
        }

        $files = $this->db->prepare("SELECT file_path, is_cover FROM spot_photo_update_files WHERE update_id = ? ORDER BY is_cover DESC, id ASC");
        $files->execute([$update['id']]);
        $update['photos'] = $files->fetchAll(PDO::FETCH_ASSOC);
        return $update;
    }

    public function hasPendingPhotoUpdate($spotId) {
        $stmt = $this->db->prepare("SELECT 1 FROM spot_photo_updates WHERE spot_id = ? AND status = 'pending' LIMIT 1");
        $stmt->execute([$spotId]);
        return (bool)$stmt->fetchColumn();
    }

    public function submitPhotoUpdate($spotId, $ownerId, $photos) {
        $this->db->beginTransaction();
        try {
            $spot = $this->db->prepare("SELECT id FROM spots WHERE id = ? AND owner_user_id = ? AND source = 'business' AND status = 'approved' FOR UPDATE");
            $spot->execute([$spotId, $ownerId]);
            if (!$spot->fetchColumn()) {
                $this->db->rollBack();
                return false;
            }

            $pending = $this->db->prepare("SELECT id FROM spot_photo_updates WHERE spot_id = ? AND status = 'pending' LIMIT 1 FOR UPDATE");
            $pending->execute([$spotId]);
            if ($pending->fetchColumn()) {
                $this->db->rollBack();
                return false;
            }

            $insert = $this->db->prepare("INSERT INTO spot_photo_updates (spot_id, status) VALUES (?, 'pending')");
            $insert->execute([$spotId]);
            $updateId = $this->db->lastInsertId();
            $photoInsert = $this->db->prepare("INSERT INTO spot_photo_update_files (update_id, file_path, is_cover) VALUES (?, ?, ?)");
            foreach ($photos as $index => $filePath) {
                $photoInsert->execute([$updateId, $filePath, $index === 0 ? 1 : 0]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function reviewPhotoUpdate($spotId, $reviewerId, $action, $reason = '') {
        $this->db->beginTransaction();
        try {
            $updateStmt = $this->db->prepare("SELECT id FROM spot_photo_updates WHERE spot_id = ? AND status = 'pending' ORDER BY submitted_at DESC LIMIT 1 FOR UPDATE");
            $updateStmt->execute([$spotId]);
            $updateId = $updateStmt->fetchColumn();
            if (!$updateId) {
                $this->db->rollBack();
                return false;
            }

            $filesStmt = $this->db->prepare("SELECT file_path, is_cover FROM spot_photo_update_files WHERE update_id = ? ORDER BY is_cover DESC, id ASC");
            $filesStmt->execute([$updateId]);
            $photos = $filesStmt->fetchAll(PDO::FETCH_ASSOC);

            if ($action === 'approve') {
                $this->db->prepare("DELETE FROM spot_photos WHERE spot_id = ?")->execute([$spotId]);
                $insert = $this->db->prepare("INSERT INTO spot_photos (spot_id, file_path, is_cover) VALUES (?, ?, ?)");
                foreach ($photos as $photo) {
                    $insert->execute([$spotId, $photo['file_path'], $photo['is_cover']]);
                }
            }

            $status = $action === 'approve' ? 'approved' : 'rejected';
            $review = $this->db->prepare("UPDATE spot_photo_updates SET status = ?, reviewed_at = NOW(), reviewed_by = ?, rejection_reason = ? WHERE id = ?");
            $review->execute([$status, $reviewerId, $action === 'reject' ? $reason : null, $updateId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM spots WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getReportSummary() {
        $db = $this->db;
        return [
            'by_category'   => $db->query("
                SELECT c.name, c.icon, COUNT(s.id) as total,
                       SUM(CASE WHEN s.status='approved' THEN 1 ELSE 0 END) as approved,
                       SUM(CASE WHEN s.status='pending'  THEN 1 ELSE 0 END) as pending,
                       SUM(CASE WHEN s.status='rejected' THEN 1 ELSE 0 END) as rejected
                FROM categories c
                LEFT JOIN spots s ON s.category_id = c.id
                GROUP BY c.id ORDER BY total DESC
            ")->fetchAll(),
            'monthly_apps'  => $db->query("
                SELECT DATE_FORMAT(created_at,'%M %Y') as month,
                       COUNT(*) as total,
                       SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) as approved,
                       SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END) as rejected
                FROM spots WHERE source='business'
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY DATE_FORMAT(created_at, '%Y-%m') DESC LIMIT 6
            ")->fetchAll(),
            'top_rated'     => $db->query("
                SELECT s.name, c.name as category, s.operational_status,
                       ROUND(AVG(r.rating),1) as avg_rating, COUNT(r.id) as review_count
                FROM spots s
                JOIN categories c ON s.category_id = c.id
                LEFT JOIN reviews r ON r.spot_id = s.id AND r.status = 'approved'
                WHERE s.status = 'approved'
                GROUP BY s.id ORDER BY avg_rating DESC, review_count DESC LIMIT 10
            ")->fetchAll(),
            'totals'        => $db->query("
                SELECT
                    COUNT(*) as total_spots,
                    SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status='pending'  THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN source='lgu'      THEN 1 ELSE 0 END) as lgu_spots,
                    SUM(CASE WHEN source='business' THEN 1 ELSE 0 END) as business_spots
                FROM spots
            ")->fetch(),
            'review_totals' => $db->query("
                SELECT COUNT(*) as total_reviews, ROUND(AVG(rating),1) as overall_avg
                FROM reviews WHERE status = 'approved'
            ")->fetch(),
        ];
    }

    public function addPhoto($spotId, $filePath, $isCover = false) {
        $stmt = $this->db->prepare("INSERT INTO spot_photos (spot_id, file_path, is_cover) VALUES (?, ?, ?)");
        return $stmt->execute([$spotId, $filePath, $isCover ? 1 : 0]);
    }

    public function getPhotos($spotId) {
        $stmt = $this->db->prepare("SELECT * FROM spot_photos WHERE spot_id = ? ORDER BY is_cover DESC, uploaded_at ASC");
        $stmt->execute([$spotId]);
        return $stmt->fetchAll();
    }
}
