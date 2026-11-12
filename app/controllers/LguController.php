<?php
// app/controllers/LguController.php

class LguController {
    public function __construct() {
        Auth::requireRole('lgu');
    }

    public function dashboard() {
        $title = "LGU Dashboard - MTMS";
        require_once '../app/models/Spot.php';
        $db = Database::getInstance()->getConnection();
        
        $totalApproved = $db->query("SELECT COUNT(*) FROM spots WHERE status = 'approved'")->fetchColumn();
        $totalPending  = $db->query("SELECT COUNT(*) FROM spots WHERE status = 'pending'")->fetchColumn();
        
        require_once '../app/views/lgu/dashboard.php';
    }

    public function spotsManage() {
        $title = "Manage Spots - LGU";
        require_once '../app/models/Spot.php';
        $spotModel = new Spot();

        $search = trim($_GET['search'] ?? '');
        $sourceFilter = $_GET['applicant_type'] ?? '';
        $filterApplied = !empty($search) || !empty($sourceFilter);

        $spots = $spotModel->getAll($search, $sourceFilter);
        require_once '../app/views/lgu/spots-manage.php';
    }

    public function spotCreate() {
        $title = "Add Official Spot - LGU";
        require_once '../app/models/Category.php';
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        require_once '../app/views/lgu/spot-create.php';
    }

    public function spotStore() {
        require_once '../app/models/Spot.php';
        $spotModel = new Spot();

        $data = [
            'name'           => $_POST['name'] ?? '',
            'category_id'    => $_POST['category_id'] ?? '',
            'description'    => $_POST['description'] ?? '',
            'barangay'       => $_POST['barangay'] ?? '',
            'latitude'       => $_POST['latitude'] ?? '',
            'longitude'      => $_POST['longitude'] ?? '',
            'contact_number' => $_POST['contact_number'] ?? '',
            'fb_link'        => $_POST['fb_link'] ?? '',
            'hours'          => $_POST['hours'] ?? '',
            'price_range'    => $_POST['price_range'] ?? ''
        ];

        if (empty($data['name']) || empty($data['category_id']) || empty($data['latitude']) || empty($data['longitude'])) {
            $_SESSION['swal_error'] = 'Please fill all required fields, including the map pin.';
            header('Location: ' . BASE_URL . '/lgu/spots/create');
            exit();
        }

        $spotId = $spotModel->createOfficial($data);

        // Handle File Uploads
        if (!empty($_FILES['spot_images']['name'][0])) {
            $uploadDir = '../public/assets/images/spots/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $isCover = true;
            $photoCount = 0;
            foreach ($_FILES['spot_images']['tmp_name'] as $key => $tmpName) {
                if ($photoCount >= 10) break;
                
                if ($_FILES['spot_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $tmpName;
                    $fileName = $_FILES['spot_images']['name'][$key];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    // Sanitize file name
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $destPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $spotModel->addPhoto($spotId, 'assets/images/spots/' . $newFileName, $isCover);
                        $isCover = false; // Only first image is cover
                        $photoCount++;
                    }
                }
            }
        }

        header("Location: " . BASE_URL . "/lgu/spots");
        exit();
    }

    public function spotEdit($id) {
        $title = "Edit Spot - LGU";
        require_once '../app/models/Spot.php';
        require_once '../app/models/Category.php';
        $spotModel     = new Spot();
        $categoryModel = new Category();
        $spot       = $spotModel->findById($id);
        $categories = $categoryModel->getAll();

        if (!$spot) {
            http_response_code(404);
            die("Spot not found.");
        }

        require_once '../app/views/lgu/spot-edit.php';
    }

    public function spotUpdate() {
        require_once '../app/models/Spot.php';
        $spotModel = new Spot();

        $id   = $_POST['spot_id'] ?? '';
        $data = [
            'name'               => $_POST['name'] ?? '',
            'category_id'        => $_POST['category_id'] ?? '',
            'description'        => $_POST['description'] ?? '',
            'barangay'           => $_POST['barangay'] ?? '',
            'latitude'           => $_POST['latitude'] ?? '',
            'longitude'          => $_POST['longitude'] ?? '',
            'contact_number'     => $_POST['contact_number'] ?? '',
            'fb_link'            => $_POST['fb_link'] ?? '',
            'hours'              => $_POST['hours'] ?? '',
            'price_range'        => $_POST['price_range'] ?? '',
            'operational_status' => $_POST['operational_status'] ?? 'open',
        ];

        if (empty($id) || empty($data['name']) || empty($data['category_id'])) {
            $_SESSION['swal_error'] = 'Please fill all required fields.';
            header('Location: ' . BASE_URL . '/lgu/spots/edit/' . $id);
            exit();
        }

        $spotModel->update($id, $data);
        header("Location: " . BASE_URL . "/lgu/spots");
        exit();
    }

    public function spotDelete($id) {
        require_once '../app/models/Spot.php';
        $spotModel = new Spot();
        $spotModel->delete($id);
        header("Location: " . BASE_URL . "/lgu/spots");
        exit();
    }

    public function reports() {
        $title = "Reports - LGU Admin";
        require_once '../app/models/Spot.php';
        $spotModel = new Spot();
        $report    = $spotModel->getReportSummary();
        require_once '../app/views/lgu/reports.php';
    }

    public function reviewsQueue() {
        $title = 'Review Moderation - LGU';
        $activePage = 'reviews';
        require_once '../app/models/Review.php';
        $reviewModel = new Review();
        $pendingReviews = $reviewModel->getPendingForModeration();
        require_once '../app/views/lgu/reviews.php';
    }

    public function reviewModerate($id) {
        require_once '../app/models/Review.php';
        $reviewModel = new Review();
        $reviewId = (int)$id;
        $decision = $_POST['decision'] ?? '';
        $note = trim($_POST['moderation_note'] ?? '');

        if ($reviewId < 1 || !in_array($decision, ['approved', 'rejected'], true)) {
            $_SESSION['swal_error'] = 'Choose whether to approve or reject this review.';
            header('Location: ' . BASE_URL . '/lgu/reviews');
            exit();
        }

        if (!$reviewModel->moderate($reviewId, $decision, Auth::user()['id'], $note)) {
            $_SESSION['swal_error'] = 'This review is no longer pending or could not be updated.';
            header('Location: ' . BASE_URL . '/lgu/reviews');
            exit();
        }

        $_SESSION['swal_success'] = $decision === 'approved'
            ? 'Review approved and published.'
            : 'Review rejected and kept off the public site.';
        header('Location: ' . BASE_URL . '/lgu/reviews');
        exit();
    }

    public function monitoring() {
        $title = 'Spot Monitoring - LGU';
        $activePage = 'monitoring';
        require_once '../app/models/Spot.php';
        Spot::markMonitoringReadForUser(Auth::user()['id'] ?? 0);
        $spotModel = new Spot();
        $spotModel->expireMonitoringListings();
        $monitorList = $spotModel->getMonitoringList();
        require_once '../app/views/lgu/monitoring.php';
    }

    public function monitoringDetail($id) {
        $spotId = (int)$id;
        if ($spotId < 1) {
            http_response_code(404);
            die('Monitoring record not found.');
        }

        require_once '../app/models/Spot.php';
        Spot::markMonitoringReadForUser(Auth::user()['id'] ?? 0);
        $spotModel = new Spot();
        $spot = $spotModel->getMonitoringDetail($spotId);

        if (!$spot) {
            http_response_code(404);
            die('Monitoring record not found.');
        }

        $title = 'Monitoring Details - LGU';
        $activePage = 'monitoring';
        require_once '../app/views/lgu/monitoring-detail.php';
    }

    public function sendMonitoringWarning($id) {
        require_once '../app/models/Spot.php';
        $spotModel = new Spot();
        $spotId = (int)$id;
        $note = trim((string)($_POST['note'] ?? ''));

        if ($spotId < 1) {
            $_SESSION['swal_error'] = 'Invalid monitoring request.';
            header('Location: ' . BASE_URL . '/lgu/monitoring');
            exit();
        }

        if ($note === '') {
            $_SESSION['swal_error'] = 'Please add a warning note before sending the notification.';
            header('Location: ' . BASE_URL . '/lgu/monitoring');
            exit();
        }

        $spotModel->sendOwnerWarning($spotId, $note);
        $_SESSION['swal_success'] = 'Owner warning sent. They have 3 months to submit an improvement update.';
        header('Location: ' . BASE_URL . '/lgu/monitoring');
        exit();
    }

    public function reviewMonitoringSubmission($id) {
        $spotId = (int)$id;
        $decision = $_POST['decision'] ?? '';
        $reason = trim((string)($_POST['reason'] ?? ''));

        if ($spotId < 1 || !in_array($decision, ['approved', 'declined'], true)) {
            $_SESSION['swal_error'] = 'Invalid monitoring review request.';
            header('Location: ' . BASE_URL . '/lgu/monitoring');
            exit();
        }

        require_once '../app/models/Spot.php';
        $spotModel = new Spot();

        // Fetch spot + owner info before updating (for email)
        $db = Database::getInstance()->getConnection();
        $spotInfo = $db->prepare("SELECT s.name, u.email, u.name AS owner_name FROM spots s LEFT JOIN users u ON u.id = s.owner_user_id WHERE s.id = ?");
        $spotInfo->execute([$spotId]);
        $spotData = $spotInfo->fetch(PDO::FETCH_ASSOC);

        $updated = $spotModel->reviewMonitoringSubmission($spotId, $decision, $reason, Auth::user()['id']);

        if (!$updated) {
            $_SESSION['swal_error'] = $decision === 'declined'
                ? 'Please provide a clear reason before declining this improvement update.'
                : 'Unable to approve this monitoring update.';
            header('Location: ' . BASE_URL . '/lgu/monitoring');
            exit();
        }

        // Send email notification to the owner
        if (!empty($spotData['email'])) {
            try {
                require_once '../app/services/EmailService.php';
                $emailService = new EmailService();
                $spotName = $spotData['name'] ?? 'Your listing';

                if ($decision === 'approved') {
                    $emailService->sendMonitoringUpdateApproval(
                        $spotData['email'],
                        $spotName,
                        $reason !== '' ? $reason : 'Your improvement update met the required standards and was approved by the LGU.',
                        'Your listing status has been restored to normal and the monitoring deadline has been cleared.'
                    );
                } else {
                    $emailService->sendMonitoringUpdateDecline(
                        $spotData['email'],
                        $spotName,
                        $reason,
                        'Please submit a new improvement update with clearer evidence and a more detailed description before the monitoring deadline expires.'
                    );
                }
            } catch (Exception $e) {
                error_log('Monitoring email failed: ' . $e->getMessage());
            }
        }

        if ($decision === 'declined') {
            $_SESSION['swal_success'] = 'Improvement update declined. The owner has been notified via email.';
        } else {
            $_SESSION['swal_success'] = 'Improvement update approved. The listing has been restored to normal and the owner notified.';
        }

        header('Location: ' . BASE_URL . '/lgu/monitoring');
        exit();
    }

    public function exportCsv() {
        require_once '../app/models/Spot.php';
        require_once '../app/models/Review.php';
        new Review();
        $db = Database::getInstance()->getConnection();
        $spots = $db->query("
            SELECT s.name, c.name as category, s.source, s.status,
                   s.operational_status, s.barangay, s.contact_number, s.hours,
                   s.price_range, s.fb_link, s.created_at,
                   ROUND(COALESCE(AVG(r.rating),0),1) as avg_rating,
                   COUNT(r.id) as review_count,
                   u.applicant_type
            FROM spots s
            JOIN categories c ON s.category_id = c.id
            JOIN users u ON s.owner_user_id = u.id
            LEFT JOIN reviews r ON r.spot_id = s.id AND r.status = 'approved'
            WHERE s.status = 'approved'
            GROUP BY s.id
            ORDER BY s.name
        ")->fetchAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="malangas_spots_' . date('Y-m-d') . '.csv"');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        $out = fopen('php://output', 'w');

        // UTF-8 BOM — forces Excel to read the file as UTF-8 (fixes ₱ and special chars)
        fputs($out, "\xEF\xBB\xBF");

        fputcsv($out, ['Name','Category','Sector','Source','Status','Operational Status','Barangay','Contact','Hours','Price Range','Facebook','Date Created','Avg Rating','Reviews']);

        foreach ($spots as $row) {
            // Prefix contact with a tab so Excel treats it as text (prevents 9.53E+09)
            $contact = !empty($row['contact_number'])
                ? "\t" . $row['contact_number']
                : '';

            // Format date as readable string so Excel doesn't show ###### or auto-convert
            $dateCreated = !empty($row['created_at'])
                ? date('M d, Y', strtotime($row['created_at']))
                : '';

            // Strip any broken encoding artifacts from price range
            $priceRange = mb_convert_encoding($row['price_range'] ?? '', 'UTF-8', 'UTF-8');

            // Map applicant_type to readable sector label
            $sector = ($row['applicant_type'] ?? 'private') === 'public'
                ? 'Public Sector'
                : 'Private Sector';

            fputcsv($out, [
                $row['name'],
                $row['category'],
                $sector,
                $row['source'],
                $row['status'],
                $row['operational_status'],
                $row['barangay'],
                $contact,
                $row['hours'],
                $priceRange,
                $row['fb_link'],
                $dateCreated,
                $row['avg_rating'],
                $row['review_count']
            ]);
        }
        fclose($out);
        exit();
    }


    public function apiGetAllSpots() {
        require_once '../app/models/Spot.php';
        $spotModel = new Spot();
        $spots = $spotModel->getAll();

        $features = [];
        foreach ($spots as $spot) {
            $features[] = [
                'type'     => 'Feature',
                'geometry' => [
                    'type'        => 'Point',
                    'coordinates' => [floatval($spot['longitude']), floatval($spot['latitude'])]
                ],
                'properties' => [
                    'id'     => $spot['id'],
                    'name'   => $spot['name'],
                    'status' => $spot['status'],
                    'source' => $spot['source']
                ]
            ];
        }

        require_once '../app/core/Response.php';
        Response::json([
            'type'     => 'FeatureCollection',
            'features' => $features
        ]);
    }
}
