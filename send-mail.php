<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formType = $_POST["formType"];

    if ($formType === "contact") {
        $fname = $_POST["fname"];
        $lname = $_POST["lname"];
        $email = $_POST["email"];
        $tel = $_POST["tel"];
        $msg = $_POST["msg"];

        $adminEmail = "lavishk@graycelltech.com";
        $subjectAdmin = "New Contact Us Submission";
        $messageAdmin = "Name: $fname $lname\nEmail: $email\nPhone: $tel\nMessage:\n$msg";

        $subjectUser = "Thank you for contacting us";
        $messageUser = "Dear $fname,\n\nThank you for reaching out. We will get back to you soon.\n\nBest Regards,\nTeam";

        mail($adminEmail, $subjectAdmin, $messageAdmin);
        mail($email, $subjectUser, $messageUser);

    } elseif ($formType === "application") {
        $name = $_POST["name"];
        $position = $_POST["position"];
        $company = $_POST["company"];
        $companyRevenue = $_POST["company-revenue"];
        $teamSize = $_POST["team-size"];
        $overview = $_POST["overview"];
        $join = $_POST["join-momentum"];

        $adminEmail = "lavishk@graycelltech.com";
        $subjectAdmin = "New Application Submission";
        $messageAdmin = "Name: $name\nPosition: $position\nCompany: $company\nRevenue: $companyRevenue\nTeam Size: $teamSize\nOverview: $overview\nReason to Join: $join";

        mail($adminEmail, $subjectAdmin, $messageAdmin);
    }

    echo "OK";
}
?>
