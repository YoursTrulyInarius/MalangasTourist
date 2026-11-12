<?php
// app/models/User.php

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->ensurePasswordResetsTableExists();
        $this->ensureApplicantTypeColumnExists();
    }

    private function ensurePasswordResetsTableExists() {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token VARCHAR(64) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)
SQL;
        $this->db->exec($sql);
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createPasswordResetToken($email, $token, $expiresAt) {
        $user = $this->findByEmail($email);
        if (!$user) {
            return false;
        }

        $this->deleteExpiredPasswordResetTokens();

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?) " .
                "ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)"
            );
            $stmt->execute([$user['id'], $token, $expiresAt]);
            return $user;
        } catch (PDOException $e) {
            error_log('Password reset token insert failed: ' . $e->getMessage());
            return false;
        }
    }

    public function findByResetToken($token) {
        $this->deleteExpiredPasswordResetTokens();

        $stmt = $this->db->prepare(
            "SELECT u.* FROM users u
             JOIN password_resets pr ON pr.user_id = u.id
             WHERE pr.token = ? AND pr.expires_at >= NOW()
             LIMIT 1"
        );
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public function deleteExpiredPasswordResetTokens() {
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE expires_at < NOW()");
        $stmt->execute();
    }

    public function deletePasswordResetToken($userId) {
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $stmt->execute([$userId]);
    }

    public function updatePassword($userId, $password) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        return $stmt->execute([$password_hash, $userId]);
    }

    private function ensureApplicantTypeColumnExists() {
        try {
            $this->db->exec("ALTER TABLE users ADD COLUMN applicant_type ENUM('private','public') NOT NULL DEFAULT 'private'");
        } catch (PDOException $e) {
            // MySQL error code 1060 = Duplicate column name
            if (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] !== 1060) {
                throw $e;
            }
            // If it's error 1060, column already exists, so we can safely ignore it
        }
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO users (name, email, phone, password_hash, role, applicant_type) VALUES (?, ?, ?, ?, ?, ?)");
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $applicantType = in_array($data['applicant_type'] ?? '', ['private', 'public'], true) ? $data['applicant_type'] : 'private';

        try {
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone'] ?? null,
                $password_hash,
                $data['role'] ?? 'business',
                $applicantType
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // Handle duplicate email
            if ($e->getCode() == 23000) {
                return false;
            }
            throw $e;
        }
    }
}
