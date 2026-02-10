<?php
/**
 * Lead Form Shortcode + AJAX Handler
 * Shortcode: [nucleus_lead_form]
 */

if (!defined('ABSPATH'))
    exit;

// Lead Form Shortcode
function nucleus_core_lead_form_shortcode($atts)
{
    $args = shortcode_atts(array(
        'title' => 'Ready to accelerate your growth?',
        'description' => 'Partner with Nucleus Advisory to unlock your organization\'s full potential.',
    ), $atts);

    ob_start();
    ?>
    <style>
        .nucleus-form {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .nucleus-form .form-group {
            margin-bottom: 1rem;
        }

        .nucleus-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
            color: #1e293b;
        }

        .nucleus-form input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .nucleus-form button {
            width: 100%;
            padding: 0.75rem;
            background: #0f172a;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
        }

        .nucleus-form button:hover {
            background: #020617;
        }

        .form-message {
            display: none;
            margin-top: 1rem;
            padding: 0.75rem;
            border-radius: 4px;
            font-size: 0.9rem;
            text-align: center;
        }

        .form-message.success {
            background-color: #dcfce7;
            color: #166534;
        }

        .form-message.error {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>

    <div class="nucleus-form-wrapper">
        <?php if ($args['title']): ?>
            <h3 style="margin-bottom: 10px;">
                <?php echo esc_html($args['title']); ?>
            </h3>
        <?php endif; ?>
        <?php if ($args['description']): ?>
            <p style="margin-bottom: 20px; color: #64748b;">
                <?php echo esc_html($args['description']); ?>
            </p>
        <?php endif; ?>

        <form id="nucleus-lead-form" class="nucleus-form">
            <?php wp_nonce_field('nucleus_lead_nonce', 'security'); ?>
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" required placeholder="John Doe">
            </div>
            <div class="form-group">
                <label>Work Email *</label>
                <input type="email" name="work_email" required placeholder="john@company.com">
            </div>
            <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="company_name" placeholder="Acme Corp">
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" placeholder="+1 (555) 000-0000">
            </div>
            <button type="submit" class="form-submit-btn">
                <span class="btn-text">Request Consultation</span>
                <span class="btn-loader" style="display:none;">Processing...</span>
            </button>
            <div class="form-message"></div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const leadForm = document.getElementById('nucleus-lead-form');
            if (leadForm) {
                leadForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const submitBtn = leadForm.querySelector('.form-submit-btn');
                    const btnText = leadForm.querySelector('.btn-text');
                    const btnLoader = leadForm.querySelector('.btn-loader');
                    const messageBox = leadForm.querySelector('.form-message');

                    submitBtn.disabled = true;
                    btnText.style.display = 'none';
                    btnLoader.style.display = 'inline-block';
                    messageBox.style.display = 'none';

                    const formData = new FormData(leadForm);
                    formData.append('action', 'nucleus_submit_lead');

                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            submitBtn.disabled = false;
                            btnText.style.display = 'inline-block';
                            btnLoader.style.display = 'none';
                            if (data.success) {
                                messageBox.className = 'form-message success';
                                messageBox.textContent = 'Success! We will be in touch.';
                                messageBox.style.display = 'block';
                                leadForm.reset();
                                // Analytics Trigger (Direct GA4)
                                if (typeof gtag === 'function') { gtag('event', 'generate_lead', { 'method': 'testing_lab_form' }); }
                            } else {
                                messageBox.className = 'form-message error';
                                messageBox.textContent = data.data || 'Error submitting form';
                                messageBox.style.display = 'block';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            submitBtn.disabled = false;
                            btnText.style.display = 'inline-block';
                            btnLoader.style.display = 'none';
                            messageBox.className = 'form-message error';
                            messageBox.textContent = 'Connection error.';
                            messageBox.style.display = 'block';
                        });
                });
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('nucleus_lead_form', 'nucleus_core_lead_form_shortcode');

// AJAX Submission Handler
function nucleus_core_handle_submission()
{
    if (!check_ajax_referer('nucleus_lead_nonce', 'security', false)) {
        wp_send_json_error('Security check failed');
    }

    $name = sanitize_text_field($_POST['full_name']);
    $email = sanitize_email($_POST['work_email']);
    $company = sanitize_text_field($_POST['company_name']);
    $phone = sanitize_text_field($_POST['phone']);

    global $wpdb;
    $table_name = $wpdb->prefix . 'nucleus_leads_testing';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        nucleus_core_activate_table();
    }

    $result = $wpdb->insert($table_name, array(
        'name' => $name,
        'email' => $email,
        'company' => $company,
        'phone' => $phone,
        'submitted_at' => current_time('mysql')
    ));

    if ($result) {
        wp_send_json_success('Lead saved (Test DB)');
    } else {
        wp_send_json_error('Database error');
    }
}
add_action('wp_ajax_nucleus_submit_lead', 'nucleus_core_handle_submission');
add_action('wp_ajax_nopriv_nucleus_submit_lead', 'nucleus_core_handle_submission');
