<?php
// public/index.php
session_start();

require_once '../config/config.php';
require_once '../app/core/Database.php';
require_once '../app/core/Router.php';
require_once '../app/core/Auth.php';

// Instantiate Router
$router = new Router();

// Define Routes

// -- Public Tourist Routes --
$router->add('GET', '/', 'TouristController', 'index');
$router->add('GET', '/map', 'TouristController', 'map');
$router->add('GET', '/spot/{id}', 'TouristController', 'spotDetails');
$router->add('GET', '/api/get-spots', 'TouristController', 'apiGetSpots');
$router->add('GET', '/api/get-nearby-places', 'TouristController', 'apiGetNearbyPlaces');
$router->add('GET', '/api/get-barangays', 'TouristController', 'apiGetBarangays');
$router->add('GET', '/api/search-location', 'TouristController', 'apiSearchLocation');
$router->add('GET', '/api/get-streets-barangay', 'TouristController', 'apiGetStreetsInBarangay');
$router->add('POST', '/api/submit-review', 'TouristController', 'submitReview');
$router->add('POST', '/api/edit-review', 'TouristController', 'editReview');

// -- Auth Routes --
$router->add('GET', '/login', 'AuthController', 'loginForm');
$router->add('POST', '/login', 'AuthController', 'login');
$router->add('GET', '/register', 'AuthController', 'registerForm');
$router->add('POST', '/register', 'AuthController', 'register');
$router->add('GET', '/tourist/login', 'AuthController', 'touristLoginForm');
$router->add('POST', '/tourist/login', 'AuthController', 'touristLogin');
$router->add('GET', '/tourist/register', 'AuthController', 'touristRegisterForm');
$router->add('POST', '/tourist/register', 'AuthController', 'touristRegister');

$router->add('GET', '/forgot-password', 'AuthController', 'forgotPasswordForm');
$router->add('POST', '/forgot-password', 'AuthController', 'forgotPasswordRequest');
$router->add('GET', '/reset-password', 'AuthController', 'resetPasswordForm');
$router->add('POST', '/reset-password/resend', 'AuthController', 'resendResetCode');
$router->add('POST', '/reset-password/verify', 'AuthController', 'verifyResetCode');
$router->add('GET', '/reset-password/new', 'AuthController', 'newPasswordForm');
$router->add('POST', '/reset-password/new', 'AuthController', 'resetPassword');

$router->add('ANY', '/logout', 'AuthController', 'logout');

// -- LGU Admin Routes --
$router->add('GET',  '/lgu',                         'LguController', 'dashboard');
$router->add('GET',  '/lgu/spots',                   'LguController', 'spotsManage');
$router->add('GET',  '/lgu/spots/create',            'LguController', 'spotCreate');  // static before {id}
$router->add('POST', '/lgu/spots/store',             'LguController', 'spotStore');
$router->add('POST', '/lgu/spots/update',            'LguController', 'spotUpdate');  // static before {id}
$router->add('GET',  '/lgu/spots/{id}/edit',         'LguController', 'spotEdit');
$router->add('POST', '/lgu/spots/{id}/delete',       'LguController', 'spotDelete');
$router->add('GET',  '/lgu/reports/export',          'LguController', 'exportCsv');   // static before {id}
$router->add('GET',  '/lgu/reports',                 'LguController', 'reports');
$router->add('GET',  '/lgu/reviews',                 'LguController', 'reviewsQueue');
$router->add('POST', '/lgu/reviews/{id}/moderate',   'LguController', 'reviewModerate');
$router->add('GET',  '/lgu/monitoring',              'LguController', 'monitoring');
$router->add('GET',  '/lgu/monitoring/{id}',         'LguController', 'monitoringDetail');
$router->add('POST', '/lgu/monitoring/{id}/warn',    'LguController', 'sendMonitoringWarning');
$router->add('POST', '/lgu/monitoring/{id}/review',  'LguController', 'reviewMonitoringSubmission');
$router->add('GET',  '/api/lgu/get-all-spots',       'LguController', 'apiGetAllSpots');
$router->add('GET',  '/lgu/applications',            'ApplicationController', 'index');
$router->add('GET',  '/lgu/applications/{id}',       'ApplicationController', 'show');
$router->add('POST', '/api/approve-reject',          'ApplicationController', 'approveReject');
$router->add('POST', '/lgu/applications/{id}/photo-update', 'ApplicationController', 'reviewPhotoUpdate');

// -- Business Routes --
$router->add('GET',  '/business',                    'BusinessController', 'dashboard');
$router->add('GET',  '/business/businesses',          'BusinessController', 'businesses');
$router->add('GET',  '/business/monitoring',          'BusinessController', 'monitoring');
$router->add('POST', '/business/monitoring/update',   'BusinessController', 'submitMonitoringUpdate');
$router->add('GET',  '/business/apply',              'BusinessController', 'applyForm');
$router->add('POST', '/api/apply-submit',            'BusinessController', 'applySubmit');
$router->add('GET',  '/business/edit/{id}',          'BusinessController', 'editForm');
$router->add('POST', '/business/edit/save',          'BusinessController', 'editSubmit');

// Get the requested URL from .htaccess query string
$url = isset($_GET['url']) ? $_GET['url'] : '/';
$method = $_SERVER['REQUEST_METHOD'];

// Dispatch the request
$router->dispatch($url, $method);
