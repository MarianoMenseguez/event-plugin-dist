<?php
/**
 * Debug Installation Script for Event Calendar Plugin
 * 
 * Run this file to test the plugin installation step by step
 * Access it via: yoursite.com/wp-content/plugins/EventCalendar-Plugin/debug-install.php
 */

// Load WordPress
require_once('../../../wp-config.php');

echo "<h1>Event Calendar Plugin - Debug Installation</h1>";

// Test 1: Check if WordPress is loaded
echo "<h2>Test 1: WordPress Environment</h2>";
if (defined('ABSPATH')) {
    echo "✅ WordPress loaded successfully<br>";
    echo "WordPress Version: " . get_bloginfo('version') . "<br>";
    echo "PHP Version: " . phpversion() . "<br>";
} else {
    echo "❌ WordPress not loaded<br>";
    exit;
}

// Test 2: Check plugin constants
echo "<h2>Test 2: Plugin Constants</h2>";
define('ECP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ECP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ECP_VERSION', '1.0.0');

echo "✅ Plugin constants defined<br>";
echo "Plugin Path: " . ECP_PLUGIN_PATH . "<br>";
echo "Plugin URL: " . ECP_PLUGIN_URL . "<br>";

// Test 3: Check if files exist
echo "<h2>Test 3: File Structure</h2>";
$required_files = array(
    'includes/class-database.php',
    'includes/class-admin.php',
    'includes/class-public.php',
    'includes/class-shortcodes.php',
    'includes/class-divi-integration.php',
    'demo-setup.php'
);

foreach ($required_files as $file) {
    $file_path = ECP_PLUGIN_PATH . $file;
    if (file_exists($file_path)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// Test 4: Try to load database class
echo "<h2>Test 4: Database Class Loading</h2>";
$db_file = ECP_PLUGIN_PATH . 'includes/class-database.php';
if (file_exists($db_file)) {
    require_once $db_file;
    if (class_exists('ECP_Database')) {
        echo "✅ ECP_Database class loaded successfully<br>";
    } else {
        echo "❌ ECP_Database class not found after loading file<br>";
    }
} else {
    echo "❌ Database class file not found<br>";
}

// Test 5: Test database table creation
echo "<h2>Test 5: Database Table Creation</h2>";
if (class_exists('ECP_Database')) {
    try {
        ECP_Database::create_tables();
        echo "✅ Database tables created successfully<br>";
        
        // Check if tables exist
        global $wpdb;
        $events_table = $wpdb->prefix . 'ecp_events';
        $attendees_table = $wpdb->prefix . 'ecp_attendees';
        
        $events_exists = $wpdb->get_var("SHOW TABLES LIKE '$events_table'") == $events_table;
        $attendees_exists = $wpdb->get_var("SHOW TABLES LIKE '$attendees_table'") == $attendees_table;
        
        if ($events_exists) {
            echo "✅ Events table exists<br>";
        } else {
            echo "❌ Events table not found<br>";
        }
        
        if ($attendees_exists) {
            echo "✅ Attendees table exists<br>";
        } else {
            echo "❌ Attendees table not found<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Error creating database tables: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Cannot test database creation - ECP_Database class not available<br>";
}

// Test 6: Check WordPress capabilities
echo "<h2>Test 6: WordPress Capabilities</h2>";
if (current_user_can('manage_options')) {
    echo "✅ Current user has manage_options capability<br>";
} else {
    echo "⚠️ Current user does not have manage_options capability<br>";
}

// Test 7: Check Divi theme
echo "<h2>Test 7: Divi Theme Check</h2>";
$current_theme = wp_get_theme();
echo "Current Theme: " . $current_theme->get('Name') . "<br>";
if (strpos(strtolower($current_theme->get('Name')), 'divi') !== false) {
    echo "✅ Divi theme detected<br>";
} else {
    echo "⚠️ Divi theme not detected - plugin will still work but Divi modules won't be available<br>";
}

// Test 8: Memory and PHP limits
echo "<h2>Test 8: System Requirements</h2>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . " seconds<br>";
echo "Upload Max Filesize: " . ini_get('upload_max_filesize') . "<br>";

// Test 9: Plugin activation simulation
echo "<h2>Test 9: Plugin Activation Simulation</h2>";
try {
    // Simulate the activation process
    if (class_exists('ECP_Database')) {
        ECP_Database::create_tables();
        echo "✅ Database tables created<br>";
    }
    
    // Test page creation
    $events_page = get_page_by_path('eventos');
    if (!$events_page) {
        echo "✅ Events page will be created (not found)<br>";
    } else {
        echo "✅ Events page already exists<br>";
    }
    
    echo "✅ Plugin activation simulation successful<br>";
    
} catch (Exception $e) {
    echo "❌ Plugin activation simulation failed: " . $e->getMessage() . "<br>";
}

echo "<h2>Debug Complete</h2>";
echo "<p>If all tests pass, the plugin should activate successfully. If any tests fail, please check the error messages above.</p>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>If all tests pass, try activating the plugin again</li>";
echo "<li>If tests fail, check the file permissions and WordPress installation</li>";
echo "<li>Make sure all plugin files are uploaded correctly</li>";
echo "</ol>";

echo "<p><a href='" . admin_url('plugins.php') . "'>← Back to Plugins</a></p>";
?>
