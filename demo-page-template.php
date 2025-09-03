<?php

/**
 * Event Calendar Plugin - Demo Page Template
 * 
 * This template shows how the events will look on the frontend
 * You can use this as a reference for creating your own event pages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div class="ecp-demo-page">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">Company Events</h1>
            <p class="page-description">Discover upcoming events, workshops, and networking opportunities</p>
        </header>

        <!-- Events Filter Section -->
        <div class="ecp-filters-section">
            <div class="ecp-filters">
                <div class="filter-group">
                    <label for="event-search">Search Events:</label>
                    <input type="text" id="event-search" placeholder="Search by title, location, or description...">
                </div>

                <div class="filter-group">
                    <label for="date-filter">Filter by Date:</label>
                    <select id="date-filter">
                        <option value="all">All Events</option>
                        <option value="upcoming">Upcoming Events</option>
                        <option value="this-month">This Month</option>
                        <option value="next-month">Next Month</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="location-filter">Filter by Location:</label>
                    <select id="location-filter">
                        <option value="all">All Locations</option>
                        <option value="downtown">Downtown</option>
                        <option value="tech-hub">Tech Hub</option>
                        <option value="hotel">Hotel</option>
                        <option value="headquarters">Headquarters</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="ecp-events-grid" id="events-container">
            <!-- Events will be loaded here via JavaScript or shortcode -->
        </div>

        <!-- Load More Button -->
        <div class="ecp-load-more">
            <button id="load-more-events" class="btn btn-primary">Load More Events</button>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div id="event-modal" class="ecp-modal">
    <div class="ecp-modal-content">
        <span class="ecp-modal-close">&times;</span>
        <div id="event-modal-body">
            <!-- Event details will be loaded here -->
        </div>
    </div>
</div>

<!-- Registration Modal -->
<div id="registration-modal" class="ecp-modal">
    <div class="ecp-modal-content">
        <span class="ecp-modal-close">&times;</span>
        <div id="registration-modal-body">
            <!-- Registration form will be loaded here -->
        </div>
    </div>
</div>

<style>
    /* Demo Page Styles */
    .ecp-demo-page {
        padding: 40px 0;
        background: #f8f9fa;
        min-height: 100vh;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .page-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .page-title {
        font-size: 3rem;
        color: #2c3e50;
        margin-bottom: 20px;
        font-weight: 700;
    }

    .page-description {
        font-size: 1.2rem;
        color: #7f8c8d;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Filters Section */
    .ecp-filters-section {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 40px;
    }

    .ecp-filters {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-group label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #2c3e50;
    }

    .filter-group input,
    .filter-group select {
        padding: 12px;
        border: 2px solid #e9ecef;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: #3498db;
    }

    /* Events Grid */
    .ecp-events-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
        margin-bottom: 40px;
    }

    .ecp-event-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }

    .ecp-event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .ecp-event-flyer {
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .ecp-event-content {
        padding: 25px;
    }

    .ecp-event-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .ecp-event-meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 15px;
    }

    .ecp-event-meta-item {
        display: flex;
        align-items: center;
        color: #7f8c8d;
        font-size: 0.9rem;
    }

    .ecp-event-meta-item i {
        width: 16px;
        margin-right: 8px;
        color: #3498db;
    }

    .ecp-event-description {
        color: #5a6c7d;
        line-height: 1.6;
        margin-bottom: 20px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .ecp-event-actions {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-primary {
        background: #3498db;
        color: white;
    }

    .btn-primary:hover {
        background: #2980b9;
        color: white;
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
        color: white;
    }

    /* Load More Button */
    .ecp-load-more {
        text-align: center;
        margin-top: 40px;
    }

    #load-more-events {
        padding: 15px 30px;
        font-size: 1.1rem;
    }

    /* Modal Styles */
    .ecp-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
    }

    .ecp-modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 0;
        border-radius: 12px;
        width: 90%;
        max-width: 800px;
        max-height: 80vh;
        overflow-y: auto;
        position: relative;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .ecp-modal-close {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
        color: #aaa;
        cursor: pointer;
        z-index: 1001;
    }

    .ecp-modal-close:hover {
        color: #000;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2rem;
        }

        .ecp-filters {
            grid-template-columns: 1fr;
        }

        .ecp-events-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .ecp-modal-content {
            width: 95%;
            margin: 10% auto;
        }
    }

    /* Loading Animation */
    .ecp-loading {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px;
    }

    .ecp-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Empty State */
    .ecp-empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #7f8c8d;
    }

    .ecp-empty-state h3 {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: #2c3e50;
    }

    .ecp-empty-state p {
        font-size: 1.1rem;
        margin-bottom: 30px;
    }

    /* Success Messages */
    .ecp-success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        border: 1px solid #c3e6cb;
    }

    /* Error Messages */
    .ecp-error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        border: 1px solid #f5c6cb;
    }
</style>

<script>
    // Demo JavaScript functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize demo functionality
        initDemo();
    });

    function initDemo() {
        // Add event listeners for filters
        const searchInput = document.getElementById('event-search');
        const dateFilter = document.getElementById('date-filter');
        const locationFilter = document.getElementById('location-filter');

        if (searchInput) {
            searchInput.addEventListener('input', filterEvents);
        }

        if (dateFilter) {
            dateFilter.addEventListener('change', filterEvents);
        }

        if (locationFilter) {
            locationFilter.addEventListener('change', filterEvents);
        }

        // Add modal close functionality
        const modals = document.querySelectorAll('.ecp-modal');
        const closeButtons = document.querySelectorAll('.ecp-modal-close');

        closeButtons.forEach(button => {
            button.addEventListener('click', closeModal);
        });

        modals.forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        });

        // Load more events functionality
        const loadMoreBtn = document.getElementById('load-more-events');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', loadMoreEvents);
        }
    }

    function filterEvents() {
        // This would integrate with the actual plugin's filtering system
        console.log('Filtering events...');
    }

    function closeModal() {
        const modals = document.querySelectorAll('.ecp-modal');
        modals.forEach(modal => {
            modal.style.display = 'none';
        });
    }

    function loadMoreEvents() {
        // This would integrate with the actual plugin's pagination system
        console.log('Loading more events...');
    }

    // Demo event data (this would come from the plugin)
    const demoEvents = [{
            id: 1,
            title: 'Tech Conference 2024',
            description: 'Join us for the biggest technology conference of the year. Featuring keynote speakers, workshops, and networking opportunities.',
            date: '2024-02-15',
            time: '09:00',
            location: 'Convention Center, Downtown',
            flyer: '',
            attendees: 150
        },
        {
            id: 2,
            title: 'Workshop: AI & Machine Learning',
            description: 'Hands-on workshop covering the fundamentals of AI and machine learning. Perfect for beginners and intermediate developers.',
            date: '2024-03-01',
            time: '14:00',
            location: 'Tech Hub, Innovation District',
            flyer: '',
            attendees: 25
        }
    ];

    // Render demo events
    function renderDemoEvents() {
        const container = document.getElementById('events-container');
        if (!container) return;

        container.innerHTML = demoEvents.map(event => `
        <div class="ecp-event-card" onclick="showEventDetails(${event.id})">
            <div class="ecp-event-flyer">
                ${event.flyer ? `<img src="${event.flyer}" alt="${event.title}">` : 'Event Flyer'}
            </div>
            <div class="ecp-event-content">
                <h3 class="ecp-event-title">${event.title}</h3>
                <div class="ecp-event-meta">
                    <div class="ecp-event-meta-item">
                        <i>📅</i>
                        <span>${new Date(event.date).toLocaleDateString()} at ${event.time}</span>
                    </div>
                    <div class="ecp-event-meta-item">
                        <i>📍</i>
                        <span>${event.location}</span>
                    </div>
                    <div class="ecp-event-meta-item">
                        <i>👥</i>
                        <span>${event.attendees} attendees</span>
                    </div>
                </div>
                <p class="ecp-event-description">${event.description}</p>
                <div class="ecp-event-actions">
                    <button class="btn btn-primary" onclick="event.stopPropagation(); showRegistrationForm(${event.id})">
                        Register Now
                    </button>
                    <button class="btn btn-secondary" onclick="event.stopPropagation(); showEventDetails(${event.id})">
                        View Details
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    }

    function showEventDetails(eventId) {
        const event = demoEvents.find(e => e.id === eventId);
        if (!event) return;

        const modal = document.getElementById('event-modal');
        const modalBody = document.getElementById('event-modal-body');

        modalBody.innerHTML = `
        <div style="padding: 30px;">
            <h2 style="margin-bottom: 20px; color: #2c3e50;">${event.title}</h2>
            <div style="margin-bottom: 20px;">
                <p><strong>Date:</strong> ${new Date(event.date).toLocaleDateString()}</p>
                <p><strong>Time:</strong> ${event.time}</p>
                <p><strong>Location:</strong> ${event.location}</p>
                <p><strong>Attendees:</strong> ${event.attendees}</p>
            </div>
            <p style="line-height: 1.6; margin-bottom: 30px;">${event.description}</p>
            <button class="btn btn-primary" onclick="showRegistrationForm(${event.id}); closeModal();">
                Register for this Event
            </button>
        </div>
    `;

        modal.style.display = 'block';
    }

    function showRegistrationForm(eventId) {
        const event = demoEvents.find(e => e.id === eventId);
        if (!event) return;

        const modal = document.getElementById('registration-modal');
        const modalBody = document.getElementById('registration-modal-body');

        modalBody.innerHTML = `
        <div style="padding: 30px;">
            <h2 style="margin-bottom: 20px; color: #2c3e50;">Register for ${event.title}</h2>
            <form id="registration-form">
                <div style="margin-bottom: 20px;">
                    <label for="first-name" style="display: block; margin-bottom: 5px; font-weight: 600;">First Name *</label>
                    <input type="text" id="first-name" name="first_name" required style="width: 100%; padding: 10px; border: 2px solid #e9ecef; border-radius: 6px;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label for="last-name" style="display: block; margin-bottom: 5px; font-weight: 600;">Last Name *</label>
                    <input type="text" id="last-name" name="last_name" required style="width: 100%; padding: 10px; border: 2px solid #e9ecef; border-radius: 6px;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label for="position" style="display: block; margin-bottom: 5px; font-weight: 600;">Position *</label>
                    <input type="text" id="position" name="position" required style="width: 100%; padding: 10px; border: 2px solid #e9ecef; border-radius: 6px;">
                </div>
                <div style="margin-bottom: 30px;">
                    <label for="company" style="display: block; margin-bottom: 5px; font-weight: 600;">Company *</label>
                    <input type="text" id="company" name="company" required style="width: 100%; padding: 10px; border: 2px solid #e9ecef; border-radius: 6px;">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">
                    Register for Event
                </button>
            </form>
        </div>
    `;

        modal.style.display = 'block';

        // Add form submission handler
        document.getElementById('registration-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Registration submitted successfully! (This is a demo)');
            closeModal();
        });
    }

    // Initialize demo when page loads
    document.addEventListener('DOMContentLoaded', function() {
        renderDemoEvents();
    });
</script>

<?php get_footer(); ?>