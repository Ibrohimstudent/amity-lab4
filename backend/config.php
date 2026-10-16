<?php

// Shared backend settings and helper functions

header("Content-Type: application/json; charset=UTF-8");

define("DATA_FILE", __DIR__ . "/data/students.json");

// Read all students from the JSON file
function load_students() {
    if (!file_exists(DATA_FILE)) {
        return [];
    }

    $data = json_decode(file_get_contents(DATA_FILE), true);

    return is_array($data) ? $data : [];
}

// Save all students to the JSON file
function save_students($students) {
    $directory = dirname(DATA_FILE);

    // Create the data directory if it does not exist
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    file_put_contents(
        DATA_FILE,
        json_encode($students, JSON_PRETTY_PRINT),
        LOCK_EX
    );
}

// Work out the grade from marks
function grade($marks) {
    if ($marks >= 90) {
        return "A+";
    }

    if ($marks >= 80) {
        return "A";
    }

    if ($marks >= 70) {
        return "B";
    }

    if ($marks >= 60) {
        return "C";
    }

    return "D";
}

// Clean user input to help prevent HTML/script injection
function clean($value) {
    return htmlspecialchars(
        trim($value ?? ""),
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}
?>