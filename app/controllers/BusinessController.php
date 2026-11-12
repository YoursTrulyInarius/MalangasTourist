<?php
// app/controllers/BusinessController.php

class BusinessController {
    public function __construct() {
        Auth::requireRole('business');
    }

    public function dashboard() {
        $title = "Business Dashboard - MTMS";
        require_once '../app/models/Application.php';
        $appModel = new Application();
        $spots = $appModel->getByUserId(Auth::user()['id']);
        require_once '../app/views/business/dashboard.php';
    }

    public function businesses() {
        $title = "View Businesses - MTMS";
        require_once '../app/models/Application.php';
        $appModel = new Application();
        $spots = $appModel->getByUserId(Auth::user()['id']);
        require_once '../app/views/business/businesses.php';
    }

    public function monitoring() {
        $title = "Monitoring & Improvements - MTMS";
        $activePage = 'monitoring';
        require_once '../app/models/Spot.php';
        $userId = Auth::user()['id'] ?? 0;
        Spot::markMonitoringReadForUser($userId);
        $spotModel = new Spot();
        $spots = $spotModel->getMonitoringForOwner($userId);
        require_once '../app/views/business/monitoring.php';
    }

    public function submitMonitoringUpdate() {
        $spotId = (int)($_POST['spot_id'] ?? 0);
        $status = $_POST['status'] ?? 'under_renovation';
        $note = trim($_POST['note'] ?? '');

        if ($spotId < 1) {
            $_SESSION['swal_error'] = 'Please choose a listing to update.';
            header('Location: ' . BASE_URL . '/business/monitoring');
            exit();
        }

        if (!in_array($status, ['under_renovation', 'improved'], true)) {
            $status = 'under_renovation';
        }

        $proofPaths = [];
        $uploadedFiles = $_FILES['proofs'] ?? ['name' => [], 'tmp_name' => [], 'error' => []];
        $fileCount = is_array($uploadedFiles['name']) ? count(array_filter($uploadedFiles['name'])) : 0;

        if ($fileCount > 10) {
            $_SESSION['swal_error'] = 'You can upload up to 10 proof images only.';
            header('Location: ' . BASE_URL . '/business/monitoring');
            exit();
        }

        if ($fileCount > 0) {
            $uploadDir = '../public/assets/images/monitoring/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($uploadedFiles['name'] as $index => $fileName) {
                if (empty($fileName)) {
                    continue;
                }

                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png'];

                if (!in_array($ext, $allowed, true) || $uploadedFiles['error'][$index] !== UPLOAD_ERR_OK) {
                    $_SESSION['swal_error'] = 'Only JPG, JPEG, and PNG files are allowed for proof images.';
                    header('Location: ' . BASE_URL . '/business/monitoring');
                    exit();
                }

                $newFileName = md5($fileName . time() . $index) . '.' . $ext;
                $destPath = $uploadDir . $newFileName;

                if (!move_uploaded_file($uploadedFiles['tmp_name'][$index], $destPath)) {
                    $_SESSION['swal_error'] = 'There was a problem uploading the proof images.';
                    header('Location: ' . BASE_URL . '/business/monitoring');
                    exit();
                }

                $proofPaths[] = 'assets/images/monitoring/' . $newFileName;
            }
        }

        $proofPath = !empty($proofPaths) ? implode('|', $proofPaths) : null;

        require_once '../app/models/Spot.php';
        $spotModel = new Spot();
        $updated = $spotModel->markMonitoringUpdate($spotId, Auth::user()['id'], $status, $note, $proofPath);

        if (!$updated) {
            $_SESSION['swal_error'] = 'Unable to update this monitoring request.';
            header('Location: ' . BASE_URL . '/business/monitoring');
            exit();
        }

        $_SESSION['swal_success'] = $status === 'improved'
            ? 'Thank you. Your improvement update has been submitted successfully.'
            : 'Your renovation update has been recorded and will be reviewed by the LGU.';
        header('Location: ' . BASE_URL . '/business/monitoring');
        exit();
    }

    public function applyForm() {
        $title = "Apply for Listing - MTMS";
        require_once '../app/models/Category.php';
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        require_once '../app/views/business/apply.php';
    }

    public function applySubmit() {
        require_once '../app/models/Application.php';
        $appModel = new Application();

        $data = [
            'owner_user_id'  => Auth::user()['id'],
            'name'           => $_POST['name'] ?? '',
            'category_id'    => $_POST['category_id'] ?? '',
            'description'    => $_POST['description'] ?? '',
            'barangay'       => $_POST['barangay'] ?? '',
            'latitude'       => $_POST['latitude'] ?? '',
            'longitude'      => $_POST['longitude'] ?? '',
            'contact_number' => $_POST['contact_number'] ?? '',
            'fb_link'        => $_POST['fb_link'] ?? '',
            'hours'          => $_POST['hours'] ?? '',
            'price_range'    => $_POST['price_range'] ?? '',
            'bir_number'     => $_POST['bir_number'] ?? '',
            'business_permit'=> $_POST['business_permit'] ?? ''
        ];

        // 1-hour cooldown check for rejections
        $recentRejection = Database::getInstance()->getConnection()->prepare("
            SELECT reviewed_at FROM spots 
            WHERE owner_user_id = ? AND status = 'rejected' 
            ORDER BY reviewed_at DESC LIMIT 1
        ");
        $recentRejection->execute([Auth::user()['id']]);
        $lastRejected = $recentRejection->fetchColumn();
        
        if ($lastRejected) {
            $lastRejectedTime = strtotime($lastRejected);
            $oneHourAgo = time() - 3600;
            if ($lastRejectedTime > $oneHourAgo) {
                $_SESSION['swal_error'] = 'You must wait 1 hour after a rejection before submitting a new application.';
                header('Location: ' . BASE_URL . '/business/apply');
                exit();
            }
        }

        if (empty($data['name']) || empty($data['category_id']) || empty($data['latitude']) || empty($data['longitude'])) {
            $_SESSION['swal_error'] = 'Please fill all required fields, including the map pin.';
            header('Location: ' . BASE_URL . '/business/apply');
            exit();
        }

        $spotId = $appModel->submit($data);

        // Handle File Uploads
        if (!empty($_FILES['spot_images']['name'][0])) {
            require_once '../app/models/Spot.php';
            $spotModel = new Spot();
            
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

        $_SESSION['swal_success'] = "Thank you for applying. Verification might take a while. Please wait for an email or check your dashboard.";
        header("Location: " . BASE_URL . "/business");
        exit();
    }

    public function editForm($id) {
        $title = "Manage Listing - MTMS";
        require_once '../app/models/Spot.php';
        require_once '../app/models/Category.php';
        $spotModel     = new Spot();
        $categoryModel = new Category();
        $spot       = $spotModel->findById($id);
        $categories = $categoryModel->getAll();

        if (!$spot || $spot['owner_user_id'] != Auth::user()['id']) {
            http_response_code(403);
            die("Access denied or listing not found.");
        }

        $photos = $spotModel->getPhotos($id);
        $pendingPhotoUpdate = $spotModel->getPendingPhotoUpdate($id);
        require_once '../app/views/business/edit.php';
    }

    public function editSubmit() {
        require_once '../app/models/Spot.php';
        $spotModel = new Spot();

        $id   = $_POST['spot_id'] ?? '';
        $data = [
            'owner_user_id'      => Auth::user()['id'],
            'description'        => $_POST['description'] ?? '',
            'barangay'           => $_POST['barangay'] ?? '',
            'contact_number'     => $_POST['contact_number'] ?? '',
            'fb_link'            => $_POST['fb_link'] ?? '',
            'hours'              => $_POST['hours'] ?? '',
            'price_range'        => $_POST['price_range'] ?? '',
            'operational_status' => $_POST['operational_status'] ?? 'open',
            'latitude'           => $_POST['latitude'] ?? '',
            'longitude'          => $_POST['longitude'] ?? '',
        ];

        if (empty($id)) {
            die("Invalid request.");
        }

        $spot = $spotModel->findById($id);
        if (!$spot || (int)$spot['owner_user_id'] !== (int)Auth::user()['id']) {
            http_response_code(403);
            die("Access denied or listing not found.");
        }

        $uploadedFiles = $_FILES['spot_images'] ?? null;
        $photoNames = $uploadedFiles && is_array($uploadedFiles['name'])
            ? array_values(array_filter($uploadedFiles['name'], static fn($name) => $name !== ''))
            : [];
        if (count($photoNames) > 10) {
            $_SESSION['swal_error'] = 'You can submit up to 10 replacement photos.';
            header("Location: " . BASE_URL . "/business/edit/" . (int)$id);
            exit();
        }

        $savedPaths = [];
        if ($photoNames) {
            if ($spot['source'] !== 'business' || $spot['status'] !== 'approved') {
                $_SESSION['swal_error'] = 'Photo updates are available for approved business listings only.';
                header("Location: " . BASE_URL . "/business/edit/" . (int)$id);
                exit();
            }
            if ($spotModel->hasPendingPhotoUpdate($id)) {
                $_SESSION['swal_error'] = 'A photo update is already pending LGU review. Please wait for the decision before submitting another.';
                header("Location: " . BASE_URL . "/business/edit/" . (int)$id);
                exit();
            }

            $files = [];
            foreach ($uploadedFiles['name'] as $index => $fileName) {
                if ($fileName === '') {
                    continue;
                }
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $imageInfo = @getimagesize($uploadedFiles['tmp_name'][$index]);
                if ($uploadedFiles['error'][$index] !== UPLOAD_ERR_OK || !in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true) || !$imageInfo || !in_array($imageInfo['mime'], ['image/jpeg', 'image/png', 'image/webp'], true)) {
                    $_SESSION['swal_error'] = 'Please upload valid JPG, PNG, or WebP images only.';
                    header("Location: " . BASE_URL . "/business/edit/" . (int)$id);
                    exit();
                }
                $files[] = ['tmp_name' => $uploadedFiles['tmp_name'][$index], 'extension' => $extension];
            }

            $uploadDir = '../public/assets/images/spots/';
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                $_SESSION['swal_error'] = 'Unable to create the photo upload folder. Please try again.';
                header("Location: " . BASE_URL . "/business/edit/" . (int)$id);
                exit();
            }
            foreach ($files as $file) {
                $newFileName = bin2hex(random_bytes(16)) . '.' . $file['extension'];
                if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
                    foreach ($savedPaths as $path) {
                        @unlink($uploadDir . basename($path));
                    }
                    $_SESSION['swal_error'] = 'Unable to save one of the replacement photos. Please try again.';
                    header("Location: " . BASE_URL . "/business/edit/" . (int)$id);
                    exit();
                }
                $savedPaths[] = 'assets/images/spots/' . $newFileName;
            }

            if (!$spotModel->submitPhotoUpdate($id, Auth::user()['id'], $savedPaths)) {
                foreach ($savedPaths as $path) {
                    @unlink($uploadDir . basename($path));
                }
                $_SESSION['swal_error'] = 'The photo update could not be submitted. Check that the listing is approved and no other photo update is awaiting review.';
                header("Location: " . BASE_URL . "/business/edit/" . (int)$id);
                exit();
            }
        }

        $spotModel->updateStatusAndCoords($id, $data);
        $_SESSION['swal_success'] = $photoNames
            ? 'Your replacement photos have been submitted as a photo update and will appear after LGU approval.'
            : 'Listing changes saved successfully.';
        header("Location: " . BASE_URL . "/business");
        exit();
    }
}
