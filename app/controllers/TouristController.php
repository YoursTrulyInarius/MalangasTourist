<?php
// app/controllers/TouristController.php

class TouristController {
    public function index() {
        $title = "Welcome to Malangas Tourism";
        
        require_once '../app/models/Spot.php';
        $spotModel = new Spot();
        $spots = $spotModel->getApproved();

        require_once '../app/views/tourist/index.php';
    }

    public function map() {
        $title = "Explore Malangas - Tourist Map";
        require_once '../app/views/tourist/map.php';
    }

    public function spotDetails($id) {
        require_once '../app/models/Spot.php';
        $spotModel = new Spot();
        $spot = $spotModel->findById($id);
        $photos = $spotModel->getPhotos($id);

        if (!$spot || $spot['status'] !== 'approved') {
            http_response_code(404);
            die("Spot not found or not approved.");
        }

        require_once '../app/models/Review.php';
        $reviewModel = new Review();
        $reviews = $reviewModel->getBySpotId($id);

        $title = htmlspecialchars($spot['name']) . " - MTMS";
        require_once '../app/views/tourist/spot-details.php';
    }

    public function submitReview() {
        $spotId = (int)($_POST['spot_id'] ?? 0);
        $modalSubmit = isset($_POST['modal']) && $_POST['modal'] === '1';
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if (empty($spotId) || $rating < 1 || $rating > 5 || $comment === '') {
            $_SESSION['review_error'] = 'Please provide a rating, a comment, and at least one photo.';
            $redirectPath = '/spot/' . $spotId . ($modalSubmit ? '?modal=1&focus=review' : '?focus=review');
            header('Location: ' . BASE_URL . $redirectPath);
            exit();
        }

        require_once '../app/models/Review.php';
        $reviewModel = new Review();
        $photoFiles = $_FILES['review_photos'] ?? [];
        $photoCount = 0;
        if (!empty($photoFiles['name']) && is_array($photoFiles['name'])) {
            $photoCount = count(array_filter($photoFiles['name'], static fn($name) => !empty($name)));
        }

        if ($photoCount < 1) {
            $_SESSION['review_error'] = 'Please upload at least 1 image with your review.';
            $redirectPath = '/spot/' . $spotId . ($modalSubmit ? '?modal=1&focus=review' : '?focus=review');
            header('Location: ' . BASE_URL . $redirectPath);
            exit();
        }

        $reviewId = $reviewModel->submit($spotId, null, $rating, $comment);
        $photoResult = $reviewModel->savePhotos($reviewId, $photoFiles);

        if ($photoResult === false) {
            $reviewModel->deleteReview($reviewId);
            $_SESSION['review_error'] = 'You may upload up to 3 images only. Please try again.';
            $redirectPath = '/spot/' . $spotId . ($modalSubmit ? '?modal=1&focus=review' : '?focus=review');
            header('Location: ' . BASE_URL . $redirectPath);
            exit();
        }

        if ($photoResult < 1) {
            $reviewModel->deleteReview($reviewId);
            $_SESSION['review_error'] = 'Please upload at least 1 valid image with your review.';
            $redirectPath = '/spot/' . $spotId . ($modalSubmit ? '?modal=1&focus=review' : '?focus=review');
            header('Location: ' . BASE_URL . $redirectPath);
            exit();
        }

        $_SESSION['swal_success'] = 'Your review was submitted to the LGU for approval. It will appear publicly after it is approved.';
        $redirectPath = '/spot/' . $spotId . ($modalSubmit ? '?modal=1&review_submitted=1' : '?review_submitted=1');
        header('Location: ' . BASE_URL . $redirectPath);
        exit();
    }

    public function editReview() {
        $reviewId = (int)($_POST['review_id'] ?? 0);
        $spotId = (int)($_POST['spot_id'] ?? 0);
        $modalSubmit = isset($_POST['modal']) && $_POST['modal'] === '1';

        $_SESSION['review_error'] = 'Anonymous reviews cannot be edited. Please submit a new review.';
        $redirectPath = '/spot/' . urlencode($spotId) . ($modalSubmit ? '?modal=1&focus=review' : '?focus=review');
        header('Location: ' . BASE_URL . $redirectPath);
        exit();
    }

    public function apiGetSpots() {
        require_once '../app/core/Response.php';
        require_once '../app/models/Spot.php';
        $spotModel = new Spot();
        $spots = $spotModel->getApproved();

        $features = [];
        foreach ($spots as $spot) {
            $monitorStatus = $spot['monitor_status'] ?? 'normal';
            $effectiveStatus = $monitorStatus === 'normal' ? ($spot['operational_status'] ?? 'open') : $monitorStatus;
            $statusMap = [
                'open' => '🟢 Open',
                'closed' => '🔴 Closed',
                'temp_closed' => '🟡 Temporarily Closed',
                'maintenance' => '🔵 Under Maintenance',
                'under_renovation' => '🔵 Under Renovation',
                'warning' => '🟠 Warning',
                'improved' => '🟢 Improved',
                'normal' => '🟢 Open'
            ];

            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [floatval($spot['longitude']), floatval($spot['latitude'])] // GeoJSON is [lng, lat]
                ],
                'properties' => [
                    'id'              => $spot['id'],
                    'name'            => $spot['name'],
                    'category'        => $spot['category_name'],
                    'icon'            => $spot['category_icon'],
                    'avg_rating'      => number_format((float)$spot['avg_rating'], 1),
                    'review_count'    => $spot['review_count'],
                    'description'     => substr($spot['description'], 0, 100) . '...',
                    'op_status'       => $effectiveStatus,
                    'op_status_label' => $statusMap[$effectiveStatus] ?? '🟢 Open',
                ]
            ];
        }

        Response::json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }

    /**
     * API endpoint to fetch barangays from philippines-addresses
     */
    public function apiGetBarangays() {
        require_once '../app/core/Response.php';
        require_once '../app/services/OverpassAPIService.php';
        $overpassService = new OverpassAPIService();
        
        $barangays = $overpassService->getBarangaysByCity();
        
        Response::json([
            'success' => true,
            'count' => count($barangays),
            'barangays' => $barangays
        ]);
    }

    /**
     * API endpoint to search location/streets by barangay
     */
    public function apiSearchLocation() {
        require_once '../app/core/Response.php';
        require_once '../app/services/OverpassAPIService.php';
        
        $location = trim($_GET['location'] ?? '');
        $barangay = trim($_GET['barangay'] ?? '');
        $city = trim($_GET['city'] ?? 'Malangas');

        if (empty($location) || empty($barangay)) {
            Response::json(['error' => 'Location and barangay are required'], 400);
            return;
        }

        $overpassService = new OverpassAPIService();
        $result = $overpassService->searchLocation($location, $barangay, $city);
        
        Response::json($result);
    }

    /**
     * API endpoint to get streets in a barangay
     */
    public function apiGetStreetsInBarangay() {
        require_once '../app/core/Response.php';
        require_once '../app/services/OverpassAPIService.php';
        
        $barangay = trim($_GET['barangay'] ?? '');
        $city = trim($_GET['city'] ?? 'Malangas');
        $province = trim($_GET['province'] ?? 'Zamboanga del Sur');

        if (empty($barangay)) {
            Response::json(['error' => 'Barangay is required'], 400);
            return;
        }

        $overpassService = new OverpassAPIService();
        $result = $overpassService->getStreetsByBarangay($barangay, $city, $province);
        
        Response::json($result);
    }

    /**
     * API endpoint to get real approved nearby places from the MTMS database
     */
    public function apiGetNearbyPlaces() {
        require_once __DIR__ . '/../core/Response.php';
        require_once __DIR__ . '/../models/Spot.php';

        $lat = floatval($_GET['lat'] ?? 0);
        $lng = floatval($_GET['lng'] ?? 0);
        $currentSpotId = intval($_GET['spot_id'] ?? 0);

        if (!$lat || !$lng) {
            Response::json(['error' => 'Valid latitude and longitude are required'], 400);
            return;
        }

        $places = [];

        // Fetch only real approved tourist spots & establishments from MTMS database
        $spotModel = new Spot();
        $approvedSpots = $spotModel->getApproved();
        foreach ($approvedSpots as $spot) {
            if ((int)$spot['id'] === $currentSpotId) {
                continue;
            }
            $spotLat = floatval($spot['latitude']);
            $spotLng = floatval($spot['longitude']);
            if (!$spotLat || !$spotLng) continue;

            $dist = $this->calculateDistanceMeters($lat, $lng, $spotLat, $spotLng);

            // Filter to within 25km
            if ($dist > 25000) {
                continue;
            }
            
            // Map real category to filter types
            $cat = strtolower($spot['category_name'] ?? '');
            $assignedCategory = 'attraction';
            $iconClass = 'fa-solid fa-mountain-sun';

            if (strpos($cat, 'restaurant') !== false || strpos($cat, 'food') !== false || strpos($cat, 'dining') !== false || strpos($cat, 'cafe') !== false) {
                $assignedCategory = 'food';
                $iconClass = 'fa-solid fa-utensils';
            } elseif (strpos($cat, 'resort') !== false || strpos($cat, 'hotel') !== false || strpos($cat, 'accommodation') !== false || strpos($cat, 'stay') !== false || strpos($cat, 'lodge') !== false) {
                $assignedCategory = 'stay';
                $iconClass = 'fa-solid fa-hotel';
            } elseif (strpos($cat, 'toilet') !== false || strpos($cat, 'restroom') !== false || strpos($cat, 'cr') !== false || strpos($cat, 'washroom') !== false) {
                $assignedCategory = 'restroom';
                $iconClass = 'fa-solid fa-restroom';
            } elseif (strpos($cat, 'other') !== false || strpos($cat, 'store') !== false || strpos($cat, 'service') !== false || strpos($cat, 'shop') !== false) {
                $assignedCategory = 'other';
                $iconClass = 'fa-solid fa-store';
            }

            $places[] = [
                'id' => 'mtms-' . $spot['id'],
                'spot_id' => $spot['id'],
                'name' => $spot['name'],
                'category' => $assignedCategory,
                'type_label' => $spot['category_name'] ?? 'Tourist Spot',
                'icon_class' => $iconClass,
                'barangay' => $spot['barangay'] ?? '',
                'lat' => $spotLat,
                'lng' => $spotLng,
                'distance' => $dist,
                'source' => 'MTMS Registered'
            ];
        }

        // Sort by real distance ascending
        usort($places, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        Response::json([
            'success' => true,
            'count' => count($places),
            'places' => $places
        ]);
    }

    private function calculateDistanceMeters($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c);
    }
}
