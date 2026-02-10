<?php
/**
 * Admin Dashboard: View Leads
 * Adds a 'Testing Dashboard' menu in WP Admin
 */

if (!defined('ABSPATH'))
    exit;

function nucleus_core_add_admin_menu()
{
    add_menu_page(
        'Nucleus Testing',
        'Testing Dashboard',
        'manage_options',
        'nucleus-leads',
        'nucleus_core_render_leads_page',
        'dashicons-beaker',
        6
    );
}
add_action('admin_menu', 'nucleus_core_add_admin_menu');

function nucleus_core_render_leads_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'nucleus_leads_testing';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        echo '<div class="wrap"><h1>No Leads Yet</h1><p>Test table does not exist yet. Submit a form to create it.</p></div>';
        return;
    }

    $leads = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submitted_at DESC LIMIT 50");
    ?>
    <div class="wrap">
        <h1 style="color: #d63638;">Nucleus Advisory Leads (Testing Lab)</h1>
        <p>Data stored in <code><?php echo esc_html($table_name); ?></code>.</p>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Phone</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                    <tr>
                        <td colspan="6">No leads found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($leads as $lead): ?>
                        <tr>
                            <td>
                                <?php echo esc_html($lead->id); ?>
                            </td>
                            <td><strong>
                                    <?php echo esc_html($lead->name); ?>
                                </strong></td>
                            <td><a href="mailto:<?php echo esc_attr($lead->email); ?>">
                                    <?php echo esc_html($lead->email); ?>
                                </a></td>
                            <td>
                                <?php echo esc_html($lead->company); ?>
                            </td>
                            <td>
                                <?php echo esc_html($lead->phone); ?>
                            </td>
                            <td>
                                <?php echo esc_html($lead->submitted_at); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
