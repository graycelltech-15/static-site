<?php
// ===== CONFIG =====
$ADMIN_EMAIL = "lavishk@graycelltech.com";     // Change this
$FROM_EMAIL  = "lavishk@graycelltech.com";     // Must be a domain you own
$SITE_NAME   = "Momentum";
$LOGO_URL    = "https://graycelltech.net/gct/Momentum/images/Logo_Momentum_BlackFont.png
"; // Absolute URL for email

if ($_SERVER["REQUEST_METHOD"] !== "POST") { 
    http_response_code(405); 
    exit; 
}

function clean($s) { return trim(strip_tags($s ?? "")); }

$fname = clean($_POST['fname'] ?? "");
$lname = clean($_POST['lname'] ?? "");
$email = filter_var($_POST['email'] ?? "", FILTER_SANITIZE_EMAIL);
$tel   = clean($_POST['tel'] ?? "");
$msg   = clean($_POST['msg'] ?? "");

// Server-side validation
$errors = [];
if (!preg_match("/^[A-Za-z][A-Za-z\s'\-]{1,}$/", $fname)) $errors[] = "fname";
if (!preg_match("/^[A-Za-z][A-Za-z\s'\-]{1,}$/", $lname)) $errors[] = "lname";
if (!filter_var($email, FILTER_VALIDATE_EMAIL))          $errors[] = "email";
$digits = preg_replace("/\D/", "", $tel);
if (!(preg_match("/^[0-9()+\-\s]{7,20}$/", $tel) && strlen($digits) >= 7 && strlen($digits) <= 15)) $errors[] = "tel";
if (strlen($msg) < 5) $errors[] = "msg";

if ($errors) { 
    echo "error"; 
    exit; 
}

$fullName = $fname . " " . $lname;
$subjectAdmin = "New Contact Submission from $fullName";
$subjectUser  = "Thanks for contacting $SITE_NAME";

// HTML email for admin
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
        <h2 style='margin:5px 0;'>$SITE_NAME - Contact Form Submission</h2>
    </div>
    <table>
        <tr><th>Name</th><td>$fullName</td></tr>
        <tr><th>Email</th><td>$email</td></tr>
        <tr><th>Phone</th><td>$tel</td></tr>
        <tr><th>Message</th><td>".nl2br(htmlspecialchars($msg))."</td></tr>
    </table>
    <div class='footer'>
        &copy; ".date('Y')." $SITE_NAME. All rights reserved.
    </div>
</body>
</html>
";

// HTML email for user
$bodyUser = "
<html>
<head><title>$subjectUser</title></head>
<body>
    <div style='text-align:center; padding:10px;'>
        <h2 style='margin:5px 0;'>Thank you for contacting $SITE_NAME</h2>
    </div>
    <p>Hi $fullName,</p>
    <p>Thanks for reaching out! We've received your message and will get back to you soon.</p>
    <div style='font-size:12px;color:#666;text-align:center;margin-top:20px;'>
        &copy; ".date('Y')." $SITE_NAME. All rights reserved.
    </div>
</body>
</html>
";

// Headers
$headersAdmin  = "From: $FROM_EMAIL\r\n";
$headersAdmin .= "Reply-To: $email\r\n";
$headersAdmin .= "MIME-Version: 1.0\r\n";
$headersAdmin .= "Content-Type: text/html; charset=UTF-8\r\n";

$headersUser   = "From: $FROM_EMAIL\r\n";
$headersUser  .= "MIME-Version: 1.0\r\n";
$headersUser  .= "Content-Type: text/html; charset=UTF-8\r\n";

// Send mails
@mail($ADMIN_EMAIL, $subjectAdmin, $bodyAdmin, $headersAdmin);
@mail($email, $subjectUser, $bodyUser, $headersUser);

echo "success";
?>
