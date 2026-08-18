<?php
/**
 * NCTB Messaging
 * Handles teacher-student messaging system
 */

if (!defined('ABSPATH')) {
    exit;
}

class NCTB_Messaging {
    private static $instance = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        // Register custom post type for messages
        add_action('init', array($this, 'register_message_post_type'));
        // AJAX endpoints
        add_action('wp_ajax_nctb_send_message', array($this, 'send_message'));
        add_action('wp_ajax_nopriv_nctb_send_message', array($this, 'send_message'));
        add_action('wp_ajax_nctb_get_messages', array($this, 'get_messages'));
        add_action('wp_ajax_nopriv_nctb_get_messages', array($this, 'get_messages'));
        add_action('wp_ajax_nctb_mark_message_read', array($this, 'mark_message_read'));
        add_action('wp_ajax_nopriv_nctb_mark_message_read', array($this, 'mark_message_read'));
        // Add messaging tab to dashboards
        add_filter('nctb_student_dashboard_tabs', array($this, 'add_messaging_tab_student'));
        add_filter('nctb_teacher_dashboard_tabs', array($this, 'add_messaging_tab_teacher'));
    }

    /**
     * Register the message post type
     */
    public function register_message_post_type() {
        $args = array(
            'label' => __('Messages', 'nctb-learning-hub'),
            'labels' => array(
                'name' => __('Messages', 'nctb-learning-hub'),
                'singular_name' => __('Message', 'nctb-learning-hub'),
                'add_new' => __('Send Message', 'nctb-learning-hub'),
                'add_new_item' => __('Send New Message', 'nctb-learning-hub'),
                'edit_item' => __('Edit Message', 'nctb-learning-hub'),
                'new_item' => __('New Message', 'nctb-learning-hub'),
                'view_item' => __('View Message', 'nctb-learning-hub'),
                'search_items' => __('Search Messages', 'nctb-learning-hub'),
                'not_found' => __('No messages found', 'nctb-learning-hub'),
                'not_found_in_trash' => __('No messages found in Trash', 'nctb-learning-hub'),
            ),
            'public' => false, // Not publicly queryable
            'show_ui' => true,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array('title', 'editor'),
        );
        register_post_type('nctb_message', $args);
    }

    /**
     * Send a message via AJAX
     */
    public function send_message() {
        // Security checks
        check_ajax_referer('nctb_ajax_nonce', 'nonce');
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Please log in'), 401);
        }

        $sender_id = get_current_user_id();
        $recipient_id = isset($_POST['recipient_id']) ? absint($_POST['recipient_id']) : 0;
        $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
        $message = isset($_POST['message']) ? wp_kses_post($_POST['message']) : '';

        if (!$recipient_id || !$subject || !$message) {
            wp_send_json_error(array('message' => 'Missing required fields'));
        }

        // Verify that sender is allowed to send to recipient (teacher to student or student to teacher)
        $sender = new WP_User($sender_id);
        $recipient = new WP_User($recipient_id);
        $sender_roles = (array) $sender->roles;
        $recipient_roles = (array) $recipient->roles;

        // Allow if sender is teacher and recipient is student, or vice versa
        $sender_is_teacher = !empty(array_intersect($sender_roles, array('author', 'editor', 'administrator')));
        $recipient_is_student = in_array('subscriber', $recipient_roles);
        $sender_is_student = in_array('subscriber', $sender_roles);
        $recipient_is_teacher = !empty(array_intersect($recipient_roles, array('author', 'editor', 'administrator')));

        if ( ! ( ($sender_is_teacher && $recipient_is_student) || ($sender_is_student && $recipient_is_teacher) ) ) {
            wp_send_json_error(array('message' => 'You are not allowed to send a message to this user'));
        }

        // Create the message post
        $message_post = array(
            'post_type' => 'nctb_message',
            'post_title' => $subject,
            'post_content' => $message,
            'post_status' => 'private',
            'post_author' => $sender_id,
        );

        $message_id = wp_insert_post($message_post);
        if (is_wp_error($message_id)) {
            wp_send_json_error(array('message' => $message_id->get_error_message()));
        }

        // Add metadata for sender and recipient
        update_post_meta($message_id, 'nctb_message_sender', $sender_id);
        update_post_meta($message_id, 'nctb_message_recipient', $recipient_id);
        update_post_meta($message_id, 'nctb_message_timestamp', current_time('mysql'));
        update_post_meta($message_id, 'nctb_message_read', 0); // 0 = unread, 1 = read

        wp_send_json_success(array(
            'message' => 'Message sent successfully',
            'message_id' => $message_id
        ));
    }

    /**
     * Get messages for the current user
     */
    public function get_messages() {
        // Security checks
        check_ajax_referer('nctb_ajax_nonce', 'nonce');
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Please log in'), 401);
        }

        $user_id = get_current_user_id();
        $folder = isset($_POST['folder']) ? sanitize_text_field($_POST['folder']) : 'inbox'; // inbox or sent

        $args = array(
            'post_type' => 'nctb_message',
            'posts_per_page' => -1,
            'post_status' => 'private',
        );

        if ($folder === 'inbox') {
            $args['meta_query'] = array(
                array(
                    'key' => 'nctb_message_recipient',
                    'value' => $user_id,
                    'compare' => '='
                )
            );
        } elseif ($folder === 'sent') {
            $args['meta_query'] = array(
                array(
                    'key' => 'nctb_message_sender',
                    'value' => $user_id,
                    'compare' => '='
                )
            );
        }

        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = 'nctb_message_timestamp';
        $args['order'] = 'DESC';

        $messages_query = new WP_Query($args);
        $messages = array();

        if ($messages_query->have_posts()) {
            while ($messages_query->have_posts()) {
                $messages_query->the_post();
                $message_id = get_the_ID();
                $sender_id = get_post_meta($message_id, 'nctb_message_sender', true);
                $recipient_id = get_post_meta($message_id, 'nctb_message_recipient', true);
                $subject = get_the_title();
                $message = get_post_field('post_content', $message_id);
                $timestamp = get_post_meta($message_id, 'nctb_message_timestamp', true);
                $is_read = get_post_meta($message_id, 'nctb_message_read', true);

                $sender = new WP_User($sender_id);
                $recipient = new WP_User($recipient_id);

                $messages[] = array(
                    'id' => $message_id,
                    'sender_name' => $sender->display_name,
                    'recipient_name' => $recipient->display_name,
                    'subject' => $subject,
                    'message' => $message,
                    'timestamp' => $timestamp,
                    'is_read' => (bool) $is_read,
                    'folder' => $folder
                );
            }
        }
        wp_reset_postdata();

        wp_send_json_success(array('messages' => $messages));
    }

    /**
     * Mark a message as read
     */
    public function mark_message_read() {
        // Security checks
        check_ajax_referer('nctb_ajax_nonce', 'nonce');
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Please log in'), 401);
        }

        $message_id = isset($_POST['message_id']) ? absint($_POST['message_id']) : 0;
        if (!$message_id) {
            wp_send_json_error(array('message' => 'Invalid message ID'));
        }

        // Verify that the current user is the recipient
        $recipient_id = get_post_meta($message_id, 'nctb_message_recipient', true);
        if ($recipient_id != get_current_user_id()) {
            wp_send_json_error(array('message' => 'You are not authorized to mark this message as read'));
        }

        update_post_meta($message_id, 'nctb_message_read', 1);

        wp_send_json_success(array('message' => 'Message marked as read'));
    }

    /**
     * Add messaging tab to student dashboard
     */
    public function add_messaging_tab_student($tabs) {
        $tabs['messaging'] = array(
            'title' => __('Messages', 'nctb-learning-hub'),
            'icon' => '💬',
            'callback' => array($this, 'render_messaging_tab_student')
        );
        return $tabs;
    }

    /**
     * Add messaging tab to teacher dashboard
     */
    public function add_messaging_tab_teacher($tabs) {
        $tabs['messaging'] = array(
            'title' => __('Messages', 'nctb-learning-hub'),
            'icon' => '💬',
            'callback' => array($this, 'render_messaging_tab_teacher')
        );
        return $tabs;
    }

    /**
     * Render messaging tab for student
     */
    public function render_messaging_tab_student() {
        $this->render_messaging_tab('student');
    }

    /**
     * Render messaging tab for teacher
     */
    public function render_messaging_tab_teacher() {
        $this->render_messaging_tab('teacher');
    }

    /**
     * Render messaging tab (common)
     */
    public function render_messaging_tab($user_type) {
        $user_id = get_current_user_id();
        ?>
        <div class="messaging-container">
            <div class="messaging-tabs">
                <button class="messaging-tab active" data-folder="inbox">Inbox</button>
                <button class="messaging-tab" data-folder="sent">Sent</button>
            </div>
            <div class="messaging-content">
                <div id="messaging-loading" class="loading">Loading messages...</div>
                <div id="messaging-inbox" class="messaging-folder"></div>
                <div id="messaging-sent" class="messaging-folder"></div>
                <div id="messaging-compose" class="messaging-compose">
                    <h3>Compose New Message</h3>
                    <form id="messaging-compose-form">
                        <input type="hidden" id="messaging-recipient-id" name="recipient_id" value="">
                        <label for="messaging-subject">Subject:</label>
                        <input type="text" id="messaging-subject" name="subject" required>
                        <label for="messaging-message">Message:</label>
                        <textarea id="messaging-message" name="message" rows="5" required></textarea>
                        <button type="submit" class="button-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            var nonce = '<?php echo wp_create_nonce('nctb_ajax_nonce'); ?>';
            var userId = <?php echo $user_id; ?>;
            var userType = '<?php echo $user_type; ?>';

            function loadMessages(folder) {
                $('#messaging-loading').show();
                $('#messaging-' + folder).html('');

                $.post(ajaxurl, {
                    action: 'nctb_get_messages',
                    folder: folder,
                    nonce: nonce
                }, function(response) {
                    $('#messaging-loading').hide();
                    if (response.success) {
                        var messages = response.messages;
                        if (messages.length === 0) {
                            $('#messaging-' + folder).html('<p>No messages found.</p>');
                            return;
                        }
                        var html = '';
                        $.each(messages, function(index, message) {
                            var readClass = message.is_read ? 'read' : 'unread';
                            html += '<div class="message-item ' + readClass + '" data-message-id="' + message.id + '">';
                            html += '<div class="message-header">';
                            html += '<strong>' + (folder === 'inbox' ? message.sender_name : message.recipient_name) + '</strong>';
                            html += '<span class="message-time">' + message.timestamp + '</span>';
                            html += '</div>';
                            html += '<div class="message-subject">' + message.subject + '</div>';
                            html += '<div class="message-excerpt">' + message.message.substring(0, 100) + (message.message.length > 100 ? '...' : '') + '</div>';
                            html += '</div>';
                        });
                        $('#messaging-' + folder).html(html);
                    } else {
                        $('#messaging-' + folder).html('<p>Error loading messages.</p>');
                    }
                });
            }

            // Tab switching
            $('.messaging-tab').click(function() {
                $('.messaging-tab').removeClass('active');
                $(this).addClass('active');
                var folder = $(this).data('folder');
                loadMessages(folder);
            });

            // Load inbox by default
            loadMessages('inbox');

            // Compose form submission
            $('#messaging-compose-form').submit(function(e) {
                e.preventDefault();
                var recipientId = $('#messaging-recipient-id').val();
                var subject = $('#messaging-subject').val();
                var message = $('#messaging-message').val();

                if (!recipientId || !subject || !message) {
                    alert('Please fill in all fields');
                    return;
                }

                // Show loading
                var submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).text('Sending...');

                $.post(ajaxurl, {
                    action: 'nctb_send_message',
                    recipient_id: recipientId,
                    subject: subject,
                    message: message,
                    nonce: nonce
                }, function(response) {
                    submitBtn.prop('disabled', false).text('Send Message');
                    if (response.success) {
                        alert('Message sent successfully');
                        $('#messaging-subject').val('');
                        $('#messaging-message').val('');
                        // Refresh sent folder
                        loadMessages('sent');
                        // Also refresh inbox in case it's a reply
                        loadMessages('inbox');
                    } else {
                        alert(response.data || 'Failed to send message');
                    }
                });
            });

            // Click to view full message (optional)
            $(document).on('click', '.message-item', function() {
                var messageId = $(this).data('message-id');
                // In a full implementation, we would show a modal with the full message
                // For now, just mark as read if it's in the inbox
                if ($('.messaging-tab.active').data('folder') === 'inbox') {
                    $.post(ajaxurl, {
                        action: 'nctb_mark_message_read',
                        message_id: messageId,
                        nonce: nonce
                    }, function(response) {
                        if (response.success) {
                            $(this).removeClass('unread').addClass('read');
                        }
                    });
                }
            });
        });
        </script>
        <style>
        .messaging-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .messaging-tabs {
            display: flex;
            margin-bottom: 20px;
        }
        .messaging-tab {
            padding: 10px 20px;
            background: #f8f9fa;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .messaging-tab.active {
            background: #2c5aa0;
            color: white;
        }
        .messaging-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .messaging-folder {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
        }
        .message-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
            cursor: pointer;
            transition: background 0.2s;
        }
        .message-item:hover {
            background: #f8f9fa;
        }
        .message-item.unread {
            background: #e9f7ff;
            font-weight: bold;
        }
        .message-item.read {
            background: #f8f9fa;
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .message-time {
            font-size: 14px;
            color: #666;
        }
        .message-subject {
            font-size: 18px;
            margin-bottom: 5px;
            color: #333;
        }
        .message-excerpt {
            font-size: 14px;
            color: #666;
        }
        .messaging-compose {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background: #f8f9fa;
        }
        .messaging-compose label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .messaging-compose input,
        .messaging-compose textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .messaging-compose button {
            background: #2c5aa0;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .messaging-compose button:hover {
            background: #1e3a8a;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        </style>
        <?php
    }
}

NCTB_Messaging::instance();