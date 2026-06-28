<?php
/**
 * icons.php
 * A small inline-SVG icon set used across the app, returned as raw markup strings.
 * Centralizing these means every page draws icons with the same stroke weight/size,
 * and we're not depending on an external icon font or library.
 *
 * Usage: echo icon('edit');
 */
function icon($name, $class = '') {
    $icons = [
        'customers' => '<path d="M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3Zm-8 0c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3Zm0 2c-2.33 0-7 1.17-7 3.5V19h7.5v-2.5c0-.96.34-2.6 2.07-3.79C9.61 12.32 8.42 13 8 13Zm8 0c-.18 0-.37.01-.57.03 1.36 1 2.07 2.36 2.07 3.97V19H23v-2.5C23 14.17 18.33 13 16 13Z"/>',
        'add' => '<path d="M12 5v14M5 12h14"/>',
        'edit' => '<path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>',
        'trash' => '<path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6h16Z"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
        'users' => '<path d="M9 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-3.33 0-8 1.67-8 5v2h16v-2c0-3.33-4.67-5-8-5Z"/><path d="M16 8a3 3 0 1 0 0-6m1 8c1.96.66 4 1.94 4 4v2h-2"/>',
        'sparkline' => '<path d="M3 14l4-4 3 3 5-6 6 4"/>',
        'building' => '<path d="M4 21V7l8-4 8 4v14M4 21h16M9 21v-5h6v5M9 11h0M15 11h0M9 7h0M15 7h0"/>',
        'arrow-left' => '<path d="M19 12H5M12 19l-7-7 7-7"/>',
        'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.5 2.5 5-5"/>',
        'alert-circle' => '<circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h0"/>',
        'empty-box' => '<path d="M21 8 12 3 3 8m18 0-9 5m9-5v9l-9 5M3 8l9 5M3 8v9l9 5m0-9v9"/>',
        'empty-search' => '<circle cx="10" cy="10" r="6"/><path d="m20 20-4.3-4.3M8 10h4"/>',
        'spinner' => '<path d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.36-6.36-2.12 2.12M7.76 16.24l-2.12 2.12m12.72 0-2.12-2.12M7.76 7.76 5.64 5.64"/>',
    ];

    $path = $icons[$name] ?? '';
    return '<svg class="icon ' . htmlspecialchars($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
}
?>