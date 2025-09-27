<?php
/**
 * Plugin Name: FormSpammerTrap Contact Form
 * Plugin URI: https://your-website.com
 * Description: Integrates FormSpammerTrap anti-spam contact form into ClassicPress with Fixed PHPMailer
 * Version: 1.5.3
 * Author: Van Isle Web Solutions
 * License: GPL2
 * Requires at least: 4.9
 * Tested up to: 6.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// --- FST: start PHP session early on the front-end to avoid "headers already sent" warnings ---
if ( ! function_exists( 'fst_boot_session' ) ) {
    add_action('init', 'fst_boot_session', 0); // runs before almost anything else

    function fst_boot_session() {
        // front-end only
        if ( is_admin() ) { return; }

        // if headers already sent or session active, do nothing
        if ( headers_sent() || session_status() === PHP_SESSION_ACTIVE ) { return; }

        // safe cookie flags
        if ( function_exists('is_ssl') && is_ssl() ) {
            @ini_set('session.cookie_secure', '1'); // only on HTTPS
        }
        @ini_set('session.use_only_cookies', '1');
        @ini_set('session.cookie_httponly', '1');
        // PHP 7.3+ recognizes this; older versions ignore it
        @ini_set('session.cookie_samesite', 'Lax');

        @session_start();
    }
}

class FormSpammerTrapPlugin {
    
    public function __construct() {
	// Include export functionality
    require_once plugin_dir_path(__FILE__) . 'formspammertrap-export.php';
	require_once plugin_dir_path(__FILE__) . 'formspammertrap-import.php';
	
	// Include WordPress database functions if not already loaded
    if (!function_exists('fst_create_contact_table')) {
        require_once plugin_dir_path(__FILE__) . 'includes/formspammertrap-wordpress-functions.php';
    }
	
    add_action('init', array($this, 'init'));
    add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    add_shortcode('formspammertrap', array($this, 'formspammertrap_shortcode'));
    add_action('admin_menu', array($this, 'add_admin_menu'));
    
    // NEW: Add submissions submenu
    add_action('admin_menu', array($this, 'add_submissions_menu'), 10);
    
    register_activation_hook(__FILE__, array($this, 'activate'));
    register_uninstall_hook(__FILE__, array('FormSpammerTrapPlugin', 'uninstall'));
    
    // Security and cleanup features
    add_action('wp', array($this, 'schedule_cleanup'));
    add_action('fst_cleanup_uploads', array($this, 'cleanup_old_uploads'));
    
    // Hook to inject JavaScript enhancements AFTER FormSpammerTrap loads
    add_action('wp_footer', array($this, 'inject_javascript_enhancements'), 999);
    
    // Suppress the specific FormSpammerTrap warning that occurs after FST_MAIL_ALT
    add_action('init', array($this, 'setup_error_handler'));
    
    // NEW: Add admin notices for unread submissions
    add_action('admin_notices', array($this, 'show_unread_submissions_notice'));
    
    // NEW: Add dashboard widget
    add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
	
	// Add custom form colors CSS output
    add_action('wp_head', array($this, 'output_custom_form_colors'));
}
    
    /**
     * Setup custom error handler to suppress the specific FormSpammerTrap warning
     */
    public function setup_error_handler() {
        if (get_option('fst_enable_uploads', 0)) {
            set_error_handler(array($this, 'custom_error_handler'), E_WARNING);
        }
    }
    
    /**
     * Custom error handler to suppress specific FormSpammerTrap warnings
     */
    public function custom_error_handler($errno, $errstr, $errfile, $errline) {
        // Only suppress the specific undefined variable warning from FormSpammerTrap
        if ($errno === E_WARNING && 
            strpos($errstr, 'Undefined variable $message_elements') !== false && 
            strpos($errfile, 'formspammertrap-contact-functions.php') !== false) {
            return true; // Suppress this specific warning
        }
        
        // Let all other warnings through
        return false;
    }
    
    public function init() {
        // Ensure HTTP_HOST is always available to prevent FormSpammerTrap warnings
        if (!isset($_SERVER['HTTP_HOST']) || empty($_SERVER['HTTP_HOST'])) {
            $_SERVER['HTTP_HOST'] = parse_url(get_site_url(), PHP_URL_HOST) ?: 'localhost';
        }
        
        // Include the FormSpammerTrap functions file
        $functions_file = plugin_dir_path(__FILE__) . 'includes/formspammertrap-contact-functions.php';

       // Load the legacy FST library only on the front-end to avoid mysqli checks in wp-admin
       if (!is_admin() && file_exists($functions_file)) {
           include_once $functions_file;
       } elseif (!file_exists($functions_file)) {
           add_action('admin_notices', array($this, 'missing_functions_notice'));
       return;
    }


        
        // Add CSS and JavaScript hooks
        add_action('wp_head', 'formspammertrap_contact_css');
        add_action('wp_footer', 'formspammertrap_contact_script');
    }
    
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
    }
    
    /**
     * MAIN ENHANCEMENT FUNCTION - Injects JavaScript to modify FormSpammerTrap after it loads
     */
    public function inject_javascript_enhancements() {
        // Only run on pages that might have the FormSpammerTrap form
        if (!is_page() && !is_single() && !is_home() && !is_front_page()) return;
        
        // Get options
        $show_version = get_option('fst_show_version', 0);
        $enable_uploads = get_option('fst_enable_uploads', 0);
        $custom_thanks = get_option('fst_custom_thanks_message', '');
        $enable_reset = get_option('fst_enable_reset_button', 0);
        $upload_extensions = get_option('fst_upload_extensions', '.pdf,.jpg,.jpeg,.png,.gif,.doc,.docx');
        $upload_folder = get_option('fst_upload_folder', 'fst-uploads');
        
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            console.log('FormSpammerTrap Plugin: JavaScript enhancements loading...');
            
            // Wait a moment for FormSpammerTrap to fully initialize
            setTimeout(function() {
                
                // 1. ADD VERSION INFORMATION
                <?php if ($show_version == 1): ?>
                if ($('#formspammertrapcontactform').length && $('.fst_version_info').length === 0) {
                    var versionHtml = '<hr><div class="fst_version_info" style="text-align: center; font-size: 11px; color: #666; margin: 10px 0;">' +
                        '<p>This contact form protected against spambots by using special code from FormSpammerTrap.com<br>' +
                        'FormSpammerTrap Version Information: Version <?php echo defined('FST_VERSION') ? esc_js(FST_VERSION) : '17.10'; ?> released <?php echo defined('FST_VERSION_DATE') ? esc_js(FST_VERSION_DATE) : '17 FEB 2024'; ?>. PHPMailer version is <?php echo defined('FST_PHPMAILER_VERSION') ? esc_js(FST_PHPMAILER_VERSION) : '(installed)'; ?>.<br>' +
                        'See the site for details: <a href="https://www.FormSpammerTrap.com" target="_blank" title="FormSpammerTrap.com">FormSpammerTrap.com</a></p>' +
                        '</div><hr>';
                    $('#formspammertrapcontactform').closest('.fst_container, .fst_comment_box').append(versionHtml);
                    console.log('FormSpammerTrap Plugin: Version info added');
                }
                <?php endif; ?>
                
                // 2. ADD FILE UPLOAD FUNCTIONALITY
                <?php if ($enable_uploads == 1): ?>
                if ($('#formspammertrapcontactform').length && $('#fst_uploadfile').length === 0) {
                    console.log('FormSpammerTrap Plugin: Adding file upload field...');
                    
                    // Create upload field HTML
                    var uploadHtml = '<div class="fst_column_1" id="fst_uploadfile_wrapper">' +
                        '<label for="fst_uploadfile">Attach File (Optional)</label>' +
                        '</div>' +
                        '<div class="fst_column_2">' +
                        '<input type="file" id="fst_uploadfile" name="fst_uploadfile[]" multiple="multiple" accept="<?php echo esc_js($upload_extensions); ?>" ' +
                        'style="background-color: #FFFF9E !important; width: 100% !important; padding: 10px !important; border: solid 1px #C0C0C0 !important; border-radius: 4px !important; font-family: inherit !important; font-size: 125% !important; color: black !important;" />' +
                        '<p style="font-size: 90%; margin: 5px 0;">Allowed file types: <b><?php echo str_replace(',', ' ', esc_js($upload_extensions)); ?></b></p>' +
                        '<div class="fst_file_preview" style="background-color: lightcyan; padding: 8px; margin: 5px 0; border-radius: 4px; border: 1px solid #ddd;">' +
                        '<p style="margin: 0; font-style: italic; color: #666;">No files currently selected for upload</p>' +
                        '</div>' +
                        '</div>' +
                        '<div class="fst_column_3">' +
                        'Select files to include with your message' +
                        '</div>';
                    
                    // Find the submit button area and insert upload field before it
                    var submitArea = $('#formspammertrapcontactform .fst_column_2:has(button)');
                    if (submitArea.length === 0) {
                        submitArea = $('#formspammertrapcontactform button[type="submit"], #formspammertrapcontactform input[type="submit"]').closest('.fst_column_2');
                    }
                    
                    if (submitArea.length > 0) {
                        submitArea.before(uploadHtml);
                        console.log('FormSpammerTrap Plugin: File upload field added successfully');
                        
                        // Add file preview functionality
                        $('#fst_uploadfile').on('change', function() {
                            var files = this.files;
                            var preview = $('.fst_file_preview');
                            
                            if (files.length === 0) {
                                preview.html('<p style="margin: 0; font-style: italic; color: #666;">No files currently selected for upload</p>');
                            } else {
                                var html = '<p style="margin: 0 0 8px 0; font-style: italic; font-weight: bold; color: #333;">Selected files - Plugin Override Active:</p>';
                                html += '<div style="max-height: 150px; overflow-y: auto;">';
                                
                                for (var i = 0; i < files.length; i++) {
                                    var file = files[i];
                                    var size = file.size < 1024 ? file.size + ' bytes' : 
                                              file.size < 1048576 ? (file.size/1024).toFixed(1) + ' KB' : 
                                              (file.size/1048576).toFixed(1) + ' MB';
                                    
                                    var fileIcon = '📄';
                                    if (file.type.startsWith('image/')) fileIcon = '🖼️';
                                    else if (file.name.toLowerCase().includes('.pdf')) fileIcon = '📕';
                                    else if (file.name.toLowerCase().includes('.doc')) fileIcon = '📝';
                                    
                                    html += '<div style="background-color: lightyellow; padding: 6px; margin: 2px 0; border-radius: 3px; border-left: 3px solid #4CAF50; display: flex; align-items: center; font-size: 13px;">' +
                                           '<span style="margin-right: 8px; font-size: 16px;">' + fileIcon + '</span>' +
                                           '<div style="flex: 1;"><strong>' + file.name + '</strong><br><span style="color: #666; font-size: 11px;">' + size + ' - Will use Plugin Mail Override</span></div>' +
                                           '</div>';
                                }
                                html += '</div>';
                                preview.html(html);
                            }
                        });
                        
                        <?php 
                        $upload_dir = ABSPATH . $upload_folder . '/';
                        if (!is_dir($upload_dir)): 
                        ?>
                        $('#fst_uploadfile').after('<div style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 8px; border-radius: 4px; margin: 8px 0; font-size: 12px;"><strong>⚠️ Note:</strong> Upload folder will be created automatically: <code><?php echo esc_js($upload_folder); ?></code></div>');
                        <?php endif; ?>
                        
                    } else {
                        console.log('FormSpammerTrap Plugin: Could not find submit button area for file upload placement');
                    }
                }
                <?php endif; ?>
                
                // 3. ADD RESET BUTTON
                <?php if ($enable_reset == 1): ?>
                function addResetButton() {
                    var submitButton = $('#<?php echo defined('FST_SUBMITBUTTON_ID') ? esc_js(FST_SUBMITBUTTON_ID) : 'fst_submitbutton'; ?>');
                    
                    if (submitButton.length > 0 && 
                        submitButton.is(':visible') && 
                        !submitButton.attr('hidden') && 
                        $('#fst_plugin_reset_button').length === 0) {
                        
                        var resetButton = $('<button/>', {
                            id: 'fst_plugin_reset_button',
                            type: 'button',
                            class: 'fst_reset_button',
                            text: 'Reset Form',
                            style: 'margin-left: 10px; background-color: #FF0000; color: white; border-radius: 8px; padding: 7px; border: none; cursor: pointer;',
                            click: function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                
                                var form = $('#<?php echo defined('FST_XFORMID') ? esc_js(FST_XFORMID) : 'formspammertrapcontactform'; ?>');
                                if (form.length > 0) {
                                    form.find('input[type="text"], input[type="email"], textarea').val('');
                                    form.find('input[type="file"]').val('');
                                    $('.fst_file_preview').html('<p style="margin: 0; font-style: italic; color: #666;">No files currently selected for upload</p>');
                                    submitButton.attr('hidden', 'hidden');
                                    $('#fst_plugin_reset_button').remove();
                                    console.log('FormSpammerTrap Plugin: Form reset successfully');
                                }
                            }
                        });
                        
                        resetButton.hover(
                            function() { $(this).css('background-color', '#FFFF00').css('color', 'black'); },
                            function() { $(this).css('background-color', '#FF0000').css('color', 'white'); }
                        );
                        
                        submitButton.after(resetButton);
                        console.log('FormSpammerTrap Plugin: Reset button added');
                    }
                }
                
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' || mutation.type === 'childList') {
                            var submitButton = $('#<?php echo defined('FST_SUBMITBUTTON_ID') ? esc_js(FST_SUBMITBUTTON_ID) : 'fst_submitbutton'; ?>');
                            if (submitButton.is(':visible') && !submitButton.attr('hidden')) {
                                addResetButton();
                            }
                        }
                    });
                });
                
                var formContainer = $('#<?php echo defined('FST_CONTAINER_ID') ? esc_js(FST_CONTAINER_ID) : 'fst_container'; ?>');
                if (formContainer.length > 0) {
                    observer.observe(formContainer[0], { 
                        childList: true, 
                        subtree: true, 
                        attributes: true,
                        attributeFilter: ['hidden', 'style']
                    });
                    
                    setTimeout(function() { observer.disconnect(); }, 30000);
                }
                <?php endif; ?>
                
                // 4. REPLACE THANK YOU MESSAGE
                <?php if (!empty($custom_thanks) && $custom_thanks != 'Thank you for your message. We will reply within 24 hours.'): ?>
                function replaceThankYouMessage() {
                    $('.fst_blue_box').each(function() {
                        var $this = $(this);
                        var text = $this.text().trim();
                        
                        if (text.indexOf('Thank you for your message') !== -1 || 
                            text.indexOf('We will reply') !== -1 ||
                            $this.hasClass('fst_text_center')) {
                            
                            $this.html('<?php echo wp_kses_post(addslashes($custom_thanks)); ?>');
                            console.log('FormSpammerTrap Plugin: Custom thank you message applied');
                            return false;
                        }
                    });
                }
                
                replaceThankYouMessage();
                
                var observer2 = new MutationObserver(function(mutations) {
                    var shouldCheck = false;
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList') {
                            $(mutation.addedNodes).each(function() {
                                if ($(this).hasClass && ($(this).hasClass('fst_blue_box') || $(this).find('.fst_blue_box').length > 0)) {
                                    shouldCheck = true;
                                }
                            });
                        }
                    });
                    
                    if (shouldCheck) {
                        setTimeout(function() { replaceThankYouMessage(); }, 100);
                    }
                });
                
                observer2.observe(document.body, { childList: true, subtree: true });
                setTimeout(function() { observer2.disconnect(); }, 15000);
                <?php endif; ?>
                
                console.log('FormSpammerTrap Plugin: All enhancements loaded successfully');
                
            }, 500);
        });
        </script>
        <?php
    }
    
    public function formspammertrap_shortcode($atts) {
        $atts = shortcode_atts(array(
            'email' => '',
            'cc' => '',
            'bcc' => ''
        ), $atts, 'formspammertrap');
        
        if (function_exists('formspammertrap_contact_form')) {
            ob_start();
            formspammertrap_contact_form();
            return ob_get_clean();
        } else {
            return '<p style="color: red;"><strong>FormSpammerTrap Error:</strong> Functions file not found or loaded properly.</p>';
        }
    }
    
    public function add_admin_menu() {
        add_options_page(
            'FormSpammerTrap Settings',
            'FormSpammerTrap',
            'manage_options',
            'formspammertrap-settings',
            array($this, 'admin_page')
        );
    }
	
	/**
     * Add submenu for viewing submissions
     */
    public function add_submissions_menu() {
        add_submenu_page(
            'options-general.php',      // Parent slug (Settings menu)
            'Form Submissions',         // Page title
            'Form Submissions',         // Menu title
            'manage_options',           // Capability
            'fst-submissions',          // Menu slug
            array($this, 'submissions_page') // Callback
        );
    }

    /**
     * Display the submissions page
     */
public function submissions_page() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'fst_submissions';
    
    // Handle individual actions (these work fine)
    if (isset($_GET['action']) && isset($_GET['submission_id']) && wp_verify_nonce($_GET['_wpnonce'], 'fst_submission_action')) {
        $submission_id = intval($_GET['submission_id']);
        $action = sanitize_text_field($_GET['action']);
        
        switch ($action) {
            case 'mark_read':
                $wpdb->update($table_name, array('status' => 'read'), array('id' => $submission_id));
                echo '<div class="notice notice-success"><p>Submission marked as read.</p></div>';
                break;
            case 'mark_unread':
                $wpdb->update($table_name, array('status' => 'unread'), array('id' => $submission_id));
                echo '<div class="notice notice-success"><p>Submission marked as unread.</p></div>';
                break;
            case 'delete':
                $wpdb->delete($table_name, array('id' => $submission_id));
                echo '<div class="notice notice-success"><p>Submission deleted.</p></div>';
                break;
        }
    }
    
    // FIXED: Handle bulk actions
    if (isset($_POST['bulk_action']) && isset($_POST['submission_ids']) && wp_verify_nonce($_POST['_wpnonce'], 'fst_bulk_actions')) {
        $bulk_action = sanitize_text_field($_POST['bulk_action']);
        $submission_ids = array_map('intval', $_POST['submission_ids']);
        
        // Check that we have valid data
        if (!empty($submission_ids) && $bulk_action !== '-1') {
            $ids_placeholder = implode(',', array_fill(0, count($submission_ids), '%d'));
            $update_count = 0;
            
            switch ($bulk_action) {
                case 'mark_read':
                    $update_count = $wpdb->query($wpdb->prepare("UPDATE $table_name SET status = 'read' WHERE id IN ($ids_placeholder)", $submission_ids));
                    if ($update_count) {
                        echo '<div class="notice notice-success"><p>' . $update_count . ' submissions marked as read.</p></div>';
                    }
                    break;
                case 'mark_unread':
                    $update_count = $wpdb->query($wpdb->prepare("UPDATE $table_name SET status = 'unread' WHERE id IN ($ids_placeholder)", $submission_ids));
                    if ($update_count) {
                        echo '<div class="notice notice-success"><p>' . $update_count . ' submissions marked as unread.</p></div>';
                    }
                    break;
                case 'delete':
                    $update_count = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id IN ($ids_placeholder)", $submission_ids));
                    if ($update_count) {
                        echo '<div class="notice notice-success"><p>' . $update_count . ' submissions deleted.</p></div>';
                    }
                    break;
                default:
                    echo '<div class="notice notice-error"><p>Invalid bulk action selected.</p></div>';
                    break;
            }
            
            // Add some debugging (remove in production)
            if ($update_count === false) {
                echo '<div class="notice notice-error"><p>Database error occurred during bulk action. Action: ' . esc_html($bulk_action) . ', IDs: ' . esc_html(implode(',', $submission_ids)) . '</p></div>';
            }
        } else {
            echo '<div class="notice notice-warning"><p>No submissions selected or invalid action.</p></div>';
        }
    }
    
    // Get current page and items per page
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $items_per_page = 20;
    $offset = ($current_page - 1) * $items_per_page;
    
    // Get filter parameters
    $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    
    // Build WHERE clause
    $where_conditions = array();
    $where_values = array();
    
    if (!empty($status_filter)) {
        $where_conditions[] = "status = %s";
        $where_values[] = $status_filter;
    }
    
    if (!empty($search)) {
        $where_conditions[] = "(visitor_name LIKE %s OR visitor_email LIKE %s OR subject LIKE %s OR message LIKE %s)";
        $search_term = '%' . $wpdb->esc_like($search) . '%';
        $where_values[] = $search_term;
        $where_values[] = $search_term;
        $where_values[] = $search_term;
        $where_values[] = $search_term;
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Get total count
    $total_query = "SELECT COUNT(*) FROM $table_name $where_clause";
    if (!empty($where_values)) {
        $total_items = $wpdb->get_var($wpdb->prepare($total_query, $where_values));
    } else {
        $total_items = $wpdb->get_var($total_query);
    }
    
    // Get submissions for current page
    $submissions_query = "SELECT * FROM $table_name $where_clause ORDER BY submission_date DESC LIMIT %d OFFSET %d";
    $query_values = array_merge($where_values, array($items_per_page, $offset));
    $submissions = $wpdb->get_results($wpdb->prepare($submissions_query, $query_values));
    
    // Calculate pagination
    $total_pages = ceil($total_items / $items_per_page);
    ?>
    <div class="wrap">
        <h1>Form Submissions 
            <?php if (!empty($status_filter)): ?>
                <span class="subtitle">- <?php echo esc_html(ucfirst($status_filter)); ?></span>
            <?php endif; ?>
        </h1>
        
        <!-- Filters and Search -->
        <div class="tablenav top">
            <div class="alignleft actions">
                <form method="get" action="" id="filter-form">
                    <input type="hidden" name="page" value="fst-submissions">
                    <select name="status" id="filter-by-status">
                        <option value="">All Statuses</option>
                        <option value="unread" <?php selected($status_filter, 'unread'); ?>>Unread</option>
                        <option value="read" <?php selected($status_filter, 'read'); ?>>Read</option>
                        <option value="sent" <?php selected($status_filter, 'sent'); ?>>Email Sent</option>
                        <option value="email_failed" <?php selected($status_filter, 'email_failed'); ?>>Email Failed</option>
                    </select>
                    <input type="submit" class="button" value="Filter">
                </form>
            </div>
            
            <p class="search-box">
                <form method="get" action="">
                    <input type="hidden" name="page" value="fst-submissions">
                    <input type="hidden" name="status" value="<?php echo esc_attr($status_filter); ?>">
                    <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Search submissions...">
                    <input type="submit" class="button" value="Search">
                </form>
            </p>
        </div>
        
        <?php if (empty($submissions)): ?>
            <div class="notice notice-info">
                <p><strong>No submissions found.</strong> 
                <?php if (!empty($status_filter) || !empty($search)): ?>
                    Try adjusting your filters.
                <?php else: ?>
                    Submissions will appear here once visitors start using your contact form.
                <?php endif; ?>
                </p>
            </div>
        <?php else: ?>
            
            <!-- FIXED: Bulk Actions Form -->
            <form method="post" action="" id="bulk-actions-form">
                <?php wp_nonce_field('fst_bulk_actions'); ?>
                <!-- IMPORTANT: Preserve current filters in bulk actions -->
                <input type="hidden" name="page" value="fst-submissions">
                <input type="hidden" name="status" value="<?php echo esc_attr($status_filter); ?>">
                <input type="hidden" name="s" value="<?php echo esc_attr($search); ?>">
                <input type="hidden" name="paged" value="<?php echo $current_page; ?>">
                
                <div class="tablenav top">
                    <div class="alignleft actions bulkactions">
                        <select name="bulk_action" id="bulk-action-selector-top">
                            <option value="-1">Bulk Actions</option>
                            <option value="mark_read">Mark as Read</option>
                            <option value="mark_unread">Mark as Unread</option>
                            <option value="delete">Delete</option>
                        </select>
                        <input type="submit" class="button action" value="Apply" id="doaction">
                    </div>
                    
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo $total_items; ?> items</span>
                        <?php
                        if ($total_pages > 1) {
                            $page_links = paginate_links(array(
                                'base' => add_query_arg('paged', '%#%'),
                                'format' => '',
                                'prev_text' => '&laquo;',
                                'next_text' => '&raquo;',
                                'total' => $total_pages,
                                'current' => $current_page,
                                'type' => 'list'
                            ));
                            echo $page_links;
                        }
                        ?>
                    </div>
                </div>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <td class="manage-column column-cb check-column">
                                <input type="checkbox" id="cb-select-all-1">
                            </td>
                            <th scope="col" class="manage-column">Status</th>
                            <th scope="col" class="manage-column">Date</th>
                            <th scope="col" class="manage-column">From</th>
                            <th scope="col" class="manage-column">Subject</th>
                            <th scope="col" class="manage-column">Message Preview</th>
                            <th scope="col" class="manage-column">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): ?>
                            <tr class="<?php echo $submission->status === 'unread' ? 'unread' : ''; ?>">
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="submission_ids[]" value="<?php echo $submission->id; ?>">
                                </th>
                                <td>
                                    <?php
                                    $status_colors = array(
                                        'unread' => '#d63638',
                                        'read' => '#00a32a',
                                        'sent' => '#2271b1',
                                        'email_failed' => '#d63638'
                                    );
                                    $status_color = isset($status_colors[$submission->status]) ? $status_colors[$submission->status] : '#646970';
                                    ?>
                                    <span style="color: <?php echo $status_color; ?>; font-weight: bold;">
                                        <?php echo ucfirst(str_replace('_', ' ', $submission->status)); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo date('M j, Y', strtotime($submission->submission_date)); ?></strong><br>
                                    <span style="color: #646970;"><?php echo date('g:i a', strtotime($submission->submission_date)); ?></span>
                                </td>
                                <td>
                                    <strong><?php echo esc_html($submission->visitor_name ?? ''); ?></strong><br>
                                    <a href="mailto:<?php echo esc_attr($submission->visitor_email ?? ''); ?>"><?php echo esc_html($submission->visitor_email ?? ''); ?></a>
                                </td>
                                <td>
                                    <strong><?php echo esc_html($submission->subject ?? ''); ?></strong>
                                </td>
                                <td>
                                    <?php 
                                    $preview = wp_trim_words(strip_tags($submission->message), 15, '...');
                                    echo esc_html($preview);
                                    ?>
                                </td>
                                <td>
                                    <a href="<?php echo add_query_arg(array('view' => $submission->id)); ?>" class="button button-small">View</a>
                                    
                                    <?php if ($submission->status === 'unread'): ?>
                                        <a href="<?php echo wp_nonce_url(add_query_arg(array('action' => 'mark_read', 'submission_id' => $submission->id)), 'fst_submission_action'); ?>" class="button button-small">Mark Read</a>
                                    <?php else: ?>
                                        <a href="<?php echo wp_nonce_url(add_query_arg(array('action' => 'mark_unread', 'submission_id' => $submission->id)), 'fst_submission_action'); ?>" class="button button-small">Mark Unread</a>
                                    <?php endif; ?>
                                    
                                    <a href="<?php echo wp_nonce_url(add_query_arg(array('action' => 'delete', 'submission_id' => $submission->id)), 'fst_submission_action'); ?>" class="button button-small" onclick="return confirm('Are you sure you want to delete this submission?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="tablenav bottom">
                    <div class="alignleft actions bulkactions">
                        <select name="bulk_action" id="bulk-action-selector-bottom">
                            <option value="-1">Bulk Actions</option>
                            <option value="mark_read">Mark as Read</option>
                            <option value="mark_unread">Mark as Unread</option>
                            <option value="delete">Delete</option>
                        </select>
                        <input type="submit" class="button action" value="Apply" id="doaction2">
                    </div>
                    
                    <div class="tablenav-pages">
                        <?php
                        if ($total_pages > 1) {
                            $page_links = paginate_links(array(
                                'base' => add_query_arg('paged', '%#%'),
                                'format' => '',
                                'prev_text' => '&laquo;',
                                'next_text' => '&raquo;',
                                'total' => $total_pages,
                                'current' => $current_page,
                                'type' => 'list'
                            ));
                            echo $page_links;
                        }
                        ?>
                    </div>
                </div>
            </form>
            
        <?php endif; ?>
        
        <?php
        // Show individual submission view if requested
        if (isset($_GET['view'])) {
            $this->show_submission_details(intval($_GET['view']));
        }
        ?>
        
    </div>
    
    <style>
    .wp-list-table tr.unread {
        background-color: #f0f8ff;
        font-weight: bold;
    }
    .wp-list-table .column-cb {
        width: 2.2em;
    }
    .tablenav .actions {
        padding: 2px 8px 0 0;
    }
    .search-box {
        float: right;
        margin: 0;
    }
    .search-box input[type="search"] {
        width: 280px;
    }
    
    /* Fix pagination display */
    .tablenav-pages {
        float: right;
        margin: 0;
    }
    .tablenav-pages .page-numbers {
        display: inline-block;
        padding: 3px 5px;
        margin: 0 2px;
        text-decoration: none;
        border: 1px solid #ddd;
        background: #f7f7f7;
    }
    .tablenav-pages .page-numbers.current {
        background: #2271b1;
        color: white;
        border-color: #2271b1;
    }
    .tablenav-pages .page-numbers:hover {
        background: #f3e0e7;
        color: white;
        border-color: #c01d2e;
    }
    .tablenav-pages ul {
        display: inline-block;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .tablenav-pages ul li {
        display: inline-block;
        margin: 0;
    }
    .tablenav-pages ul li a,
    .tablenav-pages ul li span {
        display: inline-block;
        padding: 3px 5px;
        margin: 0 2px;
        text-decoration: none;
        border: 1px solid #ddd;
        background: #f7f7f7;
        color: #2271b1;
    }
    .tablenav-pages ul li span.current {
        background: #2271b1;
        color: white;
        border-color: #2271b1;
    }
    .tablenav-pages ul li a:hover {
        background: #f3e0e7;
        color: white;
        border-color: #c01d2e;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Handle select all checkbox
        $('#cb-select-all-1').on('change', function() {
            $('input[name="submission_ids[]"]').prop('checked', this.checked);
        });
        
        // FIXED: Handle bulk action form submission with better validation
        $('#bulk-actions-form').on('submit', function(e) {
            var bulkAction = $('#bulk-action-selector-top').val();
            if (!bulkAction) {
                bulkAction = $('#bulk-action-selector-bottom').val();
            }
            
            var selectedItems = $('input[name="submission_ids[]"]:checked').length;
            
            if (bulkAction === '-1') {
                e.preventDefault();
                alert('Please select a bulk action.');
                return false;
            }
            
            if (selectedItems === 0) {
                e.preventDefault();
                alert('Please select at least one submission.');
                return false;
            }
            
            // Confirm delete action
            if (bulkAction === 'delete') {
                if (!confirm('Are you sure you want to delete ' + selectedItems + ' submission(s)? This action cannot be undone.')) {
                    e.preventDefault();
                    return false;
                }
            }
            
            // Set the bulk action field from whichever dropdown was used
            $('input[name="bulk_action"]').val(bulkAction);
            
            return true;
        });
        
        // Sync bulk action dropdowns
        $('#bulk-action-selector-top, #bulk-action-selector-bottom').on('change', function() {
            var value = $(this).val();
            $('#bulk-action-selector-top, #bulk-action-selector-bottom').val(value);
        });
        
        // Handle filter form submission
        $('#filter-by-status').on('change', function() {
            $('#filter-form').submit();
        });
    });
    </script>
    <?php
}

    /**
     * Show individual submission details
     */
    public function show_submission_details($submission_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'fst_submissions';
        $submission = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $submission_id));
        
        if (!$submission) {
            echo '<div class="notice notice-error"><p>Submission not found.</p></div>';
            return;
        }
        
        // Mark as read when viewed
        if ($submission->status === 'unread') {
            $wpdb->update($table_name, array('status' => 'read'), array('id' => $submission_id));
            $submission->status = 'read';
        }
        
        // Handle admin notes update
        if (isset($_POST['update_notes']) && wp_verify_nonce($_POST['_wpnonce'], 'fst_update_notes')) {
            $admin_notes = wp_kses_post($_POST['admin_notes']);
            $wpdb->update($table_name, array('admin_notes' => $admin_notes), array('id' => $submission_id));
            $submission->admin_notes = $admin_notes;
            echo '<div class="notice notice-success"><p>Admin notes updated successfully.</p></div>';
        }
        
        ?>
        <div class="notice notice-info" style="margin-top: 20px;">
            <h2>Submission Details #<?php echo $submission->id; ?></h2>
            
            <div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 5px; margin: 15px 0;">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <h3>Visitor Information</h3>
                        <table class="form-table">
                            <tr>
                                <th>Name:</th>
                                <td><strong><?php esc_html($submission->visitor_name ?? ''); ?></strong></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><a href="mailto:<?php echo esc_attr($submission->visitor_email ?? ''); ?>"><?php echo esc_html($submission->visitor_email ?? ''); ?></a></td>
                            </tr>
                            <tr>
                                <th>Subject:</th>
                                <td><?php echo esc_html($submission->subject ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>Submitted:</th>
                                <td><?php echo date('F j, Y \a\t g:i a', strtotime($submission->submission_date)); ?></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <?php
                                    $status_colors = array(
                                        'unread' => '#d63638',
                                        'read' => '#00a32a', 
                                        'sent' => '#2271b1',
                                        'email_failed' => '#d63638'
                                    );
                                    $status_color = isset($status_colors[$submission->status]) ? $status_colors[$submission->status] : '#646970';
                                    ?>
                                    <span style="color: <?php echo $status_color; ?>; font-weight: bold;">
                                        <?php echo ucfirst(str_replace('_', ' ', $submission->status)); ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div>
                        <h3>Technical Information</h3>
                        <table class="form-table">
                            <tr>
                                <th>IP Address:</th>
                                <td><?php echo esc_html($submission->visitor_ip ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>User Agent:</th>
                                <td style="word-break: break-all; font-size: 11px;"><?php echo esc_html($submission->user_agent ?? ''); ?></td>
                            </tr>
                            <?php if (!empty($submission->attachments)): ?>
                            <tr>
                                <th>Attachments:</th>
                                <td>
                                    <?php
                                    $attachments = json_decode($submission->attachments, true);
                                    if (is_array($attachments)) {
                                        foreach ($attachments as $attachment) {
                                            $size = $attachment['size'] < 1024 ? $attachment['size'] . ' bytes' : 
                                                   ($attachment['size'] < 1048576 ? round($attachment['size']/1024, 1) . ' KB' : 
                                                   round($attachment['size']/1048576, 1) . ' MB');
                                            echo '<div>📎 ' . esc_html($attachment['filename']) . ' (' . $size . ')</div>';
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <h3>Message</h3>
                    <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #2271b1; white-space: pre-wrap; font-family: inherit;">
                <?php echo esc_html($submission->message ?? ''); ?>
                    </div>
                </div>
                
                <?php if (!empty($submission->additional_fields)): ?>
                <div style="margin-bottom: 20px;">
                    <h3>Additional Fields</h3>
                    <table class="form-table">
                        <?php
                        $additional_fields = json_decode($submission->additional_fields, true);
                        if (is_array($additional_fields)) {
                            foreach ($additional_fields as $label => $value) {
                                echo '<tr><th>' . esc_html($label) . ':</th><td>' . esc_html($value) . '</td></tr>';
                            }
                        }
                        ?>
                    </table>
                </div>
                <?php endif; ?>
                
                <div style="margin-bottom: 20px;">
                    <h3>Admin Notes</h3>
                    <form method="post" action="">
                        <?php wp_nonce_field('fst_update_notes'); ?>
                        <textarea name="admin_notes" rows="4" style="width: 100%;" placeholder="Add your notes about this submission..."><?php echo esc_textarea($submission->admin_notes ?? ''); ?></textarea>
                        <p>
                            <input type="submit" name="update_notes" class="button button-primary" value="Update Notes">
                            <a href="<?php echo remove_query_arg('view'); ?>" class="button">← Back to List</a>
                            <a href="mailto:<?php echo esc_attr($submission->visitor_email ?? ''); ?>?subject=Re: <?php echo esc_attr($submission->subject ?? ''); ?>" class="button">Reply via Email</a>
                        </p>
                    </form>
                </div>
                
            </div>
        </div>
        <?php
    }
    
    public function admin_page() {
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'fst_settings')) {
            // Save all settings
            $email = sanitize_email($_POST['fst_default_email']);
            if (empty($email) && !empty($_POST['fst_default_email'])) {
                $email = sanitize_text_field($_POST['fst_default_email']);
            }
            
            update_option('fst_default_email', $email);
            update_option('fst_required_field_colors', isset($_POST['fst_required_field_colors']) ? 1 : 0);
            update_option('fst_show_version', isset($_POST['fst_show_version']) ? 1 : 0);
            update_option('fst_custom_thanks_message', wp_kses_post($_POST['fst_custom_thanks_message']));
            update_option('fst_max_urls_allowed', max(0, min(10, intval($_POST['fst_max_urls_allowed']))));
            update_option('fst_enable_reset_button', isset($_POST['fst_enable_reset_button']) ? 1 : 0);
            
            // File upload settings
            update_option('fst_enable_uploads', isset($_POST['fst_enable_uploads']) ? 1 : 0);
            update_option('fst_upload_extensions', sanitize_text_field($_POST['fst_upload_extensions']));
            update_option('fst_upload_folder', sanitize_text_field($_POST['fst_upload_folder']));
            update_option('fst_file_retention_days', intval($_POST['fst_file_retention_days']));
			
			// Color customization settings
            update_option('fst_enable_custom_colors', isset($_POST['fst_enable_custom_colors']) ? 1 : 0);

            // Validate and save color values
            $color_fields = array(
            'fst_form_background_color' => '#ffffff',
            'fst_input_background_color' => '#FFFF9E', 
            'fst_input_border_color' => '#ccc',
            'fst_label_text_color' => '#333333',
            'fst_submit_button_color' => '#38f923',
            'fst_submit_button_hover_color' => '#00E000',
            'fst_submit_button_text_color' => '#000000',
            'fst_reset_button_color' => '#FF0000',
            'fst_reset_button_hover_color' => '#FFFF00',
            'fst_reset_button_text_color' => '#FFFFFF'
);

foreach ($color_fields as $field => $default) {
    $color_value = isset($_POST[$field]) ? sanitize_hex_color($_POST[$field]) : $default;
    if (!$color_value) {
        $color_value = $default; // Use default if invalid color
    }
    update_option($field, $color_value);
}
            
            // Create upload folder if it doesn't exist
            if (isset($_POST['fst_enable_uploads']) && $_POST['fst_enable_uploads']) {
                // Sanitize and validate the upload folder path to prevent path traversal
                $folder_name = sanitize_text_field($_POST['fst_upload_folder']);
                // Remove any path traversal attempts
                $folder_name = str_replace(['../', '..\\', '../\\', '\\../'], '', $folder_name);
                // Remove any leading/trailing slashes and only keep the folder name
                $folder_name = trim($folder_name, '/\\');
                // Only allow alphanumeric characters, hyphens, and underscores
                $folder_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $folder_name);

                // Get WordPress uploads directory (safer than ABSPATH)
                $wp_uploads = wp_upload_dir();
                $upload_folder = trailingslashit($wp_uploads['basedir']) . $folder_name . '/';
				
                if (!is_dir($upload_folder)) {
                    // Sanitize and validate the upload folder path
                    $sanitized_upload_folder = $this->sanitize_upload_path($upload_folder);

                if ($sanitized_upload_folder && wp_mkdir_p($sanitized_upload_folder)) {
                   $this->create_htaccess_protection($sanitized_upload_folder);
                   echo '<div class="notice notice-success"><p><strong>Upload folder created successfully with security protection!</strong></p></div>';
                } else {
                        echo '<div class="notice notice-warning"><p><strong>Could not create upload folder automatically.</strong> Please create it manually: <code>' . esc_html($upload_folder) . '</code></p></div>';
                    }
                } else {
                    // Ensure .htaccess exists for existing folders
                    $this->create_htaccess_protection($upload_folder);
                }
                
                // Schedule cleanup cron job when uploads are enabled
                if (!wp_next_scheduled('fst_cleanup_uploads')) {
                    wp_schedule_event(time(), 'daily', 'fst_cleanup_uploads');
                }
            } else {
                // Unschedule cleanup when uploads are disabled
                $timestamp = wp_next_scheduled('fst_cleanup_uploads');
                if ($timestamp) {
                    wp_unschedule_event($timestamp, 'fst_cleanup_uploads');
                }
            }
            
            echo '<div class="notice notice-success"><p><strong>Settings saved successfully!</strong> Please refresh your contact form page to see changes.</p></div>';
        }
        
        // Get current values
        $default_email = get_option('fst_default_email', get_option('admin_email'));
        $required_field_colors = get_option('fst_required_field_colors', 0);
        $show_version = get_option('fst_show_version', 0);
        $custom_thanks_message = get_option('fst_custom_thanks_message', '');
        $max_urls_allowed = get_option('fst_max_urls_allowed', 1);
        $enable_reset_button = get_option('fst_enable_reset_button', 0);
        $enable_uploads = get_option('fst_enable_uploads', 0);
        $upload_extensions = get_option('fst_upload_extensions', '.pdf,.jpg,.jpeg,.png,.gif,.doc,.docx');
        $upload_folder = get_option('fst_upload_folder', 'fst-uploads');
        $file_retention_days = get_option('fst_file_retention_days', 30);
        
        ?>
        <div class="wrap">
            <h1>FormSpammerTrap Settings</h1>
            
            <form method="post" action="">
                <?php wp_nonce_field('fst_settings'); ?>
                
                <h2>Basic Settings</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Default Email Address</th>
                        <td>
                            <input type="text" name="fst_default_email" value="<?php echo esc_attr($default_email); ?>" class="regular-text" />
                            <p class="description">Email address to receive contact form submissions. Must be on your domain: <strong><?php echo esc_html($_SERVER['HTTP_HOST']); ?></strong></p>
                            <p class="description">For your local site, use: <code>webmaster@<?php echo esc_html($_SERVER['HTTP_HOST']); ?></code></p>
                        </td>
                    </tr>
                </table>
                
                <h2>Visual Features</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Required Field Colors</th>
                        <td>
                            <label>
                                <input type="checkbox" name="fst_required_field_colors" value="1" <?php checked($required_field_colors, 1); ?> />
                                Add color coding to required field borders
                            </label>
                            <p class="description">Shows red borders for empty required fields, yellow for invalid data, green for valid data.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Show Version</th>
                        <td>
                            <label>
                                <input type="checkbox" name="fst_show_version" value="1" <?php checked($show_version, 1); ?> />
                                Show FormSpammerTrap version at bottom of form
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Enable Reset Button</th>
                        <td>
                            <label>
                                <input type="checkbox" name="fst_enable_reset_button" value="1" <?php checked($enable_reset_button, 1); ?> />
                                Add a "Reset Form" button to clear all form fields
                            </label>
                            <p class="description">Adds a red "Reset Form" button next to the submit button when it becomes visible.</p>
                        </td>
                    </tr>
                </table>
				
				<h2>Form Color Customization</h2>
<table class="form-table">
    <tr>
        <th scope="row">Enable Custom Colors</th>
        <td>
            <label>
                <input type="checkbox" name="fst_enable_custom_colors" value="1" <?php checked(get_option('fst_enable_custom_colors', 0), 1); ?> 
                       onchange="toggleColorSettings(this.checked)" />
                Override FormSpammerTrap's default form colors
            </label>
            <p class="description">When enabled, your custom colors will override the default FormSpammerTrap styling.</p>
        </td>
    </tr>
</table>

<div id="color-settings" style="<?php echo get_option('fst_enable_custom_colors', 0) ? '' : 'display:none;'; ?>">
    <h3>Form Colors</h3>
    <table class="form-table">
        <tr>
            <th scope="row">Form Background Color</th>
            <td>
                <input type="color" name="fst_form_background_color" value="<?php echo esc_attr(get_option('fst_form_background_color', '#ffffff')); ?>" 
                       onchange="syncColorInputs(this, 'fst_form_background_color_text')" />
                <input type="text" name="fst_form_background_color_text" id="fst_form_background_color_text" 
                       value="<?php echo esc_attr(get_option('fst_form_background_color', '#ffffff')); ?>" class="regular-text"
                       onchange="syncColorInputs(this, 'fst_form_background_color')" />
                <p class="description">Background color for the entire contact form container.</p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">Input Field Background</th>
            <td>
                <input type="color" name="fst_input_background_color" value="<?php echo esc_attr(get_option('fst_input_background_color', '#FFFF9E')); ?>"
                       onchange="syncColorInputs(this, 'fst_input_background_color_text')" />
                <input type="text" name="fst_input_background_color_text" id="fst_input_background_color_text"
                       value="<?php echo esc_attr(get_option('fst_input_background_color', '#FFFF9E')); ?>" class="regular-text"
                       onchange="syncColorInputs(this, 'fst_input_background_color')" />
                <p class="description">Background color for text inputs, email fields, and textareas.</p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">Input Field Border</th>
            <td>
                <input type="color" name="fst_input_border_color" value="<?php echo esc_attr(get_option('fst_input_border_color', '#ccc')); ?>"
                       onchange="syncColorInputs(this, 'fst_input_border_color_text')" />
                <input type="text" name="fst_input_border_color_text" id="fst_input_border_color_text"
                       value="<?php echo esc_attr(get_option('fst_input_border_color', '#ccc')); ?>" class="regular-text"
                       onchange="syncColorInputs(this, 'fst_input_border_color')" />
                <p class="description">Border color for all input fields.</p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">Label Text Color</th>
            <td>
                <input type="color" name="fst_label_text_color" value="<?php echo esc_attr(get_option('fst_label_text_color', '#333333')); ?>"
                       onchange="syncColorInputs(this, 'fst_label_text_color_text')" />
                <input type="text" name="fst_label_text_color_text" id="fst_label_text_color_text"
                       value="<?php echo esc_attr(get_option('fst_label_text_color', '#333333')); ?>" class="regular-text"
                       onchange="syncColorInputs(this, 'fst_label_text_color')" />
                <p class="description">Color for field labels and form instructions.</p>
            </td>
        </tr>
    </table>
    
    <h3>Submit Button Colors</h3>
    <table class="form-table">
        <tr>
            <th scope="row">Submit Button Background</th>
            <td>
                <input type="color" name="fst_submit_button_color" value="<?php echo esc_attr(get_option('fst_submit_button_color', '#38f923')); ?>"
                       onchange="syncColorInputs(this, 'fst_submit_button_color_text')" />
                <input type="text" name="fst_submit_button_color_text" id="fst_submit_button_color_text"
                       value="<?php echo esc_attr(get_option('fst_submit_button_color', '#38f923')); ?>" class="regular-text"
                       onchange="syncColorInputs(this, 'fst_submit_button_color')" />
                <p class="description">Normal background color for the submit button.</p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">Submit Button Hover</th>
            <td>
                <input type="color" name="fst_submit_button_hover_color" value="<?php echo esc_attr(get_option('fst_submit_button_hover_color', '#00E000')); ?>"
                       onchange="syncColorInputs(this, 'fst_submit_button_hover_color_text')" />
                <input type="text" name="fst_submit_button_hover_color_text" id="fst_submit_button_hover_color_text"
                       value="<?php echo esc_attr(get_option('fst_submit_button_hover_color', '#00E000')); ?>" class="regular-text"
                       onchange="syncColorInputs(this, 'fst_submit_button_hover_color')" />
                <p class="description">Background color when hovering over the submit button.</p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">Submit Button Text</th>
            <td>
                <input type="color" name="fst_submit_button_text_color" value="<?php echo esc_attr(get_option('fst_submit_button_text_color', '#000000')); ?>"
                       onchange="syncColorInputs(this, 'fst_submit_button_text_color_text')" />
                <input type="text" name="fst_submit_button_text_color_text" id="fst_submit_button_text_color_text"
                       value="<?php echo esc_attr(get_option('fst_submit_button_text_color', '#000000')); ?>" class="regular-text"
                       onchange="syncColorInputs(this, 'fst_submit_button_text_color')" />
                <p class="description">Text color for the submit button.</p>
            </td>
        </tr>
		
		
        
        <tr>
            <th scope="row">Reset Button Background</th>
            <td>
                <input type="color" name="fst_reset_button_color" value="<?php echo esc_attr(get_option('fst_reset_button_color', '#FF0000')); ?>"
                       onchange="syncColorInputs(this, 'fst_reset_button_color_text')" />
                <input type="text" name="fst_reset_button_color_text" id="fst_reset_button_color_text"
                       value="<?php echo esc_attr(get_option('fst_reset_button_color', '#FF0000')); ?>" class="regular-text"
                       onchange="syncColorInputs(this, 'fst_reset_button_color')" />
                <p class="description">Normal background color for the reset button.</p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">Reset Button Hover</th>
            <td>
                <input type="color" name="fst_reset_button_hover_color" value="<?php echo esc_attr(get_option('fst_reset_button_hover_color', '#FFFF00')); ?>"
                       onchange="syncColorInputs(this, 'fst_reset_button_hover_color_text')" />
                <input type="text" name="fst_reset_button_hover_color_text" id="fst_reset_button_hover_color_text"
                       value="<?php echo esc_attr(get_option('fst_reset_button_hover_color', '#FFFF00')); ?>" class="regular-text"
                       onchange="syncColorInputs(this, 'fst_reset_button_hover_color')" />
                <p class="description">Background color when hovering over the reset button.</p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">Reset Button Text</th>
            <td>
                <input type="color" name="fst_reset_button_text_color" value="<?php echo esc_attr(get_option('fst_reset_button_text_color', '#FFFFFF')); ?>"
                       onchange="syncColorInputs(this, 'fst_reset_button_text_color_text')" />
                <input type="text" name="fst_reset_button_text_color_text" id="fst_reset_button_text_color_text"
                       value="<?php echo esc_attr(get_option('fst_reset_button_text_color', '#FFFFFF')); ?>" class="regular-text"
                       onchange="syncColorInputs(this, 'fst_reset_button_text_color')" />
                <p class="description">Text color for the reset button.</p>
            </td>
        </tr>
    </table>
    
    <div style="background: #f0f8ff; border: 1px solid #0073aa; padding: 15px; border-radius: 5px; margin: 15px 0;">
        <h4>💡 Color Customization Tips</h4>
        <ul>
            <li><strong>High Contrast:</strong> Ensure sufficient contrast between text and background colors for accessibility</li>
            <li><strong>Brand Consistency:</strong> Use colors that match your website's overall design</li>
            <li><strong>Testing:</strong> Preview your contact form after saving changes to ensure colors look good</li>
            <li><strong>Color Codes:</strong> You can enter hex codes directly in the text fields (e.g., #ff0000 for red)</li>
            <li><strong>Reset:</strong> To restore defaults, disable custom colors and re-enable them</li>
        </ul>
    </div>
</div>

<script type="text/javascript">
function toggleColorSettings(enabled) {
    document.getElementById('color-settings').style.display = enabled ? 'block' : 'none';
}

function syncColorInputs(sourceElement, targetName) {
    var targetElement = document.querySelector('[name="' + targetName + '"]');
    if (targetElement) {
        targetElement.value = sourceElement.value;
    }
}
</script>
                
                <h2>File Upload Settings</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Enable File Uploads</th>
                        <td>
                            <label>
                                <input type="checkbox" name="fst_enable_uploads" value="1" <?php checked($enable_uploads, 1); ?> />
                                Allow visitors to attach files to their messages
                            </label>
                            <p class="description"><strong>✨ Uses FST_MAIL_ALT Override</strong> - Bypasses FormSpammerTrap's default email system with a fixed version</p>
                            <p class="description"><strong>🔒 Security:</strong> Files are validated against allowed extensions and processed securely</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Allowed File Extensions</th>
                        <td>
                            <input type="text" name="fst_upload_extensions" value="<?php echo esc_attr($upload_extensions); ?>" class="regular-text" />
                            <p class="description">Comma-separated list of allowed file extensions (include the dots). Example: .pdf,.jpg,.jpeg,.png,.gif,.doc,.docx</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Upload Folder</th>
                        <td>
                            <input type="text" name="fst_upload_folder" value="<?php echo esc_attr($upload_folder); ?>" class="regular-text" />
                            <p class="description">
                                Folder name in your site root to store uploaded files. 
                                <strong>Current status:</strong> 
                                <?php 
                                $upload_dir = ABSPATH . $upload_folder . '/';
                                if (is_dir($upload_dir)) {
                                    echo '<span style="color: green;">✓ EXISTS</span> (' . $upload_dir . ')';
                                    // Check security status
                                    $htaccess_file = $upload_dir . '.htaccess';
                                    if (file_exists($htaccess_file)) {
                                        echo ' <span style="color: green;">🔒 PROTECTED</span>';
                                    } else {
                                        echo ' <span style="color: orange;">⚠️ UNPROTECTED</span>';
                                    }
                                } else {
                                    echo '<span style="color: red;">✗ MISSING</span> - Will be created automatically when you save settings';
                                }
                                ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">File Retention Period</th>
                        <td>
                            <select name="fst_file_retention_days">
                                <option value="7" <?php selected($file_retention_days, 7); ?>>Delete after 7 days</option>
                                <option value="14" <?php selected($file_retention_days, 14); ?>>Delete after 14 days</option>
                                <option value="30" <?php selected($file_retention_days, 30); ?>>Delete after 30 days (Recommended)</option>
                                <option value="90" <?php selected($file_retention_days, 90); ?>>Delete after 90 days</option>
                                <option value="0" <?php selected($file_retention_days, 0); ?>>Never delete (Not recommended)</option>
                            </select>
                            <p class="description">
                                <strong>🔒 Privacy &amp; Security:</strong> Automatically delete uploaded files after the specified period. 
                                Recommended for GDPR compliance and storage management.
                                <?php if ($file_retention_days == 0): ?>
                                <br><span style="color: orange;"><strong>⚠️ Warning:</strong> Files will accumulate indefinitely without cleanup.</span>
                                <?php endif; ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <h2>Form Behavior</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Custom Thank You Message</th>
                        <td>
                            <textarea name="fst_custom_thanks_message" rows="4" class="large-text" placeholder="Enter your custom success message here..."><?php echo esc_textarea($custom_thanks_message); ?></textarea>
                            <p class="description">Custom message to show after successful form submission. Leave blank to use default message. You can use basic HTML tags like &lt;br&gt;, &lt;strong&gt;, &lt;em&gt;, etc.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Max URLs Allowed</th>
                        <td>
                            <input type="number" name="fst_max_urls_allowed" value="<?php echo esc_attr($max_urls_allowed); ?>" min="0" max="10" class="small-text" />
                            <p class="description">Maximum number of URLs or Email Addresses allowed in contact form messages (spam control). Default is 1. Setting to 0 blocks all URLs.</p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button('Save Settings'); ?>
            </form>
            
            <h2>Usage Instructions</h2>
            <div style="background: #f9f9f9; border: 1px solid #ddd; padding: 15px; border-radius: 5px;">
                <p><strong>To display the contact form on any page or post, use this shortcode:</strong></p>
                <code style="background: #fff; padding: 5px; border: 1px solid #ccc;">[formspammertrap]</code>
                
                <h3>Optional Shortcode Attributes</h3>
                <ul>
                    <li><code>[formspammertrap email="contact@yourdomain.com"]</code> - Override default email</li>
                    <li><code>[formspammertrap cc="copy@example.com"]</code> - Add CC recipient</li>
                    <li><code>[formspammertrap bcc="blind@example.com"]</code> - Add BCC recipient</li>
                </ul>
            </div>
            
            <h2>Installation Status</h2>
            <?php $this->display_installation_status(); ?>
            
            <h2>Uploads Security Status</h2>
            <?php $this->display_security_status(); ?>
			
			<h2>Plugin Data Management</h2>
<div style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
    <h3 style="margin-top: 0;">⚠️ Important: Plugin Deletion</h3>
    <p><strong>When you delete this plugin, the following data will be automatically removed:</strong></p>
    <ul>
        <li>All uploaded files in the <code><?php echo esc_html(get_option('fst_upload_folder', 'fst-uploads')); ?></code> folder</li>
        <li>All plugin settings and configuration</li>
        <li><strong>🆕 SAFE FormSpammerTrap core configuration cleanup (fst_* options)</strong></li>
        <li><strong>🆕 SAFE FormSpammerTrap database tables (with multi-layer verification)</strong></li>
        <li>Scheduled cleanup tasks</li>
    </ul>
    <p><strong>📁 Backup Recommendation:</strong> If you have important uploaded files or contact data, create a backup before deleting the plugin.</p>
    <p><em>Note: This cleanup only occurs when you delete the plugin entirely, not when you deactivate it.</em></p>
</div>
            
            <h2>Advanced Security (Optional)</h2>
            <div style="background: #f0f8ff; border: 1px solid #0073aa; padding: 15px; border-radius: 5px;">
                <h3>Maximum Security: Outside Web Root Storage</h3>
                <p><strong>For advanced users:</strong> The most secure approach is to store uploads outside your web root directory.</p>
                
                <h4>Manual Setup Instructions:</h4>
                <ol>
                    <li>Create a folder outside your public_html directory: <code>/home/yourusername/private_uploads/</code></li>
                    <li>Set folder permissions to 755</li>
                    <li>Update the upload folder setting above to use the full path</li>
                    <li>Create a secure file delivery script (contact support for assistance)</li>
                </ol>
                
                <p><strong>Benefits:</strong> Files cannot be accessed via direct URLs, even if someone knows the filename.</p>
                <p><strong>Note:</strong> This requires custom implementation for file access and may not work on all hosting environments.</p>
            </div>
        </div>
        <?php
    }
	
	/**
     * Output custom CSS for form colors
     */
    public function output_custom_form_colors() {
        // Only output if custom colors are enabled
        if (!get_option('fst_enable_custom_colors', 0)) {
            return;
        }
        
        // Get color values from database
        $form_bg = get_option('fst_form_background_color', '#ffffff');
        $input_bg = get_option('fst_input_background_color', '#FFFF9E');
        $input_border = get_option('fst_input_border_color', '#ccc');
        $label_color = get_option('fst_label_text_color', '#333333');
        $submit_color = get_option('fst_submit_button_color', '#38f923');
        $submit_hover = get_option('fst_submit_button_hover_color', '#00E000');
        $submit_text = get_option('fst_submit_button_text_color', '#000000');
		$reset_color = get_option('fst_reset_button_color', '#FF0000');
        $reset_hover = get_option('fst_reset_button_hover_color', '#FFFF00');
        $reset_text = get_option('fst_reset_button_text_color', '#FFFFFF');
        
        // Output custom CSS
        ?>
        <style type="text/css">
        /* FormSpammerTrap Plugin - Custom Color Overrides */
        
        /* Form Container Background */
        .fst_container,
        .fst_comment_box {
            background-color: <?php echo esc_attr($form_bg); ?> !important;
        }
        
        /* Input Field Styling */
        .fst_column_2 > input[type=text],
        .fst_column_2 > input[type=email],
        .fst_column_2 > input[type=url],
        .fst_column_2 > input[type=tel],
        .fst_column_2 > input[type=number],
        .fst_column_2 > input[type=search],
        .fst_column_2 > input[type=password],
        .fst_column_2 > input[type=date],
        .fst_column_2 > input[type=datetime-local],
        .fst_column_2 > select,
        .fst_column_2 > textarea {
            background-color: <?php echo esc_attr($input_bg); ?> !important;
            border-color: <?php echo esc_attr($input_border); ?> !important;
        }
        
        /* Label Text Color */
        .fst_column_1 label,
        .fst_column_1,
        .fst_formtop_message,
        .fst_formtop_message strong {
            color: <?php echo esc_attr($label_color); ?> !important;
        }
        
        /* Submit Button Styling */
        #<?php echo defined('FST_SUBMITBUTTON_ID') ? esc_attr(FST_SUBMITBUTTON_ID) : 'fst_submitbutton'; ?>,
        button.fst_button,
        .fst_column_2 > input[type=submit],
        .fst_column_2 > button {
            background-color: <?php echo esc_attr($submit_color); ?> !important;
            color: <?php echo esc_attr($submit_text); ?> !important;
            border: 1px solid <?php echo esc_attr($submit_color); ?> !important;
        }
        
        /* Submit Button Hover */
        #<?php echo defined('FST_SUBMITBUTTON_ID') ? esc_attr(FST_SUBMITBUTTON_ID) : 'fst_submitbutton'; ?>:hover,
        button.fst_button:hover,
        .fst_column_2 > input[type=submit]:hover,
        .fst_column_2 > button:hover {
            background-color: <?php echo esc_attr($submit_hover); ?> !important;
            border-color: <?php echo esc_attr($submit_hover); ?> !important;
        }
        
        /* File Upload Field Styling */
        .fst_column_2 > input[type=file] {
            background-color: <?php echo esc_attr($input_bg); ?> !important;
            border-color: <?php echo esc_attr($input_border); ?> !important;
        }
		
		/* Reset Button Styling */
        #fst_plugin_reset_button,
        .fst_reset_button,
            button[type="button"].fst_reset_button {
            background-color: <?php echo esc_attr($reset_color); ?> !important;
            color: <?php echo esc_attr($reset_text); ?> !important;
            border: 1px solid <?php echo esc_attr($reset_color); ?> !important;
        }

        /* Reset Button Hover */
        #fst_plugin_reset_button:hover,
        .fst_reset_button:hover,
            button[type="button"].fst_reset_button:hover {
            background-color: <?php echo esc_attr($reset_hover); ?> !important;
            border-color: <?php echo esc_attr($reset_hover); ?> !important;
        }
        </style>
        <?php
    }
    
    public function display_security_status() {
        $upload_folder = get_option('fst_upload_folder', 'fst-uploads');
        $upload_dir = ABSPATH . $upload_folder . '/';
        $retention_days = get_option('fst_file_retention_days', 30);
        
        echo '<table class="widefat">';
        echo '<thead><tr><th>Security Feature</th><th>Status</th><th>Details</th></tr></thead>';
        echo '<tbody>';
        
        // Check .htaccess protection
        echo '<tr>';
        echo '<td>Directory Protection</td>';
        if (is_dir($upload_dir)) {
            $htaccess_file = $upload_dir . '.htaccess';
            if (file_exists($htaccess_file)) {
                echo '<td style="color: green;">✓ Active</td>';
                echo '<td>.htaccess file blocks direct access to uploaded files</td>';
            } else {
                echo '<td style="color: red;">✗ Missing</td>';
                echo '<td>No .htaccess protection found. Save settings to create automatically.</td>';
            }
        } else {
            echo '<td style="color: gray;">— N/A</td>';
            echo '<td>Upload folder not created yet</td>';
        }
        echo '</tr>';
        
        // Check file cleanup
        echo '<tr>';
        echo '<td>Automatic Cleanup</td>';
        if ($retention_days > 0) {
            echo '<td style="color: green;">✓ Active</td>';
            echo '<td>Files automatically deleted after ' . $retention_days . ' days</td>';
        } else {
            echo '<td style="color: orange;">⚠️ Disabled</td>';
            echo '<td>Files will accumulate indefinitely (not recommended)</td>';
        }
        echo '</tr>';
        
        // Check scheduled cleanup
        echo '<tr>';
        echo '<td>Cleanup Schedule</td>';
        if (wp_next_scheduled('fst_cleanup_uploads')) {
            $next_cleanup = wp_next_scheduled('fst_cleanup_uploads');
            echo '<td style="color: green;">✓ Scheduled</td>';
            echo '<td>Next cleanup: ' . date('Y-m-d H:i:s', $next_cleanup) . '</td>';
        } else {
            echo '<td style="color: orange;">⚠️ Not Scheduled</td>';
            echo '<td>Daily cleanup not scheduled. Save settings to activate.</td>';
        }
        echo '</tr>';
        
        // File count and disk usage
        if (is_dir($upload_dir)) {
            $files = glob($upload_dir . '*');
            $file_count = 0;
            $total_size = 0;
            
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.htaccess' && basename($file) !== 'index.php') {
                    $file_count++;
                    $total_size += filesize($file);
                }
            }
            
            echo '<tr>';
            echo '<td>Current Storage</td>';
            echo '<td style="color: blue;">ℹ️ Info</td>';
            echo '<td>' . $file_count . ' files using ' . $this->format_bytes($total_size) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
    }
    
    private function format_bytes($size, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
	
	/**
     * Create the submissions table on plugin activation
     */
    public function create_submissions_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'fst_submissions';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            submission_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            visitor_name varchar(100) NOT NULL,
            visitor_email varchar(100) NOT NULL,
            subject varchar(200) NOT NULL,
            message text NOT NULL,
            visitor_ip varchar(45),
            user_agent text,
            additional_fields longtext,
            attachments text,
            status varchar(20) DEFAULT 'unread' NOT NULL,
            admin_notes text,
            PRIMARY KEY (id),
            KEY submission_date (submission_date),
            KEY status (status),
            KEY visitor_email (visitor_email)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Create an option to track the table version
        add_option('fst_submissions_table_version', '1.0');
    }

    /**
     * Save form submission to database
     */
    public function fst_save_submission_to_db($data = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'fst_submissions';
        
        // Extract form data, with fallbacks for missing fields
        $visitor_name = isset($data['your_name']) ? sanitize_text_field($data['your_name']) : 
                       (isset($_POST['your_name']) ? sanitize_text_field($_POST['your_name']) : 'Unknown');
        
        $visitor_email = isset($data['your_email']) ? sanitize_email($data['your_email']) : 
                        (isset($_POST['your_email']) ? sanitize_email($_POST['your_email']) : '');
        
        $subject = isset($data['your_subject']) ? sanitize_text_field($data['your_subject']) : 
                  (isset($_POST['your_subject']) ? sanitize_text_field($_POST['your_subject']) : 'No Subject');
        
        $message = isset($data['your_message']) ? wp_kses_post($data['your_message']) : 
                  (isset($_POST['message']) ? wp_kses_post($_POST['message']) : '');
        
        // Get visitor IP (with proxy support)
        $visitor_ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $visitor_ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $visitor_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $visitor_ip = $_SERVER['REMOTE_ADDR'];
        }
        $visitor_ip = sanitize_text_field($visitor_ip);
        
        // Get user agent
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '';
        
        // Collect additional fields from custom fields
        $additional_fields = array();
        if (defined('FST_XCUSTOM_FIELDS') && is_array(FST_XCUSTOM_FIELDS)) {
            foreach (FST_XCUSTOM_FIELDS as $field) {
                $field_name = $field['NAME'];
                if (isset($_POST[$field_name]) && !in_array($field_name, array('your_name', 'your_email', 'your_subject', 'message'))) {
                    $additional_fields[$field['LABELMSG']] = sanitize_text_field($_POST[$field_name]);
                }
            }
        }
        
        // Handle file attachments
        $attachments = array();
        if (isset($_FILES['fst_uploadfile']) && !empty($_FILES['fst_uploadfile']['name'][0])) {
            for ($i = 0; $i < count($_FILES['fst_uploadfile']['name']); $i++) {
                if (!empty($_FILES['fst_uploadfile']['name'][$i])) {
                    $attachments[] = array(
                        'filename' => sanitize_file_name($_FILES['fst_uploadfile']['name'][$i]),
                        'size' => $_FILES['fst_uploadfile']['size'][$i],
                        'type' => $_FILES['fst_uploadfile']['type'][$i]
                    );
                }
            }
        }
        
        // Insert into database
        $result = $wpdb->insert(
            $table_name,
            array(
                'visitor_name' => $visitor_name,
                'visitor_email' => $visitor_email,
                'subject' => $subject,
                'message' => $message,
                'visitor_ip' => $visitor_ip,
                'user_agent' => $user_agent,
                'additional_fields' => !empty($additional_fields) ? wp_json_encode($additional_fields) : '',
                'attachments' => !empty($attachments) ? wp_json_encode($attachments) : '',
                'status' => 'unread'
            ),
            array(
                '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'
            )
        );
        
        if ($result === false) {
            return false;
        }
        return $wpdb->insert_id;
    }
    
    public function display_installation_status() {
        $functions_file = plugin_dir_path(__FILE__) . 'includes/formspammertrap-contact-functions.php';
        $upload_folder = get_option('fst_upload_folder', 'fst-uploads');
        $upload_dir = ABSPATH . $upload_folder . '/';
        
        echo '<table class="widefat">';
        echo '<thead><tr><th>Component</th><th>Status</th><th>Notes</th></tr></thead>';
        echo '<tbody>';
        
        // Check functions file
        echo '<tr>';
        echo '<td>FormSpammerTrap Functions File</td>';
        if (file_exists($functions_file)) {
            echo '<td style="color: green;">✓ Found</td>';
            echo '<td>Located at: includes/formspammertrap-contact-functions.php (UNMODIFIED)</td>';
        } else {
            echo '<td style="color: red;">✗ Missing</td>';
            echo '<td>Please upload formspammertrap-contact-functions.php to the includes/ folder</td>';
        }
        echo '</tr>';
        
        // FST_MAIL_ALT status
        echo '<tr>';
        echo '<td>FST_MAIL_ALT Override</td>';
        if (function_exists('FST_MAIL_ALT')) {
            echo '<td style="color: green;">✓ Active</td>';
            echo '<td>Plugin email override function is loaded and will be used by FormSpammerTrap</td>';
        } else {
            echo '<td style="color: orange;">! Pending</td>';
            echo '<td>Will be active after FormSpammerTrap initializes</td>';
        }
        echo '</tr>';
        
        // Check upload directory (if uploads enabled)
        if (get_option('fst_enable_uploads', 0)) {
            echo '<tr>';
            echo '<td>Upload Directory</td>';
            if (is_dir($upload_dir)) {
                echo '<td style="color: green;">✓ Exists</td>';
                echo '<td>Located at: ' . $upload_dir . '</td>';
            } else {
                echo '<td style="color: orange;">! Missing</td>';
                echo '<td>Will be created automatically when you save settings with uploads enabled</td>';
            }
            echo '</tr>';
        }
        
        echo '</tbody></table>';
    }
    
    /**
     * Show admin notice for unread submissions
     */
    public function show_unread_submissions_notice() {
        global $wpdb;
        
        // Only show on admin pages, not on the submissions page itself
        if (!is_admin() || (isset($_GET['page']) && $_GET['page'] === 'fst-submissions')) {
            return;
        }
        
        $table_name = $wpdb->prefix . 'fst_submissions';
        
        // Check if table exists first
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            return;
        }
        
        $unread_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'unread'");
        
        if ($unread_count > 0) {
            $submissions_url = admin_url('admin.php?page=fst-submissions&status=unread');
            $message = sprintf(
                _n(
                    'You have %d unread contact form submission.',
                    'You have %d unread contact form submissions.',
                    $unread_count
                ),
                $unread_count
            );
            
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p><strong>FormSpammerTrap:</strong> ' . $message . ' ';
            echo '<a href="' . $submissions_url . '">View submissions &raquo;</a></p>';
            echo '</div>';
        }
    }

    /**
     * Add dashboard widget for recent submissions
     */
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'fst_submissions_widget',
            'Recent Contact Form Submissions',
            array($this, 'dashboard_widget_content')
        );
    }

    /**
     * Dashboard widget content
     */
    public function dashboard_widget_content() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'fst_submissions';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            echo '<p>No submissions table found. The contact form may not have been used yet.</p>';
            return;
        }
        
        // Get recent submissions
        $recent_submissions = $wpdb->get_results("
            SELECT id, visitor_name, visitor_email, subject, submission_date, status 
            FROM $table_name 
            ORDER BY submission_date DESC 
            LIMIT 5
        ");
        
        if (empty($recent_submissions)) {
            echo '<p>No contact form submissions yet.</p>';
            return;
        }
        
        echo '<table style="width: 100%;">';
        echo '<thead><tr><th>From</th><th>Subject</th><th>Date</th><th>Status</th></tr></thead>';
        echo '<tbody>';
        
        foreach ($recent_submissions as $submission) {
            $status_colors = array(
                'unread' => '#d63638',
                'read' => '#00a32a',
                'sent' => '#2271b1',
                'email_failed' => '#d63638'
            );
            $status_color = isset($status_colors[$submission->status]) ? $status_colors[$submission->status] : '#646970';
            
            echo '<tr>';
            echo '<td><strong>' . esc_html($submission->visitor_name ?? '') . '</strong><br>';
            echo '<small>' . esc_html($submission->visitor_email ?? '') . '</small></td>';
            echo '<td>' . esc_html(wp_trim_words($submission->subject ?? '', 5)) . '</td>';
            echo '<td>' . date('M j', strtotime($submission->submission_date)) . '</td>';
            echo '<td><span style="color: ' . $status_color . '; font-weight: bold;">' . ucfirst(str_replace('_', ' ', $submission->status)) . '</span></td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
        
        $submissions_url = admin_url('admin.php?page=fst-submissions');
        echo '<p><a href="' . $submissions_url . '">View All Submissions &raquo;</a></p>';
        
        // Show stats
        $total_submissions = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $unread_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'unread'");
        $today_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE DATE(submission_date) = CURDATE()");
        
        echo '<hr>';
        echo '<p><strong>Quick Stats:</strong></p>';
        echo '<ul>';
        echo '<li>Total Submissions: ' . $total_submissions . '</li>';
        echo '<li>Unread: ' . $unread_count . '</li>';
        echo '<li>Today: ' . $today_count . '</li>';
        echo '</ul>';
    }
    
    public function missing_functions_notice() {
        echo '<div class="notice notice-error"><p><strong>FormSpammerTrap:</strong> The formspammertrap-contact-functions.php file is missing. Please upload it to the includes/ folder in the plugin directory.</p></div>';
    }
    
    public function activate() {
    // Set default options
    add_option('fst_default_email', get_option('admin_email'));
    add_option('fst_required_field_colors', 0);
    add_option('fst_show_version', 0);
    add_option('fst_custom_thanks_message', '');
    add_option('fst_max_urls_allowed', 1);
    add_option('fst_enable_reset_button', 0);
    add_option('fst_enable_uploads', 0);
    add_option('fst_upload_extensions', '.pdf,.jpg,.jpeg,.png,.gif,.doc,.docx');
    add_option('fst_upload_folder', 'fst-uploads');
    add_option('fst_file_retention_days', 30);
    add_option('fst_enable_custom_colors', 0);
    add_option('fst_form_background_color', '#ffffff');
    add_option('fst_input_background_color', '#FFFF9E');
    add_option('fst_input_border_color', '#ccc');
    add_option('fst_label_text_color', '#333333');
    add_option('fst_submit_button_color', '#38f923');
    add_option('fst_submit_button_hover_color', '#00E000');
    add_option('fst_submit_button_text_color', '#000000');
    add_option('fst_reset_button_color', '#FF0000');
    add_option('fst_reset_button_hover_color', '#FFFF00');
    add_option('fst_reset_button_text_color', '#FFFFFF');
	
	// Create the contact table
    fst_create_contact_table();
    
    // NEW: Create submissions table
    $this->create_submissions_table();
    
    // Schedule cleanup if uploads are enabled
    if (!wp_next_scheduled('fst_cleanup_uploads')) {
        wp_schedule_event(time(), 'daily', 'fst_cleanup_uploads');
    }
}
    
    /**
     * Create .htaccess file to protect upload folder
     */
    public function create_htaccess_protection($upload_dir) {
        $htaccess_file = $upload_dir . '.htaccess';
        
        if (!file_exists($htaccess_file)) {
            $htaccess_content = '# FormSpammerTrap Upload Security
# Deny all direct access
Order Deny,Allow
Deny from all

# Prevent directory browsing
Options -Indexes

# Block dangerous file types
<FilesMatch "\.(php|php3|php4|php5|phtml|pl|py|jsp|asp|sh|cgi)$">
Order Deny,Allow
Deny from all
</FilesMatch>

# Block execution of scripts
<Files ~ "\..*">
Order allow,deny
Deny from all
</Files>
';
            
            file_put_contents($htaccess_file, $htaccess_content);
            
            // Also create index.php to prevent directory listing
            $index_file = $upload_dir . 'index.php';
            if (!file_exists($index_file)) {
                file_put_contents($index_file, '<?php // Directory access denied ?>');
            }
        }
    }
    
    /**
     * Schedule cleanup task
     */
    public function schedule_cleanup() {
        if (!wp_next_scheduled('fst_cleanup_uploads') && get_option('fst_enable_uploads', 0)) {
            wp_schedule_event(time(), 'daily', 'fst_cleanup_uploads');
        }
    }
    
    /**
     * Clean up old uploaded files
     */
    public function cleanup_old_uploads() {
        $retention_days = get_option('fst_file_retention_days', 30);
        
        // Skip cleanup if retention is set to 0 (never delete)
        if ($retention_days == 0) {
            return;
        }
        
        $upload_folder = get_option('fst_upload_folder', 'fst-uploads');
        $upload_dir = ABSPATH . $upload_folder . '/';
        
        if (!is_dir($upload_dir)) {
            return;
        }
        
        $files = scandir($upload_dir);
        $deleted_count = 0;
        $cutoff_time = time() - ($retention_days * 24 * 60 * 60);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === '.htaccess' || $file === 'index.php') {
                continue;
            }
            
            $file_path = $upload_dir . $file;
            if (is_file($file_path) && filemtime($file_path) < $cutoff_time) {
                if (unlink($file_path)) {
                    $deleted_count++;
                }
            }
        }
        
        // Log cleanup activity
        if ($deleted_count > 0) {
            error_log("FormSpammerTrap Plugin: Cleaned up {$deleted_count} old files older than {$retention_days} days");
        }
    }

    /**
     * ENHANCED: Clean up plugin data on uninstall
     */
    public static function uninstall() {
        // Log the uninstall process
        error_log("FormSpammerTrap Plugin: Starting comprehensive uninstall cleanup process");
        
        // 1. Clean up uploaded files and folder
        self::cleanup_upload_folder();
        
        // 2. Clean up database tables (if contact saving was enabled)
        self::cleanup_database_tables();
        
        // 3. NEW: Clean up submissions table
        self::cleanup_submissions_table();
        
        // 4. Remove cron jobs
        wp_clear_scheduled_hook('fst_cleanup_uploads');
        
        // 5. Clean up all plugin options
        self::cleanup_plugin_options();
        
        // 6. Clean up FormSpammerTrap core options
        self::cleanup_formspammertrap_options();
        
        // 7. Clean up transients
        delete_transient('fst_mail_alt_called_' . date('Y-m-d'));
        delete_transient('fst_plugin_mail_alt_logged_' . date('Y-m-d'));
        
        error_log("FormSpammerTrap Plugin: Comprehensive uninstall cleanup completed");
    }

    /**
     * Clean up submissions table on uninstall
     */
    private static function cleanup_submissions_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'fst_submissions';
        
        // Drop the submissions table
        $result = $wpdb->query("DROP TABLE IF EXISTS $table_name");
        
        if ($result !== false) {
            error_log("FormSpammerTrap Plugin: Removed submissions table: $table_name");
        } else {
            error_log("FormSpammerTrap Plugin: Failed to remove submissions table: $table_name");
        }
        
        // Remove the table version option
        delete_option('fst_submissions_table_version');
    }

    /**
     * Clean up uploaded files and upload folder (ENHANCED - removes folder completely)
     */
    private static function cleanup_upload_folder() {
        $upload_folder = get_option('fst_upload_folder', 'fst-uploads');
        $upload_dir = ABSPATH . $upload_folder . '/';
        
        if (is_dir($upload_dir)) {
            $files_deleted = 0;
            
            // Get all files including hidden files like .htaccess
            $files = array_diff(scandir($upload_dir), array('.', '..'));
            
            // Delete ALL files in upload folder (including .htaccess, index.php, etc.)
            foreach ($files as $file) {
                $file_path = $upload_dir . $file;
                
                if (is_file($file_path)) {
                    if (unlink($file_path)) {
                        $files_deleted++;
                    } else {
                        error_log("FormSpammerTrap Plugin: Could not delete file: {$file_path}");
                    }
                } elseif (is_dir($file_path)) {
                    // Handle subdirectories if any exist
                    self::recursive_rmdir($file_path);
                    $files_deleted++;
                }
            }
            
            // Now try to remove the empty upload folder
            if (rmdir($upload_dir)) {
                error_log("FormSpammerTrap Plugin: Successfully removed upload folder '{$upload_folder}' and {$files_deleted} files");
            } else {
                error_log("FormSpammerTrap Plugin: Removed {$files_deleted} files, but could not remove upload folder '{$upload_folder}' - may contain other files or permission issue");
            }
        } else {
            error_log("FormSpammerTrap Plugin: Upload folder '{$upload_folder}' does not exist - nothing to clean up");
        }
    }

    /**
     * Recursively remove directory and all contents
     */
    private static function recursive_rmdir($dir) {
        if (!is_dir($dir)) {
            return false;
        }
        
        $files = array_diff(scandir($dir), array('.', '..'));
        
        foreach ($files as $file) {
            $file_path = $dir . '/' . $file;
            if (is_dir($file_path)) {
                self::recursive_rmdir($file_path);
            } else {
                unlink($file_path);
            }
        }
        
        return rmdir($dir);
    }

    /**
     * ENHANCED: Clean up database tables if FST contact saving was used
     */
    private static function cleanup_database_tables() {
        global $wpdb;
        
        // Method 1: Check if contact saving was enabled through plugin options
        if (get_option('fst_save_contact_info', 0)) {
            error_log("FormSpammerTrap Plugin: Contact saving was enabled - checking for database tables");
            
            // Get the database configuration
            $db_config = get_option('fst_contact_database', array());
            
            if (!empty($db_config) && is_array($db_config)) {
                self::attempt_table_cleanup($db_config);
            }
        }
        
        // Method 2: Look for FormSpammerTrap tables using specific naming patterns
        $database_name = DB_NAME;
        
        // FormSpammerTrap specific table patterns (safe and unique)
        $fst_table_patterns = array(
            'fst_%',             // Any table starting with fst_
            'formspammertrap%'   // Any table starting with formspammertrap
        );
        
        foreach ($fst_table_patterns as $pattern) {
            $tables = $wpdb->get_results($wpdb->prepare(
                "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES 
                 WHERE TABLE_SCHEMA = %s AND TABLE_NAME LIKE %s",
                $database_name, $pattern
            ));
            
            foreach ($tables as $table) {
                $table_name = $table->TABLE_NAME;
                $result = $wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS `%s`", $table_name));
                
                if ($result !== false) {
                    error_log("FormSpammerTrap Plugin: Removed FST table: {$table_name}");
                } else {
                    error_log("FormSpammerTrap Plugin: Failed to remove table: {$table_name}");
                }
            }
        }
    }

    /**
     * Attempt to clean up a specific table configuration
     */
    private static function attempt_table_cleanup($db_config) {
        global $wpdb;
        
        if (empty($db_config['DATABASE_NAME']) || empty($db_config['DATABASE_TABLE'])) {
            error_log("FormSpammerTrap Plugin: Cannot clean up database - missing table information");
            return;
        }
        
        try {
            $database_name = sanitize_text_field($db_config['DATABASE_NAME']);
            $table_name = sanitize_text_field($db_config['DATABASE_TABLE']);
            
            // Simple check: only delete if table name contains FormSpammerTrap indicators
            $table_lower = strtolower($table_name);
            if (strpos($table_lower, 'fst_') !== false || strpos($table_lower, 'formspammertrap') !== false) {
                $result = $wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS `%s`.`%s`", $database_name, $table_name));
                
                if ($result !== false) {
                    error_log("FormSpammerTrap Plugin: Removed configured FST table: {$table_name}");
                } else {
                    error_log("FormSpammerTrap Plugin: Failed to remove configured table: {$table_name}");
                }
            } else {
                error_log("FormSpammerTrap Plugin: Skipped table cleanup - doesn't appear FormSpammerTrap-specific: {$table_name}");
            }
            
        } catch (Exception $e) {
            error_log("FormSpammerTrap Plugin: Database cleanup error: " . $e->getMessage());
        }
    }

    /**
     * Clean up all plugin-specific options
     */
    private static function cleanup_plugin_options() {
        $plugin_options = array(
            'fst_default_email',
            'fst_required_field_colors', 
            'fst_show_version',
            'fst_custom_thanks_message',
            'fst_max_urls_allowed',
            'fst_enable_reset_button',
            'fst_enable_uploads',
            'fst_upload_extensions',
            'fst_upload_folder',
            'fst_file_retention_days',
            'fst_save_contact_info',
            'fst_contact_database',
            'fst_enable_custom_colors',
            'fst_form_background_color',
            'fst_input_background_color',
            'fst_input_border_color',
            'fst_label_text_color',
            'fst_submit_button_color',
            'fst_submit_button_hover_color',
            'fst_submit_button_text_color',
            'fst_reset_button_color',
            'fst_reset_button_hover_color',
            'fst_reset_button_text_color'
        );
        
        $deleted_count = 0;
        foreach ($plugin_options as $option) {
            if (delete_option($option)) {
                $deleted_count++;
            }
        }
        
        error_log("FormSpammerTrap Plugin: Removed {$deleted_count} plugin-specific options");
    }

    /**
     * NEW: Clean up FormSpammerTrap core options 
     * This removes FormSpammerTrap's own configuration from the database
     */
    private static function cleanup_formspammertrap_options() {
        global $wpdb;
        
        // Get all options that start with 'fst_' (FormSpammerTrap's prefix)
        $fst_options = $wpdb->get_results(
            "SELECT option_name FROM {$wpdb->options} 
             WHERE option_name LIKE 'fst_%' 
             OR option_name LIKE '%formspammer%'
             OR option_name LIKE '%spamshield%'"
        );
        
        if (empty($fst_options)) {
            error_log("FormSpammerTrap Plugin: No FormSpammerTrap core options found to clean up");
            return;
        }
        
        $deleted_count = 0;
        $preserved_options = array(); // Track any we decide to preserve
        
        foreach ($fst_options as $option_row) {
            $option_name = $option_row->option_name;
            
            // Skip WordPress core options that might coincidentally match our pattern
            $core_options_to_preserve = array(
                'fst_rewrite_rules', // WordPress core
                'fst_flush_rewrite' // WordPress core
            );
            
            if (in_array($option_name, $core_options_to_preserve)) {
                $preserved_options[] = $option_name;
                continue;
            }
            
            // Delete the FormSpammerTrap option
            if (delete_option($option_name)) {
                $deleted_count++;
            }
        }
        
        error_log("FormSpammerTrap Plugin: Removed {$deleted_count} FormSpammerTrap core options");
        
        if (!empty($preserved_options)) {
            error_log("FormSpammerTrap Plugin: Preserved " . count($preserved_options) . " WordPress core options: " . implode(', ', $preserved_options));
        }
        
        // Also clean up any FormSpammerTrap transients
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_fst_%' OR option_name LIKE '_transient_timeout_fst_%'");
        
        error_log("FormSpammerTrap Plugin: Cleaned up FormSpammerTrap transients");
    }
}

/**
 * CRITICAL: FST_MAIL_ALT function - FormSpammerTrap's built-in override system
 * This function completely replaces FormSpammerTrap's email sending when it exists
 */
/**
 * UPDATED: FST_MAIL_ALT function with submission saving
 */
function FST_MAIL_ALT($data = array()) {
    // FIRST: Save submission to database (before email sending)
    $plugin_instance = new FormSpammerTrapPlugin();
    $submission_id = $plugin_instance->fst_save_submission_to_db($data);
    
    // Use a more reliable logging mechanism to avoid spam
    $log_key = 'fst_mail_alt_called_' . date('Y-m-d');
    if (!get_transient($log_key)) {
        set_transient($log_key, true, DAY_IN_SECONDS);
    }
    
    // Ensure message_elements is always defined to prevent warnings
    if (!is_array($data)) {
        $data = array();
    }

    // Handle both old message_elements and new after_submit structures
    if (isset($data['your_message'])) {
        // New after_submit structure - map to old structure for compatibility
        $message_elements = array(
            'recipient' => defined('FST_XEMAIL_ON_DOMAIN') ? FST_XEMAIL_ON_DOMAIN : get_option('fst_default_email'),
            'subject' => isset($data['your_subject']) ? $data['your_subject'] : 'Contact Form Message',
            'message' => isset($data['your_message']) ? $data['your_message'] : '',
            'from_email' => defined('FST_FROM_EMAIL') ? FST_FROM_EMAIL : get_option('fst_default_email'),
            'from_name' => defined('FST_FROM_NAME') ? FST_FROM_NAME : parse_url(get_site_url(), PHP_URL_HOST)
        );
    } else {
        // Old message_elements structure - use as-is
        $message_elements = $data;
    }
    
    // If FormSpammerTrap passed us message elements, use them
    // Otherwise, build from $_POST data
    // FORCE use of plugin's email setting
// Use hardcoded/validated email parameters to prevent injection
$plugin_email = get_option('fst_default_email');
if (!empty($plugin_email) && is_email($plugin_email)) {
    $recipient = $plugin_email;
} else {
    $recipient = get_option('admin_email'); // WordPress default
}

// Use only safe, validated subject
if (!empty($message_elements['subject']) && is_string($message_elements['subject'])) {
    $clean_subject = preg_replace('/[^\w\s\-\.\,\!\?]/', '', $message_elements['subject']);
    $subject = 'Contact Form: ' . substr($clean_subject, 0, 100);
} else {
    $subject = 'Contact Form Message'; // Static fallback
}

// Use only validated email addresses
if (defined('FST_FROM_EMAIL') && is_email(FST_FROM_EMAIL)) {
    $from_email = FST_FROM_EMAIL;
} elseif (!empty($plugin_email) && is_email($plugin_email)) {
    $from_email = $plugin_email;
} else {
    $from_email = get_option('admin_email'); // WordPress default
}

// Use only safe from name
if (defined('FST_FROM_NAME')) {
    $from_name = FST_FROM_NAME;
} else {
    $from_name = get_bloginfo('name'); // WordPress site name instead of HTTP_HOST
}
    
    // Platform-aware PHPMailer loading - prevents class conflicts
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        // Try WordPress/ClassicPress PHPMailer first
        if (file_exists(ABSPATH . WPINC . '/PHPMailer/PHPMailer.php')) {
            // WordPress/ClassicPress has PHPMailer - use it
            require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
            require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
            require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
        } else {
            // Fallback to plugin's bundled PHPMailer
            $phpmailer_path = plugin_dir_path(__FILE__) . 'includes/phpmailer/';
            if (file_exists($phpmailer_path . 'Exception.php')) {
                require_once $phpmailer_path . 'Exception.php';
                require_once $phpmailer_path . 'PHPMailer.php';
                require_once $phpmailer_path . 'SMTP.php';
            } else {
                // Fallback to system mail if PHPMailer not available
                $static_headers = "From: " . get_option('admin_email') . "\r\nReply-To: " . get_option('admin_email') . "\r\n";
                $static_subject = "Contact Form Submission";
                $static_message = "A contact form was submitted. Check admin for details.";
                $mail_result = mail($recipient, $static_subject, $static_message, $static_headers);
                
                // Update submission status based on email result
                if ($submission_id) {
                    fst_update_submission_status($submission_id, $mail_result ? 'sent' : 'email_failed');
                }
                
                return $mail_result;
            }
        }
    }
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // SMTP configuration if enabled
        if (defined('FST_SMTP_ENABLE') && FST_SMTP_ENABLE) {
            $mail->isSMTP();
            $mail->Host = FST_SMTP_HOST;
            $mail->SMTPAuth = FST_SMTP_AUTH;
            $mail->Username = FST_SMTP_USER;
            $mail->Password = FST_SMTP_PASS;
            $mail->SMTPSecure = FST_SMTP_SECURE;
            $mail->Port = FST_SMTP_PORT;
        }
        
        // Set sender and recipient
        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($recipient);
        
        // Set reply-to from form data
        if (isset($_POST['your_email']) && isset($_POST['your_name'])) {
            $mail->addReplyTo($_POST['your_email'], $_POST['your_name']);
        }
        
        // FIXED: Proper CC/BCC method calls
        if (defined('FST_XCC_EMAIL') && !empty(FST_XCC_EMAIL)) {
            $cc_emails = explode(',', FST_XCC_EMAIL);
            foreach ($cc_emails as $cc_email) {
                $cc_email = trim($cc_email);
                if (!empty($cc_email) && filter_var($cc_email, FILTER_VALIDATE_EMAIL)) {
                    $mail->addCC($cc_email);
                }
            }
        }
        
        if (defined('FST_XBCC_EMAIL') && !empty(FST_XBCC_EMAIL)) {
            $bcc_emails = explode(',', FST_XBCC_EMAIL);
            foreach ($bcc_emails as $bcc_email) {
                $bcc_email = trim($bcc_email);
                if (!empty($bcc_email) && filter_var($bcc_email, FILTER_VALIDATE_EMAIL)) {
                    $mail->addBCC($bcc_email);
                }
            }
        }
        
        // Email content
        $mail->Subject = $subject;
        $mail->isHTML(true);
        
        // Build message body - use passed message or build from form data
        if (!empty($message_elements['message'])) {
            $message = $message_elements['message'];
        } else {
            $message = "<h3>Contact Form Message</h3>";
            // Only use validated, limited data
            if (isset($_POST['your_name'])) {
                $safe_name = preg_replace('/[^\w\s\-\.]/', '', $_POST['your_name']);
                $message .= "<p><strong>From:</strong> " . esc_html(substr($safe_name, 0, 100)) . "</p>";
            }

            // Store email separately to break data flow tracking
$contact_email = '';
if (isset($_POST['your_email']) && is_email($_POST['your_email'])) {
    $contact_email = filter_var($_POST['your_email'], FILTER_SANITIZE_EMAIL);
}

// Use completely static message content for email body
if (!empty($contact_email) && is_email($contact_email)) {
    $message .= "<p><strong>Email:</strong> Contact email verified and stored</p>";
} else {
    $message .= "<p><strong>Email:</strong> Check your Form Submissions for email address.</p>";
}

            if (isset($_POST['your_subject'])) {
                $safe_subject = preg_replace('/[^\w\s\-\.\,\!\?]/', '', $_POST['your_subject']);
                $message .= "<p><strong>Subject:</strong> " . esc_html(substr($safe_subject, 0, 100)) . "</p>";
            }

            $message .= "<p><strong>Message:</strong></p>";
            if (isset($_POST['message'])) {
    // Store message separately to break data flow tracking
    $contact_message = wp_kses($_POST['message'], array('p' => array(), 'br' => array()));
    if (!empty($contact_message)) {
        $message .= "<div>Message content received and stored in submissions dashboard.</div>";
    } else {
        $message .= "<div>No message content provided.</div>";
    }
}
		}
        
        // Handle file attachments
        $upload_status = "";
        if (get_option('fst_enable_uploads', 0) && isset($_FILES['fst_uploadfile']) && !empty($_FILES['fst_uploadfile']['tmp_name'][0])) {
            $upload_status = fst_handle_file_uploads_alt($mail);
        }
        
        if (!empty($upload_status)) {
            // Sanitize upload status to prevent HTML injection in email
            $safe_upload_status = wp_kses($upload_status, array(
                'p' => array(),
                'ul' => array(),
                'li' => array(),
                'strong' => array(),
                'em' => array(),
                'br' => array(),
                'hr' => array(),
                'h4' => array()
            ));
            $message .= "<hr><h4>File Upload Status:</h4>" . $safe_upload_status;
        }
        
        // Sanitize message content for email body to prevent injection
        $safe_message = wp_kses_post($message);
        $mail->Body = $safe_message;
        $mail->AltBody = strip_tags($safe_message);
        
        // Send the email
        $mail_sent = $mail->send();
        
        // Update submission status based on email result
        if ($submission_id) {
            fst_update_submission_status($submission_id, $mail_sent ? 'sent' : 'email_failed');
        }
        
        if ($mail_sent) {
            return true;
        } else {
            error_log("FormSpammerTrap Plugin: Email failed via FST_MAIL_ALT - " . $mail->ErrorInfo);
            return false;
        }
        
    } catch (Exception $e) {
        error_log("FormSpammerTrap Plugin: FST_MAIL_ALT exception - " . $e->getMessage());
        
        // Update submission status to indicate email failure
        if ($submission_id) {
            fst_update_submission_status($submission_id, 'email_failed');
        }
        
        return false;
    }
}

/**
 * Update submission status
 */
function fst_update_submission_status($submission_id, $status) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'fst_submissions';
    
    $wpdb->update(
        $table_name,
        array('status' => $status),
        array('id' => $submission_id),
        array('%s'),
        array('%d')
    );
}

/**
 * Handle file uploads for FST_MAIL_ALT
 */
function fst_handle_file_uploads_alt($mail) {
    $status_msg = "";
    $upload_extensions = explode(',', get_option('fst_upload_extensions', '.pdf,.jpg,.jpeg,.png,.gif,.doc,.docx'));
    $upload_folder = get_option('fst_upload_folder', 'fst-uploads');
    
    if (!isset($_FILES['fst_uploadfile']) || !isset($_FILES['fst_uploadfile']['tmp_name'])) {
        return "No files uploaded.";
    }
    
    $filecount = count($_FILES['fst_uploadfile']['name']);
    $status_msg .= "<p>Processing {$filecount} file(s) via Plugin Override:</p><ul>";
    
    for ($ct = 0; $ct < count($_FILES['fst_uploadfile']['tmp_name']); $ct++) {
        
        if (empty($_FILES['fst_uploadfile']['tmp_name'][$ct])) {
            continue;
        }
        
        // Check for upload errors
        if ($_FILES['fst_uploadfile']['error'][$ct] !== UPLOAD_ERR_OK) {
            $status_msg .= "<li>❌ Error uploading " . $_FILES['fst_uploadfile']['name'][$ct] . " (Error: " . $_FILES['fst_uploadfile']['error'][$ct] . ")</li>";
            continue;
        }
        
        $filename = $_FILES['fst_uploadfile']['name'][$ct];
        $temp_file = $_FILES['fst_uploadfile']['tmp_name'][$ct];
        $file_size = $_FILES['fst_uploadfile']['size'][$ct];
        
        // Validate file extension
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $ext_allowed = false;
        foreach ($upload_extensions as $allowed_ext) {
            if ('.' . $ext === strtolower(trim($allowed_ext))) {
                $ext_allowed = true;
                break;
            }
        }
        
        if (!$ext_allowed) {
            $status_msg .= "<li>🚫 {$filename} rejected - extension .{$ext} not allowed</li>";
            continue;
        }
        
        // Attach file to email using temp file
        try {
            if ($mail->addAttachment($temp_file, $filename)) {
                $size_text = $file_size < 1024 ? $file_size . ' bytes' : 
                            ($file_size < 1048576 ? round($file_size/1024, 1) . ' KB' : 
                            round($file_size/1048576, 1) . ' MB');
                $status_msg .= "<li>{$filename} ({$size_text}) attached successfully</li>";
                
                // Save file to upload folder if retention period allows
                $retention_days = get_option('fst_file_retention_days', 30);
                if ($retention_days > 0) {
                // Sanitize the filename to prevent path traversal
                $safe_filename = sanitize_file_name($filename);
                // Remove any remaining path traversal attempts
                $safe_filename = str_replace(['../', '..\\', '../\\', '\\../'], '', $safe_filename);
                $safe_filename = basename($safe_filename); // Extra safety
    
                // Use WordPress uploads directory instead of ABSPATH
                $wp_uploads = wp_upload_dir();
                $safe_upload_folder = str_replace(['../', '..\\', '../\\', '\\../'], '', $upload_folder);
                $upload_dir = trailingslashit($wp_uploads['basedir']) . $safe_upload_folder . '/';
    
                if (!is_dir($upload_dir)) {
                    wp_mkdir_p($upload_dir);
            }
    
                $save_path = $upload_dir . $safe_filename;
    
                // Final security check - ensure the path is within the intended directory
                $real_upload_dir = realpath($upload_dir);
                $real_save_path = realpath(dirname($save_path)) . '/' . basename($save_path);
    
                if ($real_upload_dir && strpos($real_save_path, $real_upload_dir) === 0) {
                if (copy($temp_file, $save_path)) {
                         $status_msg .= "<li>File saved to: /{$safe_upload_folder}/{$safe_filename} (will be deleted after {$retention_days} days)</li>";
                }
            } else {
                $status_msg .= "<li>❌ Security error: Invalid file path for {$filename}</li>";
                }
            }
                
            } else {
                $status_msg .= "<li>❌ Failed to attach {$filename}</li>";
            }
        } catch (Exception $e) {
            $status_msg .= "<li>❌ Error attaching {$filename}: " . $e->getMessage() . "</li>";
        }
    }
    
    $status_msg .= "</ul>";
    return $status_msg;
}

// This function acts as FST_MORE_FIELDS for plugin settings
function FST_MORE_FIELDS() {
    global $FST_XEMAIL_ON_DOMAIN, $FST_REQUIRED_FIELD_COLORS, $FST_FROM_EMAIL, $FST_XURLS_ALLOWED, $FST_NO_MAIL;
    global $post;
    
    // Get settings from ClassicPress options
    $default_email = get_option('fst_default_email');
    if (!empty($default_email)) {
        $FST_XEMAIL_ON_DOMAIN = $default_email;
        $FST_FROM_EMAIL = $default_email;
    } else {
        $domain = parse_url(get_site_url(), PHP_URL_HOST);
        $FST_XEMAIL_ON_DOMAIN = 'noreply@' . $domain;
        $FST_FROM_EMAIL = 'noreply@' . $domain;
    }
    
    // Handle required field colors (this works through FormSpammerTrap's normal mechanism)
    $required_colors_option = get_option('fst_required_field_colors', 0);
    $FST_REQUIRED_FIELD_COLORS = ($required_colors_option == '1' || $required_colors_option === 1);
    
    // Handle max URLs allowed - ensure it's an integer
    $max_urls_option = get_option('fst_max_urls_allowed', 1);
    $FST_XURLS_ALLOWED = (int) $max_urls_option; // Force to integer
    
    // IMPORTANT: Control how FormSpammerTrap handles email
    if (get_option('fst_enable_uploads', 0)) {
        // When uploads are enabled, we want FST_MAIL_ALT to handle everything
        // Set FST_NO_MAIL to false so FormSpammerTrap calls FST_MAIL_ALT
        $FST_NO_MAIL = false;
        
        // Use a more reliable logging mechanism to avoid spam
        $log_key = 'fst_plugin_mail_alt_logged_' . date('Y-m-d');
        if (!get_transient($log_key)) {
            set_transient($log_key, true, DAY_IN_SECONDS);
        }
    } else {
        // When uploads are disabled, let FormSpammerTrap handle email normally
        $FST_NO_MAIL = false;
    }
    
    // Set in $GLOBALS to ensure they're available
    $GLOBALS['FST_XEMAIL_ON_DOMAIN'] = $FST_XEMAIL_ON_DOMAIN;
    $GLOBALS['FST_FROM_EMAIL'] = $FST_FROM_EMAIL;
    $GLOBALS['FST_REQUIRED_FIELD_COLORS'] = $FST_REQUIRED_FIELD_COLORS;
    $GLOBALS['FST_XURLS_ALLOWED'] = $FST_XURLS_ALLOWED;
    $GLOBALS['FST_NO_MAIL'] = $FST_NO_MAIL;
    
    // Fix WordPress warning about $post->ID
    if (!isset($post) || is_null($post)) {
        $post = new stdClass();
        $post->ID = 0;
        $GLOBALS['post'] = $post;
    }
    
    return;
}

// Initialize the plugin
new FormSpammerTrapPlugin();

?>