<?php
// update_admin_password.php
// This script updates the password for all users with the role "admin" in the users.csv file.

// Load the configuration file which defines USERS_CSV
require_once __DIR__ . '/includes/config.php';

// The new password that will be assigned to all admin accounts
$newPassword = "mani0211";

// 1. Hash the password securely using the built-in PHP password_hash function with bcrypt
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

echo "Starting admin password update...\n<br>";

// Check if the CSV file exists
if (!file_exists(USERS_CSV)) {
    die("Error: The users CSV file does not exist at " . USERS_CSV . ".\n");
}

$rows = [];
$header = [];
$adminCount = 0;

// 2. Open the users.csv file for reading
if (($handle = fopen(USERS_CSV, "r")) !== false) {
    // Get the header line so we keep the exact structure
    $header = fgetcsv($handle);
    if ($header !== false) {
        $rows[] = $header;
    }

    // 3. Loop through all the remaining rows in the file
    while (($data = fgetcsv($handle)) !== false) {
        // Handle potentially empty rows safely
        if (empty($data) || count($data) < 5) {
            $rows[] = $data;
            continue;
        }

        // Identify indices based on standard structure: id, name, email, password, role
        $passwordIndex = 3;
        $roleIndex = 4;

        // 4. Detect rows where role matches 'admin'
        if (isset($data[$roleIndex]) && strtolower(trim($data[$roleIndex])) === 'admin') {

            // Replace the plain text or old hashed password with the new safe bcrypt hash
            $data[$passwordIndex] = $hashedPassword;

            $adminCount++;
            echo "Updated password for admin user: " . htmlspecialchars($data[1]) . " (" . htmlspecialchars($data[2]) . ")\n<br>";
        }

        // Add the row (updated or untouched) to our array
        $rows[] = $data;
    }
    fclose($handle);
}
else {
    die("Error: Failed to open CSV file for reading.\n");
}

// 5. Save the updated rows back to users.csv, entirely overwriting it with the new safe data
if (($handle = fopen(USERS_CSV, "w")) !== false) {
    foreach ($rows as $row) {
        // fputcsv automatically handles comma escaping and encapsulation
        fputcsv($handle, $row);
    }
    fclose($handle);
    echo "<br>\nSuccess! Total of $adminCount admin account(s) secured with new BCrypt password.\n";
}
else {
    die("Error: Failed to open CSV file for writing.\n");
}

?>
