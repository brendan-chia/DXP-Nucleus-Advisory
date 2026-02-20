# Google Sheets + Looker Studio Integration
## Auto-Sync Form Submissions → Looker Dashboard

**Purpose:** Every time a visitor submits the Contact Form 7 on the testing lab page,
their details are automatically pushed to a Google Sheet. Looker Studio then reads that
sheet in real time — giving the company one unified dashboard for both form leads and
Google Analytics traffic data.

**Author:** Development Team  
**Last Updated:** 2026-02-20  

---

## 📐 Architecture Overview

```
User submits CF7 form
        ↓
form-handler.php  ──► Saves to WordPress DB (wp_nucleus_leads_testing)
        │
        └──────────► Google Sheets API  ──► Google Sheet (auto row append)
                                                    ↓
                                            Looker Studio Dashboard
                                                    ↑
                                            Google Analytics (GA4)
```

**Result:** One Looker dashboard shows both form lead details AND website analytics side by side.

---

## ✅ Prerequisites

Before writing any code, the following must be set up:

| # | Task | Who |
|---|------|-----|
| 1 | Google Cloud Project with Sheets API enabled | Dev team |
| 2 | Google Service Account + JSON key file | Dev team |
| 3 | Google Sheet created and shared with service account | Dev team |
| 4 | Looker Studio account (free via Google) | Syahmi / Dev team |

---

## 🔧 Step-by-Step Setup

### Step 1 — Create a Google Cloud Service Account

A Service Account is a "robot Google account" that your WordPress plugin
uses to write rows to the Google Sheet without any manual login.

1. Go to [https://console.cloud.google.com](https://console.cloud.google.com)
2. Create a new project (e.g. `nucleus-advisory-leads`)
3. In the left menu → **APIs & Services** → **Library**
4. Search for **Google Sheets API** → Click **Enable**
5. In the left menu → **APIs & Services** → **Credentials**
6. Click **Create Credentials** → **Service Account**
7. Give it a name (e.g. `nucleus-sheets-writer`) → Click **Done**
8. Click on the newly created service account → **Keys** tab
9. Click **Add Key** → **Create new key** → Select **JSON** → Download

> ⚠️ **Keep this JSON file private.** It is a credential file. Do NOT commit it to GitHub.

**Upload the JSON file to the server:**
```
/wp-content/plugins/DXP-Nucleus-Advisory/includes/google-credentials.json
```

---

### Step 2 — Create & Share the Google Sheet

1. Go to [https://sheets.google.com](https://sheets.google.com) and create a new sheet
2. Name it: `Nucleus Advisory — Form Leads`
3. In **Row 1**, add these headers exactly:

| A | B | C | D | E | F |
|---|---|---|---|---|---|
| Submitted At | Full Name | Email | Company | Phone | Source |

4. Open the JSON credentials file, find the `client_email` field:
   ```json
   "client_email": "nucleus-sheets-writer@your-project.iam.gserviceaccount.com"
   ```
5. In the Google Sheet → **Share** → paste that email → set role to **Editor**
6. Copy the **Sheet ID** from the URL:
   ```
   https://docs.google.com/spreadsheets/d/  ← THIS PART →  /edit
   ```
   Example: `1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgVE2upms`

---

### Step 3 — Add Code to `form-handler.php`

Open `/includes/form-handler.php` and add the following two functions.

#### Function 1: Get Google Access Token
This exchanges your service account credentials for a temporary access token.

```php
/**
 * Generates a short-lived Google API access token from a service account JSON key.
 */
function nucleus_get_google_token($credentials) {
    $now = time();
    $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $claim  = base64_encode(json_encode([
        'iss'   => $credentials['client_email'],
        'scope' => 'https://www.googleapis.com/auth/spreadsheets',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'iat'   => $now,
        'exp'   => $now + 3600,
    ]));

    $signature = '';
    $pem_key   = $credentials['private_key'];
    openssl_sign("$header.$claim", $signature, $pem_key, 'SHA256');
    $jwt = "$header.$claim." . base64_encode($signature);

    $response = wp_remote_post('https://oauth2.googleapis.com/token', [
        'body' => [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ],
    ]);

    if (is_wp_error($response)) return null;
    $body = json_decode(wp_remote_retrieve_body($response), true);
    return $body['access_token'] ?? null;
}
```

#### Function 2: Push a Row to Google Sheets
This appends one new row to the sheet every time a form is submitted.

```php
/**
 * Appends a new lead row to Google Sheets.
 *
 * @param string $name
 * @param string $email
 * @param string $company
 * @param string $phone
 * @param string $timestamp  e.g. '2026-02-20 21:00:00'
 */
function nucleus_push_to_google_sheets($name, $email, $company, $phone, $timestamp) {
    $credentials_path = plugin_dir_path(__FILE__) . 'google-credentials.json';
    $spreadsheet_id   = 'YOUR_GOOGLE_SHEET_ID_HERE'; // ← Replace this
    $range            = 'Sheet1!A:F';

    if (!file_exists($credentials_path)) return;

    $credentials = json_decode(file_get_contents($credentials_path), true);
    $token       = nucleus_get_google_token($credentials);

    if (!$token) return; // Fail silently — don't break the form

    $values = [[
        $timestamp,
        $name,
        $email,
        $company,
        $phone,
        'Testing Lab Form',
    ]];

    $url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheet_id}/values/{$range}:append?valueInputOption=RAW";

    wp_remote_post($url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json',
        ],
        'body'    => json_encode(['values' => $values]),
        'timeout' => 15,
    ]);
}
```

#### Call It Inside the Existing Save Function

Inside the existing `nucleus_save_cf7_lead()` function (the one hooked to `wpcf7_before_send_mail`),
add this one line **after the `$wpdb->insert()` call**:

```php
// Existing code saves to DB here...
$wpdb->insert($table_name, [ ... ]);

// NEW: Also push to Google Sheets automatically
nucleus_push_to_google_sheets($name, $email, $company, $phone, $submitted_at);
```

---

### Step 4 — Connect Google Sheet to Looker Studio

1. Go to [https://lookerstudio.google.com](https://lookerstudio.google.com)
2. Click **Create** → **Report**
3. Click **Add Data** → Select **Google Sheets**
4. Choose the `Nucleus Advisory — Form Leads` sheet
5. Click **Add** → Your lead data is now in Looker ✅

**To also add Google Analytics:**
1. In the same report → **Add Data** → **Google Analytics**
2. Select the Nucleus Advisory GA4 property
3. Your analytics data is now in the same Looker report ✅

---

## 📊 Example Looker Dashboard Layout

```
┌─────────────────────────────────────────────────────────┐
│             NUCLEUS ADVISORY — LIVE DASHBOARD           │
├──────────────────────────┬──────────────────────────────┤
│   FORM LEADS             │   WEBSITE ANALYTICS (GA4)   │
│   Source: Google Sheets  │   Source: Google Analytics   │
│                          │                              │
│  • Total submissions     │  • Total page views          │
│  • Leads by date         │  • Visitors by source        │
│  • Leads by company      │  • Feature card clicks       │
│  • Lead detail table     │  • Service tag clicks        │
│                          │  • Brochure downloads        │
└──────────────────────────┴──────────────────────────────┘
```

Looker Studio auto-refreshes data every **15 minutes** by default.
You can also click **Refresh Data** manually at any time.

---

## 🔒 Security Notes

| Item | Guidance |
|------|----------|
| `google-credentials.json` | Never commit to GitHub. Add to `.gitignore`. |
| Service Account scope | Limit to `spreadsheets` only (already done in the code above) |
| Sheet sharing | Only share with the service account email, not publicly |

Add to `.gitignore`:
```
includes/google-credentials.json
```

---

## 🗓️ Estimated Implementation Time

| Task | Time |
|------|------|
| Google Cloud setup + Service Account | ~15 mins |
| Google Sheet setup + headers | ~5 mins |
| Add code to `form-handler.php` | ~45 mins |
| Test with a live form submission | ~15 mins |
| Connect Looker + build charts | ~30 mins |
| **Total** | **~1.5 to 2 hours** |

---

## ❓ FAQ

**Q: What if the Google Sheets push fails?**  
A: The function fails silently (`return;`) — the form still saves to WordPress DB normally. No data is lost.

**Q: Does this cost anything?**  
A: No. Google Sheets API has a generous free tier (500 requests per 100 seconds). At normal form volume, this will never be exceeded.

**Q: Can we add more fields later?**  
A: Yes. Just add more columns to the Sheet and update the `$values` array in the PHP function.

**Q: Can we connect GA4 events (button clicks) too?**  
A: Yes — GA4 is connected separately in Looker as a second data source. Both sources appear in the same Looker dashboard.
