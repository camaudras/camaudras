<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST["message"]));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Handle file upload
    $uploadDir = "uploads/";
    $filePath = "";
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        $file = $_FILES["file"];
        $allowedExts = ['jpg', 'jpeg', 'png', 'pdf'];
        $fileExt = pathinfo($file["name"], PATHINFO_EXTENSION);

        // Check file extension
        if (!in_array($fileExt, $allowedExts)) {
            die("Invalid file type.");
        }

        // Check file size (5MB max)
        if ($file["size"] > 5 * 1024 * 1024) {
            die("File size exceeds the limit of 5MB.");
        }

        // Secure the file name and move the file
        $fileName = basename($file["name"]);
        $filePath = $uploadDir . uniqid() . "_" . $fileName;
        if (!move_uploaded_file($file["tmp_name"], $filePath)) {
            die("Error uploading the file.");
        }
    }

    // Prepare email content
    $to = "audras@alumni.usc.edu";
    $subject = "Contact Form Message from $name";
    $body = "Name: $name\nEmail: $email\nMessage: $message\n";
    if ($filePath) {
        $body .= "File uploaded: $filePath\n";
    }

    // Send the email (this could be enhanced with attachments if necessary)
    $headers = "From: $email\r\n";
    if (!mail($to, $subject, $body, $headers)) {
        die("Error sending the email.");
    }

    echo "Thank you for contacting me!";
}
?>
