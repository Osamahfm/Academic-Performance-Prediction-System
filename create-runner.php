<?php
/**
 * Create test runner file
 * Run: php create-runner.php
 */

$sourceFile = __DIR__ . '/tests/RUN_TESTS_CONTENT.txt';
$targetFile = __DIR__ . '/tests/run-tests.php';

if (!file_exists($sourceFile)) {
    die("Error: Source file not found: $sourceFile\n");
}

// Read the content
$content = file_get_contents($sourceFile);

// Remove the first two lines (the instruction line and blank line)
$lines = explode("\n", $content);
$lines = array_slice($lines, 2); // Skip first 2 lines
$content = implode("\n", $lines);

// Write to target file
if (file_put_contents($targetFile, $content)) {
    echo "✅ Successfully created: $targetFile\n";
    echo "You can now run: C:\\xampp\\php\\php.exe tests\\run-tests.php\n";
} else {
    die("❌ Failed to create file. Check permissions.\n");
}

