<?php
// app/models/Application.php

class Application {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        require_once __DIR__ . '/Spot.php';
        Spot::ensurePhotoUpdateSchema($this->db);
    }

    public function submit($data) {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(" 
                INSERT INTO spots (
                    owner_user_id, name, category_id, description, barangay, 
                    latitude, longitude, contact_number, fb_link, hours, 
                    price_range, bir_number, business_permit, status, source
                ) VALUES (
                    ?, ?, ?, ?, ?, 
                    ?, ?, ?, ?, ?, 
                    ?, ?, ?, 'pending', 'business'
                )
            ");
            
            $stmt->execute([
                $data['owner_user_id'], $data['name'], $data['category_id'], $data['description'], $data['barangay'],
                $data['latitude'], $data['longitude'], $data['contact_number'], $data['fb_link'], $data['hours'],
                $data['price_range'], $data['bir_number'], $data['business_permit']
            ]);
            
            $spotId = $this->db->lastInsertId();

            $logStmt = $this->db->prepare("INSERT INTO application_logs (spot_id, action) VALUES (?, 'submitted')");
            $logStmt->execute([$spotId]);

            $this->db->commit();
            return $spotId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as category_name,
                   EXISTS(SELECT 1 FROM spot_photo_updates pu WHERE pu.spot_id = s.id AND pu.status = 'pending') as pending_photo_update
            FROM spots s
            JOIN categories c ON s.category_id = c.id
            WHERE s.owner_user_id = ?
            ORDER BY s.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAllApplications() {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as category_name, u.name as business_name,
                   EXISTS(SELECT 1 FROM spot_photo_updates pu WHERE pu.spot_id = s.id AND pu.status = 'pending') as pending_photo_update
            FROM spots s
            JOIN categories c ON s.category_id = c.id
            JOIN users u ON s.owner_user_id = u.id
            WHERE s.source = 'business'
            ORDER BY s.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllApplicationsFiltered($categoryId = null, $status = null) {
        $conditions = ["s.source = 'business'"];
        $params = [];

        if (!empty($categoryId)) {
            $conditions[] = "s.category_id = ?";
            $params[] = $categoryId;
        }
        if (!empty($status)) {
            if ($status === 'photo_update') {
                $conditions[] = "EXISTS(SELECT 1 FROM spot_photo_updates pu WHERE pu.spot_id = s.id AND pu.status = 'pending')";
            } else {
                $conditions[] = "s.status = ?";
                $params[] = $status;
            }
        }

        $where = implode(' AND ', $conditions);
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as category_name, u.name as business_name,
                   EXISTS(SELECT 1 FROM spot_photo_updates pu WHERE pu.spot_id = s.id AND pu.status = 'pending') as pending_photo_update
            FROM spots s
            JOIN categories c ON s.category_id = c.id
            JOIN users u ON s.owner_user_id = u.id
            WHERE $where
            ORDER BY pending_photo_update DESC, s.created_at DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getUsedCategories() {
        $stmt = $this->db->query("
            SELECT DISTINCT c.id, c.name
            FROM spots s
            JOIN categories c ON s.category_id = c.id
            WHERE s.source = 'business'
            ORDER BY c.name ASC
        ");
        return $stmt->fetchAll();
    }

}
