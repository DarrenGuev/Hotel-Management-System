## Google Cloud Setup

To use the Gmail API, you must set up a project in Google Cloud Console:

### Step 1: Create Project & Enable API
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project (e.g., "TravelMates Email")
3. Go to **APIs & Services > Library**
4. Search for **Gmail API** and enable it

### Step 2: Configure OAuth Consent Screen
1. Go to **APIs & Services > OAuth consent screen**
2. Select **External** (or Internal if you have a Workspace org)
3. Fill in App Name ("TravelMates"), User Support Email, and Developer Contact Info
4. Click **Save and Continue**
5. **Scopes**: Add the following scopes:
   - `https://www.googleapis.com/auth/gmail.modify`
   - `https://www.googleapis.com/auth/gmail.send`
   - `email`
   - `profile`
   - `openid`
6. **Test Users**: Add your Gmail address as a test user (important while app is in "Testing" mode)

### Step 3: Create Credentials
1. Go to **APIs & Services > Credentials**
2. Click **Create Credentials > OAuth client ID**
3. Application type: **Web application**
4. Name: "TravelMates Web Client"
5. **Authorized redirect URIs**:
   - Add: `http://localhost/HOTEL-MANAGEMENT-SYSTEM/integrations/gmail/googleCallback.php`
   - (Adjust domain if not on localhost)
6. Click **Create**
7. Copy your **Client ID** and **Client Secret**

---

## Installation

### Step 1: Configure Environment Variables

Update your `.env` file with the credentials from Google Cloud:

```env
# ===================================
# Google OAuth & Gmail API Configuration
# ===================================

GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret

# Your Gmail address (for display purposes)
EMAIL_FROM=your-email@gmail.com
EMAIL_FROM_NAME=TravelMates Hotel

# Database Configuration
EMAIL_DB_HOST=localhost
EMAIL_DB_USER=root
EMAIL_DB_PASS=
EMAIL_DB_NAME=travelMates
```

### Step 2: Database Setup

Run these SQL queries in **phpMyAdmin**:

```sql
-- Create email_logs table
CREATE TABLE IF NOT EXISTS email_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    direction ENUM('inbound', 'outbound') NOT NULL,
    from_email VARCHAR(255),
    to_email VARCHAR(255),
    subject VARCHAR(500),
    body TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    message_id VARCHAR(255),
    error_message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    folder VARCHAR(100) DEFAULT 'INBOX',
    email_date DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_message_id (message_id)
);

-- Create email_replies table
CREATE TABLE IF NOT EXISTS email_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_email_id INT,
    reply_body TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (original_email_id) REFERENCES email_logs(id) ON DELETE CASCADE
);
```

---

## Usage

### First-Time Connection

1. Log in to the Hotel Management System as an **Admin**.
2. Navigate to `http://localhost/HOTEL-MANAGEMENT-SYSTEM/integrations/gmail/googleLogin.php`
   - Or create a "Connect Gmail" button linking to this URL in your admin dashboard.
3. Sign in with your Google account and grant permissions.
4. You will be redirected to the Email Dashboard.
5. A `tokens.json` file will be created in `integrations/gmail/` containing your access tokens.

### Syncing Emails

1. Go to **Admin Panel > Email Dashboard**
2. Click **Sync Inbox**
3. The system will fetch recent emails using the Gmail API.

### Sending Emails

Use the dashboard "Compose" button or the API to send emails. The system handles token refreshing automatically.

---

## Troubleshooting

### "Token expired" or "Refresh token missing"
If you see errors about expired tokens:
1. Delete `integrations/gmail/tokens.json`
2. Visit `googleLogin.php` again to re-authenticate and generate a new token.

### "Redirect URI mismatch"
Ensure the URI in Google Cloud Console exactly matches your local URL (http vs https, localhost vs 127.0.0.1).

### "App not verified"
Since your app is in "Testing" mode, you will see a warning screen from Google. Click **Advanced > Go to TravelMates (unsafe)** to proceed. This is normal for personal/development apps.
