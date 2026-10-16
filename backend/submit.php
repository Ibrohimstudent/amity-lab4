<?php

// Receives form data (POST), validates it and saves it

require_once __DIR__ . "/config.php";

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "errors" => ["Only POST requests are allowed."]
    ]);

    exit;
}

// Get form data
$name = trim($_POST["name"] ?? "");
$enrollment = trim($_POST["enrollment"] ?? "");
$email = trim($_POST["email"] ?? "");
$course = trim($_POST["course"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$marks = $_POST["marks"] ?? "";

// Validation
$errors = [];

if ($name === "") {
    $errors[] = "Name is required.";
}

if (strlen($name) > 100) {
    $errors[] = "Name must not exceed 100 characters.";
}

if ($enrollment === "") {
    $errors[] = "Enrollment number is required.";
}

if (strlen($enrollment) > 50) {
    $errors[] = "Enrollment number must not exceed 50 characters.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Enter a valid email.";
}

if (strlen($email) > 254) {
    $errors[] = "Email address is too long.";
}

if (!in_array($course, [
    "B.Tech CSE",
    "BCA",
    "MCA",
    "B.Sc IT"
], true)) {
    $errors[] = "Please select a valid course.";
}

if (!in_array($gender, [
    "Male",
    "Female",
    "Other"
], true)) {
    $errors[] = "Please select gender.";
}

if (
    $marks === "" ||
    !is_numeric($marks) ||
    (float)$marks < 0 ||
    (float)$marks > 100
) {
    $errors[] = "Marks must be between 0 and 100.";
}

// Return validation errors
if (!empty($errors)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "errors" => $errors
    ]);

    exit;
}

// Load existing student records
$students = load_students();

// Check for duplicate enrollment numbers
foreach ($students as $student) {
    if (($student["enrollment"] ?? "") === $enrollment) {
        http_response_code(409);

        echo json_encode([
            "success" => false,
            "errors" => [
                "This enrollment number already exists."
            ]
        ]);

        exit;
    }
}

// Prepare the new student record
$newStudent = [
    "enrollment" => $enrollment,
    "name" => $name,
    "email" => $email,
    "course" => $course,
    "gender" => $gender,
    "marks" => (float)$marks,
    "grade" => grade((float)$marks)
];

// Add student and save records
$students[] = $newStudent;

save_students($students);

// Return success response
echo json_encode([
    "success" => true,
    "message" => "Student " . $name . " saved successfully!"
]);

?>