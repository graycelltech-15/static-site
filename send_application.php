<?php

// ===== CONFIG =====       
$ADMIN_EMAIL = "marketing@bistraining.ca"; //admin email 
$FROM_EMAIL  = "info@momentum-group.ca";     
$SITE_NAME   = "Momentum";
$LOGO_URL    = "https://yourdomain.com/images/Logo_Momentum_BlackFont.png"; // Absolute URL for email
$SMTP2GO_API_KEY = "api-44F79F1D7F4B4E4D86B11B1755234E39"; 

if ($_SERVER["REQUEST_METHOD"] !== "POST") { 
    http_response_code(405); 
    exit; 
}

// $secretKey = "6LeicLUrAAAAAKMENuOOV2IA6zG7rN7gdTCaPTi_"; //original
$secretKey = "6LdB2L0rAAAAAL6_OfU2l3vm2UGNwn8eCkw-o6sg"; //testing

$recaptchaResponse = $_POST['g-recaptcha-response'] ?? "";

$verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . urlencode($secretKey) . "&response=" . urlencode($recaptchaResponse));
$responseData = json_decode($verifyResponse);

if (!$responseData->success) {
    echo "error";
    exit;
}


function clean($s) { return trim(strip_tags($s ?? "")); }

$name     = clean($_POST['name'] ?? "");
$position = clean($_POST['position'] ?? "");
$company  = clean($_POST['company'] ?? "");
$revenue  = clean($_POST['company-revenue'] ?? "");
$team     = clean($_POST['team-size'] ?? "");
$department= clean($_POST['department'] ?? "");
$email     = clean($_POST['email'] ?? "");
$tel       = clean($_POST['tel'] ?? "");
$overview = clean($_POST['overview'] ?? "");
$join     = clean($_POST['join-momentum'] ?? "");

// Server-side validation
$errors = [];
if (!preg_match("/^[A-Za-z][A-Za-z\s'\-]{1,}$/", $name))        $errors[] = "name";
if (!preg_match("/^[A-Za-z][A-Za-z\s'\-]{1,}$/", $position))    $errors[] = "position";
if (!preg_match("/^[A-Za-z0-9&.\-'\s]{2,}$/", $company))        $errors[] = "company";
if ($revenue !== "" && !preg_match("/^\d+$/", $revenue))        $errors[] = "company-revenue";
if (!preg_match("/^\d+$/", $team) || intval($team) <= 0)        $errors[] = "team-size";
if (!preg_match("/^[A-Za-z][A-Za-z\s'\-]{1,}$/", $department))  $errors[] = "department";
if (!filter_var($email, FILTER_VALIDATE_EMAIL))                 $errors[] = "email";
if ($tel !== "" && !preg_match("/^[0-9()+\-\s]{7,20}$/", $tel)) $errors[] = "tel";
if ($overview === "") $errors[] = "overview";
if ($join === "") $errors[] = "join-momentum";

if ($errors) { 
    echo "error"; 
    exit; 
}

// ===== SUBJECTS =====
$subjectAdmin = "New Application Enrollment: $name";
$subjectUser  = "Thank you for applying to $SITE_NAME";

// ===== HTML EMAIL (Admin) =====
$revenueRow = !empty($revenue) ? "<tr><th>Company Revenue</th><td>$revenue</td></tr>" : "";
$phoneRow   = !empty($tel) ? "<tr><th>Phone</th><td>$tel</td></tr>" : "";

$bodyAdmin = "
<html>
<head>
<title>$subjectAdmin</title>
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
        $revenueRow
        <tr><th>Team Size</th><td>$team</td></tr>
        <tr><th>Department</th><td>$department</td></tr>
        <tr><th>Email</th><td>$email</td></tr>
        $phoneRow
        <tr><th>Overview</th><td>".nl2br(htmlspecialchars($overview))."</td></tr>
        <tr><th>Reason to Join</th><td>".nl2br(htmlspecialchars($join))."</td></tr>
    </table>
    <div class='footer'>
        &copy; ".date('Y')." $SITE_NAME. All rights reserved.
    </div>
</body>
</html>
";

// ===== HTML EMAIL (User) =====
$bodyUser = "
<html>
<head><title>$subjectUser</title></head>
<body>
    <div style='text-align:center; padding:10px;'>
        <h2 style='margin:5px 0;'>Thank you for applying to $SITE_NAME</h2>
    </div>
    <p>Hi $name,</p>
    <p>Thanks for submitting your application! We've received it and our team will review your details. 
    We'll get back to you soon.</p>
    <div style='font-size:12px;color:#666;text-align:center;margin-top:20px;'>
        &copy; ".date('Y')." $SITE_NAME. All rights reserved.
    </div>
</body>
</html>
";

// ===== SEND VIA SMTP2GO =====
function sendSMTP2GO($to, $subject, $htmlBody, $from, $replyTo = null) {
    global $SMTP2GO_API_KEY;

    $payload = [
        "api_key"   => $SMTP2GO_API_KEY,
        "to"        => [$to],
        "sender"    => $from,
        "subject"   => $subject,
        "html_body" => $htmlBody
    ];

    if ($replyTo) {
        $payload["reply_to"] = $replyTo;
    }

    $ch = curl_init("https://api.smtp2go.com/v3/email/send");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

// Send mails
sendSMTP2GO($ADMIN_EMAIL, $subjectAdmin, $bodyAdmin, $FROM_EMAIL, $email);
sendSMTP2GO($email, $subjectUser, $bodyUser, $FROM_EMAIL);

echo "success";
?>
