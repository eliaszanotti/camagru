<?php

require_once __DIR__ . '/../validators/UserValidator.php';

$testFiles = [
    __DIR__ . '/unit/UserValidatorTest.php',
    __DIR__ . '/pages/RegisterPageTest.php',
    __DIR__ . '/pages/LoginPageTest.php',
    __DIR__ . '/pages/ForgotPasswordPageTest.php',
];

$tests = [];

foreach ($testFiles as $testFile) {
    if (!is_file($testFile)) {
        fwrite(STDERR, "Test file not found: {$testFile}\n");
        exit(1);
    }

    $fileTests = require $testFile;
    if (!is_array($fileTests)) {
        fwrite(STDERR, "Test file must return an array: {$testFile}\n");
        exit(1);
    }

    $tests = array_merge($tests, $fileTests);
}

$passed = 0;
$failed = 0;

foreach ($tests as $name => $test) {
    try {
        $test();
        fwrite(STDOUT, "PASS {$name}\n");
        $passed++;
    } catch (Throwable $error) {
        fwrite(STDERR, "FAIL {$name}: {$error->getMessage()}\n");
        $failed++;
    }
}

fwrite(STDOUT, "\n{$passed} passed, {$failed} failed\n");
exit($failed > 0 ? 1 : 0);
