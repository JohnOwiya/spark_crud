<?php
/**
 * helpers.php
 * Small reusable helper functions shared across pages.
 */

// Shorthand for safely printing user data into HTML (prevents XSS)
function h($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Turns "Amina Yusuf" into "AY" for the avatar badge
function initials($name) {
    $parts = preg_split('/\s+/', trim($name));
    $first = $parts[0][0] ?? '';
    $last  = count($parts) > 1 ? $parts[count($parts) - 1][0] : '';
    return strtoupper($first . $last);
}

// Deterministically picks one of a small palette of avatar colors based on the name,
// so the same customer always gets the same color, but colors vary across the table.
function avatarColor($name) {
    $palette = ['#2D6BFF', '#16A34A', '#D97706', '#7C3AED', '#DB2777', '#0EA5E9'];
    $index = array_sum(array_map('ord', str_split($name))) % count($palette);
    return $palette[$index];
}
?>