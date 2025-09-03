# Event Calendar Plugin - Demo Guide

## 🚀 Quick Start Demo

This guide will help you set up and test the Event Calendar Plugin to see how it works and looks.

## 📋 Prerequisites

- WordPress installation with Divi theme
- Admin access to your WordPress site
- Basic knowledge of WordPress admin panel

## 🔧 Installation Steps

### 1. Install the Plugin

1. Upload the plugin files to `/wp-content/plugins/event-calendar-plugin/`
2. Activate the plugin in WordPress Admin → Plugins
3. You should see "Event Calendar" in your admin menu

### 2. Set Up Demo Data

1. Go to **Event Calendar → Demo Setup** in your WordPress admin
2. Click "Create Demo Data" button
3. Wait for the confirmation message

This will create:

- 4 sample events with realistic data
- Random attendees for each event
- Sample social media links and registration URLs

## 🎨 Frontend Demo Setup

### Option 1: Using Divi Builder (Recommended)

1. Create a new page in WordPress
2. Use Divi Builder to edit the page
3. Add the "ECP Events" module from the Divi modules
4. Configure the module settings:
   - **Layout**: Grid or List
   - **Events per page**: 6
   - **Show filters**: Yes
   - **Show search**: Yes
5. Save and preview the page

### Option 2: Using Shortcodes

Add these shortcodes to any page or post:

```
[ecp_events layout="grid" limit="6" show_filters="yes"]
```

```
[ecp_upcoming_events limit="3" show_register_button="yes"]
```

### Option 3: Using the Demo Template

1. Copy the `demo-page-template.php` file to your active theme directory
2. Create a new page template in WordPress
3. Use the demo template to see the full styling

## 🎯 Demo Features to Test

### 1. Admin Panel Features

**Events Management:**

- Go to **Event Calendar → Events**
- View the list of demo events
- Click "Add New Event" to create a custom event
- Test the event form with all fields
- Upload a flyer image
- Set event date and time
- Add social media links

**Attendees Management:**

- Go to **Event Calendar → Attendees**
- View registered attendees for each event
- Export attendees to CSV
- Search and filter attendees

### 2. Frontend Features

**Event Display:**

- View events in grid layout
- Test responsive design on mobile
- Check event cards with hover effects
- Verify event information display

**Filtering and Search:**

- Use the search box to find events
- Filter by date range
- Filter by location
- Test the "Load More" functionality

**Event Details:**

- Click on any event card
- View the modal with full event details
- Check social media links
- Test the registration button

**Registration Process:**

- Click "Register Now" on any event
- Fill out the registration form
- Submit the form
- Check the success message
- Verify the attendee appears in admin panel

### 3. Divi Integration

**Divi Modules:**

- **ECP Events**: Main events listing module
- **ECP Event Card**: Single event display module
- **ECP Upcoming Events**: Upcoming events widget

**Module Settings:**

- Test different layout options
- Adjust colors and typography
- Configure event display options
- Test responsive settings

## 🎨 Styling and Customization

### CSS Customization

The plugin includes comprehensive CSS that you can customize:

```css
/* Customize event cards */
.ecp-event-card {
  border-radius: 15px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

/* Customize colors */
.ecp-event-title {
  color: #your-brand-color;
}

/* Customize buttons */
.btn-primary {
  background: #your-primary-color;
}
```

### JavaScript Functionality

The plugin includes interactive features:

- Modal popups for event details
- AJAX form submissions
- Real-time filtering and search
- Lazy loading for performance
- Form validation

## 📱 Responsive Design

Test the plugin on different devices:

- **Desktop**: Full grid layout with all features
- **Tablet**: Responsive grid with adjusted spacing
- **Mobile**: Single column layout with touch-friendly buttons

## 🔍 Testing Checklist

### Admin Panel Testing

- [ ] Plugin activates without errors
- [ ] Database tables are created
- [ ] Demo data is created successfully
- [ ] Can create new events
- [ ] Can edit existing events
- [ ] Can delete events
- [ ] Can view attendees
- [ ] Can export attendees to CSV
- [ ] Image upload works for flyers

### Frontend Testing

- [ ] Events display correctly
- [ ] Event cards are clickable
- [ ] Modal popups work
- [ ] Registration forms submit
- [ ] Search functionality works
- [ ] Filters work correctly
- [ ] Responsive design works
- [ ] Social media links work
- [ ] Registration links work

### Divi Integration Testing

- [ ] Divi modules appear in builder
- [ ] Module settings work
- [ ] Custom styling applies
- [ ] Responsive settings work
- [ ] Module preview works

## 🐛 Troubleshooting

### Common Issues

**Plugin not activating:**

- Check PHP version (requires 7.4+)
- Check WordPress version (requires 5.0+)
- Check for plugin conflicts

**Database errors:**

- Ensure database user has CREATE TABLE permissions
- Check for existing table conflicts

**Divi modules not showing:**

- Ensure Divi theme is active
- Check if Divi Builder is enabled
- Clear any caching plugins

**Styling issues:**

- Check for theme conflicts
- Verify CSS files are loading
- Check browser console for errors

### Debug Mode

Enable WordPress debug mode to see detailed error messages:

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## 📊 Performance Testing

### Load Testing

- Test with 50+ events
- Test with 100+ attendees per event
- Monitor page load times
- Check database query performance

### Optimization Tips

- Use lazy loading for images
- Implement caching for event queries
- Optimize database indexes
- Use CDN for static assets

## 🎉 Demo Scenarios

### Scenario 1: Company Event Management

1. Create a "Tech Conference 2024" event
2. Set date 3 months in the future
3. Add detailed description and location
4. Upload a professional flyer
5. Add social media links
6. Create a blog post about the event
7. Test registration process

### Scenario 2: Workshop Series

1. Create multiple workshop events
2. Set different dates and times
3. Use different locations
4. Test filtering by location
5. Test search functionality
6. Export attendee lists

### Scenario 3: Networking Events

1. Create monthly networking events
2. Set recurring dates
3. Test upcoming events display
4. Test registration limits
5. Test attendee management

## 📈 Next Steps

After testing the demo:

1. **Customize the styling** to match your brand
2. **Configure email notifications** for registrations
3. **Set up payment integration** if needed
4. **Add custom fields** for specific event types
5. **Implement advanced features** like event categories
6. **Set up automated event reminders**
7. **Integrate with calendar systems**

## 🆘 Support

If you encounter any issues:

1. Check the troubleshooting section above
2. Review the plugin documentation
3. Check WordPress error logs
4. Test with default WordPress theme
5. Disable other plugins to check for conflicts

## 🎯 Success Metrics

A successful demo should show:

- ✅ Smooth admin panel experience
- ✅ Professional frontend display
- ✅ Working registration system
- ✅ Responsive design
- ✅ Fast loading times
- ✅ Error-free operation

---

**Happy Testing! 🚀**

The Event Calendar Plugin is designed to provide a professional, user-friendly experience for managing company events. The demo setup will give you a complete understanding of all features and capabilities.
