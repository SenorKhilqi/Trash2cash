<?php
/**
 * Trash2Cash - Main entry point
 * Redirects to the beranda (homepage) section
 */

// Start session if needed
session_start();

// Redirect to beranda section
header("Location: beranda/");
exit();
?>
