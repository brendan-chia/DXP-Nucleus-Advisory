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

// Hook CSV export action to admin_init to ensure headers are sent before any output
function nucleus_core_handle_csv_export()
{
    if (isset($_POST['nucleus_export_csv']) && check_admin_referer('nucleus_export_csv_action')) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nucleus_leads_testing';
        $filename = 'nucleus-leads-' . date('Y-m-d') . '.csv';

        // Clean buffer to prevent HTML from mixing with CSV
        if (ob_get_level())
            ob_end_clean();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Name', 'Email', 'Company', 'Phone', 'Date'));

        // Get all data without pagination/filters for the export
        $all_leads = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submitted_at DESC");

        foreach ($all_leads as $row) {
            fputcsv($output, array(
                $row->id,
                $row->name,
                $row->email,
                $row->company,
                $row->phone,
                $row->submitted_at
            ));
        }

        fclose($output);
        exit;
    }
}
add_action('admin_init', 'nucleus_core_handle_csv_export');

function nucleus_core_render_leads_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'nucleus_leads_testing';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        echo '<div class="wrap"><h1>No Leads Yet</h1><p>Test table does not exist yet. Submit a form to create it.</p></div>';
        return;
    }

    // --- 2. HANDLE FILTERS ---
    $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $date_start = isset($_GET['date_start']) ? sanitize_text_field($_GET['date_start']) : '';
    $date_end = isset($_GET['date_end']) ? sanitize_text_field($_GET['date_end']) : '';

    $where_clauses = array("1=1");
    if ($search_query) {
        $where_clauses[] = $wpdb->prepare("(name LIKE %s OR email LIKE %s OR company LIKE %s)", "%$search_query%", "%$search_query%", "%$search_query%");
    }
    if ($date_start) {
        $where_clauses[] = $wpdb->prepare("submitted_at >= %s", $date_start . ' 00:00:00');
    }
    if ($date_end) {
        $where_clauses[] = $wpdb->prepare("submitted_at <= %s", $date_end . ' 23:59:59');
    }

    $where_sql = implode(' AND ', $where_clauses);
    $leads = $wpdb->get_results("SELECT * FROM $table_name WHERE $where_sql ORDER BY submitted_at DESC LIMIT 100");

    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Nucleus Advisory Leads (Testing Lab)</h1>

        <!-- FILTERS BAR -->
        <div class="tablenav top"
            style="height: auto; padding: 10px; background: #fff; border: 1px solid #ccd0d4; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
            <form method="get" style="display: flex; gap: 10px; align-items: center;">
                <input type="hidden" name="page" value="nucleus-leads" />

                <!-- Search -->
                <input type="search" name="s" value="<?php echo esc_attr($search_query); ?>"
                    placeholder="Search name, email, company..." style="width: 250px;">

                <!-- Date Range -->
                <input type="date" name="date_start" value="<?php echo esc_attr($date_start); ?>">
                <span style="color: #666;">to</span>
                <input type="date" name="date_end" value="<?php echo esc_attr($date_end); ?>">

                <button type="submit" class="button button-primary">Filter Results</button>
                <a href="<?php echo admin_url('admin.php?page=nucleus-leads'); ?>" class="button">Reset</a>
            </form>

            <!-- Export Button -->
            <form method="post">
                <?php wp_nonce_field('nucleus_export_csv_action'); ?>
                <button type="submit" name="nucleus_export_csv" class="button button-secondary"
                    style="border-color: #2271b1; color: #2271b1;">
                    <span class="dashicons dashicons-download" style="vertical-align: middle;"></span> Export CSV
                </button>
            </form>
        </div>

        <p style="color: #666; font-style: italic;">
            Showing recent 100 results. Data stored in <code><?php echo esc_html($table_name); ?></code>.
        </p>

                <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Phone</th>
                    <th width="160">Date Submitted</th>
                    <th width="80">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px; color: #666;">
                            No leads found matching your criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($leads as $lead): ?>
                        <tr>
                            <td>#<?php echo esc_html($lead->id); ?></td>
                            <td>
                                <strong><?php echo esc_html($lead->name); ?></strong>
                            </td>
                            <td>
                                <a href="mailto:<?php echo esc_attr($lead->email); ?>" style="text-decoration: none;">
                                    <?php echo esc_html($lead->email); ?>
                                </a>
                            </td>
                            <td>
                                <?php if($lead->company): ?>
                                    <span class="dashicons dashicons-building" style="font-size: 14px; color: #888;"></span> 
                                    <?php echo esc_html($lead->company); ?>
                                <?php else: ?>
                                    <span style="color: #ccc;">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($lead->phone); ?></td>
                            <td>
                                <?php echo date('M j, Y g:i a', strtotime($lead->submitted_at)); ?>
                            </td>
                            <td>
                                <?php 
                                    $delete_url = wp_nonce_url(
                                        admin_url('admin.php?page=nucleus-leads&action=delete&id=' . $lead->id), 
                                        'nucleus_delete_lead_' . $lead->id
                                    ); 
                                ?>
                                <a href="<?php echo $delete_url; ?>" onclick="return confirm('Are you sure you want to delete this lead? This cannot be undone.');" style="color: #a00; text-decoration: none;">
                                    <span class="dashicons dashicons-trash"></span> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
