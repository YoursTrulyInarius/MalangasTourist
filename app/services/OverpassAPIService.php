<?php
// app/services/OverpassAPIService.php

class OverpassAPIService {
    private $overpassUrl = 'https://overpass-api.de/api/interpreter';
    private $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
    private $timeout = 15;
    private $philippinesAddressPath = '../philippine-addresses/';

    /**
     * Load barangay data from philippines-addresses JSON
     * @return array - All barangays data
     */
    private function loadBarangayData() {
        $paths = [
            dirname(__DIR__, 2) . '/philippine-addresses/barangay.json',
            $this->philippinesAddressPath . 'barangay.json',
            'philippine-addresses/barangay.json'
        ];
        foreach ($paths as $path) {
            if (file_exists($path)) {
                return json_decode(file_get_contents($path), true) ?? [];
            }
        }
        return [];
    }

    /**
     * Get barangays for a specific city
     * @param string $city - City code or name
     * @return array - Barangays in that city
     */
    public function getBarangaysByCity($city = '') {
        $allBarangays = $this->loadBarangayData();
        
        if (empty($city)) {
            return $allBarangays;
        }

        // Filter by city code (for Malangas, city_code is typically found in data)
        $filtered = array_filter($allBarangays, function($b) use ($city) {
            return stripos($b['city_code'], $city) === 0 || 
                   stripos($b['brgy_name'], $city) !== false;
        });

        return array_values($filtered);
    }

    /**
     * Get streets and landmarks within a barangay using Overpass API
     * @param string $barangay - Barangay name
     * @param string $city - City name (e.g., "Malangas")
     * @param string $province - Province name (e.g., "Zamboanga del Sur")
     * @return array - Array of streets/landmarks with coordinates
     */
    public function getStreetsByBarangay($barangay, $city = 'Malangas', $province = 'Zamboanga del Sur') {
        try {
            // First, get barangay boundary to limit search area
            $barangayBounds = $this->getBarangayBounds($barangay, $city, $province);
            
            if (!$barangayBounds) {
                return ['error' => 'Barangay not found'];
            }

            // Query Overpass for streets and landmarks
            $query = $this->buildOverpassQuery($barangayBounds);
            $response = $this->queryOverpass($query);
            
            if (isset($response['error'])) {
                return $response;
            }

            // Parse response and extract streets
            $streets = $this->parseOverpassResponse($response);
            
            return [
                'success' => true,
                'barangay' => $barangay,
                'bounds' => $barangayBounds,
                'streets' => $streets,
                'count' => count($streets)
            ];
        } catch (Exception $e) {
            return ['error' => 'Service error: ' . $e->getMessage()];
        }
    }

    /**
     * Search for specific location within a barangay
     * @param string $location - Location name/query
     * @param string $barangay - Barangay name
     * @param string $city - City name
     * @return array - Matching locations with coordinates
     */
    public function searchLocation($location, $barangay, $city = 'Malangas') {
        try {
            $searchQuery = $location . ', ' . $barangay . ', ' . $city . ', Philippines';
            
            $params = [
                'q' => $searchQuery,
                'format' => 'json',
                'limit' => 10,
                'polygon_geojson' => 1
            ];

            $url = $this->nominatimUrl . '?' . http_build_query($params);
            
            $response = $this->makeHttpRequest($url);
            $results = json_decode($response, true);

            if (!is_array($results)) {
                return ['error' => 'Invalid response from nominatim'];
            }

            $formatted = [];
            foreach ($results as $result) {
                $formatted[] = [
                    'name' => $result['display_name'] ?? '',
                    'latitude' => (float)($result['lat'] ?? 0),
                    'longitude' => (float)($result['lon'] ?? 0),
                    'type' => $result['type'] ?? 'unknown',
                    'osm_id' => $result['osm_id'] ?? null
                ];
            }

            return [
                'success' => true,
                'query' => $location,
                'results' => $formatted,
                'count' => count($formatted)
            ];
        } catch (Exception $e) {
            return ['error' => 'Search failed: ' . $e->getMessage()];
        }
    }


    /**
     * Get bounding box of a barangay
     * @param string $barangay
     * @param string $city
     * @param string $province
     * @return array|null - [south, west, north, east] bounding box
     */
    private function getBarangayBounds($barangay, $city, $province) {
        try {
            $searchQuery = $barangay . ', ' . $city . ', ' . $province . ', Philippines';
            
            $params = [
                'q' => $searchQuery,
                'format' => 'json',
                'limit' => 1,
                'polygon_geojson' => 1
            ];

            $url = $this->nominatimUrl . '?' . http_build_query($params);
            $response = $this->makeHttpRequest($url);
            $results = json_decode($response, true);

            if (is_array($results) && count($results) > 0 && isset($results[0]['boundingbox'])) {
                $bbox = $results[0]['boundingbox'];
                return [
                    'south' => (float)$bbox[0],
                    'north' => (float)$bbox[1],
                    'west' => (float)$bbox[2],
                    'east' => (float)$bbox[3]
                ];
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Build Overpass API query for streets and landmarks
     * @param array $bounds - Bounding box
     * @return string - Overpass QL query
     */
    private function buildOverpassQuery($bounds) {
        $south = $bounds['south'];
        $north = $bounds['north'];
        $west = $bounds['west'];
        $east = $bounds['east'];

        // Query for streets, landmarks, and POIs
        $query = <<<QUERY
[out:json];
(
  way["highway"]["name"]($south,$west,$north,$east);
  way["amenity"]($south,$west,$north,$east);
  node["amenity"]($south,$west,$north,$east);
  node["tourism"]($south,$west,$north,$east);
  node["historic"]($south,$west,$north,$east);
);
out geom;
QUERY;

        return $query;
    }

    /**
     * Query Overpass API
     * @param string $query
     * @return array
     */
    private function queryOverpass($query) {
        try {
            $postData = 'data=' . urlencode($query);
            
            $response = $this->makeHttpRequest($this->overpassUrl, $postData);
            $data = json_decode($response, true);

            if (isset($data['error'])) {
                return ['error' => 'Overpass API error: ' . $data['error']['message']];
            }

            if (!isset($data['elements']) || !is_array($data['elements'])) {
                return ['elements' => []];
            }

            return $data;
        } catch (Exception $e) {
            return ['error' => 'Overpass query failed: ' . $e->getMessage()];
        }
    }

    /**
     * Parse Overpass API response
     * @param array $response
     * @return array - Formatted streets and landmarks
     */
    private function parseOverpassResponse($response) {
        $results = [];
        $seen = [];

        if (!isset($response['elements']) || !is_array($response['elements'])) {
            return [];
        }

        foreach ($response['elements'] as $element) {
            $lat = null;
            $lon = null;
            $name = null;
            $type = 'unknown';

            // Extract coordinates
            if (isset($element['lat']) && isset($element['lon'])) {
                $lat = (float)$element['lat'];
                $lon = (float)$element['lon'];
            } elseif (isset($element['center'])) {
                $lat = (float)$element['center']['lat'];
                $lon = (float)$element['center']['lon'];
            } else {
                continue;
            }

            // Extract name and type
            if (isset($element['tags']['name'])) {
                $name = $element['tags']['name'];
            }

            // Determine type
            if (isset($element['tags']['highway'])) {
                $type = 'street';
            } elseif (isset($element['tags']['amenity'])) {
                $type = 'amenity';
            } elseif (isset($element['tags']['tourism'])) {
                $type = 'tourism';
            } elseif (isset($element['tags']['historic'])) {
                $type = 'historic';
            }

            // Skip if no name
            if (!$name) {
                continue;
            }

            // Avoid duplicates
            $key = strtolower($name) . '|' . round($lat, 4) . '|' . round($lon, 4);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $results[] = [
                'name' => $name,
                'latitude' => $lat,
                'longitude' => $lon,
                'type' => $type,
                'osm_id' => $element['id'] ?? null
            ];
        }

        // Sort by name for consistency
        usort($results, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return array_slice($results, 0, 50); // Limit to 50 results
    }

    /**
     * Make HTTP request
     * @param string $url
     * @param string $postData - Optional POST data
     * @return string - Response body
     */
    private function makeHttpRequest($url, $postData = null) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MalangasTourist/1.0');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Note: For production, verify SSL

        if ($postData) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        $response = curl_exec($ch);
        
        if ($response === false) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);
        return $response;
    }
}
?>
