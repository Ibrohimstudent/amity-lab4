<?php

// Sends all saved students to the frontend as JSON

require_once __DIR__ . "/config.php";

// Load all saved students
$students = load_students();

// Calculate each student's grade
foreach ($students as &$student) {
    $student["grade"] = grade($student["marks"] ?? 0);
}

unset($student);

// Calculate the class average
$totalStudents = count($students);

$average = $totalStudents > 0
    ? round(
        array_sum(array_column($students, "marks")) / $totalStudents,
        1
    )
    : 0;

// Send student data to the frontend as JSON
echo json_encode([
    "count" => $totalStudents,
    "average" => $average,
    "students" => $students
], JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE);

?>