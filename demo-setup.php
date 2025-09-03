<?php

/**
 * Event Calendar Plugin - Demo Setup
 * 
 * This file helps you set up a demo environment to test the plugin
 * Run this file once after activating the plugin to create sample data
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class ECP_Demo_Setup
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_demo_menu'));
        add_action('wp_ajax_ecp_create_demo_data', array($this, 'create_demo_data'));
    }

    public function add_demo_menu()
    {
        add_submenu_page(
            'event-calendar',
            'Demo Setup',
            'Demo Setup',
            'manage_options',
            'ecp-demo-setup',
            array($this, 'demo_setup_page')
        );
    }

    public function demo_setup_page()
    {
?>
        <div class="wrap">
            <h1>Event Calendar Plugin - Demo Setup</h1>
            <p>This will create sample events and attendees to demonstrate the plugin functionality.</p>

            <div class="card">
                <h2>Demo Events</h2>
                <p>The demo will create the following sample events:</p>
                <ul>
                    <li><strong>Tech Conference 2024</strong> - A major technology conference</li>
                    <li><strong>Workshop: AI & Machine Learning</strong> - Hands-on workshop</li>
                    <li><strong>Networking Event</strong> - Professional networking opportunity</li>
                    <li><strong>Product Launch</strong> - New product announcement</li>
                </ul>
            </div>

            <div class="card">
                <h2>Demo Attendees</h2>
                <p>Each event will have sample attendees with realistic data.</p>
            </div>

            <div class="card">
                <h2>Frontend Demo</h2>
                <p>After creating the demo data, you can:</p>
                <ol>
                    <li>Create a new page in WordPress</li>
                    <li>Add the Divi Events module to display events</li>
                    <li>Use shortcodes like <code>[ecp_events]</code> or <code>[ecp_upcoming_events]</code></li>
                </ol>
            </div>

            <p>
                <button type="button" class="button button-primary" id="create-demo-data">
                    Create Demo Data
                </button>
                <span id="demo-status"></span>
            </p>

            <div id="demo-results" style="display: none;">
                <h3>Demo Data Created Successfully!</h3>
                <p>You can now:</p>
                <ul>
                    <li>View events in the <a href="<?php echo admin_url('admin.php?page=event-calendar'); ?>">Events admin page</a></li>
                    <li>Create a new page and add the Divi Events module</li>
                    <li>Test the registration functionality</li>
                </ul>
            </div>
        </div>

        <script>
            jQuery(document).ready(function($) {
                $('#create-demo-data').click(function() {
                    var button = $(this);
                    var status = $('#demo-status');
                    var results = $('#demo-results');

                    button.prop('disabled', true);
                    status.html('Creating demo data...');

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'ecp_create_demo_data',
                            nonce: '<?php echo wp_create_nonce('ecp_demo_nonce'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                status.html('Demo data created successfully!');
                                results.show();
                                button.hide();
                            } else {
                                status.html('Error: ' + response.data);
                                button.prop('disabled', false);
                            }
                        },
                        error: function() {
                            status.html('Error creating demo data');
                            button.prop('disabled', false);
                        }
                    });
                });
            });
        </script>
<?php
    }

    public function create_demo_data()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'ecp_demo_nonce')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        global $wpdb;
        $events_table = $wpdb->prefix . 'ecp_events';
        $attendees_table = $wpdb->prefix . 'ecp_attendees';

        // Sample events data
        $demo_events = array(
            array(
                'title' => 'Tech Conference 2024',
                'description' => 'Join us for the biggest technology conference of the year. Featuring keynote speakers, workshops, and networking opportunities.',
                'event_date' => date('Y-m-d', strtotime('+30 days')),
                'event_time' => '09:00:00',
                'location' => 'Convention Center, Downtown',
                'registration_link' => 'https://example.com/register/tech-conference',
                'social_media_links' => json_encode(array(
                    'facebook' => 'https://facebook.com/techconference2024',
                    'twitter' => 'https://twitter.com/techconf2024',
                    'linkedin' => 'https://linkedin.com/company/techconference'
                )),
                'flyer_url' => '',
                'status' => 'active',
                'created_at' => current_time('mysql')
            ),
            array(
                'title' => 'Workshop: AI & Machine Learning',
                'description' => 'Hands-on workshop covering the fundamentals of AI and machine learning. Perfect for beginners and intermediate developers.',
                'event_date' => date('Y-m-d', strtotime('+45 days')),
                'event_time' => '14:00:00',
                'location' => 'Tech Hub, Innovation District',
                'registration_link' => 'https://example.com/register/ai-workshop',
                'social_media_links' => json_encode(array(
                    'facebook' => 'https://facebook.com/aiworkshop',
                    'twitter' => 'https://twitter.com/aiworkshop'
                )),
                'flyer_url' => '',
                'status' => 'active',
                'created_at' => current_time('mysql')
            ),
            array(
                'title' => 'Networking Event',
                'description' => 'Connect with industry professionals and expand your network. Light refreshments will be provided.',
                'event_date' => date('Y-m-d', strtotime('+15 days')),
                'event_time' => '18:00:00',
                'location' => 'Grand Hotel, Rooftop Terrace',
                'registration_link' => 'https://example.com/register/networking',
                'social_media_links' => json_encode(array(
                    'linkedin' => 'https://linkedin.com/company/networking-event'
                )),
                'flyer_url' => '',
                'status' => 'active',
                'created_at' => current_time('mysql')
            ),
            array(
                'title' => 'Product Launch',
                'description' => 'Be the first to see our revolutionary new product. Live demonstration and Q&A session.',
                'event_date' => date('Y-m-d', strtotime('+60 days')),
                'event_time' => '19:00:00',
                'location' => 'Company Headquarters, Main Auditorium',
                'registration_link' => 'https://example.com/register/product-launch',
                'social_media_links' => json_encode(array(
                    'facebook' => 'https://facebook.com/company',
                    'twitter' => 'https://twitter.com/company',
                    'instagram' => 'https://instagram.com/company'
                )),
                'flyer_url' => '',
                'status' => 'active',
                'created_at' => current_time('mysql')
            )
        );

        // Sample attendees data
        $demo_attendees = array(
            array('John', 'Smith', 'Senior Developer', 'TechCorp Inc.'),
            array('Sarah', 'Johnson', 'Product Manager', 'Innovation Labs'),
            array('Michael', 'Brown', 'CTO', 'StartupXYZ'),
            array('Emily', 'Davis', 'UX Designer', 'Design Studio'),
            array('David', 'Wilson', 'Marketing Director', 'Growth Agency'),
            array('Lisa', 'Garcia', 'Data Scientist', 'AI Solutions'),
            array('Robert', 'Martinez', 'Software Engineer', 'DevTeam Pro'),
            array('Jennifer', 'Anderson', 'Business Analyst', 'Consulting Group'),
            array('Christopher', 'Taylor', 'Project Manager', 'Project Solutions'),
            array('Amanda', 'Thomas', 'Sales Manager', 'Sales Pro')
        );

        $created_events = array();

        // Insert demo events
        foreach ($demo_events as $event) {
            $result = $wpdb->insert($events_table, $event);
            if ($result) {
                $event_id = $wpdb->insert_id;
                $created_events[] = $event_id;

                // Add random attendees to each event
                $num_attendees = rand(3, 8);
                $selected_attendees = array_rand($demo_attendees, $num_attendees);

                foreach ($selected_attendees as $attendee_index) {
                    $attendee = $demo_attendees[$attendee_index];
                    $wpdb->insert($attendees_table, array(
                        'event_id' => $event_id,
                        'first_name' => $attendee[0],
                        'last_name' => $attendee[1],
                        'position' => $attendee[2],
                        'company' => $attendee[3],
                        'registration_date' => current_time('mysql')
                    ));
                }
            }
        }

        wp_send_json_success(array(
            'message' => 'Demo data created successfully',
            'events_created' => count($created_events),
            'event_ids' => $created_events
        ));
    }
}

// Initialize demo setup
new ECP_Demo_Setup();
