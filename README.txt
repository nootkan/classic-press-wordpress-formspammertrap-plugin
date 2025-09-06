=== FormSpammerTrap Contact Form ===
Contributors: Van Isle Web Solutions
Tags: contact form, spam protection, anti-spam, form security, email, file uploads, form submissions, dashboard, import, export, color customization
Requires at least: 4.9
Tested up to: 6.8.2
Stable tag: 1.5.4
Requires PHP: => 7.2
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional anti-spam contact form with submission management, file uploads, import/export functionality, complete color customization, and enterprise-level security features powered by FormSpammerTrap.

== Description ==

FormSpammerTrap Contact Form provides a secure, spam-resistant contact form for your ClassicPress/Wordpress website. Built on the proven FormSpammerTrap anti-spam system (https://FormSpammerTrap.com) by Rick Helliwell, this plugin offers advanced protection against bots, spammers, and automated attacks while maintaining excellent usability for legitimate visitors.

**🆕 NEW in v1.5.4: WordPress/ClassicPress DB integration + admin stability fix**

* ** Auto database configuration** — The plugin now integrates with your site’s existing WordPress/ClassicPress database configuration (from `wp-config.php`). No manual DB host/name/user/pass edits are required for the FormSpammerTrap core.
* ** Front-end-only load of the legacy FST core** — The legacy FormSpammerTrap library is now only loaded on front-end requests. This prevents rare admin “critical error” screens (e.g., after **Users → Add New**) caused by legacy `mysqli` sanity checks.
* ** No behavior change to spam protection** — Honeypot/timing/JS traps, submission logging, email notifications, import/export, and color customization all work exactly as before.
* ** Compatibility** — Works on WordPress and ClassicPress; tested with PHP 8.2. No known conflicts with WooCommerce (checkout/account flows unaffected).
* ** mu-plugin no longer required** — If you previously installed `wp-content/mu-plugins/fix-formspammertrap-phpmailer.php` to mitigate PHPMailer duplication, you can now remove it. The legacy core only loads on the front-end, so there’s no PHPMailer conflict in wp-admin.
* **Session handling hardening** — Prevents “headers already sent” warnings by starting sessions early on `init` (front-end only) and guarding the legacy session start. No change to spam protection behavior.


**🆕 NEW in v1.5.3:
* **🔒 Critical Security Update! Fixes multiple XSS, Path Traversal, and Email Content Injection vulnerabilities. Comprehensive security improvements with enhanced input validation and sanitization. Highly recommended update for all users. All functionality preserved while significantly improving security.
* **🆕 Discovered an issue in Wordpress and Classic Press where a critical error occurred due to duplicate PHPMailer conflicts as the formspammertrap core file does a sanity check to ensure the PHPMailer folder and files exists in the formspammertrap includes directory. Wordpress and ClassicPress both include PHPMailer in their core files also so this was mitigated with a plugin placed in the wp-content/mu-plugins directory. Provided the mu-plugins directory in the zip file in case it isn't inside the wp-contents directory.  If already there than just upload the fix-formspammertrap-phpmailer file.  Look over the readme file in the mu-plugins folder for more info.

**🆕 NEW in v1.5.2: Complete Form Color Customization System**

* **🎨 Visual Form Designer** - Complete control over all form colors and appearance
* **🎯 Brand Integration** - Match your website's design perfectly with custom color schemes
* **⚡ Live Preview** - Real-time color updates with instant visual feedback
* **🔧 Professional Controls** - Color pickers with hex code input for precise color matching
* **📱 Responsive Design** - Custom colors work perfectly on all devices and screen sizes
* **🎮 Easy Management** - Simple enable/disable toggle with automatic default restoration
* **🖌️ Complete Coverage** - Customize every visual element including submit and reset buttons

**🔥 FEATURED in v1.5.1: Complete Import/Export System**

* **📥 CSV Import Functionality** - Import form submissions from other contact form plugins
* **🔄 Intelligent Field Mapping** - Automatic detection and manual mapping of CSV fields to database fields
* **👀 Import Preview** - See exactly what will be imported before processing
* **🧪 Test Mode** - Safe preview mode to verify imports without affecting your database
* **📤 Multi-Format Export** - Export submissions in CSV, JSON, XML, TXT, and HTML formats
* **🎯 Advanced Export Filtering** - Export by status, date range, or specific fields
* **📊 Export Preview** - Preview your export before downloading
* **🔧 Flexible Configuration** - Support for various CSV formats from different form plugins

**🔥 FEATURED in v1.5.0: Complete Form Submissions Management System**

* **📊 Submissions Dashboard** - View, search, and manage all form submissions in one place
* **🔍 Advanced Search & Filtering** - Find submissions by status, date, name, email, or message content
* **📝 Individual Submission View** - See complete details including technical information and attachments
* **📋 Admin Notes** - Add internal notes to any submission for team collaboration
* **📈 Dashboard Widget** - Quick view of recent submissions on your WordPress dashboard
* **🔔 Admin Notifications** - Get notified about unread submissions throughout your admin area
* **📊 Submission Status Tracking** - Monitor email delivery success/failure for each submission
* **🗂️ Bulk Actions** - Mark multiple submissions as read/unread or delete them efficiently
* **🔎 File Attachment Tracking** - See what files were uploaded with each submission
* **🏷️ Status Management** - Automatic status tracking (unread, read, sent, email failed)

**Key Features:**

* **🎨 Complete Color Customization** - Match your brand with custom form colors and themes
* **Advanced Spam Protection** - Multiple layers of bot detection and spam prevention
* **No CAPTCHA Required** - Uses invisible protection methods that don't burden users
* **Secure File Uploads** - Allow visitors to attach files with enterprise-level security
* **Complete Import/Export System** - Full data migration capabilities with multiple format support
* **Form Submissions Management** - Complete dashboard to view and manage all form submissions
* **Complete Clean Uninstall** - Removes ALL plugin data, files, and database entries when deleted
* **Automatic File Cleanup** - GDPR-compliant automatic deletion of uploaded files
* **Directory Protection** - .htaccess security prevents direct file access for uploads folder
* **Security Dashboard** - Real-time monitoring of security status and file storage
* **Database Cleanup** - Comprehensive removal of all FormSpammerTrap configuration on uninstall
* **Customizable Thank You Messages** - Create personalized success messages
* **URL Spam Control** - Configurable limits on URLs in messages to prevent link spam
* **Form Reset Button** - Optional reset functionality with customizable colors
* **Field Color Coding** - Visual feedback for required field validation
* **Version Display** - Optional FormSpammerTrap version information display to help promote FormSpammerTrap developer
* **Email Validation** - Built-in email address verification
* **Responsive Design** - Mobile-friendly form layout with custom color support
* **PHPMailer Integration** - Reliable email delivery system
* **Multiple Recipients** - Support for CC and BCC email addresses

**🎨 Form Color Customization Features:**

* **🎯 Complete Visual Control:**
  - **Form Background Color** - Customize the entire contact form container background
  - **Input Field Colors** - Set background and border colors for all input fields
  - **Label Text Color** - Control the color of field labels and form instructions
  - **Submit Button Styling** - Normal, hover, and text colors for the submit button
  - **Reset Button Styling** - Complete color control for the optional reset button

* **🔧 Professional Design Tools:**
  - **Color Picker Interface** - Visual color selection with instant preview
  - **Hex Code Input** - Direct entry of specific color codes (#ff0000, etc.)
  - **Synchronized Controls** - Color picker and text input automatically sync
  - **Enable/Disable Toggle** - Easy on/off control with automatic default restoration
  - **Brand Integration Tips** - Built-in guidance for accessibility and design best practices

* **⚡ Advanced Implementation:**
  - **CSS Override System** - Uses !important declarations to ensure color consistency
  - **Responsive Compatibility** - Custom colors work on all devices and screen sizes
  - **Performance Optimized** - Only loads custom CSS when color customization is enabled
  - **Safe Defaults** - Automatic fallback to original colors if customization is disabled
  - **Professional Output** - Clean, semantic CSS that doesn't interfere with site performance

**📥📤 Import/Export System Features:**

* **📥 CSV Import Capabilities:**
  - Import from Contact Form 7/Flamingo exports
  - Import from Formidable Forms exports
  - Import from any CSV file with proper headers
  - Support for up to 1,000 records per import
  - Maximum file size: 5MB
  - Intelligent auto-detection of field mappings
  - Manual field mapping override capabilities
  - Test mode for safe preview before importing
  - Real-time import preview with data validation

* **📤 Multi-Format Export Options:**
  - **CSV Export** - Excel-compatible spreadsheet format
  - **JSON Export** - Structured data format for developers
  - **XML Export** - Structured markup format for data exchange
  - **TXT Export** - Simple text format for basic viewing
  - **HTML Export** - Web page format for viewing and printing

* **🎯 Advanced Export Filtering:**
  - Filter by submission status (unread, read, sent, failed)
  - Filter by date range (from/to dates)
  - Select specific fields to include or exclude
  - Choose between all submissions or filtered subsets
  - Include/exclude column headers
  - Format dates for readability
  - Clean HTML tags from messages

* **🔧 Import Configuration:**
  - Auto-detect common field mappings (Name → Visitor Name, etc.)
  - Manual dropdown selection for custom field mapping
  - Support for various date formats and structures
  - Email validation during import process
  - Skip fields not needed in your database
  - Comprehensive error handling and validation

**📊 Form Submissions Management Features:**

* **Comprehensive Submission Storage** - All form data automatically saved to secure database
* **Advanced Admin Interface** - Professional dashboard with sorting, filtering, and pagination
* **Email Integration** - Direct links to reply to submitters via your email client
* **Technical Details** - IP addresses, user agents, submission timestamps for security analysis
* **Search Functionality** - Find specific submissions by any field content
* **Status Tracking** - Track which submissions have been read and which emails succeeded
* **Bulk Management** - Efficiently manage multiple submissions simultaneously
* **Admin Collaboration** - Internal notes system for team communication about submissions
* **Dashboard Integration** - Quick access widget shows recent submissions on main dashboard
* **Smart Notifications** - Admin notices alert you to unread submissions throughout the admin area

**🧹 Complete Cleanup Features (Enhanced in v1.5.1):**

* **Submission Data Cleanup** - Removes all stored form submissions and related data
* **Database Options Cleanup** - Removes all plugin-specific options (fst_default_email, fst_enable_uploads, etc.)
* **FormSpammerTrap Core Cleanup** - Removes all FormSpammerTrap configuration options (fst_show_required_message, fst_cc_email, etc.)
* **Database Tables Cleanup** - Safely removes FormSpammerTrap database tables (fst_* and formspammertrap* patterns only)
* **Upload Folder Cleanup** - Completely removes upload folder and all contents (files, .htaccess, security files)
* **Color Customization Cleanup** - Removes all custom color settings and CSS overrides
* **Transients Cleanup** - Removes all cached data and temporary settings
* **Cron Jobs Cleanup** - Removes scheduled cleanup tasks
* **Zero Leftover Data** - Ensures no plugin traces remain in your database or file system

**Security Features:**

* **Multi-Layer Bot Detection** - Session validation, JavaScript delays, hidden field manipulation detection
* **File Upload Security** - Extension validation, automatic .htaccess protection, secure storage for uploads directory
* **Privacy Compliance** - Automatic uploads folder file deletion for GDPR compliance
* **Directory Protection** - Prevents direct URL access to uploaded files
* **Script Execution Blocking** - Prevents dangerous file types from being executed in the uploads directory
* **Automated Cleanup** - Daily cron jobs remove old upload folder files based on retention settings
* **Security Monitoring** - Real-time dashboard shows protection status and storage usage of uploads directory
* **Safe Table Cleanup** - Intelligent database cleanup that only targets FormSpammerTrap-specific tables
* **Secure Submission Storage** - All form data stored with proper sanitization and validation
* **Import Security** - CSV files validated and sanitized during import process
* **Export Security** - Admin-only access to export functionality with nonce verification
* **Color Customization Security** - All color inputs sanitized and validated for XSS prevention

**File Upload Features:**

* **Multiple File Support** - Visitors can attach multiple files to their messages
* **Extension Filtering** - Configurable allowed file types (PDF, images, documents)
* **File Preview** - Visual preview of selected files before submission
* **Size Monitoring** - Automatic file size reporting and disk usage tracking
* **Retention Control** - Automatic deletion after 7, 14, 30, or 90 days
* **Secure Storage** - Files protected from direct access while available to admins
* **Complete Removal** - All upload files and folders removed during plugin uninstall
* **Attachment Tracking** - View file information for each submission in the admin dashboard

**Easy to Use:**

Simply add the `[formspammertrap]` shortcode to any page or post to display the contact form. The plugin includes a comprehensive admin interface for configuration, security monitoring, submission management, color customization, and data import/export.

== Installation ==

**Automatic Installation:**
1. Download and upload the plugin zip file from the wp-admin/plugins "Upload" setting
2. Activate the plugin from the 'Plugins' menu in ClassicPress/Wordpress
3. Follow the settings instructions below

**Manual Installation:**
1. Download and extract the plugin files
2. Upload the entire `formspammertrap-plugin` folder to `/wp-content/plugins/`
3. Activate the plugin in your ClassicPress admin
4. Complete the required setup steps

**Required Files Setup:**
5. Upload `formspammertrap-contact-functions.php` to the `includes/` folder in the plugin directory
6. Upload PHPMailer files to `includes/phpmailer/` folder if not already present

**Configuration:**
7. Go to Settings > FormSpammerTrap in your admin
8. Set your email address (must be on your domain)
9. Configure form colors in the "Form Color Customization" section
10. Configure file upload options if desired
11. Review security status and configure retention settings
12. Access Settings > Form Submissions to view and manage all form submissions
13. Use Settings > Import Submissions to import data from other contact form plugins
14. Use Settings > Export Submissions to export your form data in multiple formats

**🗑️ Clean Removal:**
When you no longer want the plugin, simply delete it from the Plugins page. The enhanced cleanup system will automatically remove all plugin data, uploaded files, form submissions, database entries, color customizations, and configuration - leaving your site completely clean!

== Configuration ==

**Basic Settings:**

1. **Default Email Address** - Where form submissions will be sent (must be on your domain)
2. **Required Field Colors** - Enable visual feedback for form validation
3. **Show Version** - Display FormSpammerTrap version information
4. **Custom Thank You Message** - Personalize the success message
5. **Max URLs Allowed** - Control spam by limiting URLs in messages (0-10)
6. **Enable Reset Button** - Add a form reset option for users

**🎨 Form Color Customization Settings:**

1. **Enable Custom Colors** - Master toggle to enable/disable all color customizations
2. **Form Colors Section:**
   - **Form Background Color** - Set the background color for the entire contact form container
   - **Input Field Background** - Customize the background color for all text inputs and textareas
   - **Input Field Border** - Set the border color for all form input fields
   - **Label Text Color** - Control the color of field labels and form instructions

3. **Submit Button Colors Section:**
   - **Submit Button Background** - Normal background color for the submit button
   - **Submit Button Hover** - Background color when users hover over the submit button
   - **Submit Button Text** - Text color for the submit button

4. **Reset Button Colors Section (when reset button is enabled):**
   - **Reset Button Background** - Normal background color for the reset button
   - **Reset Button Hover** - Background color when users hover over the reset button
   - **Reset Button Text** - Text color for the reset button

5. **Color Customization Features:**
   - **Visual Color Pickers** - Click to select colors using a visual interface
   - **Hex Code Input** - Type specific color codes directly (e.g., #ff0000 for red)
   - **Synchronized Controls** - Color picker and text input automatically sync
   - **Built-in Tips** - Guidance for accessibility and brand consistency
   - **Instant Preview** - Colors update immediately on your contact form

**📊 Form Submissions Settings:**

1. **Automatic Saving** - All form submissions are automatically saved to the database
2. **Admin Access** - View submissions via Settings > Form Submissions
3. **Status Tracking** - Submissions are marked as read/unread automatically
4. **Email Delivery Tracking** - Monitor whether emails were sent successfully
5. **Admin Notes** - Add internal comments to any submission
6. **Search & Filter** - Find specific submissions quickly

**📥📤 Import/Export Settings:**

1. **Import Access** - Available via Settings > Import Submissions
2. **Export Access** - Available via Settings > Export Submissions
3. **Supported Import Format** - CSV files only (up to 5MB, 1,000 records)
4. **Export Formats** - CSV, JSON, XML, TXT, HTML
5. **Field Mapping** - Automatic detection with manual override options
6. **Data Validation** - Email validation and field sanitization during import

**File Upload Settings:**

1. **Enable File Uploads** - Allow visitors to attach files to messages
2. **Allowed File Extensions** - Configure permitted file types (e.g., .pdf,.jpg,.doc)
3. **Upload Folder** - Specify storage location (automatically secured)
4. **File Retention Period** - Automatic deletion timeline (7-90 days or never)

**Uploads Folder Security Features:**

1. **Automatic .htaccess Protection** - Blocks direct file access
2. **Daily Cleanup Schedule** - Automatic old file removal
3. **Storage Monitoring** - Track file count and disk usage
4. **Security Status Dashboard** - Real-time protection monitoring
5. **Complete Removal on Uninstall** - No leftover files or folders

**Email Settings:**

The default email address must be on your domain to prevent delivery issues. For example:
- ✅ Good: `contact@yourdomain.com`
- ❌ Bad: `yourname@gmail.com`

== Usage ==

**Basic Usage:**
Add this shortcode to any page or post:
```
[formspammertrap]
```

**Advanced Usage with Attributes:**
```
[formspammertrap email="contact@yourdomain.com"]
[formspammertrap cc="copy@example.com"]
[formspammertrap bcc="blind@example.com"]
[formspammertrap email="contact@yourdomain.com",bcc="blind@example.com",cc="copy@example.com,another@example.com"]
```

**🎨 Customizing Form Colors:**

1. **Access Color Settings** - Go to Settings > FormSpammerTrap > Form Color Customization
2. **Enable Customization** - Check "Override FormSpammerTrap's default form colors"
3. **Choose Colors** - Use color pickers or enter hex codes directly:
   - **Visual Method:** Click the color squares to open the color picker
   - **Manual Method:** Type hex codes in the text fields (e.g., #ff0000 for red)
   - **Synced Controls:** Changes in either method automatically update the other
4. **Preview Changes** - Visit your contact form page to see colors update in real-time
5. **Brand Integration** - Use colors that match your website's design
6. **Accessibility Tips** - Ensure sufficient contrast between text and background colors
7. **Save Settings** - Click "Save Settings" to apply your color scheme
8. **Reset to Defaults** - Uncheck "Enable Custom Colors" to restore original colors

**Color Customization Tips:**
- **High Contrast:** Ensure text is readable against background colors
- **Brand Consistency:** Use your website's existing color palette
- **Testing:** Check colors on different devices and browsers
- **Professional Colors:** Avoid overly bright or distracting color combinations
- **Accessibility:** Consider color-blind users when choosing color schemes

**📊 Managing Form Submissions:**

1. **View All Submissions** - Go to Settings > Form Submissions
2. **Search Submissions** - Use the search box to find specific entries
3. **Filter by Status** - Show only unread, read, sent, or failed submissions
4. **View Individual Submissions** - Click "View" to see complete details
5. **Add Admin Notes** - Click "View" then add notes in the admin notes section
6. **Bulk Actions** - Select multiple submissions and mark as read/unread or delete
7. **Email Integration** - Click "Reply via Email" to respond directly to submitters

**📥 Importing Form Submissions:**

1. **Access Import** - Go to Settings > Import Submissions
2. **Upload CSV File** - Select your CSV file (max 5MB, 1,000 records)
3. **Preview Data** - Click "Upload & Preview File" to see your data
4. **Map Fields** - Review auto-detected field mapping or adjust manually:
   - **Green "(Auto-detected)"** - System found a good match
   - **Dropdown menus** - Choose which CSV column maps to which database field
   - **"-- Skip this field --"** - Don't import that type of data
5. **Test Import** - Keep "Test mode" checked for safe preview
6. **Process Import** - Uncheck "Test mode" and click "Process Import" to import data

**📤 Exporting Form Submissions:**

1. **Access Export** - Go to Settings > Export Submissions
2. **Choose Format** - Select CSV, JSON, XML, TXT, or HTML
3. **Set Filters** - Filter by status, date range, or specific fields
4. **Configure Options** - Include headers, format dates, clean HTML
5. **Preview Export** - Click "Preview Export" to see sample output
6. **Download** - Click "Download Export" to get your file

**Shortcode Attributes:**
* `email` - Override default recipient email
* `cc` - Add CC recipients (comma-separated for multiple)
* `bcc` - Add BCC recipients (comma-separated for multiple)

== Form Fields ==

The contact form includes these standard fields:
* **Your Name** (Required) - Visitor's full name
* **Your Email** (Required) - Visitor's email address  
* **Subject** (Required) - Message subject line
* **Message** (Required) - Main message content
* **Attach File** (Optional) - Multiple file uploads with preview

All fields include validation and spam protection features. File uploads include extension validation and security scanning. **All form data is automatically saved to the submissions dashboard for easy management and can be exported in multiple formats. Form appearance can be completely customized with the color customization system.**

== 📊 Form Submissions Dashboard ==

**Dashboard Features:**

* **📋 Submissions List** - Comprehensive table showing all form submissions
* **🔍 Advanced Search** - Search by name, email, subject, or message content
* **📊 Status Filtering** - Filter by unread, read, sent, or email failed status
* **📄 Pagination** - Navigate through large numbers of submissions efficiently
* **📈 Quick Stats** - See total submissions, unread count, and today's submissions

**Individual Submission View:**

* **👤 Visitor Information** - Name, email, subject, submission date, and status
* **🔧 Technical Details** - IP address, user agent, and timestamp for security analysis
* **🔎 File Attachments** - List of uploaded files with sizes and types
* **📝 Complete Message** - Full message content in easy-to-read format
* **🗑️ Admin Notes** - Add and edit internal notes for team collaboration
* **📧 Email Integration** - Direct "Reply via Email" links to your email client

**Bulk Management:**

* **✅ Bulk Actions** - Mark multiple submissions as read/unread
* **🗑️ Bulk Delete** - Remove multiple submissions efficiently
* **📱 Select All** - Checkbox to select all visible submissions
* **📊 Status Management** - Change status for multiple submissions simultaneously

**Dashboard Widget:**

* **📈 Recent Submissions Widget** - Shows latest 5 submissions on WordPress dashboard
* **📊 Quick Statistics** - Total, unread, and today's submission counts
* **🔗 Quick Access** - Direct link to full submissions dashboard

**Admin Notifications:**

* **🔔 Unread Alerts** - Admin notices show when you have unread submissions
* **📍 Smart Placement** - Notifications appear throughout admin area (except on submissions page)
* **🔗 Direct Links** - Click notification to go directly to unread submissions

== 📥📤 Import/Export System ==

**📥 Import Functionality:**

* **📁 CSV File Support** - Import data from any properly formatted CSV file
* **🔄 Automatic Field Detection** - Smart mapping of common field names:
  - Name/Full Name → Visitor Name
  - Email → Visitor Email  
  - Subject/Title → Subject
  - Message/Content → Message
  - Timestamp/Date → Submission Date
  - IP → IP Address

* **🎯 Manual Field Mapping** - Override automatic detection with dropdown selections
* **👀 Import Preview** - See exactly what will be imported before processing
* **🧪 Test Mode** - Safe preview without affecting your database
* **📊 Import Statistics** - See how many records will be processed
* **🔒 Data Validation** - Email validation and field sanitization
* **📋 Import History** - Track successful imports and any issues

**📤 Export Functionality:**

* **📊 Multiple Export Formats:**
  - **CSV** - Perfect for Excel, Google Sheets, and database imports
  - **JSON** - Ideal for developers and API integrations  
  - **XML** - Great for structured data exchange and archival
  - **TXT** - Simple text format for basic viewing
  - **HTML** - Professional format for printing and presentations

* **🎯 Advanced Filtering Options:**
  - Filter by submission status (all, unread, read, sent, failed)
  - Filter by date range (from/to dates)
  - Select specific fields to include or exclude
  - Include/exclude column headers
  - Format dates for better readability
  - Remove HTML tags from message content

* **👀 Export Preview** - See sample output before downloading
* **📊 Export Statistics** - Know exactly how many records will be exported
* **🔄 Flexible Configuration** - Choose exactly what data to export

**🔧 Import/Export Security:**

* **Admin-Only Access** - Only administrators can import/export data
* **Nonce Verification** - CSRF protection for all import/export operations
* **File Validation** - Strict validation of uploaded CSV files
* **Data Sanitization** - All imported data is properly cleaned and validated
* **Size Limits** - Maximum file size (5MB) and record limits (1,000) for safety
* **Error Handling** - Comprehensive error reporting and validation

== Plugin Security Features ==

**Multi-Layer Protection:**
* Session validation prevents direct form access
* JavaScript delays prevent automated submissions
* Hidden field manipulation detection
* reCAPTCHA support (optional)
* URL counting prevents link spam
* Email validation prevents invalid addresses

**📊 Submission Security:**
* All form data sanitized before database storage
* Secure database queries prevent SQL injection
* Admin-only access to submissions dashboard
* Nonce verification for all admin actions
* XSS protection on all output
* CSRF protection for form actions

**🎨 Color Customization Security:**
* All color input values sanitized and validated
* XSS prevention for custom CSS output
* Secure storage of color preferences in database
* Admin-only access to color customization settings
* Safe CSS generation with proper escaping
* No direct user input in CSS output

**📥📤 Import/Export Security:**
* Admin-only access to import/export functions
* File type validation (CSV only for imports)
* File size limits prevent abuse
* Data sanitization during import process
* Nonce verification for all operations
* Comprehensive error handling and logging

**File Upload Security:**
* Extension whitelist prevents dangerous files
* Automatic .htaccess protection blocks direct access
* Directory browsing prevention
* Script execution blocking
* Secure temporary file handling
* Automatic cleanup prevents storage abuse
* Complete removal on plugin deletion

**Bot Detection:**
* User agent analysis
* Submission timing analysis
* Form interaction patterns
* Session-based verification

**Privacy & Compliance:**
* GDPR-compliant file retention policies
* Automatic deletion of old uploads
* Privacy-focused logging controls
* Secure file storage practices
* Complete data removal on plugin deletion (including submissions)

**🆕 Database Protection:**
* Safe cleanup that only targets FormSpammerTrap-specific tables
* Preserves other plugin data (Contact Form 7, WPForms, etc.)
* Intelligent pattern matching for table identification
* Complete options and transients cleanup
* Secure submission data storage with proper validation

== Frequently Asked Questions ==

= 🆕 How do I customize the colors of my contact form? =
Go to Settings > FormSpammerTrap > Form Color Customization. Check "Enable Custom Colors" and use the color pickers or enter hex codes directly. You can customize form background, input fields, labels, and both submit and reset buttons. Colors update instantly on your contact form!

= Can I use my website's brand colors? =
Absolutely! The color customization system is designed for brand integration. Use your website's existing color palette to create a seamless design. You can enter specific hex codes (like #ff0000) or use the visual color picker to match your brand perfectly.

= What if I want to restore the default colors? =
Simply uncheck "Enable Custom Colors" in the Form Color Customization section and save your settings. This will instantly restore all original FormSpammerTrap colors while preserving your custom color settings for future use.

= Do custom colors work on mobile devices? =
Yes! The color customization system is fully responsive and works perfectly on all devices and screen sizes. Your custom colors will look great on desktop, tablet, and mobile devices.

= How do I ensure my custom colors are accessible? =
Follow these guidelines: ensure sufficient contrast between text and background colors, test with color-blind simulation tools, avoid using color alone to convey information, and preview your form on different devices. The plugin includes built-in accessibility tips in the admin interface.

= 🆕 Can I import submissions from other contact form plugins? =
Yes! The import system supports CSV files from Contact Form 7/Flamingo, Formidable Forms, and any other contact form plugin that can export to CSV format. The system automatically detects common field mappings and allows manual adjustment as needed.

= How do I import data from Contact Form 7? =
1. Export your Contact Form 7 data using the Flamingo plugin's export feature
2. Go to Settings > Import Submissions in your WordPress admin
3. Upload the CSV file and use the preview feature
4. Review the automatic field mapping (or adjust manually)
5. Run in test mode first, then uncheck test mode to import

= What export formats are available? =
You can export in five formats: CSV (Excel-compatible), JSON (for developers), XML (structured data), TXT (simple text), and HTML (for printing/viewing). Each format is optimized for different use cases.

= Can I export only certain submissions? =
Absolutely! You can filter exports by status (unread, read, sent, failed), date range, and choose specific fields to include. The export system is very flexible.

= What happens to my form submissions when I delete the plugin? =
**All form submissions are permanently deleted!** Version 1.5.2 features comprehensive cleanup that removes all stored submissions along with files, configuration, color customizations, and database entries. If you need to keep submission data, export or backup the information before deleting the plugin.

= How do I access my form submissions? =
Go to Settings > Form Submissions in your WordPress admin. You'll see a complete dashboard with all submissions, search functionality, and filtering options.

= Can I search through my form submissions? =
Yes! The submissions dashboard includes advanced search that looks through names, email addresses, subjects, and message content. You can also filter by status (unread, read, sent, email failed).

= What information is saved with each submission? =
Each submission includes: visitor name, email, subject, message, submission date/time, IP address, browser information, file attachments (if any), current status, and space for admin notes.

= Can I add notes to submissions? =
Absolutely! Click "View" on any submission and you'll find an admin notes section where you can add internal comments for team collaboration.

= How do I reply to someone who submitted the form? =
Click "View" on any submission, then click the "Reply via Email" button. This will open your default email client with the person's email address pre-filled.

= What gets removed when I delete the plugin? =
**Everything!** Version 1.5.2 features comprehensive cleanup that removes:
- All form submissions and related data
- All plugin configuration options including color customizations
- All FormSpammerTrap core options (fst_* database entries)
- Upload folder and all files (including security files)
- FormSpammerTrap database tables (if any)
- Scheduled cleanup tasks
- Cached data and transients
Your site will be completely clean with zero leftover data.

= Will deleting this plugin affect my other form plugins? =
No! The cleanup system only targets FormSpammerTrap-specific data (fst_* and formspammertrap* patterns). Your Contact Form 7, WPForms, Gravity Forms, and other plugins are completely safe.

= Why do I need FormSpammerTrap files? =
This plugin integrates with the FormSpammerTrap system, which provides the core anti-spam functionality. The FormSpammerTrap files contain the proven spam protection algorithms.

= Can I use my Gmail address to receive emails? =
No, the email address must be on your domain (e.g., contact@yourdomain.com). This prevents delivery issues and spam filtering.

= Are uploaded files secure? =
Yes! The plugin automatically creates .htaccess protection to prevent direct URL access to uploaded files. Files are also automatically deleted based on your retention settings for privacy compliance.

= What file types can visitors upload? =
You can configure allowed file extensions in the admin settings. Common safe types include: .pdf, .jpg, .jpeg, .png, .gif, .doc, .docx. Dangerous file types like .php are automatically blocked.

= How long are uploaded files kept? =
You can set retention periods of 7, 14, 30, or 90 days. Files are automatically deleted via daily cron jobs. The recommended setting is 30 days for GDPR compliance.

= Can visitors access uploaded files directly? =
No, the plugin automatically creates security protection that blocks direct URL access to uploaded files. Only site administrators can access them through the server.

= Why doesn't the submit button appear immediately? =
This is a security feature. The submit button appears after visitors interact with required fields, preventing automated submissions.

= What happens if I disable file uploads? =
The upload folder and cleanup schedule are automatically managed. Disabling uploads stops new uploads but doesn't affect existing files until their retention period expires.

= How much storage do uploaded files use? =
The Security Status dashboard shows real-time file count and storage usage. The automatic cleanup prevents unlimited storage growth.

= Can I use this on multiple sites? =
Yes, but each site needs its own FormSpammerTrap setup and email configuration.

= Is reCAPTCHA required? =
No, FormSpammerTrap provides effective protection without requiring CAPTCHA.

= What happens to spam submissions? =
Spam submissions are automatically blocked and redirected. No spam emails reach your inbox.

= How do I know the cleanup worked? =
Check your error logs for detailed cleanup reports. You'll see exactly what was removed: options, files, folders, database entries, and form submissions.

= Can I backup my form submissions before switching plugins? =
Yes! Use the export functionality to download all your submissions in CSV, JSON, or XML format before making any changes.

== Requirements ==

**Minimum Requirements:**
* ClassicPress/Wordpress 4.9 or higher
* PHP 7.2 or higher
* MySQL 5.6 or higher
* HTTPS/SSL enabled (required for security)
* Email account on your domain

**Recommended:**
* PHP 8.0 or higher
* Modern web browser with JavaScript enabled
* Reliable email hosting
* Adequate disk space for file uploads (if enabled)
* Adequate database space for form submissions storage

**Server Requirements:**
* PHP mail() function or SMTP access
* Session support enabled
* CURL support (for reCAPTCHA if used)
* File system write permissions for upload folder
* Cron job support for automatic cleanup
* Database write permissions for submissions storage

== Installation Status ==

The plugin includes a comprehensive installation status checker that verifies:
* FormSpammerTrap functions file
* PHPMailer directory and files
* Upload directory (if using file uploads)
* Security protection status
* Cleanup schedule status
* Core requirements
* **NEW:** Submissions database table status
* **NEW:** Import/Export functionality status
* **NEW:** Color customization system status

Check Settings > FormSpammerTrap > Installation Status for detailed status information.

== Security Status ==

The Security Status dashboard provides real-time monitoring of:
* **Directory Protection** - .htaccess security status
* **Automatic Cleanup** - File retention and deletion schedule
* **Cleanup Schedule** - Cron job status and next run time
* **Current Storage** - File count and disk usage statistics
* **🆕 Submissions Database** - Table status and entry counts
* **🆕 Import/Export Security** - Admin access and file validation status
* **🆕 Color Customization Security** - CSS output validation and admin access control

== Data Management ==

**🆕 Enhanced in v1.5.2:**

**Color Customization Features:**
- ✅ **Complete visual control** - Customize every aspect of form appearance
- ✅ **Professional color tools** - Color pickers with hex code input
- ✅ **Brand integration support** - Match your website's design perfectly
- ✅ **Responsive design compatibility** - Works on all devices and screen sizes
- ✅ **Accessibility guidance** - Built-in tips for proper color contrast
- ✅ **Safe defaults and easy reset** - Restore original colors anytime

**Import/Export Capabilities:**
- ✅ **Import from any CSV source** - Contact Form 7, Formidable Forms, custom exports
- ✅ **Export to multiple formats** - CSV, JSON, XML, TXT, HTML
- ✅ **Advanced filtering and field selection** for exports
- ✅ **Safe preview mode** for testing imports
- ✅ **Intelligent field mapping** with manual override options
- ✅ **Data validation and sanitization** for all imports

**What Gets Cleaned Up on Plugin Deletion:**
- ✅ **All form submissions and related data**
- ✅ All plugin configuration (fst_default_email, fst_enable_uploads, etc.)
- ✅ **All color customization settings** (fst_enable_custom_colors, fst_form_background_color, etc.)
- ✅ All FormSpammerTrap core options (fst_show_required_message, fst_cc_email, etc.)
- ✅ Upload folder and ALL contents (files, .htaccess, index.php, subdirectories)
- ✅ FormSpammerTrap database tables (fst_* and formspammertrap* patterns only)
- ✅ Scheduled cleanup tasks and cron jobs
- ✅ Cached data and transients

**What's Protected:**
- ❌ Other plugin data (Contact Form 7, WPForms, etc.)
- ❌ WordPress core tables and options
- ❌ Your content and posts
- ❌ Other plugin upload folders

**Backup Recommendation:**
If you have important uploaded files or want to keep form submission data, use the export functionality to create backups before deleting the plugin. The cleanup is thorough and permanent!

== Support ==

**Getting Help:**
1. Check the FAQ section above
2. Review the FormSpammerTrap documentation at FormSpammerTrap.com
3. Verify all required files are properly installed
4. Check the Installation Status and Security Status in the plugin settings
5. **NEW:** Check the Form Submissions dashboard for submission-related issues
6. **NEW:** Use the import/export functionality to migrate data between systems
7. **NEW:** Review color customization settings if form appearance issues occur

**Common Issues:**
* **Blank page after submission** - Usually indicates a PHP error or missing files
* **Emails not received** - Check that your email address is on your domain
* **Form not appearing** - Verify FormSpammerTrap function file is in the includes/ folder
* **Submit button not appearing** - This is normal; fill out required fields first
* **File uploads not working** - Check server permissions and file extension settings
* **Security warnings** - Review Security Status dashboard for specific issues
* **🆕 Submissions not saving** - Verify database permissions and check Installation Status
* **🆕 Can't access submissions dashboard** - Ensure you have admin privileges and the database table was created
* **🆕 Import not working** - Check CSV format, file size (max 5MB), and field mappings
* **🆕 Export files empty** - Verify you have submissions and check filter settings
* **🆕 Custom colors not applying** - Check that "Enable Custom Colors" is checked and clear browser cache
* **🆕 Colors look wrong on mobile** - Custom colors are responsive; check specific color choices and contrast

**For FormSpammerTrap Core Issues Only:**
Visit FormSpammerTrap.com for support with the core FormSpammerTrap system.

== Changelog ==

= 1.5.4 =
* **🆕 Integration & Stability**
* Added WordPress/ClassicPress database adapter: automatically uses site DB credentials (no manual edits required).
* Changed legacy FormSpammerTrap core to load on **front-end only**, eliminating rare admin “critical error” pages (e.g., after creating a user) due to legacy `mysqli` sanity checks.
* No changes to spam protection logic, submissions dashboard, import/export, or color customization.
* Verified compatibility with WooCommerce; no checkout/account hooks are modified.
* Deprecated the temporary mu-plugin PHPMailer shim; it is no longer required and can be removed.
* Fixed PHP warnings: `ini_set(): Session ini settings cannot be changed after headers have already been sent` and `session_start(): Session cannot be started after headers have already been sent` by booting the session on front-end `init` and guarding the legacy call.
* Session is not started in wp-admin; behavior of spam traps and submissions is unchanged.


= 1.5.3 =
* **🔒 COMPREHENSIVE SECURITY UPDATE**
*Note: As of 1.5.4, the mu-plugin mentioned below is no longer required.*
* Fixed multiple XSS (Cross-Site Scripting) vulnerabilities in admin interface output
* Enhanced input sanitization using WordPress security functions (esc_html, sanitize_text_field, sanitize_email)
* Fixed Path Traversal vulnerabilities in file upload and directory creation functions
* Improved file upload path validation to prevent directory traversal attacks
* Strengthened upload folder creation security with proper path sanitization
* Fixed Email Content Injection vulnerabilities in form submission processing
* Enhanced email content filtering to prevent malicious HTML injection in email bodies
* Improved email header sanitization to prevent header injection attacks
* Added comprehensive input validation for all user-controlled data throughout the plugin
* Enhanced security for HTTP_HOST usage with proper sanitization and WordPress core functions
* Improved email notification system with secure content handling
* All security improvements maintain full plugin functionality and compatibility
* Enhanced notification email system focuses on essential information while preserving all data in submissions dashboard
* Comprehensive security audit completed with professional-grade vulnerability remediation
* Security fixes apply to both WordPress and ClassicPress installations
* All changes follow WordPress coding standards and security best practices
* **🎨 Created mu-plugin to mitigate the "PHP Fatal error:  Cannot declare class PHPMailer\PHPMailer\PHPMailer, because the name is already in use" issue with both ClassicPress and Wordpress. Also the issues with mailing password reset links and other email issues have been resolved with this plugin.

= 1.5.2 =
* **🎨 COMPLETE FORM COLOR CUSTOMIZATION SYSTEM**
* Professional color picker interface with hex code input for all form elements
* Complete visual control over form background, input fields, labels, and buttons
* Separate color customization for submit and reset buttons (normal, hover, text colors)
* Brand integration support with accessibility guidelines and design tips
* Responsive design compatibility ensuring custom colors work on all devices
* Safe enable/disable toggle with automatic restoration of default colors
* Synchronized color picker and text input controls for precise color matching
* CSS override system using !important declarations for consistent color application
* Performance optimized - custom CSS only loads when color customization is enabled
* Comprehensive cleanup of all color settings during plugin uninstall
* Professional admin interface with intuitive color management tools
* Security enhancements with proper sanitization and validation of all color inputs
* Integration with existing plugin features including submissions dashboard and import/export
* Enhanced user experience with instant color preview and visual feedback

= 1.5.1 =
* **🆕 COMPLETE IMPORT/EXPORT SYSTEM**
* CSV import functionality with intelligent field mapping and preview
* Multi-format export system (CSV, JSON, XML, TXT, HTML)
* Advanced export filtering by status, date range, and field selection
* Safe test mode for import preview without affecting database
* Support for importing from Contact Form 7/Flamingo and Formidable Forms
* Automatic field detection with manual mapping override capabilities
* Import validation and sanitization for data security
* Export preview functionality to verify output before download
* Comprehensive error handling and user guidance for import process
* Enhanced admin interface for data management and migration
* Support for various CSV formats and date structures
* Complete documentation and user-friendly field mapping interface

= 1.5.0 =
* **🆕 FORM SUBMISSIONS MANAGEMENT SYSTEM**
* Complete submissions dashboard with advanced search and filtering
* Individual submission view with full details and technical information
* Admin notes system for internal team collaboration
* Status tracking (unread, read, sent, email failed) for all submissions
* Bulk actions for efficient submission management
* Dashboard widget showing recent submissions and statistics
* Admin notifications for unread submissions throughout admin area
* Email integration with direct "Reply via Email" functionality
* File attachment tracking and display in submission details
* Automatic submission ID references in email subject lines
* Enhanced database security with prepared statements and data sanitization
* Complete submission data cleanup during plugin uninstall
* Professional admin interface with pagination and responsive design

= 1.4.4 =
* **🧹 ENHANCED CLEANUP SYSTEM**
* Complete upload folder removal including all security files (.htaccess, index.php)
* Enhanced recursive directory cleanup for nested folders
* Improved error logging and cleanup reporting
* Better handling of file permissions during cleanup
* Complete folder removal after successful file cleanup
* Zero leftover files or folders after plugin deletion

= 1.4.3 =
* **🗄️ COMPREHENSIVE DATABASE CLEANUP**
* Automatic removal of all FormSpammerTrap core options (fst_* entries)
* Safe cleanup that preserves other plugin data
* FormSpammerTrap database table cleanup (fst_* and formspammertrap* patterns)
* Complete transients and cached data removal
* Enhanced logging of cleanup operations
* Zero database traces after plugin deletion

= 1.4.2 =
* Enhanced safety features for database cleanup
* Improved plugin detection and protection
* Better logging and error reporting

= 1.4.1 =
* Added initial comprehensive uninstall cleanup system
* Database options cleanup functionality
* Enhanced security for cleanup operations

= 1.4.0 =
* **Major Security Update**
* Added secure file upload functionality
* Automatic .htaccess protection for upload directories
* GDPR-compliant file retention system (7-90 days)
* Daily automated file cleanup via cron jobs
* Security Status dashboard with real-time monitoring
* File storage usage tracking and reporting
* Enhanced upload folder protection and validation
* Improved logging mechanisms with daily limits
* Fixed HTTP_HOST warnings in various server environments
* Added file preview functionality for selected uploads
* Enhanced admin interface with security indicators

= 1.3.0 =
* Improved error handling and warning suppression
* Enhanced session management
* Better compatibility across hosting environments
* Optimized JavaScript enhancements

= 1.2.0 =
* Added custom thank you message functionality
* Enhanced form reset button features
* Improved admin interface

= 1.1.0 =
* Added URL spam control features
* Enhanced field validation
* Improved PHPMailer integration

= 1.0.0 =
* Initial release
* Integration with FormSpammerTrap anti-spam system
* Basic contact form functionality
* Responsive form design
* Comprehensive admin interface
* Shortcode support with attributes
* Multi-recipient email support (CC/BCC)

== Upgrade Notice ==
== 1.5.4 =
* **🆕 Stability update: removes rare admin “critical error” screens by loading the legacy FormSpammerTrap core on front-end only, and auto-configures database credentials from WordPress/ClassicPress. Also fixes PHP session warnings (“headers already sent”) by starting sessions early on front-end `init`. No changes to spam protection.

**Note:** If you previously installed the mu-plugin `wp-content/mu-plugins/fix-formspammertrap-phpmailer.php`, you may safely delete it in 1.5.4.


== 1.5.3 =
* **🔒 Critical Security Update! Fixes multiple XSS, Path Traversal, and Email Content Injection vulnerabilities. Comprehensive security improvements with enhanced input validation and sanitization. Highly recommended update for all users. All functionality preserved while significantly improving security.
* **🆕 Discovered an issue in Wordpress and Classic Press where a critical error occurred due to duplicate PHPMailer conflicts as the formspammertrap core file does a sanity check to ensure the PHPMailer folder and files exists in the formspammertrap includes directory. Wordpress and ClassicPress both include PHPMailer in their core files also so this was mitigated with a plugin placed in the wp-content/mu-plugins directory. Provided the mu-plugins directory in the zip file in case it isn't inside the wp-contents directory.  If already there than just upload the fix-formspammertrap-phpmailer file.  Look over the readme file in the mu-plugins folder for more info.

= 1.5.2 =
🎨 Major Feature Update! Adds complete Form Color Customization System with professional color picker interface, brand integration support, and responsive design compatibility. Customize every aspect of your form's appearance including background, input fields, labels, and buttons. Perfect for matching your website's brand and creating a seamless user experience!

= 1.5.1 =
🆕 Major Feature Update! Adds complete Import/Export System with CSV import capabilities, multi-format exports, intelligent field mapping, and data migration tools. Perfect for switching from other contact form plugins or creating backups. Import from Contact Form 7, Formidable Forms, and more!

= 1.5.0 =
🆕 Major Feature Update! Adds complete Form Submissions Management System with dashboard, search, filtering, admin notes, status tracking, and email integration. All form submissions are now automatically saved and manageable through a professional admin interface. Highly recommended update for enhanced form management capabilities.

= 1.4.4 =
🧹 Enhanced cleanup system! Now completely removes upload folders and all contents during plugin deletion. Automatic recursive cleanup ensures zero leftover files. Update recommended for complete data removal capability.

= 1.4.3 =
🗄️ Comprehensive database cleanup! Now removes ALL FormSpammerTrap data from database when plugin is deleted. Safe cleanup protects other plugins. Highly recommended update for clean uninstall functionality.

= 1.4.0 =
Major security update! Adds secure file upload functionality, automatic file cleanup, and enhanced security protection. Existing users should review new security settings after update.

= 1.3.0 =
Improved compatibility and error handling. Recommended update for all users.

= 1.0.0 =
Initial release of FormSpammerTrap Contact Form plugin.

== Privacy ==

This plugin:
* Processes form submissions and sends them via email
* **🆕 Stores all form submissions in the database for admin management**
* **🆕 Provides import/export capabilities for data management and migration**
* **🆕 Stores color customization preferences in the database**
* May temporarily store uploaded files based on retention settings
* Uses session data temporarily for spam protection
* May log basic form submission data if logging is enabled
* Automatically deletes uploaded files according to configured retention periods
* **🆕 Completely removes ALL data including form submissions and color settings when plugin is deleted**
* Respects visitor privacy while maintaining security

**🆕 Form Submissions Privacy:**
* All form submissions are stored securely in your site's database
* Only site administrators can access the submissions dashboard
* All data is properly sanitized and validated before storage
* Submissions include visitor IP addresses for security purposes
* Admin notes are private and not visible to form submitters
* **Complete removal of all submission data when plugin is uninstalled**

**🆕 Color Customization Privacy:**
* Color preferences are stored in your site's database
* Only site administrators can access color customization settings
* No personal data is collected through color customization features
* Color settings are completely removed when plugin is uninstalled
* Custom CSS is generated server-side with proper security validation

**🆕 Import/Export Privacy:**
* Import/export functions are admin-only capabilities
* All imported data is validated and sanitized before database storage
* Export files contain only the data you select and filter
* No automatic sharing or transmission of exported data
* All import/export operations are logged for security

**File Upload Privacy:**
* Uploaded files are automatically protected from direct URL access
* Files are automatically deleted based on your retention settings
* No permanent storage of personal files unless specifically configured
* All file handling complies with GDPR requirements when using recommended settings
* **Complete removal of all files and data when plugin is uninstalled**

Form submissions are processed according to your site's privacy policy. Consider adding appropriate privacy notices for form users, especially regarding data storage, import/export capabilities, file uploads, color customization, and retention periods.

== Credits ==

* Built on the FormSpammerTrap anti-spam system by Rick Hellewell
* Uses PHPMailer for reliable email delivery
* Designed for ClassicPress/Wordpress compatibility
* Follows ClassicPress/Wordpress coding standards and best practices
* Security features designed with GDPR compliance in mind
* Enhanced cleanup system ensures responsible data management
* **🆕 Form submissions system designed with security and privacy in mind**
* **🆕 Import/export system designed for data portability and migration**
* **🆕 Color customization system designed for accessibility and brand integration**

== License ==

This plugin is licensed under the GPL2 license. FormSpammerTrap components retain their original licensing terms.

== Technical Notes ==

**File Structure:**
```
fst-uploads/ (created automatically when file uploads enabled)
    ├── .htaccess (security protection)
    └── index.php (directory protection)
wp-content/mu-plugins/
├── fix-formspammertrap-phpmailer.php (duplicate phpmailer conflict fix)
wp-content/plugins/formspammertrap-plugin/
├── formspammertrap-plugin.php (main plugin file)
├── formspammertrap-import.php (import functionality)
├── formspammertrap-export.php (export functionality)
├── includes/
│   ├── formspammertrap-contact-functions.php (required)
│   ├── formspammertrap-wordpress-functions.php (WordPress/ClassicPress DB adapter; no manual DB config needed)
│   └── phpmailer/
│       ├── PHPMailer.php (required)
│       ├── Exception.php (required)
│       └── SMTP.php (required for SMTP)
└── readme.txt
```

**🆕 Database Implementation:**
* **Submissions Table:** `wp_fst_submissions` (created automatically on activation)
* **Table Structure:** Includes all form data, technical information, and admin notes
* **Security:** All queries use prepared statements and proper data sanitization
* **Performance:** Indexed fields for efficient searching and filtering
* **Privacy:** Complete table removal during plugin uninstall
* **Color Settings:** Stored as WordPress options with proper validation

**🆕 Color Customization Implementation:**
* **CSS Generation:** Server-side generation with proper escaping and validation
* **Performance:** Custom CSS only output when color customization is enabled
* **Security:** All color inputs sanitized with hex color validation
* **Compatibility:** Uses !important declarations to ensure color consistency
* **Responsive:** Custom colors work across all device sizes and orientations
* **Database Storage:** Color preferences stored as WordPress options
* **Cleanup:** All color settings removed during plugin uninstall

**🆕 Import/Export Implementation:**
* **Import Support:** CSV files up to 5MB with up to 1,000 records
* **Export Formats:** CSV, JSON, XML, TXT, HTML with advanced filtering
* **Field Mapping:** Intelligent auto-detection with manual override capabilities
* **Data Validation:** Comprehensive sanitization and validation for all imports
* **Security:** Admin-only access with nonce verification and file validation

**🆕 Cleanup Implementation:**
* Enhanced recursive folder removal with subdirectory support
* Safe database table cleanup using pattern matching
* Complete options cleanup (plugin + FormSpammerTrap core + color settings)
* **Form submissions table and data removal**
* **Import/export configuration cleanup**
* **Color customization settings cleanup**
* Automatic transients and cached data removal
* Cron job cleanup and scheduled task removal
* Comprehensive logging of all cleanup operations

**Security Implementation:**
* .htaccess rules block direct file access
* Daily cron jobs handle automated cleanup
* File extension validation prevents dangerous uploads
* Session-based protection prevents bot submissions
* Automatic directory protection creation
* **Secure form submission storage with validation**
* **Admin-only access to submissions dashboard**
* **Import/export security with file validation**
* **Color customization XSS protection with input sanitization**
* **XSS and CSRF protection for all admin functions**
* **Complete security cleanup on plugin removal**
**Session handling (v1.5.4):**
* Session is started early on front-end `init` to avoid “headers already sent” warnings.
* Legacy session start is guarded (skips if headers were already sent).
* No session is started in wp-admin.


**🆕 Submissions Dashboard:**
* Professional WordPress-style admin interface
* Pagination for handling large numbers of submissions
* Advanced search across all form fields
* Status filtering and bulk actions
* Individual submission detailed view
* Admin notes system with update functionality
* Email integration for direct communication
* Dashboard widget with recent submissions and statistics

**🆕 Color Customization System:**
* Real-time CSS generation with security validation
* Synchronized color picker and hex input controls
* Responsive design support for all device types
* Accessibility guidelines integration
* Brand consistency tools and tips
* Performance optimization with conditional CSS loading

**🆕 Import/Export System:**
* Secure file upload handling with validation
* Multiple export format generation
* Advanced filtering and field selection
* Preview functionality for both import and export
* Comprehensive error handling and user feedback
* Data migration capabilities between different form systems

**Cron Jobs:**
* Daily cleanup job: `fst_cleanup_uploads`
* Automatically scheduled when file uploads are enabled
* Respects retention period settings for file deletion
* **Automatically removed when plugin is deleted**

**🗑️ Cleanup Logging:**
Monitor your error logs during plugin deletion to see detailed cleanup reports:
```
FormSpammerTrap Plugin: Starting comprehensive uninstall cleanup process
FormSpammerTrap Plugin: Successfully removed upload folder 'fst-uploads' and X files
FormSpammerTrap Plugin: Removed submissions table: wp_fst_submissions
FormSpammerTrap Plugin: Removed X plugin-specific options (including color settings)
FormSpammerTrap Plugin: Removed X FormSpammerTrap core options
FormSpammerTrap Plugin: Cleaned up FormSpammerTrap transients
FormSpammerTrap Plugin: Comprehensive uninstall cleanup completed
```

== Advanced Configuration ==

**For Advanced Users:**

**🆕 Color Customization Advanced Options:**
The color customization system supports advanced CSS integration:
* Custom colors use !important declarations for consistency
* Colors are validated server-side for security
* CSS is generated dynamically based on enabled options
* Responsive design ensures colors work on all devices
* Integration with existing FormSpammerTrap CSS classes

**🆕 Import/Export Configuration:**
The import/export system supports advanced customization:
* Custom field mapping for non-standard CSV formats
* Batch processing for large datasets
* Advanced filtering options for targeted exports
* Multiple date format support for imports
* Case-insensitive field matching for better compatibility

**🆕 Submissions Management:**
The submissions system is designed for security and performance:
* All form data is stored with proper validation and sanitization
* Database queries use WordPress prepared statements for security
* Admin interface includes nonce verification for all actions
* Bulk operations are optimized for handling large datasets
* Search functionality uses efficient database indexing

**Maximum Security Setup:**
For highest security, consider moving the upload folder outside your web root directory:
1. Create a folder outside public_html: `/home/username/private_uploads/`
2. Set the upload folder path to the full system path
3. Implement custom file delivery scripts for administrative access

**Custom Retention Policies:**
The retention system can be customized for specific compliance requirements. Contact support for enterprise-level retention configurations.

**Server-Level Security:**
* Ensure proper file permissions (755 for directories, 644 for files)
* Consider additional server-level upload restrictions
* Monitor disk usage if allowing large file uploads
* Implement backup procedures for important uploaded files before plugin deletion
* **🆕 Consider database backup procedures for form submissions before plugin deletion**
* **🆕 Use export functionality to create regular backups of form submissions**

**🆕 Data Migration:**
For users migrating between form systems:
* Export all data before switching plugins
* Test imports in a staging environment first
* Verify field mapping accuracy before final import
* Consider data transformation needs for different systems

**🆕 Color Customization Advanced Features:**
For developers and advanced users:
* Color values are stored as WordPress options with proper sanitization
* CSS is generated server-side for security and performance
* Custom colors can be programmatically accessed via get_option()
* Integration with theme customizer is possible with custom development
* CSS selectors target specific FormSpammerTrap elements for precision

**🆕 Cleanup Customization:**
The cleanup system is designed to be safe and comprehensive. Advanced users can modify cleanup patterns in the plugin source code, but this is not recommended as it may affect the safety of the cleanup process.

**🆕 Submissions Database Optimization:**
For sites expecting high submission volumes:
* Monitor database size and performance
* Consider implementing custom archiving procedures for old submissions
* Review server resources if storing submissions long-term
* Use export functionality to create periodic backups
* Database table is optimized with indexes for efficient searching

== Donations ==

If you find this plugin useful, consider supporting:
* FormSpammerTrap development at https://FormSpammerTrap.com
* ClassicPress development at https://ClassicPress.net
* Wordpress development at https://www.wordpress.org

---

**🆕 A Special Thank You:**
Specific thankyou to Rick Helliwell for creating such a magnificent form script which to date works flawlessly to mitigate all form spam including bots!

The Wordpress/ClassicPress plugin version, Form Submissions Management System, Import/Export functionality and Color Customization System were developed through collaborative effort and community feedback. We appreciate all users who contribute to making this plugin better!


Thank you for using FormSpammerTrap Contact Form!