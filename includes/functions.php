<?php
if (!function_exists('calculateMaterials')) {

// Function to calculate materials with province support
function calculateMaterials($area, $floors, $buildingType, $materialQuality, $provinceId, $hasBasement = false, $hasGarage = false, $finishingType = 'none') {
    global $pdo;
    
    // Get material prices for the province
    $materialPrices = getProvinceMaterialPrices($pdo, $provinceId);
    
    // Base calculations per square meter
    $cementPerSqm = 0.15; // tons
    $steelPerSqm = 12; // kg
    $sandPerSqm = 0.5; // m³
    $bricksPerSqm = 50; // pieces
    $paintPerSqm = 0.2; // liters
    $tilesPerSqm = 1.1; // m²
    
    // Adjust for building type
    $buildingTypeMultiplier = 1;
    switch($buildingType) {
        case 'villa':
            $buildingTypeMultiplier = 1.2;
            break;
        case 'apartment':
            $buildingTypeMultiplier = 1.1;
            break;
        case 'commercial':
            $buildingTypeMultiplier = 1.3;
            break;
        default: // house
            $buildingTypeMultiplier = 1;
    }
    
    // Adjust for material quality
    $qualityMultiplier = 1;
    switch($materialQuality) {
        case 'premium':
            $qualityMultiplier = 1.3;
            break;
        case 'luxury':
            $qualityMultiplier = 1.6;
            break;
        default: // standard
            $qualityMultiplier = 1;
    }
    
    // Calculate total area (considering floors, basement, and garage)
    $totalArea = $area * $floors;
    if ($hasBasement) {
        $totalArea += $area * 0.8; // Basement is typically smaller
    }
    if ($hasGarage) {
        $totalArea += $area * 0.5; // Garage area
    }
    
    // Calculate quantities
    $cement = $totalArea * $cementPerSqm * $buildingTypeMultiplier;
    $steel = $totalArea * $steelPerSqm * $buildingTypeMultiplier;
    $sand = $totalArea * $sandPerSqm * $buildingTypeMultiplier;
    $bricks = $totalArea * $bricksPerSqm * $buildingTypeMultiplier;
    $paint = $totalArea * $paintPerSqm;
    $tiles = $totalArea * $tilesPerSqm;
    
    // Calculate costs using province-specific prices
    $cementPrice = findMaterialPrice($materialPrices, 'إسمنت') ?? findMaterialPrice($materialPrices, 'إسمنت أسود') ?? 115;
    $steelPrice = findMaterialPrice($materialPrices, 'حديد') ?? 655;
    $sandPrice = findMaterialPrice($materialPrices, 'رمل') ?? findMaterialPrice($materialPrices, 'رمل وبحص') ?? 16;
    $brickPrice = findMaterialPrice($materialPrices, 'بلوك 20 سم') ?? findMaterialPrice($materialPrices, 'بلوك') ?? 0.4;
    
    $cementCost = $cement * $cementPrice * $qualityMultiplier;
    $steelCost = ($steel / 1000) * $steelPrice * $qualityMultiplier; // Convert kg to tons
    $sandCost = $sand * $sandPrice * $qualityMultiplier;
    $brickCost = $bricks * $brickPrice * $qualityMultiplier;
    
    // Calculate finishing cost
    $finishingCost = calculateFinishingCost($finishingType, $area);
    
    $totalMaterialCost = $cementCost + $steelCost + $sandCost + $brickCost;
    $laborCost = $totalMaterialCost * 0.4; // Labor is 40% of material cost
    $grandTotal = $totalMaterialCost + $laborCost + $finishingCost;
    
    return [
        'materials' => [
            'cement' => [
                'quantity' => $cement,
                'unit' => 'طن',
                'unit_price' => $cementPrice,
                'total' => $cementCost
            ],
            'steel' => [
                'quantity' => $steel,
                'unit' => 'كغ',
                'unit_price' => $steelPrice,
                'total' => $steelCost
            ],
            'sand' => [
                'quantity' => $sand,
                'unit' => 'م³',
                'unit_price' => $sandPrice,
                'total' => $sandCost
            ],
            'bricks' => [
                'quantity' => $bricks,
                'unit' => 'قطعة',
                'unit_price' => $brickPrice,
                'total' => $brickCost
            ]
        ],
        'totalMaterialCost' => $totalMaterialCost,
        'laborCost' => $laborCost,
        'finishingCost' => $finishingCost,
        'grandTotal' => $grandTotal,
        'qualityMultiplier' => $qualityMultiplier,
        'totalArea' => $totalArea
    ];
}

// Function to get material prices for a specific province
function getProvinceMaterialPrices($pdo, $provinceId) {
    $stmt = $pdo->prepare("
        SELECT material_name, unit, price 
        FROM province_materials 
        WHERE province_id = ?
    ");
    $stmt->execute([$provinceId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Helper function to find material price by name
function findMaterialPrice($materialPrices, $materialName) {
    foreach ($materialPrices as $material) {
        if (strpos($material['material_name'], $materialName) !== false) {
            return $material['price'];
        }
    }
    return null;
}

// Function to calculate finishing cost
function calculateFinishingCost($finishingType, $area) {
    $finishingCosts = [
        'none' => 0,
        'stone' => 50, // $ per square meter
        'marble' => 80,
        'tiles' => 30,
        'panels' => 40,
        'full' => 120
    ];
    
    return ($finishingCosts[$finishingType] ?? 0) * $area;
}

// Function to save calculation to database (updated with new fields)
function saveCalculation($pdo, $userId, $area, $buildingType, $floors, $materialQuality, $totalCost, $provinceId = null, $basement = 'no', $garage = 'no', $finishingType = null) {
    $stmt = $pdo->prepare("
        INSERT INTO calculations 
        (user_id, area, building_type, floors, material_quality, total_cost, province_id, basement, garage, finishing_type) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
        $userId, 
        $area, 
        $buildingType, 
        $floors, 
        $materialQuality, 
        $totalCost, 
        $provinceId, 
        $basement, 
        $garage, 
        $finishingType
    ]);
}
}

// Function to get companies by province
if (!function_exists('getCompaniesByProvince')) {
    function getCompaniesByProvince($pdo, $provinceId) {
        $stmt = $pdo->prepare("
            SELECT * FROM companies 
            WHERE province_id = ?
        ");
        $stmt->execute([$provinceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('getProvinces')) {

// Function to get all provinces
function getProvinces($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM provinces ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}

if (!function_exists('saveMessage')) {

// Function to save contact message
function saveMessage($pdo, $name, $email, $message) {
    $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
    return $stmt->execute([$name, $email, $message]);
}
}
if (!function_exists('isLoggedIn')) {

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
}
if (!function_exists('isAdmin')) {

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
}
if (!function_exists('requireLogin')) {

// Function to redirect to login if not authenticated
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}
}
if (!function_exists('requireAdmin')) {

// Function to redirect to admin login if not admin
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: ../admin/login.php");
        exit();
    }
}
}
if (!function_exists('getUserCalculations')) {

// Function to get user calculations
function getUserCalculations($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT c.*, p.name as province_name 
        FROM calculations c 
        LEFT JOIN provinces p ON c.province_id = p.id 
        WHERE c.user_id = ? 
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
if (!function_exists('getRecentCalculations')) {

// Function to get recent calculations for admin
function getRecentCalculations($pdo, $limit = 10) {
    $stmt = $pdo->prepare("
        SELECT c.*, u.username, p.name as province_name 
        FROM calculations c 
        LEFT JOIN users u ON c.user_id = u.id 
        LEFT JOIN provinces p ON c.province_id = p.id 
        ORDER BY c.created_at DESC 
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
if (!function_exists('getCalculationStats')) {

// Function to get calculation statistics
function getCalculationStats($pdo) {
    $stats = [];
    
    // Total calculations
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM calculations");
    $stats['total_calculations'] = $stmt->fetchColumn();
    
    // Today's calculations
    $stmt = $pdo->query("SELECT COUNT(*) as today FROM calculations WHERE DATE(created_at) = CURDATE()");
    $stats['today_calculations'] = $stmt->fetchColumn();
    
    // Average cost
    $stmt = $pdo->query("SELECT AVG(total_cost) as avg_cost FROM calculations");
    $stats['average_cost'] = $stmt->fetchColumn();
    
    return $stats;
}
}
if (!function_exists('sanitizeInput')) {

// Function to validate and sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
}
if (!function_exists('formatCurrency')) {

// Function to format currency
function formatCurrency($amount, $currency = '$') {
    return number_format($amount, 2) . ' ' . $currency;
}
}
if (!function_exists('getBuildingTypeName')) {

// Function to get building type name in Arabic
function getBuildingTypeName($type) {
    $types = [
        'house' => 'منزل',
        'villa' => 'فيلا',
        'apartment' => 'عمارة',
        'commercial' => 'تجاري'
    ];
    return $types[$type] ?? $type;
}
}
if (!function_exists('getMaterialQualityName')) {

// Function to get material quality name in Arabic
function getMaterialQualityName($quality) {
    $qualities = [
        'standard' => 'عادية',
        'premium' => 'ممتازة',
        'luxury' => 'فاخرة'
    ];
    return $qualities[$quality] ?? $quality;
}
}
if (!function_exists('getFinishingTypeName')) {

// Function to get finishing type name in Arabic
function getFinishingTypeName($finishing) {
    $finishingTypes = [
        'none' => 'بدون إكساء',
        'stone' => 'تركيب الحجر',
        'marble' => 'تلبيس الرخام',
        'tiles' => 'تركيب البلاط',
        'panels' => 'الألواح المعدنية أو الخشبية',
        'full' => 'إكساء كامل'
    ];
    return $finishingTypes[$finishing] ?? $finishing;
}
}
?>