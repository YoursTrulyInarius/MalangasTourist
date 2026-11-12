<?php
// app/models/Review.php

class Review {
    private static $moderationSchemaReady = false;
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureReviewPhotosTableExists();
        $this->ensureReviewsTableSupportsAnonymousUsers();
        self::ensureModerationSchema($this->db);
    }

    private function ensureReviewPhotosTableExists() {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS review_photos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  review_id INT NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE
)
SQL;
        $this->db->exec($sql);
    }

    private function ensureReviewsTableSupportsAnonymousUsers() {
        try {
            $this->db->exec('ALTER TABLE reviews MODIFY user_id INT NULL');
        } catch (PDOException $e) {
            // Ignore if the column is already nullable or the database does not support this change.
        }
    }

    public static function ensureModerationSchema($db) {
        if (self::$moderationSchemaReady) return;

        $columns = $db->query('SHOW COLUMNS FROM reviews')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('status', $columns, true)) {
            $db->exec("ALTER TABLE reviews ADD COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
            $db->exec("UPDATE reviews SET status = 'approved'");
            $columns[] = 'status';
        }
        if (!in_array('reviewed_by', $columns, true)) {
            $db->exec('ALTER TABLE reviews ADD COLUMN reviewed_by INT NULL');
        }
        if (!in_array('reviewed_at', $columns, true)) {
            $db->exec('ALTER TABLE reviews ADD COLUMN reviewed_at TIMESTAMP NULL DEFAULT NULL');
        }
        if (!in_array('moderation_note', $columns, true)) {
            $db->exec('ALTER TABLE reviews ADD COLUMN moderation_note TEXT NULL');
        }
        self::$moderationSchemaReady = true;
    }

    public function submit($spotId, $userId, $rating, $comment) {
        $normalizedUserId = empty($userId) ? null : (int)$userId;
        $stmt = $this->db->prepare("SELECT id FROM reviews WHERE spot_id = ? AND user_id IS NOT NULL AND user_id = ? LIMIT 1");
        $stmt->execute([$spotId, $normalizedUserId]);
        $reviewId = $stmt->fetchColumn();

        if ($reviewId) {
            $update = $this->db->prepare("UPDATE reviews SET rating = ?, comment = ?, status = 'pending', reviewed_by = NULL, reviewed_at = NULL, moderation_note = NULL, created_at = CURRENT_TIMESTAMP WHERE id = ?");
            $update->execute([$rating, $comment, $reviewId]);
            return (int)$reviewId;
        }

        $insert = $this->db->prepare("INSERT INTO reviews (spot_id, user_id, rating, comment, status) VALUES (?, ?, ?, ?, 'pending')");
        $insert->execute([$spotId, $normalizedUserId, $rating, $comment]);
        return (int)$this->db->lastInsertId();
    }

    public function findByIdForUser($reviewId, $userId) {
        $stmt = $this->db->prepare("SELECT id, spot_id, user_id, rating, comment FROM reviews WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$reviewId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findBySpotAndUser($spotId, $userId) {
        $stmt = $this->db->prepare("SELECT id FROM reviews WHERE spot_id = ? AND user_id IS NOT NULL AND user_id = ? LIMIT 1");
        $stmt->execute([$spotId, $userId]);
        return $stmt->fetchColumn();
    }

    public function updateReview($reviewId, $rating, $comment) {
        $stmt = $this->db->prepare("UPDATE reviews SET rating = ?, comment = ?, created_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$rating, $comment, $reviewId]);
    }

    public function getBySpotId($spotId) {
        $stmt = $this->db->prepare("
            SELECT r.*, COALESCE(u.name, 'Anonymous') as user_name
            FROM reviews r
            LEFT JOIN users u ON r.user_id = u.id
            WHERE r.spot_id = ? AND r.status = 'approved'
            ORDER BY r.rating DESC, r.created_at DESC
        ");
        $stmt->execute([$spotId]);
        $reviews = $stmt->fetchAll();

        foreach ($reviews as &$review) {
            $review['photos'] = $this->getPhotosByReviewId($review['id']);
        }

        return $reviews;
    }

    public function getPendingForModeration() {
        $stmt = $this->db->query("
            SELECT r.*, s.name AS spot_name, u.name AS reviewer_name, u.email AS reviewer_email
            FROM reviews r
            JOIN spots s ON s.id = r.spot_id
            LEFT JOIN users u ON u.id = r.user_id
            WHERE r.status = 'pending'
            ORDER BY r.created_at ASC, r.rating DESC
        ");
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reviews as &$review) {
            $review['photos'] = $this->getPhotosByReviewId($review['id']);
        }
        return $reviews;
    }

    public function moderate($reviewId, $status, $adminId, $note = '') {
        if (!in_array($status, ['approved', 'rejected'], true)) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE reviews SET status = ?, reviewed_by = ?, reviewed_at = CURRENT_TIMESTAMP, moderation_note = ? WHERE id = ? AND status = 'pending'");
        $updated = $stmt->execute([$status, $adminId, $note !== '' ? $note : null, $reviewId]);

        if ($updated && $status === 'approved') {
            require_once __DIR__ . '/Spot.php';
            $spotStmt = $this->db->prepare("SELECT spot_id FROM reviews WHERE id = ?");
            $spotStmt->execute([$reviewId]);
            $spotId = $spotStmt->fetchColumn();
            if ($spotId) {
                $spotModel = new Spot();
                $spotModel->evaluateSpotRatingMonitoring((int)$spotId);
            }
        }

        return $stmt->rowCount() > 0;
    }

    public function getPhotosByReviewId($reviewId) {
        $stmt = $this->db->prepare("SELECT file_path FROM review_photos WHERE review_id = ? ORDER BY uploaded_at ASC");
        $stmt->execute([$reviewId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function deleteReview($reviewId) {
        $stmt = $this->db->prepare('DELETE FROM review_photos WHERE review_id = ?');
        $stmt->execute([$reviewId]);

        $stmt = $this->db->prepare('DELETE FROM reviews WHERE id = ?');
        return $stmt->execute([$reviewId]);
    }

    public function savePhotos($reviewId, $files) {
        if (empty($files['name']) || !is_array($files['name']) || empty($files['name'][0])) {
            return 0;
        }

        $photoCount = count(array_filter($files['name'], static fn($name) => !empty($name)));
        if ($photoCount > 3) {
            return false;
        }

        $uploadDir = '../public/assets/images/review-photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Delete existing photos for this review so user can update their set.
        $delete = $this->db->prepare("DELETE FROM review_photos WHERE review_id = ?");
        $delete->execute([$reviewId]);

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $saved = 0;
        foreach ($files['tmp_name'] as $index => $tmpName) {
            if ($saved >= 3) {
                break;
            }

            if (empty($files['name'][$index]) || $files['error'][$index] !== UPLOAD_ERR_OK) {
                continue;
            }

            $fileName = $files['name'][$index];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions, true)) {
                continue;
            }

            $imageInfo = @getimagesize($tmpName);
            if ($imageInfo === false) {
                continue;
            }

            $newFileName = md5(time() . $fileName . rand()) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;
            if (move_uploaded_file($tmpName, $destPath)) {
                $stmt = $this->db->prepare("INSERT INTO review_photos (review_id, file_path) VALUES (?, ?)");
                $stmt->execute([$reviewId, 'assets/images/review-photos/' . $newFileName]);
                $saved++;
            }
        }

        return $saved;
    }
}
