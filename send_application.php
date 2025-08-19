<?php
// ===== CONFIG =====
$ADMIN_EMAIL = "lavishk@graycelltech.com";     // Change this
$FROM_EMAIL  = "lavishk@graycelltech.com";     // Must be a domain you own
$SITE_NAME   = "Momentum";
$LOGO_URL    = "https://yourdomain.com/images/Logo_Momentum_BlackFont.png"; // Absolute URL for email

if ($_SERVER["REQUEST_METHOD"] !== "POST") { 
    http_response_code(405); 
    exit; 
}

function clean($s) { return trim(strip_tags($s ?? "")); }

$name     = clean($_POST['name'] ?? "");
$position = clean($_POST['position'] ?? "");
$company  = clean($_POST['company'] ?? "");
$revenue  = clean($_POST['company-revenue'] ?? "");
$team     = clean($_POST['team-size'] ?? "");
$overview = clean($_POST['overview'] ?? "");
$join     = clean($_POST['join-momentum'] ?? "");

// Server-side validation
$errors = [];
if (!preg_match("/^[A-Za-z][A-Za-z\s'\-]{1,}$/", $name))        $errors[] = "name";
if (!preg_match("/^[A-Za-z][A-Za-z\s'\-]{1,}$/", $position))    $errors[] = "position";
if (!preg_match("/^[A-Za-z0-9&.\-'\s]{2,}$/", $company))        $errors[] = "company";
if (!preg_match("/^\d+$/", $revenue))                           $errors[] = "company-revenue";
if (!preg_match("/^\d+$/", $team) || intval($team) <= 0)        $errors[] = "team-size";
if (strlen($overview) < 10)                                     $errors[] = "overview";
if (strlen($join) < 10)                                         $errors[] = "join-momentum";

if ($errors) { 
    echo "error"; 
    exit; 
}

// Subject
$subject = "New Application Enrollment: $name";

// HTML Email Body
$bodyAdmin = "
<html>
<head>
<title>$subject</title>
<style>
    table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
    td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f4f4f4; }
    .footer { font-size: 12px; color: #666; text-align: center; margin-top: 20px; }
</style>
</head>
<body>
    <div style='text-align:center; padding:10px;'>
        <h2 style='margin:5px 0;'>$SITE_NAME - Application Enrollment</h2>
    </div>
    <table>
        <tr><th>Name</th><td>$name</td></tr>
        <tr><th>Position</th><td>$position</td></tr>
        <tr><th>Company</th><td>$company</td></tr>
        <tr><th>Company Revenue</th><td>$revenue</td></tr>
        <tr><th>Team Size</th><td>$team</td></tr>
        <tr><th>Overview</th><td>".nl2br(htmlspecialchars($overview))."</td></tr>
        <tr><th>Reason to Join</th><td>".nl2br(htmlspecialchars($join))."</td></tr>
    </table>
    <div class='footer'>
        &copy; ".date('Y')." $SITE_NAME. All rights reserved.
    </div>
</body>
</html>
";

// Headers
$headers  = "From: $FROM_EMAIL\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

// Send to admin
@mail($ADMIN_EMAIL, $subject, $bodyAdmin, $headers);

echo "success";
?>
