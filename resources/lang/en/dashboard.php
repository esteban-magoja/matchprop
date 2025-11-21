<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard Translations - English
    |--------------------------------------------------------------------------
    |
    | User dashboard translations
    |
    */

    // Main menu
    'dashboard' => 'Dashboard',
    'my_listings' => 'My Listings',
    'my_requests' => 'My Requests',
    'matches' => 'Matches',
    'messages' => 'Messages',
    'settings' => 'Settings',
    'logout' => 'Logout',
    
    // Dashboard home
    'welcome' => 'Welcome, :name',
    'quick_stats' => 'Quick Stats',
    'total_listings' => 'Total Listings',
    'active_requests' => 'Active Requests',
    'total_matches' => 'Matches Found',
    'recent_activity' => 'Recent Activity',
    'no_activity' => 'No recent activity',
    
    // Listings
    'listings' => [
        'title' => 'My Listings',
        'create' => 'Create Listing',
        'edit' => 'Edit Listing',
        'delete' => 'Delete Listing',
        'view' => 'View Listing',
        'no_listings' => 'You have no listings',
        'create_first' => 'Create your first listing',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'publish' => 'Publish',
        'unpublish' => 'Unpublish',
        'featured' => 'Featured',
        'views' => 'Views',
        'created_at' => 'Created at',
        'updated_at' => 'Updated at',
    ],
    
    // Requests
    'requests' => [
        'title' => 'My Clients',
        'description' => 'Manage your clients and property search requests',
        'create' => 'New Request',
        'edit' => 'Edit',
        'delete' => 'Delete Request',
        'view' => 'View Request',
        'view_matches' => 'View Matches',
        'no_requests' => 'You have no requests',
        'create_first' => 'Create your first request to find the ideal property',
        'create_button' => 'Create Request',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'expired' => 'Expired',
        'activate' => 'Activate',
        'deactivate' => 'Deactivate',
        'expires_at' => 'Expires',
        'never_expires' => 'No expiration',
        'created' => 'Created',
        'type' => 'Type',
        'operation' => 'Operation',
        'budget' => 'Budget',
        'location' => 'Location',
        'client' => 'Client',
        'min_bedrooms_short' => 'Min. :count bd.',
        'min_bathrooms_short' => 'Min. :count ba.',
        'min_area_short' => 'Min. :areasqm',
    ],
    
    // Request form
    'request_form' => [
        'new_title' => 'New Request/Client',
        'edit_title' => 'Edit Request',
        'client_data' => 'Client Data',
        'client_name' => 'Client Name',
        'client_email' => 'Client Email',
        'client_phone' => 'Client Phone',
        'client_name_placeholder' => 'John Doe',
        'client_email_placeholder' => 'client@email.com',
        'client_phone_placeholder' => '+1 555 123 4567',
        'title_label' => 'Request Title',
        'title_placeholder' => 'E.g: Looking for house with garden in Córdoba',
        'description_label' => 'Description',
        'description_placeholder' => 'Describe in detail what type of property you are looking for...',
        'property_type' => 'Property Type',
        'transaction_type' => 'Transaction Type',
        'budget_section' => 'Budget',
        'budget' => 'Budget',
        'min_budget' => 'Minimum Budget',
        'max_budget' => 'Maximum Budget',
        'currency' => 'Currency',
        'location_section' => 'Location',
        'location' => 'Location',
        'country' => 'Country',
        'province' => 'Province',
        'state' => 'Province/State',
        'city' => 'City',
        'minimum_features' => 'Minimum Features (Optional)',
        'min_bedrooms' => 'Minimum Bedrooms',
        'min_bathrooms' => 'Minimum Bathrooms',
        'min_garages' => 'Minimum Garages',
        'min_parking' => 'Minimum Parking',
        'min_area' => 'Minimum Area (sqm)',
        'expiration' => 'Expiration Date (Optional)',
        'save' => 'Save Request',
        'update' => 'Update Request',
        'cancel' => 'Cancel',
        'back' => 'Back',
        'select_option' => 'Select...',
        'required' => 'required',
        'optional' => 'optional',
    ],
    
    // Matches
    'matches_section' => [
        'title' => 'Matches',
        'for_listing' => 'Matches for',
        'for_request' => 'Matching Properties',
        'no_matches' => 'No matches found',
        'match_level' => 'Match Level',
        'exact_match' => 'Exact Match',
        'intelligent_match' => 'Intelligent Match',
        'flexible_match' => 'Flexible Match',
        'match_score' => ':score% match',
        'reasons' => 'Reasons',
        'contact_requester' => 'Contact Requester',
        'requester_info' => 'Requester Information',
        'see_all_matches' => 'See All Matches',
        'matches_summary' => 'Matches Summary',
    ],
    
    // Messages
    'messages_section' => [
        'title' => 'Messages',
        'inbox' => 'Inbox',
        'sent' => 'Sent',
        'no_messages' => 'You have no messages',
        'new_message' => 'New Message',
        'reply' => 'Reply',
        'delete' => 'Delete',
        'mark_read' => 'Mark as Read',
        'mark_unread' => 'Mark as Unread',
    ],
    
    // Common actions
    'actions' => [
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'view' => 'View',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'confirm' => 'Confirm',
        'back' => 'Back',
        'search' => 'Search',
        'filter' => 'Filter',
        'export' => 'Export',
        'import' => 'Import',
    ],
    
    // Confirmations
    'confirmations' => [
        'delete_listing' => 'Are you sure you want to delete this listing?',
        'delete_request' => 'Are you sure you want to delete this request?',
        'delete_message' => 'Are you sure you want to delete this message?',
        'cannot_undo' => 'This action cannot be undone.',
    ],
    
    // Language tabs
    'languages' => [
        'spanish' => 'Español',
        'english' => 'English',
        'fill_both' => 'Fill in information in both languages',
        'spanish_required' => 'Spanish (required)',
        'english_optional' => 'English (optional)',
    ],
    
    // Dashboard alerts and messages
    'alerts' => [
        'welcome_message' => 'Control panel where you can manage your listings, requests and view matches.',
        'email_verified' => 'Email verified successfully!',
        'email_verified_desc' => 'Your email address has been verified correctly. You can now use all platform features.',
        'terms_pending_title' => 'Terms and Conditions Pending',
        'terms_pending_desc' => 'To publish listings on the platform, you must accept our terms and conditions.',
        'view_accept_terms' => 'View and Accept Terms',
    ],
    
    // Titles and descriptions
    'home' => [
        'title' => 'Dashboard',
        'description' => 'Manage your properties, requests and leads from here.',
        'my_listings' => 'My Listings',
        'clients' => 'Clients',
        'messages' => 'Messages',
        'matches' => 'Matches',
        'view_listings' => 'View listings',
        'publish_listing' => 'Publish listing',
        'view_requests' => 'View requests',
        'add_request' => 'Add request',
        'unread_messages' => ':count unread',
        'view_messages' => 'View messages',
        'view_matches' => 'View matches',
        'role_message' => 'You have the role of',
    ],
    
    // Request detail (show)
    'request_detail' => [
        'title' => 'Request Details',
        'description' => 'Properties that match your search',
        'client_data' => 'Client Data',
        'name' => 'Name',
        'email' => 'Email',
        'whatsapp' => 'WhatsApp',
        'property_type' => 'Property Type',
        'operation' => 'Operation',
        'budget' => 'Budget',
        'location' => 'Location',
        'min_bedrooms' => 'Min. bedrooms',
        'min_bathrooms' => 'Min. bathrooms',
        'min_parking' => 'Min. parking',
        'min_area' => 'Min. area',
        'matching_properties' => 'Matching Properties',
        'no_matches' => 'No matches found',
        'no_matches_desc' => 'There are no properties that match this request yet.',
        'match_score' => ':score% match',
        'contact_owner' => 'Contact Owner',
    ],
];
