<?php
// app/controllers/ApplicationController.php

class ApplicationController {
    public function __construct() {
        Auth::requireRole('lgu');
    }

    public function index() {
        $title = "Applications Inbox - LGU";
        require_once '../app/models/Application.php';
        require_once '../app/models/Category.php';
        $appModel = new Application();
        $catModel = new Category();

        $filterCategory = trim($_GET['category'] ?? '');
        $filterStatus   = trim($_GET['status'] ?? '');

        $applications = $appModel->getAllApplicationsFiltered(
            $filterCategory ?: null,
            $filterStatus   ?: null
        );
        $categories = $catModel->getAll();

        require_once '../app/views/lgu/applications.php';
    }

    public function show($id) {
        require_once '../app/models/Spot.php';
        $spotModel = new Spot();
        $application = $spotModel->findById($id);

        if (!$application || $application['source'] !== 'business') {
            http_response_code(404);
            die("Application not found.");
        }

        $pendingPhotoUpdate = $spotModel->getPendingPhotoUpdate($id);
        $currentPhotos = $spotModel->getPhotos($id);
        $title = "Review Application: " . htmlspecialchars($application['name']);
        require_once '../app/views/lgu/application-view.php';
    }

    public function reviewPhotoUpdate($id) {
        $action = $_POST['action'] ?? '';
        $reason = trim($_POST['reason'] ?? '');
        if (!in_array($action, ['approve', 'reject'], true) || ($action === 'reject' && $reason === '')) {
            $_SESSION['swal_error'] = 'Choose approve or reject, and provide a reason when rejecting a photo update.';
            header("Location: " . BASE_URL . "/lgu/applications/" . (int)$id);
            exit();
        }

        require_once '../app/models/Spot.php';
        $spotModel = new Spot();
        $spot = $spotModel->findById($id);
        if (!$spot || $spot['source'] !== 'business') {
            http_response_code(404);
            die("Business listing not found.");
        }

        if (!$spotModel->reviewPhotoUpdate($id, Auth::user()['id'], $action, $reason)) {
            $_SESSION['swal_error'] = 'There is no pending photo update to review.';
        } else {
            $_SESSION['swal_success'] = $action === 'approve'
                ? 'Photo update approved. The new photos are now live.'
                : 'Photo update rejected. The current live photos remain unchanged.';
        }
        header("Location: " . BASE_URL . "/lgu/applications/" . (int)$id);
        exit();
    }

    public function approveReject() {
        $spotId = $_POST['spot_id'] ?? '';
        $action = $_POST['action'] ?? ''; // 'approve' or 'reject'
        $reason = $_POST['reason'] ?? '';

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if (empty($spotId) || empty($action)) {
            if ($isAjax) {
                require_once '../app/core/Response.php';
                Response::json(['error' => 'Invalid request.'], 400);
                return;
            }
            die("Invalid request.");
        }

        if ($action === 'reject' && empty($reason)) {
            if ($isAjax) {
                require_once '../app/core/Response.php';
                Response::json(['error' => 'A reason is required for rejection.'], 400);
                return;
            }
            die("A reason is required for rejection.");
        }

        require_once '../app/models/Spot.php';
        $db = Database::getInstance()->getConnection();
        
        $newStatus = $action === 'approve' ? 'approved' : 'rejected';
        
        $db->beginTransaction();
        try {
            // Update status
            $stmt = $db->prepare("UPDATE spots SET status = ?, rejection_reason = ?, reviewed_by = ?, reviewed_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$newStatus, $action === 'reject' ? $reason : null, Auth::user()['id'], $spotId]);

            // Log action
            $logStmt = $db->prepare("INSERT INTO application_logs (spot_id, action, performed_by, comment) VALUES (?, ?, ?, ?)");
            $logStmt->execute([$spotId, $newStatus, Auth::user()['id'], $reason]);

            $db->commit();
            
            $emailSent = false;
            // Fetch owner email and spot name
            $emailStmt = $db->prepare("SELECT u.email, s.name FROM spots s JOIN users u ON s.owner_user_id = u.id WHERE s.id = ?");
            $emailStmt->execute([$spotId]);
            $ownerData = $emailStmt->fetch();

            if ($ownerData && !empty($ownerData['email'])) {
                require_once '../app/services/EmailService.php';
                $emailService = new EmailService();
                $emailSent = $emailService->sendApplicationResult($ownerData['email'], $ownerData['name'], $newStatus, $action === 'reject' ? $reason : '');
            }
            
            $msg = "Application has been successfully " . $newStatus . ($emailSent ? " and an email notification has been sent to the applicant!" : "!");
            
            if ($isAjax) {
                require_once '../app/core/Response.php';
                Response::json([
                    'success' => true,
                    'message' => $msg
                ]);
                return;
            }

            $_SESSION['swal_success'] = $msg;
        } catch (Exception $e) {
            $db->rollBack();
            if ($isAjax) {
                require_once '../app/core/Response.php';
                Response::json(['error' => 'Database error: ' . $e->getMessage()], 500);
                return;
            }
            die("Database error: " . $e->getMessage());
        }

        header("Location: " . BASE_URL . "/lgu/applications");
        exit();
    }
}
