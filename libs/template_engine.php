<?php
function renderTemplate($type, $section) {
    $basePath = __DIR__ . '/../includes/';
    
    $filename = $basePath . "{$type}_{$section}.php";
    
    if (file_exists($filename)) {
        include $filename;
    } else {
        echo "<!-- Template {$type}_{$section} tidak ditemukan -->";
    }
}
?>
