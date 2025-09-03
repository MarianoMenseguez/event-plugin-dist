# Event Calendar Plugin - Installation Troubleshooting

## 🚨 Activation Error Fix

If you're getting the error: `Fatal error: Uncaught Error: Class "ECP_Database" not found`, follow these steps:

## 🔧 Quick Fix Steps

### Step 1: Deactivate and Delete the Plugin
1. Go to **WordPress Admin → Plugins**
2. Find "Event Calendar Plugin" and click **Deactivate**
3. Click **Delete** to remove the plugin completely

### Step 2: Re-upload the Plugin Files
1. Make sure all plugin files are uploaded to `/wp-content/plugins/EventCalendar-Plugin/`
2. Verify the file structure matches this:

```
EventCalendar-Plugin/
├── event-calendar-plugin.php
├── demo-setup.php
├── debug-install.php
├── includes/
│   ├── class-database.php
│   ├── class-admin.php
│   ├── class-public.php
│   ├── class-shortcodes.php
│   ├── class-divi-integration.php
│   └── divi/
│       ├── class-et-pb-ecp-events.php
│       ├── class-et-pb-ecp-event-card.php
│       └── class-et-pb-ecp-upcoming-events.php
└── assets/
    ├── css/
    │   ├── style.css
    │   └── admin.css
    └── js/
        ├── script.js
        ├── public.js
        ├── admin.js
        └── divi.js
```

### Step 3: Test the Installation
1. Go to: `yoursite.com/wp-content/plugins/EventCalendar-Plugin/debug-install.php`
2. Run the debug script to check for issues
3. If all tests pass, proceed to Step 4

### Step 4: Activate the Plugin
1. Go to **WordPress Admin → Plugins**
2. Find "Event Calendar Plugin" and click **Activate**
3. The plugin should now activate successfully

## 🐛 Common Issues and Solutions

### Issue 1: File Permissions
**Problem:** Files not readable by WordPress
**Solution:** 
```bash
chmod 644 *.php
chmod 644 includes/*.php
chmod 644 assets/css/*.css
chmod 644 assets/js/*.js
```

### Issue 2: Missing Files
**Problem:** Some plugin files are missing
**Solution:** 
- Re-upload all plugin files
- Check that the `includes/` directory exists
- Verify all class files are present

### Issue 3: WordPress Version Compatibility
**Problem:** Plugin requires WordPress 5.0+
**Solution:** 
- Update WordPress to the latest version
- Check PHP version (requires 7.4+)

### Issue 4: Plugin Conflicts
**Problem:** Another plugin is interfering
**Solution:** 
- Deactivate all other plugins temporarily
- Try activating Event Calendar Plugin
- Reactivate other plugins one by one

### Issue 5: Database Issues
**Problem:** Database tables can't be created
**Solution:** 
- Check database user permissions
- Ensure database user has CREATE TABLE privileges
- Check WordPress database connection

## 🔍 Debug Information

### Check Plugin Status
Run this in your WordPress admin or add to `wp-config.php` temporarily:
```php
// Add this to wp-config.php for debugging
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Check Error Logs
Look for errors in:
- `/wp-content/debug.log`
- Server error logs
- WordPress error logs

### Manual Database Check
If the plugin activates but tables aren't created, run this SQL:
```sql
-- Check if tables exist
SHOW TABLES LIKE 'wp_ecp_events';
SHOW TABLES LIKE 'wp_ecp_attendees';

-- Create tables manually if needed
CREATE TABLE wp_ecp_events (
    id int(11) NOT NULL AUTO_INCREMENT,
    title varchar(255) NOT NULL,
    description text,
    event_date date NOT NULL,
    event_time time NOT NULL,
    location varchar(255),
    registration_link varchar(500),
    social_media_links text,
    flyer_url varchar(500),
    status varchar(20) DEFAULT 'active',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY event_date (event_date),
    KEY status (status)
);

CREATE TABLE wp_ecp_attendees (
    id int(11) NOT NULL AUTO_INCREMENT,
    event_id int(11) NOT NULL,
    first_name varchar(100) NOT NULL,
    last_name varchar(100) NOT NULL,
    position varchar(255),
    company varchar(255),
    email varchar(255),
    phone varchar(50),
    registration_date datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY event_id (event_id),
    KEY email (email)
);
```

## ✅ Success Indicators

After successful installation, you should see:
- ✅ Plugin activates without errors
- ✅ "Event Calendar" appears in WordPress admin menu
- ✅ Database tables are created
- ✅ No PHP errors in debug logs

## 🆘 Still Having Issues?

If you're still experiencing problems:

1. **Check the debug script**: Run `debug-install.php` and share the results
2. **Check error logs**: Look for specific error messages
3. **Test with default theme**: Switch to a default WordPress theme temporarily
4. **Check server requirements**: Ensure PHP 7.4+ and WordPress 5.0+

## 📞 Support Information

When asking for help, please provide:
- WordPress version
- PHP version
- Error messages (exact text)
- Debug script results
- Server environment details

---

**The plugin has been updated with better error handling and fallback mechanisms. The activation should now work properly!**
