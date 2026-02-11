<?php
// migrate/includes/captcha.php

function generate_captcha() {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $answer = $num1 + $num2;
    $_SESSION['captcha_answer'] = $answer;
    return "$num1 + $num2 goals = ?";
}

function verify_captcha($user_answer) {
    return isset($_SESSION['captcha_answer']) && (int)$user_answer === (int)$_SESSION['captcha_answer'];
}

/**
 * For a real "Match Image" captcha, we would generate an image with GD.
 * Here's a placeholder for that logic.
 */
function get_captcha_image_url() {
    // In a real app, this would return a link to a script that generates a GD image
    // For now, we'll stick to the text-based one as it's more reliable in this environment
    return null;
}
