# Malangas Tourism Management System (MTMS)

The **Malangas Tourism Management System (MTMS)** is a comprehensive, full-stack municipal web application developed for the **Municipality of Malangas, Zamboanga Sibugay**. 

The system provides tourists with real-time GPS navigation and nearby amenity discovery, empowers private tourism business owners to register listings and submit quality compliance improvements, and equips Local Government Unit (LGU) administrators with verification, rating moderation, and facility monitoring tools.

---

## 🌟 Key Features & Functional Modules

### 1. 🗺️ Dynamic Mapping & Real-Time GPS Navigation
* **Interactive Leaflet Mapping:** Replaced static maps with an interactive canvas utilizing high-resolution OpenStreetMap vector layers and Esri World Imagery.
* **Real-Time Geolocation Tracking:** Continuously tracks tourist geolocation with live pulse accuracy circles and heading markers via the HTML5 Geolocation API.
* **Multi-Modal Route Guidance (OSRM):** Turn-by-turn road routing powered by the Open Source Routing Machine supporting:
  * 🚗 **Drive / Motorcycle** (Road travel profile)
  * 🚲 **Bicycle** (Cycling path profile)
  * 🚶 **Walking** (Pedestrian trail profile)
* **Live Trip Metrics:** Dynamically computes total distance (meters/km) and estimated travel duration based on selected mode.

### 2. 🚻 Nearby Amenities & Point-of-Interest (POI) Discovery
Interactive discovery powered by OpenStreetMap Overpass queries and local database records across 5 color-coded amenity categories:
* 🚻 **Restrooms / Comfort Rooms:** Public washrooms, beach wash stations, and municipal comfort rooms (`fa-restroom`).
* 🍽️ **Food & Dining:** Local seafood grills, carenderias, cafes, and Poblacion eateries (`fa-utensils`, `fa-mug-saucer`).
* 🏨 **Stays & Accommodations:** Resorts, beach cottages, lodges, and traveler inns (`fa-hotel`, `fa-bed`).
* 🏛️ **Attractions:** Waterfalls, mangrove eco-parks, islands, and cultural landmarks (`fa-mountain-sun`, `fa-landmark`).
* 🏪 **Other Establishments:** Convenience stores/sari-sari stores, health centers/clinics, pharmacies, ATMs, and gas/fuel stations (`fa-store`, `fa-gas-pump`, `fa-money-bill-wave`).

### 3. ⭐ Star Ratings, Reviews & Moderation
* **Always-Visible 5-Star Baseline:** Spot cards and detail views display a 5-star baseline (showing 5 empty stars `☆☆☆☆☆` with `0.0 (0 reviews)` even when a spot has zero reviews yet).
* **Smart 5-Star Priority Sorting:** Public reviews are automatically sorted with 5-star reviews listed first (`ORDER BY rating DESC, created_at DESC`).
* **Photo Attachments:** Tourists can submit feedback with up to 3 attached photos.
* **LGU Moderation Workflow:** Tourist reviews enter a `pending` queue. Administrators review ratings, feedback, and photos before approving them for public viewing.

### 4. 🎨 Modernized User Interface & Design System
* **Curated Visual Palette:** Deep forest emerald (`#052e1c`, `#064e3b`, `#059669`) paired with warm amber gold accents (`#f59e0b`).
* **Original Cover Photo Integration:** Rich municipal collage hero banner with high-contrast text overlay.
* **Modern Typography:** High-legibility typography via **Google Fonts (Plus Jakarta Sans & Inter)**.
* **High-Visibility Alert System:** Polished alert banners (`.alert-error`, `.alert-success`, `.alert-warning`, `.alert-info`) with accent borders and slide-in animations.
* **High-Impact Button System:** Cohesive, gradient-styled action buttons (`.btn-primary`, `.btn-success`, `.btn-danger`, `.btn-secondary`) with smooth elevation and hover states.

### 5. 🏢 Private Business Listing Lifecycle
* **Spot Registration:** Business owners submit spot details (operating hours, contact numbers, price ranges, business permits, BIR numbers, and GPS coordinates).
* **Multi-Photo Uploads:** Supports up to 10 high-resolution images per listing.
* **LGU Verification Dashboard:** Applications remain in `pending` status with highlighted decision controls (Approve / Reject with feedback).

### 6. 📋 Quality Monitoring & 3-Month Improvement Deadlines
* **Compliance Oversight:** LGU monitors spots receiving negative feedback or facility issues.
* **Automated 3-Month Deadlines:** Admin issues warning notices with an automated **3-month improvement countdown** (`deadline = NOW() + INTERVAL 3 MONTH`).
* **Dynamic Notification Badges:** Header navigation badges illuminate in real-time when new monitoring notices or owner submissions occur.
* **Renovation Update Verification:** Owners submit renovation descriptions and up to 10 proof photos for LGU re-inspection.

### 7. 🔐 Authentication & Role-Based Access Control
* Role-specific workflows for **Tourists**, **Business Owners**, and **LGU Administrators**.
* Secure password hashing (`PASSWORD_BCRYPT`) and OTP email reset codes dispatched via PHPMailer SMTP.

---

## 💻 Technology Stack

| Layer | Technologies & Libraries |
| :--- | :--- |
| **Backend Framework** | PHP 7.4+ (Lightweight custom MVC: Router, Controllers, Models, Prepared PDO statements) |
| **Database** | MySQL / MariaDB (Relational schema with foreign keys, monitoring logs, review queues) |
| **Web Server** | Apache with `mod_rewrite` URL routing via `.htaccess` |
| **Mapping & GIS** | **Leaflet.js** (Interactive mapping canvas)<br>**OpenStreetMap (OSM)** (Vector tile layers)<br>**OSRM API** (Open Source Routing Machine for multi-modal road directions)<br>**Overpass API & Nominatim** (Geocoding and POI search) |
| **Geographic Dataset** | **`philippine-addresses`** (Standard PSGC JSON dataset for Region IX, Zamboanga Sibugay, Malangas municipality, and all 25 Barangays) |
| **Frontend & UI** | Semantic HTML5, Vanilla CSS3 (Custom Responsive Grid/Flexbox design system), Vanilla JavaScript (ES6+) |
| **Typography & Icons** | **Google Fonts** (Plus Jakarta Sans, Inter)<br>**FontAwesome 6.4.0** (Vector icons) |
| **Email Service** | **PHPMailer** with SMTP integration (Gmail / custom SMTP for OTP verification & monitoring notices) |
| **Document Generation**| **FPDF** (Automated PDF reports for tourism metrics and listing registries) |

---

## 📂 Directory Structure

```text
MalangasTourist/
├── .htaccess                     # Root rewrite rules forwarding traffic to public/
├── README.md                     # Comprehensive system documentation
├── PHPMailer/                    # PHPMailer core libraries
├── app/
│   ├── controllers/              # Application controllers (Tourist, Business, Lgu, Auth)
│   ├── core/                     # Core MVC engine (Router, Database, Response, Auth)
│   ├── models/                   # Data models (Spot, Review, User, Application, SpotMonitoring)
│   ├── services/                 # External service integrations (EmailService, OverpassAPIService)
│   └── views/
│       ├── auth/                 # Login, registration, OTP password reset forms
│       ├── business/             # Private sector dashboard, listing forms, monitoring updates
│       ├── lgu/                  # Admin dashboard, application review, monitoring management
│       ├── partials/             # Headers, footers, navigation bars, modals
│       └── tourist/              # Public directory, interactive map, spot details modal
├── config/
│   └── config.php                # Database credentials, dynamic BASE_URL, SMTP & map configs
├── philippine-addresses/         # Standard PSGC JSON datasets (Region, Province, City, Barangay)
├── public/
│   ├── index.php                 # Application front controller & routing definitions
│   └── assets/
│       ├── css/                  # Custom stylesheet (style.css)
│       ├── js/                   # Mapping scripts (map-public.js), GPS tracking & UI logic
│       └── images/               # Spot cover photos, gallery uploads, and review photos
├── sql/
│   ├── malangas_tourism.sql      # ⭐ Full database dump (Schema + Sample Spots, Users & Reviews)
│   └── schema.sql                # Clean, empty database blueprint with default admin
└── vendor/                       # Bundled third-party dependencies (FPDF, PHPMailer, autoload)
```

---

## 🚀 Local Setup & Installation (XAMPP on Windows)

### 1. Copy Project Files
Place the entire `MalangasTourist` folder into your XAMPP web root:
```text
C:\xampp\htdocs\MalangasTourist
```

### 2. Start Services
Open the **XAMPP Control Panel** and start **Apache** and **MySQL**.

### 3. Import Database
1. Open phpMyAdmin at [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/).
2. Create a database named **`malangas_tourism`**.
3. Import **[`sql/malangas_tourism.sql`](sql/malangas_tourism.sql)** *(Recommended: includes sample spots, approved reviews, and categories)* OR **[`sql/schema.sql`](sql/schema.sql)** *(Clean empty structure)*.

### 4. Configuration Check
Confirm settings in [`config/config.php`](config/config.php):
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'malangas_tourism');
```

### 5. Launch the Application
Open your web browser and navigate to:
```text
http://localhost/MalangasTourist/
```

---

## 🔑 Default Administrator Credentials

| Role | Email | Default Password |
| :--- | :--- | :--- |
| **LGU Administrator** | `admin@malangas.gov.ph` | `password` |

> [!IMPORTANT]
> Change the default administrator password immediately after first login in a production deployment.

---

## 🔒 Security & Standards

* **Parameterized PDO Queries:** 100% of database transactions utilize prepared statements to prevent SQL Injection.
* **Output Sanitization:** All user-generated content is sanitized using `htmlspecialchars()` upon rendering to prevent Cross-Site Scripting (XSS).
* **Role-Based Route Middleware:** Protected endpoints are guarded via `Auth::requireRole()` checks.
* **Secure File Handling:** Uploaded images undergo MIME-type verification, extension whitelisting, and are stored with randomized MD5 hash filenames.
