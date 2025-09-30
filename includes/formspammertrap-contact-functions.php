<?php
	/*
	formspammertrap-contact-functions.php

	Functions file for FormSpammerTrap contact page form that reduces attacks from form spammers
	- by Rick Hellewell, www.formspammertrap.comand www.cellarweb.com and www.securitydawg.com
	- uses Javascript and other techniques prevent spam bots from spamming a 'contact' page.

	VERSION  17.10 (17 Feb 2024)

	- Copyright 2011-2024 by Rick Hellewell - All Rights Reserved
	https://www.cellarweb.com and https://www.securitydawg.com and https://www.FormSpammerTrap.com

	- Information at FormSpammerTrap site https://www.formspammertrap.com
	- Version Change Log is in the formspammertrap-change-log.txt file

	----------------------------------------------------------
	Please see documentation, change log, and implementation instructions in the formspammertrap-contact-readme.txt file
	----------------------------------------------------------

	- License Information
	This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by he Free Software Foundation, either version 3 of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

	For a copy of the GNU General Public License go to 'www.gnu.org/licenses'

	- Although the program is free, if you wish to donate any amount for my efforts in this contact form, please see the information at www.formspammertrap.com and/or www.securitydawg.com

	INSTRUCTIONS

	General documentation and customization information in fthe associated PDF files that came with the FST program (request the latest version from the https://wwww.FormSpammerTrap.com site). Please read the documentation to help with implementation on your site.

	IF YOU NEED HELP - Use the contact form on the FormSpammerTrap site.

	WE CAN DO YOUR IMPLEMENATION - we'll do all of the implementation work for a nominal fee. Contact us for details. Simple implementation can be completed within 24 hours.

	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

	YOU SHOULD NOT CHANGE ANY VALUES IN THIS FILE - THINGS MIGHT BREAK!

	There is one required setting that you must include in your contact form code. This setting - and all customized settings - is placed in the FST_MORE_FIELDS() function that you MUST add to your contact page.

	IF YOU CHANGE THINGS IN THIS FILE, IT MIGHT BREAK THE WHOLE THING !!!!!

	DO NOT CHANGE ANY FIELDS IN THIS FILE !!!!!!!

	(I tell you three times! [even more than three times] )

	See the documentation.    (RTF docs!)

	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

	DON"T CHANGE ANY OF THESE  VALUES OR THE CODE - OR ANYTHING IN THIS FILE !!!!

	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

	- These are all of the optional settings available.
	- We recommend that you change these values in your contact form page, not in this file.
	- See the formspammertrap-sample-contact-extended.php file for an example of how to customize the settings, including adding additonal fields to your form.
	- Putting the customized values in your contact form page will make your form compatible with future versions - because your customized settings are not in this file .
	- Overriding values in your custom contact form will allow you to easily use FST in multiple contact forms.
	- These additional settings are available for advanced users. The basic form only needs the one required FST_XEMAIL_ON_DOMAIN setting that is placed in the FST_MORE_FIELDS function in your contact form. This is the recipient email for the message, which must be a valid email on your domain.
	- NOte that the FST_XEMAIL_ON_DOMAIN will default to noreply@your-domain-name . If this is not a valid email, the message will disappear into the bit bucket.

	 */
	// --------------------------------------------------------------------------------
	// BEGIN -  Session and Version and PHP Version checking   DO NOT OVERRIDE THESE VALUES
	// --------------------------------------------------------------------------------

	// setup the session if needed (has to be first code executed)
	// Session is started early on 'init' by the plugin. Guard here to avoid warnings if something echoed.
if ( session_status() !== PHP_SESSION_ACTIVE && ! headers_sent() ) {
    if ( function_exists('is_ssl') && is_ssl() ) {
        @ini_set('session.cookie_secure', '1');  // only on HTTPS
    }
    @ini_set('session.use_only_cookies', '1');
    @ini_set('session.cookie_httponly', '1');
    @ini_set('session.cookie_samesite', 'Lax'); // PHP 7.3+
    @session_start();
}
	define('FST_VERSION', " 17.10"); // in case you want to use it on your form
	define('FST_VERSION_DATE', "17  FEB 2024"); // in case you want to use it on your form

	/* New PHP version check - if not PHP version 7.2+, FST won't work.
	- this is placed at the very top here to ensure the notice is shown.
	- additional sanity checks are done later.
	 */
	if (!version_compare(PHP_VERSION, '7.2.0', '>=')) { // returns true if less than 7.2.0    (fixed since version 17.00)
		$msg = 'PHP version 7.2+ is required. You have version' . PHP_VERSION . " . FormSpammerTrap aborted. Please upgrade your site to at least PHP version 7.2 to use FormSpammerTrap (we recommend version 8.x).";
		fst_display_fatal($msg);
		die($msg);
	}
	// --------------------------------------------------------------------------------
	// END -  Version and PHP Version checking   DO NOT OVERRIDE THESE VALUES
	// --------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	//  AVAILABLE CUSTOMIZATION VALUES
	// --------------------------------------------------------------------------------
	/* These are customizations settings that you can override via your forms FST_MORE_FIELDS() function. See the documentation.

	DO NOT CHANGE THESE VALUES OR ANYTHING IN THIS FILE

	SET CUSTOMIZATION THE VALUES IN THE FST_MORE_FIELDS() FUNCTION OF YOUR FORM.

	READ THE DOCS FOR INSTRUCTIONS !!!

	 */

	// --------------------------------------------------------------------------------
	// BEGIN - Show FST Version Info on the form
	// --------------------------------------------------------------------------------
	/* This shows the FST version info on the form, just before the form.
	- this is for debugging/non-production use; it is normally set to false (default) for production sites, although it can be enabled. (We appreciate the visibility of our hard work!)
	- set this in the FST_MORE_FIELDS function of your form page; don't forget to GLOBAL it
	 */
	$FST_SHOW_VERSION = false;
	// --------------------------------------------------------------------------------
	// END - Show Version Info
	// --------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// BEGIN - Email Address Settings
	// --------------------------------------------------------------------------------
	/*      MAIL MESSAGE RECIPIENT'S EMAIL ADDRESS
	This is who will get the Contact Form message. It needs to be a valid email account your domain, or many hosts may reject and not deliver the message.
	 */
	$FST_XEMAIL_ON_DOMAIN = ""; // valid email account on your server, but set this value on your form page code.  If the domain doesn't match the sending domain, then the message will probably get blocked as spam.
	//  Note: only an email address  (since version 17)
	// This is OK -  "your_name@yourdomain.com"
	// This may cause delivery problems - "Your Name <your_name@yourdomain.com>"
	// If the email address doesn't exist, the message will disappear into a black hole, never to be read by anyone (that we know of).

	/*      FROM EMAIL ADDRESS
	valid email address for 'from' value in sent mail; must be something@your-domain.com or mail will be caught by recipient email client.
	Default is noreply@yourdomain.com (default set later)
	 */
	$FST_FROM_EMAIL = ""; // enter valid email on your domain; default is noreply@your-domain.com  . Thie email address must be one on your domain, even if it doesn't exist.

	// since version 17.00
	$FST_FROM_NAME = ""; // enter valid email name, default is the site domaain name. This value will show in the client mail list as the 'from' value

	/*      CC TO EMAIL ADDRESS
	email for CC copies of the message; multiple emails separated by comma character. Leave blank if no CC copies needed

	IMPORTANT NOTE: if you have :
	- set up the $FST_FROM_EMAIL value to a valid email on your domain
	- set up forwarding for that email to go to an external address
	- set the $FST_XCC_EMAIL value to that same forwarding value
	the email may not be delivered . (It won't be delivered on gmail accounts.)
	Using an alias like fred+sitename@gmail.com won't work either (at lest on gmail).
	 */
	$FST_XCC_EMAIL = ""; //valid email address for BCC copies of the message; not validated.

	/*      BCC TO EMAIL ADDRESS
	email for BCC copies of the message; multiple emails separated by comma character. Leave blank if no BCC copies needed

	IMPORTANT NOTE: same thing applies to $FST_XBCC_EMAIL as $FST_XCC_EMAIL (above)
	 */
	$FST_XBCC_EMAIL = ""; //valid email address for BCC copies of the message; not validated.

	/*      PREPEND TO MESSAGE SUBJECT TEXT IN the SENT MESSAGE
	change to text to be prepended to the subject text entered in the form by the visitor
	- this is added to the message subject from the form when mailed
	 */
	$FST_XEMAIL_SUBJECT = "Contact Form Message: "; // the prepended text to the subject line in the sentmessage

	/*     SEND AS HTML MESSAGE FLAG
	set false if you don't want to send HTML mail (false sends text mail)
	 */
	/* NOTE - This now obsolete with version 14, but kept for compatability. All messages will have a plain text and HTML text message automatically
	 */
	$FST_XSEND_HTML = true;

	/*      CHECK EMAIL VALUE
	set true by default to allow email address checking of the email value in form
	- this is not perfect, but close enough
	- set false if you don't want to check email address entered in the form
	 */
	$FST_EMAIL_CHECK = true;

	/* Don't send an email
	- for use when you have another process to send the message; that process is usually in FST_AFTER_SUBMIT
	- default is false, so message from the contact form is sent
	 */
	$FST_NO_MAIL = false;

	/*  Reply-To overrides
	- will set the Reply-To header to these values, overriding the standard Reply-To of the visitor's email
	- useful for using FST in other types of contact forms (subscribes, etc)

	 */

	/*  Set Reply-To email values and overrides (Version 11)
	- Name and Email used in the Reply-To
	- must set $FST_REPLY_TO_OVERRIDE to true to use the $FST_REPLY_TO_EMAIL and $FST_REPLY_TO_NAME in the Reply-To of the message

	 */
	$FST_REPLY_TO_EMAIL = ""; // email address used in Reply-To
	$FST_REPLY_TO_NAME  = ""; // name for the Reply-To value
	/* Override standard Reply-To
	- if true, the email filled in the form will not be set as Reply-To
	- if false, the above values will be added to the form's email value
	- the FST_REPLY_TO_EMAIL and FST_REPLY_TO_NAME will be used as the Reply-To
	 */
	$FST_REPLY_TO_OVERRIDE = false; // set true to use above values instead of the form email as Reply-To

	/* OPTION to not use WP_MAIL function on WordPress site
	- since version 16
	- default is true - use WP_MAIL if on WordPress site
	- false - use MAIL function on WordPress site
	- note that WP uses PHPMailer - you can verify proper functioning by registering as a WP user - if that works, then WP_MAIL is working properly
	 */
	$FST_USE_WP_MAIL = true;

	// --------------------------------------------------------------------------------
	// END - Email Address Settings
	// --------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// BEGIN - reCaptcha settings
	// --------------------------------------------------------------------------------

	/*  RECAPTCHA KEYS (recommended if you want to use reCAPTCHA on the form; although they aren't really needed with all of the other protections of FST)
	if you don't want to use RECAPTCHA, leave these two variables blank. ReCAPTCHA is disabled by default.

	You must get your own keys that are associated with your domain (no sharing!).

	To use Google reCAPTCHA (not required), you need to get your own Google reCAPTCHA key from the Googles. Introduction to reCAPTCHA use is here: https://developers.google.com/recaptcha/intro
	- Sign up for reCAPTCHA is here: https://www.google.com/recaptcha/admin
	- Make sure you specify reCAPTCHA version 2, and the 'invisible reCAPTCHA' when you sign up. Other versions/options won't work with this code
	- Note that the reCAPTCHA keys you use **must** be associated with your domain!
	- reCAPTCHA use is not required with version 4.10+; see below

	WARNING !!!!!!!!!
	If you use the wrong key or one that is not assigned to your domain, the form will not submit !!

	Get your own key! (see above) - or leave both values blank to disable reCAPTCHA
	Remember that CURL must be enabled on your server (see above)*/

	$FST_RECAPTCHA_SITEKEY = ""; // enter your reCAPTCHA site key
	$FST_RECAPTCHA_SECRET  = ""; // enter your reCAPTCHA secret key

	// --------------------------------------------------------------------------------
	// END - reCaptcha settings
	// --------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// BEGIN -   Form and Fields Customization
	// --------------------------------------------------------------------------------

	/*      NUMBER OF URLS ALLOWED IN MESSAGE
	number of URLs allowed in the yourcomment field; spammers like to put lots of them
	but allowing one is reasonable for real folks; change it if you will allow more (or none)*/
	$FST_XURLS_ALLOWED = 1;

	/*      PAGE TO PROCESS THE FORM
	the page that will process the form; by default, set to current page.
	Recommended to leave this as it is, unless you are going to customize your own form response page.

	Note that you can add processing after form submit with a customized FST_CUSTOM_AFTER_SUBMIT() function - see documentation. */
	$FST_XACTION_PAGE = ""; // enter your customized response page (you have to build it). Should be relative to the form's page location.

	/*      TOP OF FORM MESSAGE
	change for text displayed at the top of the actual form */
	$FST_XFORMTOP_MESSAGE = "Please enter all required information.";

	/*      MESSAGE BELOW SUBMIT BUTTON
	this next is shown under the submit button; change if needed; use an empty string if you don't want a message
	you might use this for a GDPR-type privacy message. Text only.*/
	$FST_XSUBMIT_MESSAGE = "We may use your email to contact you, and we may gather site analytics information about your visit. This information is not shared with or sold to others."; // change for message under submit button area

	/*      SUBMIT BUTTON TEXT
	the text to show on the Submit button. Text only.*/
	$FST_XSUBMIT_BUTTON_TEXT = "Send Message";

	/*      JAVASCRIPT REQUIRED MESSAGE
	the text to show above the form to alert that Javascript is required  */
	$FST_XJAVA_REQUIRED = "Please be sure that Javascript is enabled on your computer.  This will assure that your message will be received.";

	/*      MAIL SENT SUCCESS MESSAGE
	message to show on screen when form submitted and mail sent
	you might use this for a GDPR-type privacy message. Text only.*/
	$FST_XTHANKS_MESSAGE = "Thank you for your message. We will reply within 24 hours."; // message shown after form submitted and mail sent

	/*      THANK YOU PAGE REDIRECT URL
	if set to a valid URL, then successful submit/email will redirect to this URL. Must be a complete URL, not a relative location. Leave blank if you just wish to display the Thank You message set above.
	 */
	$FST_XTHANKS_URL = "";

	/*      POP THE THANKS MESSAGE (since version 17)
	if set true, an JS alert with a 'thanks' message will display on form submission and the message is sent (PHP mail() returnes true)
	- default is false
	- uses the FST_POP_THANKS_MESSAGE text (below) as message text
	- this is useful (and maybe required) if your FST form is a popup, rather than inline
	 */
	$FST_POP_THANKS = false;
	/*      POP THE THANKS MESSAGE TEXT (since version 17)
	the 'thanks' text to display if the FST_POP_THANKS flag is set true (above)
	- default is a generic thanks message
	 */
	$FST_POP_THANKS_MESSAGE = "Thanks for contacting us. We will reply as needed.";

	/*      PRIVACY PAGE URL
	if URL exists (and is valid) will show a link to your privacy page.
	Displayed after the 'submit' message.
	- Change to a valid link to your privacy page.
	- Leave blank if you don't need to display a privacy link.
	- You might need this for GDPR compliance.
	- URL should be relative to form page location  */
	$FST_XPRIVACY_PAGE_URL = "";

	/*      HONEYTRAP URL FOR BOTS/SPAMMERS
	The 'action' URL for the site to send bot/spammers. Make sure it is a full URL.
	- Also used to redirect any direct access to the form (like via CURL/WGET)
	- The default value is our FormSpammerTrap site (we don't mind if you use it). The query parameter is used for tracking the number of spammers caught when they get redirected to our site.
	- Spambot 'scrapers' will see this as the action location. It gets changed if a human is filling out the form.
	- the query parameter is used to track the number of spammers caught (v14.01)
	 */
	$FST_XSPAMMER_URL = "https://www.FormSpammerTrap.com?goaway=spammer";

	/*      THE "GO AWAY" MESSAGE
	displayed when a non-human is sensed - or when the visitor tries to resubmit*/
	$FST_XGO_AWAY_MSG = "Invalid form submission; submission cancelled.";

	/*      ID PARAMETER OF CONTACT FORM (required)
	enter the ID of the contact form. Default as shown (new in Version 4)*/
	$FST_XFORMID = "formspammertrapcontactform";

	/*      SHOW ALERT TYPE MESSAGE WHEN FORM TARGET CHANGES (debugging tool)
	if set to true, then show an alert type message when the form target it changed
	-    mostly used to show you things are working; normally set false in production.
	- note that the alert is delayed, and will show for each required field.*/
	$FST_XSHOW_HELLO = false;

	/*      DELAY FACTOR THAT ADDS JS FUNCTION TO REQUIRED FIELDS
	the delay factor (in ms) to delay adding the functions to required fields. Also used for the delay in changing the form's target value. Defaults to '5000' (which is 5 seconds). A value that is too long might affect human users; this five-second  delay is probably sufficient.*/
	$FST_XDELAY = 5000; // default of 5 seconds

	/*   ONLY USE CUSTOMIZED FIELDS ON THE FORM (version 14)

	Set this flag true to only show your FST_XCUSTOM_FIELDS (defined in your form's FST_MORE_FIELDS function) in the form. The 'base' four fields (name, email, subject, message) will not be displayed in the form.
	You will need to process these fields via the FST_AFTER_SUBMIT function in your form's page code. The form's data is not emailed; your functions will need to take care of sending the email.
	This is an advanced feature; see the separate documentation for details and examples.
	 */
	$FST_CUSTOM_FIELDS_ONLY = false; // set to true to override fields shown on form

	/*  ADD RESET FORM BUTTON AT BOTTOM OF FORM (version 15+)

	Set this flag true to show a 'reset' button at the bottom of the form. Default=false;
	Text inside the button is set to FST_LANG_TEXT['reset_button_text'] , default is "Reset Form"
	An example of this button is in the formspammertrap-sample-contact-extended.php file.
	 */
	$FST_SHOW_RESET = false;

	/* SHOW CLOSE FORM BUTTON (version 15+)
	Set this true to show a 'close form' button at the bottom of the form. Default = false.
	Text inside the button is set to FST_LANG_TEXT['close_button_text'] , default is "Close Form"
	Note that closing the form is done via a CSS 'display:none" command via JS, applied against the form ID - which is set to $FST_XFORMID value.
	Closing the form may affect how your page looks, so test it with your site page.
	An example of this button is in the formspammertrap-sample-contact-extended.php file. It looks like it clears the entire page, since the form is the only thing on that test page.
	You can use this with the $FST_CLOSE_REDIRECT setting (below).
	 */
	$FST_SHOW_CLOSE = false; // show the 'close form' button.

	/* CLOSE FORM REDIRECT  (version 15+)
	Sets a redirect location for the "Close Form" button (if enabled)
	Set this to a valid URL on your site. Include the full URL, including domain name.
	When the "Close Form" button clicked, the visitor will be redirected to the page you specify.
	Default is the current page, so the default effect is to reload the page.
	Should be used with the $FST_SHOW_CLOSE setting. With both enabled, the result will be the form will disappear, and the redirect to the new URL will take place. Otherwise, the current page will still be shown, but without the form.
	 */
	$FST_CLOSE_REDIRECT = ""; // set to valid full URL to redirect on the 'close form' button.

	/* CONTAINER NAME FOR THE ENTIRE FORM  (version 15+)
	the ID of the div for the entire form.
	ALlows use of the FST_SHOW_CLOSE button (if enabled) to close (hide) the entire form area
	Note that if you set this to another value, you may need to add your own CSS rules for that form container ID
	This is different from the FST_FORM_ID setting, which is the ID of the form inside the form container.
	 */
	$FST_CONTAINER_ID = 'fst_container';

	/*   SUBMIT BUTTON ID VALUE - (Since version 17.00)
		the id value of the submit button; default is 'fst_submitbutton' (was 'submitbutton' in prior versions)
		- allows for a different value if that ID is used elsewhere

	*/
	$FST_SUBMITBUTTON_ID = 'fst_submitbutton';


	// ------------------$FST_XFORMID--------------------------------------------------------------
	// END - Forms and Fields customization
	// --------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// BEGIN - Messages customization
	// --------------------------------------------------------------------------------

	/*  Text for "Required" ; used in the form to show a required value
	Obsolete for version 16+, this is handled with the $FST_LANG_TEXT['required'] field
	 */
	$FST_MSG_REQUIRED = "(required)";

	// --------------------------------------------------------------------------------
	// END - Messagess customization
	// --------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// BEGIN - DEBUGGING customization
	// --------------------------------------------------------------------------------

	/*      WRITE TO LOG FILE FLAG
	set this flag to true to write to a 'mail sent' log; ensure that you adjust the
	location/name/security of log file . Also make sure that the file is writable*/
	$FST_XWRITELOG = false; // default log file is 'maillogs.log' in current folder

	/*      NAME OF LOG FILE (required if log file flag true)
	name of the mail log file. Will be created if it does not exist.
	- if you specify a folder, do not include a leading slash character;
	note that the folder must already exist or the Sanity Check process will cause the form to fail.
	- file name (including any folder name) is relative to the current folder
	 */
	$FST_XWRITELOGFILE = "maillogs.log"; // default log file namer, will be in current directory

	/*      CAPTURE ALL REQUEST HEADERS ON A SUBMIT
	set to true, will output on screen all request headers on a submit mostly used for debugging the form, so normally false*/
	$FST_XCAPTURE = false;

	/*      SHOW ALL POSTED FIELDS IN MAIL MESSAGE
	normally used when debugging, this will show all $_POST values from the form in the sent email message, in addition to the nromal message.*/
	$FST_XSHOW_SUBMIT = false;

	/*      SHOW ALL $SERVER VARIABLES IN MESSAGE *
	normally used when debugging, will show all $_SERVER variables in the sent message
	 */
	$FST_XSHOW_SERVER = false;

	/* Mail Sanity Check flag
	- if true, will email the sanity check results to the default email (FST_XEMAIL_ON_DOMAIN)
	- this will prevent sanity check errors appearing on the user's screen in case something has chan   ged after deployment
	- a polite error message will be displayed instead of the form that the form is not available
	- if false, sanity check error messages are displayed on screen

	- recommend setting false during development, and true for production
	 */
	$FST_SANITY_CHECK_EMAIL = false; // should be set to true in production. Default of false causes the Sanity Check message to be displayed on screen. This helps as you  are building/testing the form.

	/*  Show email header in sent message (since version 16)
	- true - send email header array as text in the sent email; useful for debugging email header issues
	- false (default) - do not include email header array

	NOTE: since the header will contain several email addresses, the email might be processed as spam because of multiple email addresses in the message. During development, this setting is helpful as long as you are aware that your test messages might show up as spam - or get blocked.

	 */
	$FST_SHOW_HEADER = false;

	// --------------------------------------------------------------------------------
	// END - Debugging Info customization
	// --------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// BEGIN - Additional Fields customization
	// --------------------------------------------------------------------------------

	/* Define where to insert custom/added fields (all, as a block of fields)
	- three locations are available:
	1 =  before the first field on the form (before 'your name')
	2 =  just before comment area (default if not specified)
	3 =  after the comment area (the last field)
	- value is normally an integer (not string). Any other value will cause the default to be used (before comment area).
	- all customized/added fields will be places in this area
	 */
	$FST_CUSTOM_FIELDS_LOCATION = 2; // value 1, 2, 3; must be integer, not string , default is 2

	/* Added CSS class for customized/added
	- allows you to specify a CSS class for the block of added fields. No checking if the class exists. Could be used to put a colored box around the added fields.

	Note: uses that class around each individual added field. See next item.
	 */
	$FST_CUSTOM_FIELDS_CSS_CLASS = "";

	/* CSS class for entire customized/added fields (not just individual added fields
	- could be used to put a colored box around all of the added fields, not just the individual fields

	 */
	$FST_CUSTOM_FIELDS_CSS_CLASS_MASTER = "";

	/*
	Enable only the short fields option, where only the name and email address is shown.
	- this allows you to have a quick signup form, for example, since all you are interested in is the name and email address for your database.
	- this should be enabled (true) along with the database settings below (gotta have a place to store the data
	- the contact form will just be the name and email address fields
	 */
	$FST_SHORT_FORM = false; // true = only name/email on form, false (default) = full form (name/email/subject/message)

	/*
	Array used for  additional form fields. Should be defined in your contact form; do not put the array here. See documentation for use.  */
	$FST_XCUSTOM_FIELDS = array(); // replace this with your custom fields if needed

	/* Array of required fields. Add to this array in your contact form, not here
	- these fields are required by default; you can override in your custom contact form

	- but if you use required fields with other names, you are responsible for sending the message with these parameters.

	- We recommend that you use these field names in your custom form, if you override them. (These are the field names used by the fst_four_fields function, which displays these default form fields.)

	 */
	$FST_REQUIRED_FIELDS = array("your_name", "your_email", "your_subject", "message");

	/* Array to insert additional code in the form area.

	!!!!!!!  WARNING !!!!!!!  !!!!!!!  WARNING !!!!!!!  !!!!!!!  WARNING !!!!!!!
	You must ensure that the code is correct; there is no syntax checking.
	!!!!!!!  WARNING !!!!!!!  !!!!!!!  WARNING !!!!!!!  !!!!!!!  WARNING !!!!!!!

	- an example might be if you wanted to insert a hidden field (although v15+ allows you to define a hidden or read-only field), or a customized option or checkbox block
	- the ORDER parameters is used to specify where to put the additional code
	- three locations are available:
	1 =  before the first field on the form (before 'your name')
	2 =  just before comment area (default if not specified)
	3 =  after the comment area (the last field)

	Example:
	$FST_CODE_INSERT[] = array (
	'CODEBLOCK' => "<input type='hidden' name='extrastuff' id='extrastuff' value='a hidden value'>",   // must be HTML code for an form input
	);

	Since version 17.00
	- the  $FST_CODE_INSERT value can also be a code string

	!!!!!!!  WARNING !!!!!!!  !!!!!!!  WARNING !!!!!!!  !!!!!!!  WARNING !!!!!!!
	!!!!!!!  THERE BE DRAGONS HERE !!!!   THERE BE DRAGONS HERE !!!!   THERE BE DRAGONS  !!!!

	You are responsible for ensuring the extra HTML code is properly formatted. And sanitized.
	There could be security issues in your code, so ensure it is correct.

	Your code is not sanitized!  "Careful, grasshopper!"

	!!!!!!!  WARNING !!!!!!!  !!!!!!!  WARNING !!!!!!!  !!!!!!!  WARNING !!!!!!!

	 */
	$FST_CODE_INSERT = array();

	/* Where to put the custom code ($FST_CODE_INSERT array)
	- three locations are available:
	1 =  before the first field on the form (before 'your name')
	1 =  before the first field on the form (before 'your name')
	2 =  just before comment area (default if not specified)
	3 =  after the comment area (the last field)
	- default is 2
	 */

	$FST_CODE_INSERT_LOCATION = 2; // value 1, 2, 3; must be integer, not string , default is 2

	// --------------------------------------------------------------------------------
	// END - Field Customizastion area
	// --------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// BEGIN - Upload File customization
	// --------------------------------------------------------------------------------

	/* Note that there are several upload options that might be set, according to your needs.
	- check all default values below, and change them in your FST_MORE_FIELDS() function in your contact form to your requiredments.
	 */

	/* Allowed extensions if you add a file upload to the form via FST_MORE_FIELDS() function in your form. Extensions are an array of values. Be careful about allowing non-doc or non-image type files (like zips). You are responsible for security implications of any file that you recieve. Should be defined in your contact form, not here; see documentation.
	Example:
	$FST_UPLOAD_EXTENSIONS = array(".pdf", ".jpg", ".jpeg", ".bmp", ".png");

	Note: make sure that there is a leading period character in the array, so that the browse btuton will show only those types of files (new in version 14). (Although v14 will insert the leading period character if you forget. Isn't that nice?)

	The available extensions are shown on the form under the Browse button. They are separated with a 'wbr' tag which will allow the list of fields to not overflow it's container (version 15.10).

	 */
	$FST_UPLOAD_EXTENSIONS = array(); // should be empty unless you want uploads; otherwise set as the example. Make sure the FST_UPLOAD_FOLDER (next item) uploads folder exists.

	/* Upload folder if you are going to use an aupload button in your form. Sanity check process will ensure folder exists. If no upload button planned, leave this blank. The folder must be off of the folder where the form is.

	- Create that folder to allow uploaded files to be stored.
	- Folder should not have leading or trailing slash, and relative to the form's folder.
	- The folder must exist, or the file will not be stored.
	- The folder location is relative to the location of the contact form; normally off of the site root.

	- NOTE/WARNING *************

	 ***** the file will be deleted from the uploads folder if the  $FST_UPLOADS_DELETE is set true (default)

	 */
	$FST_UPLOAD_FOLDER = ""; // folder is placed in the site root

	/* Flag to delete the uploaded file (if any) after the message sent
	- true = yes, delete it (default)
	- false = no, keep it for later use

	Note that if your form allows uploads, then this must be set false to keep the file in the uploads folder. By default, the file is deleted, even if successfully uploaded.
	 */
	$FST_UPLOADS_DELETE = true;

	// --------------------------------------------------------------------------------
	// END -  File Uploads section
	// --------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// BEGIN - Save Contact Info section
	// --------------------------------------------------------------------------------

	/* Flag to store name and email in the database. Set true to store the data. If true:
	- there will be a checkbox (empty) for permission to store name and email. The checkbox will always display if true.
	- you ae responsble for any compliance with GDPA-type requirements for your site. You should also have a privacy notice about storing this data.
	- you are responsbile for any process that allows the person to unsubscribe and/or remoove their data
	- database credentials need to be properly configured (next section).
	- Sanity Check will check database credentials. Ensure no Sanity Check messages during form testing.
	- if set false, there will be no verify 'save info' message

	You can override this value in your customized form page.
	 */
	$FST_SAVE_CONTACT_INFO = false; // true if name/email should be saved; will also Sanity Check database credentials (next item)

	/* Message to display after the 'save email' checkbox, if enabled */
	$FST_SAVE_CONTACT_MESSAGE = "Check this box to allow us to save your name and email for any needed contact other than responding to this message. We don't spam, and we don't share your information.";

	/* Label to place to the left of the 'save contact' checkbox. Default is blank.
	 */
	$FST_SAVE_CONTACT_LABEL = "";

	/* DATABASE CONNECTION CREDENTIAL DETAILS: this is the info and credentials for the database and table storing emails of contacts, specified in an array. Database will not be used if previous $FST_SAVE_CONTACT_INFO is set to false.

	Note that there are additional required fields in the structure for version 10+:
	- FIELD_GUID
	= FIELD_STATUS
	These additional fields must be in the structure of the database

	Elements of the array are as follows

	- DATABASE_LOC = localhost by default
	- DATABASE_NAME - full name of the database
	- DATABASE_USER - full user name allowed access to the database. User should have read/write/delete access.
	- DATABASE_PASS - full password of the user
	- FIELD_EMAIL   - field name to store the email address. Email from the form stored here
	- FIELD_FULLNAME - field name for the full name of the user, from the form
	- FIELD_DATESTAMP - field name for the 'last updated' datestamp
	- FIELD_GUID (v10+) - field name for a GUID value, used for verification
	- FIELD_STATUS (v10+) - field name for verification status (10=unverified, 20=verified)
	- FIELD_DEFAULTS (v16+) - an array of extra fields and their values to match your database. The elements are the field name (which much exist in your database), and the default value store in that field.

	Example array . All values are required. The 'key' name for each entry should not be changed.
	$FST_CONTACT_DATABASE = array(
	"DATABASE_LOC" => "localhost",
	"DATABASE_NAME" => "sitedata.dbname",       // the database name on your site. Required, must exist
	"DATABASE_USER" => "sitename.username",     // the user name for that database. Required. Must be valid.
	"DATABASE_PASS" => "VerySecurePassword",    // the secure/strong password for the database. Required. Password strength not checked.
	"DATABASE_TABLE" => "CONTACT",              // the table in the database to store contact information
	"FIELD_EMAIL"   => "CONTACT_EMAIL",         // the field name for the email address. Required. Must be at least 25 length.
	"FIELD_FULLNAME" => "CONTACT_FULLNAME",         // the field name to store the name entered into the form. Required. Must be at least 50 length
	"FIELD_DATESTAMP" => "LAST_UPDATE",     // datestamp field for last record update datestamp
	"FIELD_GUID" => "GUID",                 // GUID field, used for verification messages
	"FIELD_STATUS" => "STATUS",         // status of entry: 10=unverified, 20=verified
	"FIELD_DEFAULT" => array ("fieldname1" => "fieldvalue 1", "fieldname2" => "field value 2"),
	);

	The "Sanity Check" function will ensure that the values are correct and the database can be accessed:
	- check that key names are correct. Key names should not be changed.
	- do a test connection to the database to ensure it exists.
	- do a test connection to the table.
	- check the field names in the table for existance
	- check the fields for proper type
	- check for the existance of any extra fields you have specified in the FIELD_DEFAULT array.

	An error message will be displayed if the database connection fails.

	Note that the database should only contain at least the one table (in the examople "CONTACT"). You should ensure that the credentials are strong (including the password). YOu can have other tables in that database, but only the table you speficy will be used by FST. Make sure that the database credentials (user/pass) should be unique to all of your site databases.
	User credentials should have Create, Delete, Index, Insert, Select, and Update privileges. Without those credentials, the Sanity Check for the database will fail.

	 */
	// Get WordPress database configuration if available
if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASSWORD')) {
    $FST_CONTACT_DATABASE = array(
        "DATABASE_LOC" => DB_HOST,
        "DATABASE_NAME" => DB_NAME,
        "DATABASE_USER" => DB_USER,
        "DATABASE_PASS" => DB_PASSWORD,
        "DATABASE_TABLE" => (isset($wpdb) ? $wpdb->prefix : (isset($GLOBALS['table_prefix']) ? $GLOBALS['table_prefix'] : 'wp_')) . "fst_contacts",
        "FIELD_EMAIL" => "email",
        "FIELD_FULLNAME" => "name",
        "FIELD_GUID" => "guid",
        "FIELD_STATUS" => "status",
        "FIELD_DATESTAMP" => "last_updated"
    );
} else {
    // Fallback to empty array if not in WordPress
    $FST_CONTACT_DATABASE = array(
        "DATABASE_LOC" => "",
        "DATABASE_NAME" => "",
        "DATABASE_USER" => "",
        "DATABASE_PASS" => "",
        "DATABASE_TABLE" => "",
        "FIELD_EMAIL" => "",
        "FIELD_FULLNAME" => "",
        "FIELD_GUID" => "",
        "FIELD_STATUS" => "",
        "FIELD_DATESTAMP" => "",
    );
}

	// --------------------------------------------------------------------------------
	// END - Save Contact Info section
	// --------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// START  - Email Signup Verification section
	// --------------------------------------------------------------------------------

	/* SEND VERIFICATION EMAIL ON SIGHUP
	- this flag, if true, will send out a signup verification email. A special link will be added to the email that will automatically verify the signup. The link will included the GUID value stored in the database.
	- on verification, the status of the entry will be changed from 10 (set during signup) to 20 (set when verified)
	- Ensure that you have setup the database properly; the sanity check will ensure that the fields are correctly defined.
	 */

	$FST_CONTACT_VERIFY = false; // set to true if you want to send out a signup verification message. If so, the status of the entry will be set to 10 (unverified) or 20 (verified)  . Default is to not send out a verify message (but check your GDPR-type requirements).

	/* URL to process to verify signup  by a 'subscriber'
	- if you have set FST_CONTACT_VERIFY true, then need a page to verify the signup
	- you are responsible for the verify process
	- Sanity Check will check if this value is set if FST_CONTACT_VERIFY true, but only checks that the URL is value, not that it exists.
	- the URL will contain
	- a query parameter of 'id' will be added to the URL. This is a GUID value that is created and displayed in the message. The GUID value matches the user_guid value that is inserted into the database for the subscriber
	- the verify query parameter should be 'verify=<somevalue>'
	-
	 */
	$FST_CONTACT_VERIFY_URL = ""; // must be set to actual verify page if FST_CONTACT_VERIFY true

	/*
	- verify subject and message sent in the verify email.
	- defaults to a standard message
	- allows you to customize the verify message
	- suggest putting the message in a separate function in your contact form; easier to build and format. Can include HTML
	- during testing, try with default message and customize from there
	- The Verify Message is placed at the top of the message, then there is some standardized text. Note that the text of the Verify Message and Subject is only in English, and does not at this time have a FST_LANG_TEXT value that can be used. Message can be overrident with the FST_CONTACT_VERIFY_MESSAGE_DATA array elements.
	 */
	$FST_CONTACT_VERIFY_MESSAGE = ""; // message to be added to the beginning of the verify email
	$FST_CONTACT_VERIFY_SUBJECT = ""; // subject line of that verify message

	/* Site Name
	- used in the signup Verify success message. Should be the 'tiitle' of your site
	- if not defined (default), the URL of your site will be used
	 */
	$FST_SITE_NAME = "";

	/* Verify page redirect
	- this is link the full URL to the page you want to show on the verification page after a newsletter signup with verify.
	- defaults to the site URL<br>
	- Make sure you specify a complete URL; no validation on the value
	 */
	$FST_CONTACT_VERIFY_REDIRECT = "https://" . $_SERVER['HTTP_HOST'];

	/*  Do not email with normal process
	- if set true, the email will not be sent via the normal process
	- you are responsible for sending mail with the FST_AFTER_SUBMIT function of your form
	 */
	/* Version 12 - revised way for verify email content/parameters. Used to specify content/etc of the verification email, if sent
	'verify_url' = 'verify url', //a 'id' parameter of a guid is added automatically. You are responsbile for that process.
	'verify_message' = 'message',   // message to include in the verify email
	'verify_subject' = 'verify-email-subject',  // subject line of the verify email
	'verify_site_name' = 'site name',       // site name, used in verify email message
	'verify_from_name' = 'verify from name',    // 'from' name used in email
	'verify_from_email' = 'verify email',   // 'from' email used in email
	'verify_reply_to'   = 'reply email',    // reply-to parameter in verify email
	 */

	$FST_CONTACT_VERIFY_MESSAGE_DATA = array(
		'verify_site_name' => '', // site name, used in verify email message
		'verify_from_name' => '', // 'from' name used in email
		'verify_from_email' => $FST_XEMAIL_ON_DOMAIN, // 'from' email used in email
		'verify_reply_to' => $FST_XEMAIL_ON_DOMAIN, // reply-to parameter in verify email
		'verify_url' => 'verify url', //a 'id' parameter of a guid is added automatically. You are responsbile for that process.
		'verify_message' => 'Please verify your intention to be added to our mailing list by clicking the link below. If you do not wish to be added, just ignore this message.', // message to include in the verify email
		'verify_subject' => 'Email Subscription Verification', // subject line of the verify email
		'verify_contact' => $FST_XEMAIL_ON_DOMAIN, // verify contact email or url
	);

	$FST_NO_MAIL = false; // if true, don't send an email with normal process

	// --------------------------------------------------------------------------------
	// END - Email Signup Verification section
	// --------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// BEGIN - CSS global settings section
	// --------------------------------------------------------------------------------

	/*  Enable/disable dark mode for the FST container (FST_CONTAINER_ID) only.

	This is useful for sites that have dark backgrounds. All colors are 'flipped' using this CSS if enabled. Normal setting is disabled - the 'light' mode. Note that your should ensure any custom coloring you specify in options to display properly in dark mode. Override the fst CSS colors as needed.

	Dark mode only inverts the colors in any element of the form. It does not affect browser light/dark preferences. If you want to allow browser light/dark preferences to apply to your whole site, then do not enable this option. Instead, you should use CSS such as this (comment the first line):

	media queries for light/dark
	@media (prefers-color-scheme: dark) {
	filter: invert(1) hue-rotate(180deg) !important;
	}

	@media (prefers-color-scheme: light) {
	filter: invert(0) hue-rotate(180deg) !important;
	}

	 */
	$FST_DARK_MODE_ENABLE = false; // set to true if dark mode for the form desired

	/*
	FST_MORE_CSS   version 10.10
	- allows insert of additional CSs via the fst_contact_css function
	- for when CSS doesn't work in the page code
	- must be valid CSS code, including the opening/closing <styles>
	- there is no syntax checking of the code
	- called at the end of the FST_contact_css function
	- WARNING - No sanitization of this CSS code !! Make sure it is valid !!
	 */
	$FST_MORE_CSS = ""; // set empty string, define it in the  FST_MORE_FIELDS function of your form

	/* Add required field styling (version 11)
	- adds red/yellow/green input field borders depending on validation
	- red = required field input missing
	- yellow = missing
	- green = required field input OK
	- default is no input box border styling (false)
	 */
	$FST_REQUIRED_FIELD_COLORS = false; // set to true to color required borders

	/* Adds customized border color/width/style for required fields via an array. (version 15+)
	- Array elements are as shone in the default below.
	- Override in your FST_MORE_FIELDS function.
	- you can specify color names, or values (hex or RGB)
	- note that require fields will get the invalid settings on initial page load, as a blank entry is empty and therefore invalid. (This is the way browsers work.)
	- Make sure your customized settings are an array, or the values will default to these values.
	- these array elements are required, any missing is set to default values:
	'background-color'
	'border-color'
	'border-width'
	'border-top-style'
	'border-right-style'
	'border-bottom-style'
	'border-left-style'
	'border-radius'
	'color'   (text color)
	 */
	// sets default/empty values for these array; you can redefine in fst_more_fields in your contact form. See array structure above.
	$FST_INPUT_REQUIRED = array();
	$FST_INPUT_VALID    = array();
	$FST_INPUT_INVALID  = array();

	/* Show required message at top of form above fields  (version 15+)
	- default = false; don't show the mssage
	- message text is defined in the $FST_LANG_TEXT array as
	$FST_LANG_TEXT['show_required_message']
	- customize that message in the FST_MORE_FIELDS function
	 */
	$FST_SHOW_REQUIRED_MESSAGE = false;

	// --------------------------------------------------------------------------------
	// END - CSS global settings section
	// --------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// BEGIN - PHPMailer SETTINGS (since version 17.00)
	// --------------------------------------------------------------------------------
	/*
	PHPMailer is enabled by default because of the ease of implementing file attachments, among other nice advantages.
	It is also required to use. The Sanity Check will fail if PHPMailer not properly installed. The exception to this is if your form has enabled FST_MAIL_ALT because you are using your own mail process.
	- File attachments without using PHPMailer are not supported, unless yo FST_MAIL_ALT

	- any changes to these settings are done in your form's FST_MORE_FIELDS() function (don't forget to GLOBAL them there first). Do not change values here
	- note that SMTP is disabled by default; if used, you must also provide SMTP user/pass/port values. Your SMTP settings are not validated. Test SMTP useage prior to production.

	TO IMPLEMENT
	- the phpmailer folder must be a subfolder of the folder containing your contact form. That folder should contain three files with these exact names:
		- Exception.php
		- PHPMailer.php
		- SMTP.php
	- you are responsible for downloading and installing these three files from https://github.com/PHPMailer/PHPMailer
	- See installation instructions for details

	- put this line at the top of your contact form code page
	<?php use PHPMailer\PHPMailer\PHPMailer; ?>
	(remove the PHP tags if they already exist in your form)
	- PHP Mailer is used by default

	- if you wish to use your SMTP server, then add the FST_SMTP_* variables to your contact page FST_MORE_FIELDS() function. Include the GLOBAL commands for each variable first.  The values you set are not verified - if incorrect, you'll see a message on the screen. Verify proper values before your contact page goes 'live'.

	 */
	$FST_USE_PHPMAILER = true; // enabled by default; set to false to use your own mail process (FST_MAIL_ALT() function).
	$FST_SMTP_ENABLE   = false; // enable if using SMTP capabilities in PHPMailer
	$FST_SMTP_HOST     = ""; // SMTP host name
	$FST_SMTP_AUTH     = ""; // SMTP authorization
	$FST_SMTP_USER     = ""; // for SMTP user name; not validated (filtered with htmlspecialchars, so watch for possible filtered characters)
	$FST_SMTP_PASS     = ""; // for SMTP password; not validated (filtered with htmlspecialchars, so watch for possible filtered characters)
	$FST_SMTP_PORT     = ""; // for SMTP port number; not validated
	$FST_SMTP_SECURE   = ""; // for SMPT Secure setting; not validated


	// --------------------------------------------------------------------------------
	// END - - PHPMailer SETTINGS (since version 17.00)
	// --------------------------------------------------------------------------------

	/*
	CUSTOMIZATION SETTINGS ENDS

	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

	FORMSPAMMERTRAP CONTACT PAGE CODE STARTS - DON'T CHANGE ANYTHING AFTER THIS!

	(You can look, but don't touch!)

	If you change below code, any new FST release will break your changes.

	We recommend that you change the customization values in your contact form, as we have done in the formspammertrap-sample-contact-extended.php file (which also shows you how to override the settings in this file).

	If there is something that you think needs changing for your site that might be useful to you or others, let us know via the Contact form at https://www.FormSpammerTrap.com

	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

	 */

	// ------------------------------------------------------
	// this loaded on all pages
	// ------------------------------------------------------

	// ------------------------------------------------------
	/* since Version 14
	- If FST is used in a WordPress template, we set the FST_XEMAIL_ON_DOMAIN to the site admin's email
	- This will be overridden
	- if the template overrides via a FST_MORE_FIELDS function in their template
	- if there are attributes in the shortcode
	- We check if the WP function 'get_bloginfo' is defined to determine if FST is running via a WP template
	- This must happen before the FST_MORE_FIELDS function is called; any setting of $FST_XEMAIL_ON_DOMAIN in the FST_MORE_FIELDS function of the template will override this value.
	 */
	if (function_exists('get_bloginfo')) { // most likely a WordPress function
		if (get_bloginfo('admin_email')) { // set it only if there is a value (a double-check)
			$FST_XEMAIL_ON_DOMAIN = get_bloginfo('admin_email');
		}
	}

	// these are the default values for input field CSS rules   They will be merged with any values from FST_MORE_FIELDS To provide the final rules for input fields
	$FST_DEFAULT_INPUT_REQUIRED = array( /* for Required Fields */
		'background-color' => '#FFE0E0',
		'border-color' => '#FF0000',
		'border-width' => '2px',
		'border-top-style' => 'solid',
		'border-right-style' => 'solid',
		'border-bottom-style' => 'solid',
		'border-left-style' => 'solid',
		'border-radius' => '5px',
		'color' => 'black',
	);
	$FST_DEFAULT_INPUT_VALID = array( /* for Valid fields */
		'background-color' => '#C1F5C1',
		'border-color' => '#1AFF1A',
		'border-width' => '2px',
		'border-top-style' => 'solid',
		'border-right-style' => 'solid',
		'border-bottom-style' => 'solid',
		'border-left-style' => 'solid',
		'border-radius' => '5px',
		'color' => 'black',
	);
	$FST_DEFAULT_INPUT_INVALID = array( /* for Invalid fields */
		'background-color' => '#FFFFC7',
		'border-color' => '#C1F5C1',
		'border-width' => '2px',
		'border-top-style' => 'solid',
		'border-right-style' => 'solid',
		'border-bottom-style' => 'solid',
		'border-left-style' => 'solid',
		'border-radius' => '5px',
		'color' => 'black',
	);

	// ------------------------------------------------------
	// load any customized-type variables if available via the optional FST_MORE_FIELDS function.
	// note that all those variables must be defined as GLOBAL to get their scope into this file
	if (function_exists('FST_MORE_FIELDS')) {FST_MORE_FIELDS();} // override settings; will be filtered and defined as constants later

	// merge CSS input rules for final version after validating they are arrays
	// each array is validated; if not, the default values will be used

	if (is_array($FST_INPUT_REQUIRED)) {
		$FST_INPUT_REQUIRED = array_replace($FST_DEFAULT_INPUT_REQUIRED, $FST_INPUT_REQUIRED);
	} else {
		$FST_INPUT_REQUIRED = $FST_DEFAULT_INPUT_REQUIRED;
	}
	if (is_array($FST_INPUT_VALID)) {
		$FST_INPUT_VALID = array_replace($FST_DEFAULT_INPUT_VALID, $FST_INPUT_VALID);
	} else {
		$FST_INPUT_VALID = $FST_DEFAULT_INPUT_VALID;
	}
	if (is_array($FST_INPUT_INVALID)) {
		$FST_INPUT_INVALID = array_replace($FST_DEFAULT_INPUT_INVALID, $FST_INPUT_INVALID);
	} else {
		$FST_INPUT_INVALID = $FST_DEFAULT_INPUT_INVALID;
	}

	// ------------------------------------------------------
	/*    since version 16 - checking for any shortcode apps if in WordPress
	current allowed attributes in the 'formspammertrap' shortcode (others are ignored)
	- email = primary email recipient (must be on domain)
	- cc    = CC email recipient (can be outside domain)
	- bcc   = BCC email recipient (can be outisde domain
	Note that FST sets a constant for the attributes for possible future use.
	 */
	if (function_exists('get_bloginfo')) { // won't exist in non-WP environment
		// get shortcode atts into an array, otherwise false
		$atts = fst_get_shortcode_atts("formspammertrap");
		// attributes found, so set some variables after globaling them
		// shortcode atts will override values in FST_MORE_FIELDS
		// shortcode attribute names are forced lowercase by WP
		if (is_array($atts)) {
			foreach ($atts as $attribute => $value) {
				switch ($attribute) {
					case "email":
						// main email address
						$FST_XEMAIL_ON_DOMAIN = $value;
						break;

					case "cc":
						// CC email address
						$FST_XCC_EMAIL = $value;
						break;

					case "bcc":
						// BCC email address
						$FST_BCC_EMAIL = $value;
						break;
				}
			}
		}
	}

	// sanitize all custimization values
	// since version 17, FILTER_SANITIZE_STRING (depreacted in PHP 8.1+) replaced with FILTER_SANITIZE_FULL_SPECIAL_CHARS or htmlspecialchars or other filters
	//echo "email domain before filter 969 " . $FST_XEMAIL_ON_DOMAIN . "<br>";
	$FST_XCC_EMAIL        = filter_var($FST_XCC_EMAIL, FILTER_SANITIZE_EMAIL);
	$FST_XEMAIL_ON_DOMAIN = filter_var($FST_XEMAIL_ON_DOMAIN, FILTER_SANITIZE_EMAIL);
	//echo "email domain after filter 972 " . $FST_XEMAIL_ON_DOMAIN . "<br>";
	$FST_XBCC_EMAIL = filter_var($FST_XBCC_EMAIL, FILTER_SANITIZE_EMAIL);

	$FST_XSUBMIT_MESSAGE     = filter_var($FST_XSUBMIT_MESSAGE, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$FST_XSUBMIT_BUTTON_TEXT = filter_var($FST_XSUBMIT_BUTTON_TEXT, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$FST_XTHANKS_MESSAGE     = filter_var($FST_XTHANKS_MESSAGE, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$FST_XPRIVACY_PAGE_URL   = filter_var($FST_XPRIVACY_PAGE_URL, FILTER_SANITIZE_URL);
	$FST_XSPAMMER_URL        = filter_var($FST_XSPAMMER_URL, FILTER_SANITIZE_URL);
	$FST_XTHANKS_URL         = filter_var($FST_XTHANKS_URL, FILTER_SANITIZE_URL);
	$FST_XGO_AWAY_MSG        = filter_var($FST_XGO_AWAY_MSG, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$FST_XJAVA_REQUIRED      = filter_var($FST_XJAVA_REQUIRED, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$FST_REQUIRED_FIELDS     = filter_var_array($FST_REQUIRED_FIELDS, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$FST_UPLOAD_EXTENSIONS   = filter_var_array($FST_UPLOAD_EXTENSIONS, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	// since version 14 - let's ensure each extension has a leading periods, which is required in this version
	$FST_UPLOAD_EXTENSIONS = array_unique($FST_UPLOAD_EXTENSIONS); // remove dups (version 15.10)
	$temp                  = $FST_UPLOAD_EXTENSIONS; // initialize
	$FST_UPLOAD_EXTENSIONS = array();
	foreach ($temp as $item) {
		if (substr($item, 0, 1) != ".") {
			$FST_UPLOAD_EXTENSIONS[] = "." . $item;} else {
			$FST_UPLOAD_EXTENSIONS[] = $item;}
	}
	$FST_UPLOAD_FOLDER  = filter_var($FST_UPLOAD_FOLDER, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$FST_UPLOADS_DELETE = filter_var($FST_UPLOADS_DELETE, FILTER_VALIDATE_BOOLEAN);
	$FST_FROM_EMAIL     = filter_var(trim($FST_FROM_EMAIL), FILTER_SANITIZE_EMAIL);
	if (!$FST_FROM_EMAIL) {$FST_FROM_EMAIL = "noreply@" . trim(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']));}
	if (!$FST_FROM_NAME) {$FST_FROM_NAME =  trim(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']));}
	$FST_FROM_NAME = htmlspecialchars($FST_FROM_NAME);     // since version 17.00

	// make sure the honeypot URL has been specified; default to the FormSpammerSite if invalid
	if (!$FST_XSPAMMER_URL) {$FST_XSPAMMER_URL = "https://www.FormSpammerTrap.com";}
	$FST_XURLS_ALLOWED = function_exists('get_option') ? intval(get_option('fst_max_urls_allowed', 1)) : intval($FST_XURLS_ALLOWED); // convert to int - FIXED by plugin
	// build the privacy page link if needed
	if ($FST_XPRIVACY_PAGE_URL) {
		$FST_XPRIVACY_PAGE_LINK = "<a href='" . $FST_XPRIVACY_PAGE_URL . "' target = '_blank' title='Our Privacy page'>Our Privacy Page</a>";
	} else {
		$FST_XPRIVACY_PAGE_LINK = "";
	}
	$FST_SAVE_CONTACT_MESSAGE = filter_var($FST_SAVE_CONTACT_MESSAGE, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$FST_SAVE_CONTACT_LABEL   = filter_var($FST_SAVE_CONTACT_LABEL, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$FST_EMAIL_CHECK          = filter_var($FST_EMAIL_CHECK, FILTER_VALIDATE_BOOLEAN); // since version 10
	$FST_CONTACT_VERIFY       = filter_var($FST_CONTACT_VERIFY, FILTER_VALIDATE_BOOLEAN); // since version 10
	$FST_SITE_NAME            = filter_var($FST_SITE_NAME, FILTER_SANITIZE_FULL_SPECIAL_CHARS); //since version 10
	// if blank, default to host name
	if (!$FST_SITE_NAME) {$FST_SITE_NAME = $_SERVER['HTTP_HOST'];}
	$FST_CONTACT_VERIFY_REDIRECT = filter_var($FST_CONTACT_VERIFY_REDIRECT, FILTER_VALIDATE_URL); // new in version 10
	$FST_CUSTOM_FIELDS_LOCATION  = intval($FST_CUSTOM_FIELDS_LOCATION); // convert to integer, just in case specified as string
	$FST_CUSTOM_FIELDS_LOCATION  = filter_var($FST_CUSTOM_FIELDS_LOCATION, FILTER_VALIDATE_INT, array(
		'options' => array(
			'min_range' => 1,
			'max_range' => 3
		))); // makes sure value is integer (will be range validated later
	if (!$FST_CUSTOM_FIELDS_LOCATION) {$FST_CUSTOM_FIELDS_LOCATION = 2;} // moddle position is default
	$FST_CUSTOM_FIELDS_CSS_CLASS        = htmlspecialchars($FST_CUSTOM_FIELDS_CSS_CLASS);
	$FST_CUSTOM_FIELDS_CSS_CLASS_MASTER = htmlspecialchars($FST_CUSTOM_FIELDS_CSS_CLASS_MASTER);

	// since version 10.10
	$FST_CODE_INSERT_LOCATION = filter_var($FST_CODE_INSERT_LOCATION, FILTER_VALIDATE_INT);
	// if set true, don't send email
	$FST_NO_MAIL            = filter_var($FST_NO_MAIL, FILTER_VALIDATE_BOOLEAN);
	$FST_CONTACT_VERIFY_URL = filter_var($FST_CONTACT_VERIFY_URL, FILTER_SANITIZE_URL);

	// since version 11.x
	$FST_REPLY_TO_EMAIL              = filter_var($FST_REPLY_TO_EMAIL, FILTER_SANITIZE_EMAIL);
	$FST_REPLY_TO_NAME               = filter_var($FST_REPLY_TO_NAME, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$FST_REPLY_TO_OVERRIDE           = htmlspecialchars($FST_REPLY_TO_OVERRIDE);
	$FST_REQUIRED_FIELD_COLORS       = htmlspecialchars($FST_REQUIRED_FIELD_COLORS);
	$FST_SHOW_REQUIRED_MESSAGE       = filter_var($FST_SHOW_REQUIRED_MESSAGE, FILTER_VALIDATE_BOOLEAN);
	$FST_DARK_MODE_ENABLE            = filter_var($FST_DARK_MODE_ENABLE, FILTER_VALIDATE_BOOLEAN);
	$FST_XSEND_HTML                  = filter_var($FST_XSEND_HTML, FILTER_VALIDATE_BOOLEAN);
	$FST_SHOW_VERSION                = filter_var($FST_SHOW_VERSION, FILTER_VALIDATE_BOOLEAN);
	$FST_CONTACT_VERIFY_MESSAGE      = filter_var($FST_CONTACT_VERIFY_MESSAGE, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$FST_CONTACT_VERIFY_MESSAGE_DATA = filter_var($FST_CONTACT_VERIFY_MESSAGE_DATA, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	// filter all custom messages (since version 14)
	$FST_MSG_REQUIRED = htmlspecialchars($FST_MSG_REQUIRED);

	// form cancel button (since version 15+)
	$FST_SHOW_RESET = filter_var($FST_SHOW_RESET, FILTER_VALIDATE_BOOLEAN);

	// hide the form button (since version 15+)
	$FST_SHOW_CLOSE = filter_var($FST_SHOW_CLOSE, FILTER_VALIDATE_BOOLEAN);
	// container ID for the entire form (version 15+)
	$FST_CONTAINER_ID = filter_var($FST_CONTAINER_ID, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	// close form button redirect
	$FST_CLOSE_REDIRECT = filter_var($FST_CLOSE_REDIRECT, FILTER_SANITIZE_URL);

	// since version 16
	$FST_USE_WP_MAIL = filter_var($FST_USE_WP_MAIL, FILTER_VALIDATE_BOOLEAN);
	$FST_SHOW_HEADER = filter_var($FST_SHOW_HEADER, FILTER_VALIDATE_BOOLEAN);

	// since version 17
	$FST_SHORT_FORM         = filter_var($FST_SHORT_FORM, FILTER_VALIDATE_BOOLEAN);
	$FST_POP_THANKS         = filter_var($FST_POP_THANKS, FILTER_VALIDATE_BOOLEAN);
	$FST_POP_THANKS_MESSAGE = filter_var($FST_POP_THANKS_MESSAGE, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$FST_XWRITELOG          = filter_var($FST_XWRITELOG, FILTER_VALIDATE_BOOLEAN);

	// Filter PHPMailer settings prior to setting constants (since version 17.00)
	$FST_USE_PHPMAILER = filter_var($FST_USE_PHPMAILER, FILTER_VALIDATE_BOOLEAN);
	$FST_SMTP_ENABLE   = filter_var($FST_SMTP_ENABLE, FILTER_VALIDATE_BOOLEAN);
	$FST_SMTP_USER     = htmlspecialchars($FST_SMTP_USER, ENT_QUOTES);
	$FST_SMTP_PASS     = htmlspecialchars($FST_SMTP_PASS, ENT_QUOTES);
	$FST_SMTP_PORT     = filter_var($FST_SMTP_PORT, FILTER_VALIDATE_INT);
	$FST_SMTP_SECURE   = htmlspecialchars($FST_SMTP_SECURE, ENT_QUOTES);
	$FST_SMTP_HOST     = htmlspecialchars($FST_SMTP_HOST, ENT_QUOTES);
	$FST_SMTP_AUTH     = filter_var($FST_SMTP_AUTH, FILTER_VALIDATE_BOOLEAN);
	$FST_SUBMITBUTTON_ID = filter_var($FST_SUBMITBUTTON_ID, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	// PHPMailer constants  (since version 17.00)
	define('FST_USE_PHPMAILER', $FST_USE_PHPMAILER);
	// define('FST_PHPMAILER_VERSION', $FST_PHPMAILER_VERSION); // this is defined later after loading; left here to remind that it is done later
	define('FST_SMTP_ENABLE', $FST_SMTP_ENABLE);
	define('FST_SMTP_USER', $FST_SMTP_USER);
	define('FST_SMTP_PASS', $FST_SMTP_PASS);
	define('FST_SMTP_PORT', $FST_SMTP_PORT);
	define('FST_SMTP_SECURE', $FST_SMTP_SECURE);

	// ------------------------------------------------------
	// lets turn the rest of the customization variables into constants so they are available where needed
	// ------------------------------------------------------

	define('FST_RECAPTCHA_SITEKEY', $FST_RECAPTCHA_SITEKEY);
	define('FST_RECAPTCHA_SECRET', $FST_RECAPTCHA_SECRET);
	define('FST_XEMAIL_ON_DOMAIN', $FST_XEMAIL_ON_DOMAIN);
	define('FST_XEMAIL_SUBJECT', $FST_XEMAIL_SUBJECT);
	define('FST_XCC_EMAIL', $FST_XCC_EMAIL);
	define('FST_XBCC_EMAIL', $FST_XBCC_EMAIL);
	define('FST_XSEND_HTML', true); // version 14: forced true, since all messages now have plain and HTML elements
	define('FST_XWRITELOG', $FST_XWRITELOG);
	define('FST_XWRITELOGFILE', $FST_XWRITELOGFILE);
	define('FST_XURLS_ALLOWED', intval($FST_XURLS_ALLOWED));
	define('FST_XACTION_PAGE', $FST_XACTION_PAGE);
	define('FST_XFORMTOP_MESSAGE', $FST_XFORMTOP_MESSAGE);
	define('FST_XSUBMIT_MESSAGE', $FST_XSUBMIT_MESSAGE);
	define('FST_XSUBMIT_BUTTON_TEXT', $FST_XSUBMIT_BUTTON_TEXT);
	define('FST_XTHANKS_MESSAGE', $FST_XTHANKS_MESSAGE);
	define('FST_XPRIVACY_PAGE_URL', $FST_XPRIVACY_PAGE_URL);
	define('FST_XPRIVACY_PAGE_LINK', $FST_XPRIVACY_PAGE_LINK);
	define('FST_XSPAMMER_URL', $FST_XSPAMMER_URL);
	define('FST_XFORMID', $FST_XFORMID);
	define('FST_XSHOW_HELLO', $FST_XSHOW_HELLO);
	define('FST_XDELAY', $FST_XDELAY);
	define('FST_XTHANKS_URL', $FST_XTHANKS_URL);
	define('FST_XCAPTURE', $FST_XCAPTURE);
	define('FST_XGO_AWAY_MSG', $FST_XGO_AWAY_MSG);
	define('FST_XSHOW_SUBMIT', $FST_XSHOW_SUBMIT);
	define('FST_XSHOW_SERVER', $FST_XSHOW_SERVER);
	define('FST_CUSTOM_FIELDS_LOCATION', $FST_CUSTOM_FIELDS_LOCATION);
	define('FST_CUSTOM_FIELDS_CSS_CLASS', $FST_CUSTOM_FIELDS_CSS_CLASS);
	define('FST_CUSTOM_FIELDS_CSS_CLASS_MASTER', $FST_CUSTOM_FIELDS_CSS_CLASS_MASTER);
	define('FST_REQUIRED_FIELDS', $FST_REQUIRED_FIELDS);
	define('FST_UPLOAD_EXTENSIONS', $FST_UPLOAD_EXTENSIONS);
	define('FST_UPLOAD_FOLDER', $FST_UPLOAD_FOLDER);
	define('FST_UPLOADS_DELETE', $FST_UPLOADS_DELETE);
	define('FST_FROM_EMAIL', $FST_FROM_EMAIL);
	define('FST_FROM_NAME', $FST_FROM_NAME);
	define('FST_SAVE_CONTACT_INFO', $FST_SAVE_CONTACT_INFO);
	define('FST_SAVE_CONTACT_MESSAGE', $FST_SAVE_CONTACT_MESSAGE);
	define('FST_SAVE_CONTACT_LABEL', $FST_SAVE_CONTACT_LABEL);
	define('FST_CONTACT_DATABASE', $FST_CONTACT_DATABASE);
	define('FST_SANITY_CHECK_EMAIL', $FST_SANITY_CHECK_EMAIL);
	define('FST_XJAVA_REQUIRED', $FST_XJAVA_REQUIRED);
	define('FST_EMAIL_CHECK', $FST_EMAIL_CHECK);
	define('FST_CONTACT_VERIFY', $FST_CONTACT_VERIFY);
	define('FST_SITE_NAME', $FST_SITE_NAME);
	define('FST_CONTACT_VERIFY_REDIRECT', $FST_CONTACT_VERIFY_REDIRECT);
	define('FST_CODE_INSERT', $FST_CODE_INSERT);
	define('FST_CODE_INSERT_LOCATION', $FST_CODE_INSERT_LOCATION);
	define('FST_NO_MAIL', $FST_NO_MAIL);
	define('FST_MORE_CSS', $FST_MORE_CSS);
	define('FST_CONTACT_VERIFY_URL', $FST_CONTACT_VERIFY_URL);
	define('FST_CONTACT_VERIFY_SUBJECT', $FST_CONTACT_VERIFY_SUBJECT);
	define('FST_CONTACT_VERIFY_MESSAGE', $FST_CONTACT_VERIFY_MESSAGE);
	define('FST_CONTACT_VERIFY_MESSAGE_DATA', $FST_CONTACT_VERIFY_MESSAGE_DATA);

	// version 11.x
	define('FST_REPLY_TO_EMAIL', $FST_REPLY_TO_EMAIL);
	define('FST_REPLY_TO_NAME', $FST_REPLY_TO_NAME);
	define('FST_REPLY_TO_OVERRIDE', $FST_REPLY_TO_OVERRIDE);
	define('FST_REQUIRED_FIELD_COLORS', $FST_REQUIRED_FIELD_COLORS);
	define('FST_DARK_MODE_ENABLE', $FST_DARK_MODE_ENABLE);

	// version 12.x
	define('FST_SHOW_VERSION', $FST_SHOW_VERSION);

	// since version 10, but fixed in version 17
	// this constant must be defined before adjusting FST_XCUSTOM_FIELDS
	define('FST_SHORT_FORM', $FST_SHORT_FORM); // used in fst_four_fie$FST_REPLY_TO_EMAILlds

	// this will display the four base fields, unless $FST_CUSTOM_FIELDS_ONLY is set true (overrides four base fields)
	$FST_FOUR_FIELDS = fst_four_fields(); // set up four fields by default
	if ($FST_CUSTOM_FIELDS_ONLY) { // if only use custom fields
		$FST_FOUR_FIELDS = array(); // reset to empty array (since version 17)
	}
	$FST_XCUSTOM_FIELDS = array_merge($FST_FOUR_FIELDS, $FST_XCUSTOM_FIELDS);
	$FST_XCUSTOM_FIELDS = filter_var_array($FST_XCUSTOM_FIELDS, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	// able to define this constant now
	define('FST_XCUSTOM_FIELDS', $FST_XCUSTOM_FIELDS);

	// version 14
	define('FST_CUSTOM_FIELDS_ONLY', $FST_CUSTOM_FIELDS_ONLY);

	// version 15+
	define('FST_SHOW_RESET', $FST_SHOW_RESET);
	define('FST_SHOW_CLOSE', $FST_SHOW_CLOSE);
	define('FST_CONTAINER_ID', $FST_CONTAINER_ID);
	define('FST_CLOSE_REDIRECT', $FST_CLOSE_REDIRECT);

	define('FST_MSG_REQUIRED', $FST_MSG_REQUIRED);
	// required message at top of form  (version 15+)
	define('FST_SHOW_REQUIRED_MESSAGE', $FST_SHOW_REQUIRED_MESSAGE);
	// input border values (version 15+)
	define("FST_INPUT_REQUIRED", $FST_INPUT_REQUIRED);
	define("FST_INPUT_VALID", $FST_INPUT_VALID);
	define("FST_INPUT_INVALID", $FST_INPUT_INVALID);

	// since version 16
	define("FST_USE_WP_MAIL", $FST_USE_WP_MAIL);
	define("FST_SHOW_HEADER", $FST_SHOW_HEADER);

	// since version 17
	define("FST_POP_THANKS", $FST_POP_THANKS);
	define("FST_POP_THANKS_MESSAGE", $FST_POP_THANKS_MESSAGE);
	define('FST_SUBMITBUTTON_ID', $FST_SUBMITBUTTON_ID);

	// if additional fields defined, check them out (fatal error if fields are incorrect)
	// (note: can't use an array as a constant in PHP < 7 )
	if (count(FST_XCUSTOM_FIELDS)) {fst_check_field_names(FST_XCUSTOM_FIELDS);}
	// for custom code insert

	// load the phpMailer namespace, which is required by sanitycheck when the form is displayed
 	// load the class files so we can get the version number
			// Platform-aware PHPMailer loading - prevents class conflicts
		if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
			// Try WordPress/ClassicPress PHPMailer first
			if (defined('ABSPATH') && file_exists(ABSPATH . WPINC . '/PHPMailer/PHPMailer.php')) {
				// WordPress/ClassicPress has PHPMailer - use it
				require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
				require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
				require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
			} else {
				// Fallback to plugin's bundled PHPMailer
				require_once __DIR__ . '/phpmailer/Exception.php';
				require_once __DIR__ . '/phpmailer/PHPMailer.php';
				require_once __DIR__ . '/phpmailer/SMTP.php';
			}
		}

	if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
 	   $FST_PHPMAILER_VERSION = PHPMailer\PHPMailer\PHPMailer::VERSION; }
	else {$FST_PHPMAILER_VERSION = "(not installed)";
	}
	define('FST_PHPMAILER_VERSION', $FST_PHPMAILER_VERSION);

	// this moved since we use FST_PHPMAILER_VERSION in the language text array
		// load default lang_text if not already defined in FST_MORE_FIELDS() function
	if (!isset($FST_LANG_TEXT)) {
		$FST_LANG_TEXT = fst_language_text();
	}
	array_map("htmlentities", $FST_LANG_TEXT);
	//$FST_LANG_TEXT = filter_var_array($FST_LANG_TEXT, FILTER_SANITIZE_STRING);
	define("FST_LANG_TEXT", $FST_LANG_TEXT); // convert to constant for use in user-visible messages


	// check for sumission by bot; die immediately
	//  - we check the post later, but do it here to prevent any page code output if bot is using CURL/etc to 'bot' the form
	if (isset($_POST["submitform"])) {
		// next several IFs to check for bots
		// check for session; if not there, then direct access
		//      this will defeat most CURL/etc bots, because session variables are stored on the server, and not available on client (CURL/etc bot) side
		define("FST_SPAMMER_MSG", "Incorrect setting or submission - ");
		if (isset($_SESSION['formspammertrap']) && !$_SESSION['formspammertrap'] === 'FormSpammerTrap') {
			fst_go_away(FST_SPAMMER_MSG . ' 1200');die();
		}
		// invisible recaptcha check
		if (! fst_check_human()) { // failed the invisible recaptcha
			fst_go_away(FST_SPAMMER_MSG . '  1201'); die();	// die spammer!
		}
		// this next field should have been changed when valid form submitted
		if (!$_POST['fst_idnumber'] == 'human') {
			// didn't change that hidden field, so "die, spambot!"
			fst_go_away(FST_SPAMMER_MSG . '  1202');die();
		}
	}

	// check if the form has an 'verify' parameter, indicating a verify-signup needed
	//  note that the verify function will display a simple success/fail message, plus a link to the site. No further processing after that (program 'dies').
	if (isset($_GET['id'])) {
		$xguid    = $_GET['id'];
		$verified = fst_signup_verify($xguid);
	}
	// ------------------------------------------------------
	// FORMSPAMMERTRAP Contact Page functions starts
	// ------------------------------------------------------
	// ------------------------------------------------------
	// main calling function for the form and the response
	// ------------------------------------------------------

	function formspammertrap_contact_form() {
		//echo "xcc after form function " . FST_XCC_EMAIL . "<br>";

		if (!fst_sanity_check()) {
			return;
		}
		$recipient = FST_XEMAIL_ON_DOMAIN;
		//echo "recipient 1261 " . $recipient  . "<br>";
		$show_form = true;
		fst_no_script();
		// process submittal form
		if (isset($_SESSION['formspammertrap']) && !$_SESSION['formspammertrap'] === 'FormSpammerTrap') {fst_go_away(FST_SPAMMER_MSG . '1205');}
		if (isset($_SESSION['formspammertrap']) && !$_SESSION['formspammertrap'] === 'FormSpammerTrap') {fst_go_away(FST_SPAMMER_MSG . '1206');}
		if (isset($_POST["submitform"]) && $_SESSION['formspammertrap'] === 'FormSpammerTrap') {
			fst_process_form();
		} // closing bracket for if submitted
		// set session, since valid access
		$_SESSION['formspammertrap'] = "FormSpammerTrap";
		$_SESSION['FST_USER_ID'] = (session_id()) ? session_id() : fst_guid();

		if ($show_form) {; // show the form
			fst_the_form(); // now a separate function for easier customization
		} // end show the form

		return;
	}

	// ----------------------------------------------------
	//  Sends the signup verification email
	// ----------------------------------------------------

	function fst_mail_verify($xyour_email = "", $guid = "") {
		/* FST_CONTACT_VERIFY_URL = full URL of verify process (external)
		-parameter:  id= USER GUID in the database
		 */
		/*
		'verify_url' = 'verify url', //a 'id' parameter of a guid is added automatically. You are responsbile for that process.
		'verify_message' = 'message',   // message to include in the verify email
		'verify_subject' = 'verify-email-subject',  // subject line of the verify email
		'verify_site_name' = 'site name',       // site name, used in verify email message
		'verify_from_name' = 'verify from name',    // 'from' name used in email
		'verify_from_email' = 'verify email',   // 'from' email used in email
		'verify_reply_to'   = 'reply email',    // reply-to parameter in verify email
		 */

		if (is_array(FST_CONTACT_VERIFY_MESSAGE_DATA)) {
			$verify_subject  = FST_CONTACT_VERIFY_MESSAGE_DATA['subject'];
			$verify_message  = "<p>" . FST_CONTACT_VERIFY_MESSAGE_DATA['verify_message'] . "</p>";
			$full_verify_url = FST_CONTACT_VERIFY_MESSAGE_DATA['verify_url'] . "?id=$guid";
			// puts the ID query parameter - the guid of the inserted record
			$verify_message .= "<p>" . FST_LANG_TEXT['verify_link_text'];
			$verify_message .= $full_verify_url . "</p>";
			$verify_message .= "<p>You can contact us at " . FST_CONTACT_VERIFY_MESSAGE_DATA['verify_contact'] . " .</p>";
			$verify_subect = FST_CONTACT_VERIFY_MESSAGE_DATA['verify_subject'];
		} else // use default content/parameters
		{
			$verify_subject = "Hello from " . FST_SITE_NAME . " !";
			$verify_message = "<h2 align='center'>$verify_subect</h2><p>" . FST_CONTACT_VERIFY_MESSAGE . "</p><p>Please verify your intent to add your email to the contact list at " . FST_SITE_NAME . " (" . $_SERVER['HTTP_HOST'] . " ) by clicking the link below.</p><p>If you did not sign up, please ignore this message.</p><p>You can contact us via the Contact page at " . $_SERVER['HTTP_HOST'] . "</p><p>Click this link to verify: <a href='" . FST_CONTACT_VERIFY_URL . "'> " . FST_CONTACT_VERIFY_URL . "</a></p>";
		}
		// added additional headers since version 16
		/*
		$headers  = "From: testsite <mail@testsite.com>\n";
		$headers .= "Cc: testsite <mail@testsite.com>\n";
		$headers .= "X-Sender: testsite <mail@testsite.com>\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		$headers .= "X-Priority: 1\n"; // Urgent message!
		$headers .= "Return-Path: mail@testsite.com\n"; // Return path for errors
		 */

		$headers = array(
			'MIME-Version' => '1.0',
			'Content-type' => 'text/html; charset=iso-8859-1',
			'From' => FST_FROM_EMAIL,
			'Sender' => FST_FROM_EMAIL,
			'MIME-Version' => '1.0',
			'Return-Path' => FST_FROM_EMAIL,
			'X-Sender' => FST_FROM_EMAIL,
			'X-Mailer' => 'PHP ' . phpversion(),
		);
		if (FST_XCC_EMAIL) {
			$headers['BCC'] = FST_XCC_EMAIL;
		}
		$html_start       = "<html><body>";
		$html_end         = "</body></html>";
		$sendmsg          = $html_start . $verify_message . $html_end;
		$message_elements =
		array(
			'recipient' => $xyour_email, // mail recipient
			'subject' => $verify_subject, // subject text
			'message' => $sendmsg, // message text
			'headers' => $headers, // all mail header elements
		);
			fst_send_mail_phpmailer($message_elements); // for verify message

		return;}

	// --------------------------------------------------------------------------
	// append number to $filename if it already exists in the $path
	//      result will be path/filename(1).ext, etc
	//      from: https://stackoverflow.com/a/43342298/1466973
	// --------------------------------------------------------------------------
	function fst_new_file_name($path, $filename) {
		$res = "$path/$filename";
		if (!file_exists($res)) {
			return $res;
		}

		$fnameNoExt = pathinfo($filename, PATHINFO_FILENAME);
		$ext        = pathinfo($filename, PATHINFO_EXTENSION);

		$i = 1;
		while (file_exists("$path/$fnameNoExt" . "-" . "($i).$ext")) {
			$i++;
		}

		return "$path/$fnameNoExt" . "-" . "($i).$ext";
	}

	// --------------------------------------------------------------------------
	// check for valid email address
	// was deprecated in prior versions, but now (version 10) allowed by default
	// can turn off email validation with the FST_EMAIL_CHECK set false (default true)
	//------------------------------------------------------------------------------
	function fst_is_email_ok($email) {
		if (FST_EMAIL_CHECK) { // do email check (default is true)
			if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
				return true;
			} else {
				return false;
			}
		}
		return true;
	}

	// --------------------------------------------------------------------------
	// check if site is SSL - for Sanity Check
	// --------------------------------------------------------------------------
	function fst_is_ssl() {
		if (isset($_SERVER['HTTPS'])) {
			if ('on' == strtolower($_SERVER['HTTPS'])) {
				return true;
			}

			if ('1' == $_SERVER['HTTPS']) {
				return true;
			}
		} elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
			return true;
		}
		return false;
	}

	// --------------------------------------------------------------------------
	// check any fields for additional validation; match checks with form field names
	// an example might be checking fields for multiple URLs to block link spammers
	// --------------------------------------------------------------------------
	function fst_check_fields($errormsg) {
		// this next field should have been changed when valid form submitted
		if (!$_POST['fst_idnumber'] == 'human') {
			// didn't change that hidden field, so "die, spambot!"
			fst_go_away($spammer_msg . ' 1512');
			die();
		}
		if (!$_POST['fst_number'] === $_SESSION['FST_USER_ID']) {
			// didn't change that hidden field, so "die, spambot!"
			fst_go_away($spammer_msg . ' 1515');
			die();
		}

		if (FST_CUSTOM_FIELDS_ONLY) {return $errormsg;} // only custom fields, so return
		$missing_info = "";
		// check required fields array, if any
		if (is_array(FST_REQUIRED_FIELDS)) {
			foreach (FST_REQUIRED_FIELDS as $item) {
				if (!trim($_POST[$item])) {
					$missing_info .= "Please enter the required information: $item .<br>";
				}
			}
		}
		if (FST_EMAIL_CHECK) {
			if (!fst_is_email_ok($_POST['your_email'])) {
				$missing_info .= "Invalid email address.<br>";
			}
		}
		// do any additional checking defined in the form code
		if (function_exists("FST_ADD_CHECKS")) {
			$add_checks .= fst_add_checks();
			if (is_array($add_checks)) { // just in case it's an array
				$missing_info .= explode("br", $add_checks); // convert array to string
			}
		}
		return $missing_info;
	}

	// --------------------------------------------------------------------------
	// create a guid type value for unique values of the processing page
	// --------------------------------------------------------------------------
	function fst_guid() {
		return sprintf('%04X%04X_%04X_%04X_%04X_%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}

	// --------------------------------------------------------------------------
	// these CSS are needed by the form; call this function in the page head section
	// not all CSS styles are used; they are here for convenience
	// note that CSS code is not optimized for clarity in case you want to change things,
	// but shouldn't affect execution time
	//
	// if you want to adjust/override CSS, put your custom css after the call to this function
	// --------------------------------------------------------------------------
	function formspammertrap_contact_css() {;

	?>
<!-- this meta allows better responsiveness when using a grid -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<style type="text/css">
.fst_red_box {
 color: #FFF;
 background-color: #F00;
 padding: 10px;
 border: thin solid #333;
 margin-left: 15px;
 margin-top:15px;
 margin-bottom:15px;
 text-align:center;
 }

.fst_purple_box {
 color: #FFF;
 background-color: #F0F;
 padding: 10px;
 border: thin solid #333;
 margin-left: 15px;
 margin-top:15px;
 margin-bottom:15px;
 text-align:center;
}

.fst_green_box {
 color: #000;
 background-color: #82FF82;
 padding: 10px;
 /*font-weight: bold;*/
 border: thin solid #333;
 margin-top:15px;
 margin-bottom:15px;
  margin-left:15px;
}

.fst_yellow_box {
 color: #000;
 background-color: #FFFF66;
 padding: 10px;
 /*font-weight: bold;*/
 border: thin solid #333;
 margin-top:15px;
 margin-bottom:15px;
 margin-left:15px;
 }

.fst_blue_box {
 color: #FFF;
 background-color: #287EFF;
 padding: 10px;
 border: thin solid #333;
 margin-top:15px;
 margin-bottom:15px;
}
.fst_comments {
    height:200px;
}

.fst_comment_box {
 font-size: 11px;
 font-family: Verdana, Geneva, sans-serif;
 padding: 15px;
 color: #000;
 margin-top: 10px;
 margin-right: 10px;
 margin-bottom: 10px;
 margin-left: 10px;
 width: 92%;
}

.fst_comment_box table tr td {
 padding: 12px 20px 12px 20px;
 vertical-align: top;
}

.fst_comment_box a:link {
 color: #000099;
 text-decoration: underline;
 outline: none;
}

.fst_comment_box a:hover {
 color: #3366CC;
 text-decoration: underline;
 outline: none;
}

.fst_comment_box a:active {
 color: #000099;
 text-decoration: underline;
 outline: none;
}

.fst_comment_box a:visited {
 color: #000099;
 text-decoration: underline;
 outline: none;
}

.fst_comment_button, .fst_comment_button, input[type="submit"] {
 background: none repeat scroll 0 0 #eeeeee;
 border: 1px solid #999999;
 border-radius: 7px 7px 7px 7px;
 color: #000000;
 font-size: 11px;
 padding-bottom: 20px;
}

.fst_added_text {
    width:auto;
    float:left;
}
.fst_allowed_file_types{
    font-size: 100%;
}

/* some of these text color classes are available if you want to change text colors */

.fst_fatal_message {
    background-color:white !important;
    color:black !important;
}
.fst_text_red {
 color: #F00;
 background-color: #fff;
}

.fst_text_black {
 color: #000;
}

.fst_text_white {
 color: #fff;
}

.fst_small_text {
 font-size: 9px;
}
.fst_back_red {
    background-color:red;
}
.fst_box {
    border: 1px solid black;
    padding:5px;
}
.fst_checkbox_left {
    text-align:left;
}
.fst_label_checkbox {
    padding-top:0 !important;
}

.fst_text_center {
    text-align: center;
}
.fst_aftermsg {
    text-align: left;
    padding-left:10px;
}
.fst_afterfile_msg {
    text-align: left;
    /*float: left;*/
    display:none;  /* will be displayed after file selected */
}
.fst_red_question::before {
    color: red;
    margin: 0;
    font-size: 500%;
    content: "?";
    background: aliceblue;
    padding: 10px 10px;
}
.fst_red_x_icon::before, .fst_red_x_icon {
    color: red;
    margin: 0;
    font-size: 120%;
    content: "X";
    background: aliceblue;
    padding: 10px 10px;
    display:none !important;
}
.fst_pdf_icon::before, .fst_pdf_icon {
    color: green;
    margin: 0;
    font-size: 120%;
    content: " PDF ";
    background: #0f82e6;
    z-index: 10;
    display: block !important;
    width: 30px;
    height: 30px;
    color: white;
    padding-top: 5px;
    padding-left: 5px;
    padding-right: 5px;
    padding-bottom: 5p;
}
.fst_button {   /* submit button styling */
    }

.grecaptcha-badge { /*hides the recaptcha badge*/
 display: none;
}

.EVIL_SPAMMER{       /* for the evil spammer message div */
 color: #FFF;
 background-color: #F00;
 padding: 10px;
 border: thin solid #333;
 margin-left: 15px;
 margin-top:15px;
 margin-bottom:15px;
 text-align:center;
 }
.fst_thumbnail {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    text-align: center;
}
/* For the reset and close buttons (version 15+) */
.fst_close_button {
    background-color:cyan;
    color:black;
    border-radius: 8px;
    padding:7px;
    }

.fst_reset_button  {
    background-color:#FF0000;
    color:white;
    border-radius: 8px;
    padding:7px;
}

.fst_close_button:hover, .fst_reset_button:hover  {
    background-color:#FFFF00;
    color:black;
}

/* since version 17 for optional alignment of select boxes (or other elements) */
.fst_float_left {
    float:left;
}
.fst_float_center {
    float:center;
}
.fst_float_right {
    float:right;
}

 @media only screen and (max-width: 700px) {
    /* since version 17 - removes 3rd column, adds additional margin/padding to first column to adjust for smaller screens */

    .fst_column_1 {
        margin-top: 5% !important;
        margin-bottom: 3% !important;
        padding-top: 2% !important;
    }
    .fst_column_3 {
        display: none;
        margin-bottom: 2%;
    }
}

/* since version 17 - adds css for the file upload area if used */

    .fst_file_preview {
        background-color: lightcyan;
        padding: 3px;
        margin-bottom: 5px;
    }
    .fst_filelist_heading {
        border: none;
        background-color: inherit;
        font-style: italic;
    }
     .fst_filelist ol {
      padding-left: 0;
    }

   .fst_filelist li {
        background-color:lightyellow;
      display: flex;
      justify-content: space-between;
      margin:4px;
	  padding-left:10px;
      list-style-type: none;
      border: none;
	  text-align:left;
    }

   .fst_filelist_img {
      height: 64px;
      order: 1;
    }
    .fst_filelist img {
      height: 64px;
      order: 1;
        padding:4px 10px;
    }
    .fst_filelist img:hover {
      transform: scale(2); /* Increase the size by 100% (2 times) */
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); /* Add a shadow to the image */
      transition: transform 0.3s ease, box-shadow 0.3s ease; /* Add transitions for both properties */}

/* end CSS */
</style>
<?php
	fst_the_form_css(); // additional CSS for version 5.00 responsive form

		// version 10.10 - add CSS code from FST_MORE_FIELDS
		if (FST_MORE_CSS) {
			echo FST_MORE_CSS;
		}
		return;
	}

	// --------------------------------------------------------------------------
	// build the various scripts needed for the changes and delays
	// --------------------------------------------------------------------------
	function formspammertrap_contact_script() {
		// split the action page in half if not left blank
		if (strlen(FST_XACTION_PAGE)) {
			$xhalf  = (int) floor(strlen(FST_XACTION_PAGE) / 2);
			$xpage1 = substr(FST_XACTION_PAGE, 0, $xhalf);
			$xpage2 = substr(FST_XACTION_PAGE, $xhalf);
		} else { // empty, so make empty halves
			$xpage1 = "";
			$xpage2 = "";
		}
		$xguid1 = fst_guid();
		$xguid2 = fst_guid();
	?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<?php
	// this is for the new invisible recaptha added
		// sets up the real action page, set up in the customization area
		// the name of the form, must match the form 'name' parameter; must be 'contact' for reCAPTCHA to work
		// if reCAPTCHA not enabled (FST_RECAPTCHA_SITEKEY is empty), don't load the API
	if (strlen(FST_RECAPTCHA_SITEKEY)) {?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php }?>
<script>
        var fst_code_<?php echo $xguid1; ?> = "<?php echo $xpage1; ?>";
        var fst_code_<?php echo $xguid2; ?> = "<?php echo $xpage2; ?>";
        var FormID = "<?php echo FST_XFORMID; ?>";
        </script>
<?php

		/* new for version 4.0: delays adding click/focus and changing form target
		this script delays various functions
		- first, the fields with a class of required will get an onclick action after a delay
		- second, the onclick's action will be delayed
		the result is a 'cascading' delay of events added and executed
		this solution to delay action of click on a required field modified from code found here

		https://stackoverflow.com/questions/36125391/vanilla-js-delay-click-event-to-add-animation
		adds the onclick function to fields with class = required
		delays this action by 5000 ms (5 seconds)

		the functionfires on a click on the required field
		this will change the action parameter of the form to actual action needed
		this action change is delayed for 5000 ms (5 seconds) (default)

		calls function that runs the code that changes the form action, plus changes the hidden field to 'human'

		additional actions can be added using the same format as above

		will be delayed 5000 ms (5 seconds) after calling the fst_delaytheclick function*/

	;?>
<script>

    function onSubmit(token) {
                document.getElementById("<?php echo FST_XFORMID; ?>").submit();
    }
        window.onload = function()
        {
            setTimeout(function()
                {
                    var links = document.getElementsByClassName('fst_required');

                    for(var i = 0, il = links.length; i< il; i ++)
                    {
                        links[i].onclick = fst_delaytheclick;
                        links[i].onfocus = fst_delaytheclick;
                    }
                }
                ,<?php echo FST_XDELAY; ?>);

            function fst_delaytheclick(event)
            {
                event.preventDefault();
                setTimeout(function()
                    {
                        document.getElementById("<?php echo FST_XFORMID; ?>").action = fst_code_<?php echo $xguid1; ?>+fst_code_<?php echo $xguid2; ?>;
                        document.getElementById('fst_idnumber').value='human';
                        document.getElementById('your_name').name='your_name';
                        document.getElementById('your_email').name='your_email';
                    }
                    ,<?php echo FST_XDELAY; ?>);

                setTimeout(function()
                    {
                        document.getElementById("<?php echo FST_SUBMITBUTTON_ID;?>").removeAttribute("hidden");
                    }
                    ,<?php echo FST_XDELAY; ?>);
                var showhello = "<?php if (FST_XSHOW_HELLO) {echo "true";} else {echo "";}?>";
                if (showhello) {
                setTimeout(function()
                    {
                        alert("Form's action value changed!")
                    }
                    ,<?php echo FST_XDELAY; ?>);
                }
            };
        }

        function fst_eventFire(el, etype){
            if (el.fireEvent) {
                    el.fireEvent('on' + etype);
            } else {
                    var evObj = document.createEvent('Events');
                    evObj.initEvent(etype, true, false);
                    el.dispatchEvent(evObj);
                    }
            }
    </script>
       <script>
    // for the close/hide form button, if enabled
    function fst_close_form() {
            document.getElementById("<?php echo FST_CONTAINER_ID; ?>").style.display = "none";
        // if the redirect after close is set
        <?php if (FST_CLOSE_REDIRECT) {?>
            window.location.href="<?php echo FST_CLOSE_REDIRECT; ?>";
        <?php }?>
        }

	 </script>
        <?php
        	return;
        	}

        	// --------------------------------------------------------------------------
        	// script that delays changing the form's action, placed here so we can use it elsewhere
        	// --------------------------------------------------------------------------
        	function fst_delaytheclick($xguid1, $xguid2) {
        	?>
<script>
                setTimeout(function()
                    {
                        document.getElementById("<?php echo FST_XFORMID; ?>").action = formspammertrap_code_<?php echo $xguid1; ?>+formspammertrap_code_<?php echo $xguid2; ?>;
                    }
                    ,<?php echo FST_XDELAY; ?>);
</script>
<?php
	return;
	}


	// --------------------------------------------------------------------------
	// this code is called by any non-standard values in hidden fields
	//      go away!
	// --------------------------------------------------------------------------
	function fst_go_away($reason = 'unknown') {

		// as of v17 - show die message in log file
		if (FST_XWRITELOG) {
			fst_write_debug_log('DEBUG - goaway called - reason = ' . $reason);
		}

		unset($_SESSION['formspammertrap']); // clear it
		unset($_SESSION['']);
		// v 14.01 - tracking for spammers caught
		$spammerurl = (FST_XSPAMMER_URL == "https://www.formspammertrap.com") ? "https://www.formspammertrap.com?goaway=spammer" : FST_XSPAMMER_URL;
	?>
    <script>

    window.setTimeout(function () {
        window.location = "<?php echo $spammerurl; ?>";
    }, 5000);

</script>

<?php
	$die_msg = '<div class="EVIL_SPAMMER">' . FST_XGO_AWAY_MSG . '</div>';
		die($die_msg);
		return;
	}

	// --------------------------------------------------------------------------------
	// update a log file if you want to log contact mail sent
	// change the log file to whatever you want. Note that you may want to
	// further secure the log file with various techniques
	// that further security of the log file is not done here - it's up to you
	// be careful about the log file size over time; it may get quite big on a site
	// that gets lots of comments
	// --------------------------------------------------------------------------------
	function fst_write_log_file($updatetxt) {
		if (FST_XWRITELOGFILE == "") {$thelogfile = 'maillog.txt';} else { $thelogfile = FST_XWRITELOGFILE;}
		// just in case it was blank
		$logtext  = date('j-M-Y \a\t g:m:s a') . " - Message sent via FormSpammerTrap-enabled Contact form : = " . $updatetxt . "\n";
		$xlogfile = getcwd() . "/" . $thelogfile;
		$fh       = fopen($xlogfile, 'a');
		if (!$fh) {return;} // error writing log file, just return without error
		fwrite($fh, $logtext);
		fclose($fh);
		return;
	}

	// ----------------------------------------------------------------------------
	// check for too many urls in the comment content
	function fst_count_urls($text) {
		// get the urls_allowed value
		// regex to find all types of urls in the text
		$regex = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
		// put found urls in the $urls_array array
		preg_match_all($regex, $text, $urls_array);
		// extract the elements of the first element to get a one-dimension array
		$urls_array = $urls_array[0];
		// remove the first 'urls_allowed' count elements from the $results array
		$xurl_count = count($urls_array);
		return $xurl_count; // count of urls in the message text
	}

	// ------------------------------------------------------
	// validates the invisible ReCAPTCHA; uses your secret code/keys from google
	function fst_check_human() {
		// if recaptcha disabled, pretend all is well
		if (strlen(FST_RECAPTCHA_SECRET) == 0) {return true;}

		// check to ensure CURL is installed/active, returns false if no CURL
		// CURL required to use invisible reCAPTCHA
		if (fst_check_for_curl()) {
			// reCaptcha info
			$remoteip = $_SERVER["REMOTE_ADDR"];
			$url      = "https://www.google.com/recaptcha/api/siteverify";
			// Form info
			$response = htmlspecialchars($_POST["g-recaptcha-response"]);
			// Curl Request
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, array('secret' => FST_RECAPTCHA_SECRET, 'response' => $response, 'remoteip' => $remoteip));
			$curlData = curl_exec($curl);
			curl_close($curl);
			// Parse data
			$recaptcha = json_decode($curlData, true);
			if ($recaptcha["success"]) { // check for recaptcha success
				return true;
			} else {
				return false;
			}
		} else { // show an error message if CURL not enabled on server
			echo '<p align="center" class="fst_p text_red">' . FST_LANG_TEXT['curl_not_enabled'] . '</p>';
			return false;
		}
		return false;
	}

	// --------------------------------------------------------------------------
	// Script to test if the CURL extension is installed on this server
	// --------------------------------------------------------------------------
	function fst_check_for_curl() {

		if (in_array('curl', get_loaded_extensions())) {
			return true;
		} else {
			return false;
		}
		return false; // redundant return, just in case of <something>
	}

	// --------------------------------------------------------------------------
	// capture all of the POST request headers
	//  used to see what the spammer is doing if enabled; added to the sent message
	// --------------------------------------------------------------------------
	function fst_capture_post() {

		$xcapture_info = "<hr>POST Request Headers<br>";
		foreach (getallheaders() as $name => $value) {
			$xcapture_info .= "$name: $value <br>";
		}
		foreach ($_SERVER as $key_name => $key_value) {
			$xcapture_info .= $key_name . " = " . $key_value . "<br>";
		}

		return $xcapture_info;
	}

	// --------------------------------------------------------------------------
	// the responsive form and css (next two functions) are based on
	// https://www.w3schools.com/howto/howto_css_responsive_form.asp
	// --------------------------------------------------------------------------

	// --------------------------------------------------------------------------
	// the new version 5+ form, responsive, will adjust to display width
	// --------------------------------------------------------------------------
	function fst_the_form() {
		//    global $FST_XCUSTOM_FIELDS; // for additional custom field names if specified
		$fieldnames   = array(); // array for all input field names, items added for each input name value
		$fst_idnumber = fst_shortnumber(); // sets random value of hidden field

		// v16 - show the required message using required field colors
		if (FST_REQUIRED_FIELD_COLORS) {
			// use the fst_field_required CSS class
			$required_field = " <p align='center'><span class='fst_field_required'>required</span></p> ";
		} else {
			$required_field = "";
		}
		$required_field = (FST_REQUIRED_FIELD_COLORS) ? " required " : "";
	?>

<div id="<?php echo FST_CONTAINER_ID; ?>" class="fst_container">
    <div class='fst_comment_box  ' align="center">
        <?php // version 10 - split formtop and java-required messages for separate CSS ;;?>
        <p class="fst_formtop_message"><strong><?php echo FST_XFORMTOP_MESSAGE; ?></strong></p>
            <p class="fst_p" ><em><?php echo FST_XJAVA_REQUIRED; ?></em></p>

        <?php if (FST_SHOW_REQUIRED_MESSAGE) {
        			echo "<p align='center'><span class='fst_field_required_text'>" . FST_LANG_TEXT['show_required_message'] . "</span></p>";
        		}
        		; //fst_show_globals();
        	?>
        <form action='<?php echo FST_XSPAMMER_URL; ?>' method='POST' name="contact_form" id="<?php echo FST_XFORMID; ?>" enctype="multipart/form-data" class="fst_grid">
            <input type="hidden" name = "fst_idnumber" id="fst_idnumber" value="<?php echo $fst_idnumber; ?>">
            <input type='hidden' name='fst_version' id='fst_version' value='<?php echo FST_VERSION; ?>' >
            <input type='hidden' name='fst_number' id='fst_number' value='<?php echo $_SESSION['FST_USER_ID']; ?>' >



<?php
	/* new function to display standard, standard + custom or custom-only fields
		- if standard, show the four base fields
		- if standard and additional fields defined, show the four base fields plus the additional fields
		- if custom-only, only show the custom fields defined in FST_MORE_FIELD
		 */
		fst_the_form_fields();
		// insert save contact checkbox
	?>
            <div class="fst_column_2" >
                <?php

                		// if you don't want to use reCAPTCHA v2, then leave the FST_RECAPTCHA_SITEKEY blank; this bypasses it if the key is blank
                	if (strlen(FST_RECAPTCHA_SITEKEY) > 0) {?>
                <!-- new button implementing recaptcha -->
                <p class="fst_p" ><?php echo FST_LANG_TEXT['recaptcha_notice']; ?></p>
                <p class="fst_p" >
                    <button class="g-recaptcha fst_button" id='<?php echo FST_SUBMITBUTTON_ID;?>' hidden data-sitekey="<?php echo FST_RECAPTCHA_SITEKEY; ?>" data-callback='onSubmit' ><?php echo FST_XSUBMIT_BUTTON_TEXT; ?></button>
                </p>
                <?php } else {?>
                <p class="fst_p" >
                    <button class="g-recaptcha fst_button" id='<?php echo FST_SUBMITBUTTON_ID;?>' hidden ><?php echo FST_XSUBMIT_BUTTON_TEXT; ?></button>
                </p>
                <?php }
                		; // this next section to make reCaptcha 2 happy
                	?>

                <!--<button value="Submit"
 class="g-recaptcha"
 data-size="invisible"
 data-sitekey="<?php //echo FST_RECAPTCHA_SITEKEY; ;;?>"
 data-callback="captchaSubmit"> Submit</button>
-->
                <!-- this needed to process the form -->
                <input type="hidden" name="submitform" value="FormSpammerTrap" />
                <!-- hidden submit so the form will process after recaptcha check -->
                <!--<input type='submit' value='Submit' name="submit" class="g-recaptcha" data-sitekey="
" data-callback="captchaSubmit" data-badge="inline" >-->
                <!--<input type='hidden' value='1' name='submitted'>-->
                <strong>
                <?php
                	echo "<p align='center' class='fst_row fst_column_2'>" . FST_XSUBMIT_MESSAGE . "</p>" . FST_XPRIVACY_PAGE_LINK;
                	?>
                </strong>
                <p>                                       <?php if (FST_SHOW_RESET) {?>
               <button id='form_reset' class='fst_reset_button' type='reset'><?php echo FST_LANG_TEXT['reset_button_text']; ?></button>
                <?php }?>
<?php if (FST_SHOW_CLOSE) {?>
                &nbsp;&nbsp;&nbsp;<button id='fst_form_close' class='fst_close_button' type='button' onclick="fst_close_form();" ><?php echo FST_LANG_TEXT['close_button_text']; ?></button>
                <?php }?>
                 </p>
                </div>

        </form>
        <?php
        	if (FST_SHOW_VERSION) { // show version info (normally used for test only)
        			echo "<hr>";
        			echo FST_LANG_TEXT['show_version_message'];
        			// this is here so the tags are not filtered if it was part of the FST_LANG_TEXT array
        			echo "<a href='https://www.FormSpammerTrap.com' target='_blank' title='FormSpammerTrap.com'>FormSpammerTrap.com</a>";
        			echo "<hr>";
        		}
        	?>
    </div>
</div>
<?php

		return;
	}

	// --------------------------------------------------------------------------
	// display the form fields: standard or custom plus add-in fields as defined in the form  (v14)
	// --------------------------------------------------------------------------
	function fst_the_form_fields() {
		$fieldnames     = array(); // array for all input field names, items added for each input name value
		$fst_idnumber   = fst_shortnumber(); // sets random value of hidden field
		$required_field = (FST_REQUIRED_FIELD_COLORS) ? " <span class='fst_field_required_text'>required</span>" : "";
		// closing form tag in parent to allow form footer stuff
		foreach (FST_XCUSTOM_FIELDS as $field) {
			$required_class = ($field["REQUIRED"]) ? "fst_required fst_field_required" : "";
			$field_type     = strtoupper($field['TYPE']);
			$checkbox_class = ($field_type == "CHECKBOX") ? "fst_checkbox_left" : "";
			if (!isset($field['ID'])) {$field['ID'] = $field['NAME'];}
			/*  using  https://www.w3.org/WAI/WCAG21/Techniques/css/C38.html
			<div class="form-group">
			<div class="form-col-4">
			<label for="fname">First Name</label>
			</div>
			<div class="form-col-8">
			<input type="text" id="fname" autocomplete="given-name">
			</div>
			</div>
			 */
			$checkbox_label_class = ($field_type == "CHECKBOX") ? " fst_label_checkbox " : "";
			$hidden_nodisplay = ($field_type == "HIDDEN") ? " style='display:none;' " : "" ;
		?>
            <div class='fst_column_1<?php echo $checkbox_label_class; ?>' id="<?php echo $field['ID'] . '"' . $hidden_nodisplay; ?>">
                    <label for="<?php echo $field['LABELMSG']; ?>">
                    <?php echo $field['LABELMSG']; ?>
<?php if ($required_class) {echo " <span class='fst_field_required_text'>" . FST_LANG_TEXT['required'] . "</span>";}?>
</label>
                </div>
             <div class='fst_column_2 <?php echo $checkbox_class . "' "; echo $hidden_nodisplay;?>>
                    <?php
                    	if (isset($_POST[$field['NAME']])) {
                    				$value = $_POST[$field['NAME']]; // in case form redisplayed, prior value will be there or set to nothing , unlese the added field has a value parameter
                    			} else { $value = (isset($field['VALUE'])) ? $value = $field['VALUE'] : $value = "";
                    			}
								if ($field_type == "FILE") {  // puts the script to display the files selected and their thumbnails

								}
                    			// build field according to type
                    			switch ($field_type) {
                    				case 'TEXTAREA':
                    					echo "<textarea ";
                    					echo "id='" . $field['ID'] . "' ";
                    					echo "class='" . $field['CLASS'] . " " . $required_class . "' ";
                    					echo "name='" . $field['NAME'] . "' ";
                    					echo "placeholder='" . $field['PLACEHOLDER'] . "' ";
                    					if ($required_class) {echo " required='required'";}
                    					echo "cols='" . $field['MAXCHARS'] . "' ";
                    					echo " rows='" . $field['MAXLENGTH'] . "'";
                    					echo ">";
                    					echo $value;
                    					echo "</textarea>";
                    					break;

                    					break;

                    				case "SELECT":
                    					fst_insert_codeblock($field['CODEBLOCK']); // pass codeblock value, since version 17
                    					break;

                    				case "FILE":     // separate since version 17
					   					fst_file_input_field($field);
                     					break;

                    				case "CHECKBOX":
                    				case "TEXT":
                    				case "EMAIL":
                    				case "URL":
                    				// this takes care of the other possible input types that might need client-side validation
                    				default:
                    					if ($field_type == "CHECKBOX") {
                    						echo "<p style='margin-top:0px'>";
                    					}
                    					echo "<input ";
                    					echo "type='" . strtolower($field['TYPE']) . "' ";
                    					echo "id='" . $field['ID'] . "' ";
                    					echo "class='" . $field['CLASS'] . " " . $required_class . "' " . $hidden_nodisplay ;
	                   					echo "name='" . $field['NAME'] . "' ";
                    					if (isset($field['READONLY'])) {echo " readonly ";}
                    					if ($field_type == "CHECKBOX") { // v14 to make value = name of field
                    						echo "value='" . $field['NAME'] . "' ";
                    					}
                    					echo "placeholder='" . $field['PLACEHOLDER'] . "' ";
                    					if ($required_class) {echo " required='required'";}
                    					echo "maxlength='" . $field['MAXCHARS'] . "' ";
                    					echo " size='" . $field['MAXLENGTH'] . "'";
                    					echo " value='" . $value . "'";
                    					echo ">";
                    					if ($field_type == "CHECKBOX") {
                    						echo $field['AFTERMSG'] . "</p>";
                    					}
                    					break;
                    			}
                    		?>
                </div>
                <div class='fst_column_3' id="aftermsg_<?php echo $field['ID']; ?>" <?php echo $hidden_nodisplay; ?> >
               	<?php 			echo $field['AFTERMSG']; ?>
                </div>

                            <?php }
                            		; // end of field input loop
                            		// insert save contact info checkbox here if enabled
                            		if (FST_SAVE_CONTACT_INFO) {fst_insert_save_checkbox();}

                            		return;
                            	}

// --------------------------------------------------------------------------
// creates the file input field if needed  (since version 17)
//		- separated here because of all the stuff
// --------------------------------------------------------------------------
function fst_file_input_field($field) {

  //echo "<br><label for=\"fst_uploadfile\">Choose the files to upload </label>";
	$required_class = $field['REQUIRED'];
	echo "<input ";
	echo " type='" . strtolower($field['TYPE']) . "' ";
	echo " id='" . $field['ID'] . "x' ";   // add the 'x' to keep id/name different for script processing
	echo " class='" . $field['CLASS'] . " " . $required_class . "' ";
	echo " name='" . $field['NAME'] . "[]' ";
	echo " multiple='multiple' ";
  //	if (isset($field['READONLY'])) {echo " readonly ";}
	if ($required_class) {echo " required='required'";}
	echo ' accept="' . implode(",", FST_UPLOAD_EXTENSIONS) . ' " ';
	echo ">";
	echo "<p>" . FST_LANG_TEXT['allowed_file_types'] . "&nbsp;&nbsp;&nbsp;<b>" . implode("&nbsp;<wbr>&nbsp;&nbsp;", FST_UPLOAD_EXTENSIONS) . "</b></p>";
	// display area for list of files
	?>
    <div class="fst_file_preview">
      <p>No files currently selected for upload</p>
    </div>

	<?php
	fst_show_selected_files_script($field);
	return;
	}

	function fst_show_selected_files_script($field) {
	?>
	  <script>
	document.addEventListener("DOMContentLoaded", function() {
		// x is added to keep id/name different, otherwise script not find input value
	    const input = document.querySelector('#<?php echo $field["ID"]; ?>x');
	    const fst_file_preview = document.querySelector('.fst_file_preview');
	    //input.style.opacity = 0;

	    input.addEventListener('change', updateImageDisplay);
	    function updateImageDisplay() {
	      while(fst_file_preview.firstChild) {
	        fst_file_preview.removeChild(fst_file_preview.firstChild);
	      }

	      const curFiles = input.files;
	      const list_heading = document.createElement('p');
	           list_heading.classList.add('fst_filelist_heading');
	      list_heading.textContent = "Selected files (hover over image to enlarge)";
	        fst_file_preview.appendChild(list_heading);

	      if(curFiles.length === 0) {
	        const para = document.createElement('p');
	        para.textContent = 'No files currently selected for upload';
	        fst_file_preview.appendChild(para);
	      } else {
	        const list = document.createElement('ol');
	           list.classList.add('fst_filelist');
	        fst_file_preview.appendChild(list);

	        for(const file of curFiles) {
	          const listItem = document.createElement('li');
	          const para = document.createElement('p');
	           listItem.classList.add('fst_filelist');
	           para.classList.add('fst_filelist');

	            para.textContent = `   ${file.name}, ${returnFileSize(file.size)} `;
	            const image = document.createElement('img');
	            image.src = URL.createObjectURL(file);
	            if (file.type.startsWith('image/')) {
	                listItem.appendChild(image);
	            }
	            listItem.appendChild(para);

	          list.appendChild(listItem);
	        }
	      }
	    }


	    function returnFileSize(number) {
	      if(number < 1024) {
	        return number + ' bytes';
	      } else if(number > 1024 && number < 1048576) {
	        return (number/1024).toFixed(1) + ' KB';
	      } else if(number > 1048576) {
	        return (number/1048576).toFixed(1) + ' MB';
	      }
	    }
    });     // end of document-load
  </script>

<?php
	return;
	}

   	// --------------------------------------------------------------------------
   	// additional CSS used by the version 5 responsive form
   	// --------------------------------------------------------------------------
   	function fst_the_form_css() {
   	?>
<!-- this meta statement needed to help with devices with smaller screens
             and helps with the form's responsiveness on smaller screens -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* {
box-sizing: border-box;
}

/* the input rules below should only apply to the fst_column_2 containers, so that other inputs on the page are not affected  (version 15+) */
.fst_column_2 > input[type=text],
.fst_column_2 > select,
.fst_column_2 > textarea
 {
background-color:#FFFF9E;
width: 100%;
padding: 12px;
border: 1px solid #ccc;
border-radius: 4px;
resize: vertical;
font-family: inherit;
 font-size: inherit;
 font-weight: inherit;
 }

.fst_column_2 > label {
padding: 12px 12px 12px 0;
display: inline-block;
float:right;
text-align:right;
}
.fst_column_2 > input[type=submit] {
    background-color: #4CAF50;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin:15px auto;
    display:block;}

.fst_column_2 > input[type=submit]:hover {
background-color: #45a049;
}

/* add CSS for required input boxes if enabled (Version 11) */
        .fst_column_2 > input,
        .fst_column_2 > textarea {
        background-color:#FFFF9E;
        width:100%;
        float:left;
        padding:10px;
        border: solid 1px #C0C0C0;
        border-radius:4px;
        font-family: inherit;
        font-size: 125%;
       color:<?php echo FST_INPUT_REQUIRED['color']; ?>;
        }
        /* read-onlyh fields get a green background */
        .fst_column_2 > input:read-only,
        .fst_column_2 > textarea:read-only {
            background-color:<?php echo FST_INPUT_VALID['background-color']; ?>;
            pointer-events: none;
        }


       .fst_column_2 >  input:required,
       .fst_column_2 > textarea:required,
       .fst_column_2 > input:focus:required,
       .fst_field_required {
        /*  border-color:<?php (FST_INPUT_REQUIRED['border-color']) ? FST_INPUT_REQUIRED['border-color'] : "red";?>;    */
            border-color:<?php echo FST_INPUT_REQUIRED['border-color']; ?>;
            border-width:<?php echo FST_INPUT_REQUIRED['border-width']; ?>;
            background-color:<?php echo FST_INPUT_REQUIRED['background-color']; ?>;
           border-top-style:<?php echo FST_INPUT_REQUIRED['border-top-style']; ?>;
           border-right-style:<?php echo FST_INPUT_REQUIRED['border-right-style']; ?>;
           border-bottom-style:<?php echo FST_INPUT_REQUIRED['border-bottom-style']; ?>;
           border-left-style:<?php echo FST_INPUT_REQUIRED['border-left-style']; ?>;
           border-radius:<?php echo FST_INPUT_REQUIRED['border-radius']; ?>;
           color:<?php echo FST_INPUT_REQUIRED['color']; ?>;
            }
        .fst_column_2 > input:required:invalid,
        .fst_column_2 > textarea:required:invalid,
        .fst_column_2 > input:focus:invalid,
        .fst_field_invalid {
            border-color:<?php echo FST_INPUT_INVALID['border-color']; ?>;
            border-width:<?php echo FST_INPUT_INVALID['border-width']; ?>;
            background-color:<?php echo FST_INPUT_INVALID['background-color']; ?>;
           border-top-style:<?php echo FST_INPUT_INVALID['border-top-style']; ?>;
           border-right-style:<?php echo FST_INPUT_INVALID['border-right-style']; ?>;
           border-bottom-style:<?php echo FST_INPUT_INVALID['border-bottom-style']; ?>;
           border-left-style:<?php echo FST_INPUT_INVALID['border-left-style']; ?>;
           border-radius:<?php echo FST_INPUT_INVALID['border-radius']; ?>;
           color:<?php echo FST_INPUT_INVALID['color']; ?>;
        }

        .fst_column_2 > input:required:valid,
        .fst_column_2 > textarea:required:valid ,
        .fst_field_valid {
            border-color:<?php echo FST_INPUT_VALID['border-color']; ?>;
            border-width:<?php echo FST_INPUT_VALID['border-width']; ?>;
           background-color:<?php echo FST_INPUT_VALID['background-color']; ?>;
           border-top-style:<?php echo FST_INPUT_VALID['border-top-style']; ?>;
           border-right-style:<?php echo FST_INPUT_VALID['border-right-style']; ?>;
           border-bottom-style:<?php echo FST_INPUT_VALID['border-bottom-style']; ?>;
           border-left-style:<?php echo FST_INPUT_VALID['border-left-style']; ?>;
           border-radius:<?php echo FST_INPUT_VALID['border-radius']; ?>;
           color:<?php echo FST_INPUT_VALID['color']; ?>;
        }
        .fst_field_required,
        .fst_field_valid,
        .fst_field_invalid {
            width:fit-content;
            padding-left:4px;
            padding-right:4px;
        }
       .fst_field_required_text {
        /*  border-color:<?php (FST_INPUT_REQUIRED['border-color']) ? FST_INPUT_REQUIRED['border-color'] : "red";?>;    */
            background-color:<?php echo FST_INPUT_REQUIRED['background-color']; ?>;
           color:<?php echo FST_INPUT_REQUIRED['color']; ?>;
           border: none;
           padding: 0 1%;
            }

        /* this needs to be here to override read-only pointer events so the button will work */
        .fst_column_2 > input[type=file]{
        pointer-events: auto ;
    }

/* next two IDs are used for the submit button. The ID value is now customizable (since version 17) ; the default value is 'fst_submitbutton' */
#<?php echo FST_SUBMITBUTTON_ID;?> {
    margin-top:10px;
    background-color:#38f923;
    border-radius: 8px;
    padding:7px;
    color:black;
}
#<?php echo FST_SUBMITBUTTON_ID;?>:hover {
    background-color: #00E000;
    color:white;
}

.fst_container {
    border-radius:25px;
    background-color:white;
}
.fst_form {
    padding:5px 10px;
}
.fst_formtop_message {
    text-align: center
}

.fst_col_25 {
    float: left;
    width: 15%;
    margin-top: 6px;
}

.fst_col_75 {
    float: left;
    width: 85%;
    margin-top: 6px;
}

.fst_grid {
    display: grid;
    grid-template-columns:25% 50% 25%;
}

.fst_column_1 {
    grid-column: 1;
    padding:10px;
}

.fst_column_2 {
    grid-column: 2;
    padding:10px;
}
.fst_column_3 {
    grid-column: 3;
    padding: 10px;
    text-align: left;
}
/* Clear floats after the columns */
.fst_row:after {
    content: "";
    display: table;
    clear: both;
}
  .fst_column_2 > input[type=checkbox],
  .fst_column_2 > input[type=radio] {
    /* Reset to static positioning (ideally, remove the position: absolute; from Bootstrap) */
    position: static;
    align-self: center;
    /* Align in the case where flex doesn't apply (checkbox & radio addons, mostly) */
    vertical-align: middle;
    /* Setting width and height is optional; alignment works without. However, setting it allows for more predictable layouts. */
    float: left;
    margin-top: 12px;
    vertical-align: middle;
    margin-top: 0;
    float: none;
}

@media screen and (max-width: 900px) {
    .fst_container {
        padding:0;
    }
    .fst_grid {
       grid-template-columns: 1fr;
    }
    .fst_column_1, .fst_column_2, .fst_column_3 {
        grid-column:1 ;
    }
    .fst_column_1 {
        margin-top:10px;
        text-align:center;
        float:revert;
        font-weight:bold;
        border-top:solid black 1px;
        padding-top:4%;
    }

    .fst_column_3 {
        text-align:center;
        margin-bottom: 10px;
    }
    .fst_column_2 > label {
        float:left;
    }
   .fst_column_2 >  input[type="checkbox"],
   .fst_column_2 > input[type="radio"] {
        vertical-align: middle;
        margin-top: 0;
        display: inline;
        float: none;
    }
    label {       /* to remove float so text can center */
        float: left;
        float: revert;
    }

}
    /* since version 16.0 ; helps with proper field placement on phones*/
    @media (max-width: 900px) {

        .fst_column_1, .fst_column_3, .fst_column_2, .fst_column_2 > input {
        padding:0;
        margin:0;
        }
      .fst_column_2 > p > input[type="checkbox"] {
            width: auto;
        }
    }

</style>

<?php
	if (FST_DARK_MODE_ENABLE) {; // sets form area to dark mode if enabled
		?>
        <style>

        .fst_container
        {
            filter: invert(100%) hue-rotate(180deg) !important;
        }
        </style>
    <?php }
    		return;
    	}

    	// --------------------------------------------------------------------------
    	// end of responsive form area
    	// --------------------------------------------------------------------------

    	// --------------------------------------------------------------------------
    	// This function allows our code to be used in a WordPress Contact page.
    	//  Implementation instructions for WordPress are in a separate txt file
    	//
    	// This creates a shortcode to allow you to place the contact form anywhere on
    	/// your WP page. You must create a template for the page. Full instrucitons
    	//  in the WordPress instruction PDF.
    	//
    	// This function uses the ob_start/clean stuff to 'return' the form, since shortcodes
    	//  must use a return value, not 'echo' statements.

    	//  version 16.0 - process any shortcode attributes
    	//      attributes that are currently allowed are
    	//          email  = primary email address
    	//          cc      = CC email address
    	//          bcc     = BCC email address
    	//      invalid email addresses are ignored; they may cause a Sanity Check message
    	//      any attributes in the shortcode will override those specified in FST_MORE_FUNCTIONS
    	//          this allows you to have a generic FST template in WordPress, and then use shortcodes to adjust the allowed parameters
    	//      any invalid attributes are ignored and will not cause an error
    	//      shortcode attributes are forced lowercase.
    	// --------------------------------------------------------------------------
    	function formspammertrap_shortcode($atts = "") {

    		ob_start();
    		formspammertrap_contact_form();
    		return ob_get_clean();
    	}

    	// --------------------------------------------------------------------------
    	// supporting function for shortcode to extract shortcode atts from the content

    	function fst_get_shortcode_atts($shortcode_id) {
    // get the content before the loop (see https://wordpress.stackexchange.com/a/377671/29416 )
    global $post;
    
    // Try multiple methods to get the page ID
    $pageID = false;
    
    // Method 1: Try global $post
    if ($post && is_object($post) && property_exists($post, 'ID') && $post->ID) {
        $pageID = $post->ID;
    }
    
    // Method 2: Try get_the_ID() if available
    if (!$pageID && function_exists('get_the_ID')) {
        $pageID = get_the_ID();
    }
    
    // Method 3: Try get_queried_object_id() if available
    if (!$pageID && function_exists('get_queried_object_id')) {
        $pageID = get_queried_object_id();
    }
    
    // If we still don't have a page ID, return false
    if (!$pageID) {
        return false;
    }
    
    $fst_content = get_the_content(null, false, $pageID); // get post content out of the loop
    
    // extract shortcode attributes (from  https://wordpress.stackexchange.com/a/172285/29416
    preg_match_all('/' . get_shortcode_regex() . '/s', $fst_content, $matches);
    $shortcode_atts_formspammertrap = array();
    if (isset($matches[2])) {
        foreach ((array) $matches[2] as $key => $value) {
            if ($shortcode_id === $value) {
                $shortcode_atts_formspammertrap[] = shortcode_parse_atts($matches[3][$key]);
            }
        }
    }
    if (!count($shortcode_atts_formspammertrap)) { // none found, so return false
        return false;
    }
    // 0th element of the array contains an array of the attributes
    $shortcode_atts_formspammertrap = $shortcode_atts_formspammertrap[0];
    // a constant for possible use in future versions
    if (!defined("SHORTCODE_ATTS")) {
        define("SHORTCODE_ATTS", $shortcode_atts_formspammertrap);
    }
    return $shortcode_atts_formspammertrap; // could have also returned the constant
}

    	// ------------------------------------------------------
    	// New in Version 7.x : display and process additional fields

    	// ------------------------------------------------------
    	// check the additional fields array for proper values.
    	//      Fatal error if not defined properly
    	// ------------------------------------------------------
    	function fst_check_field_names($FST_XCUSTOM_FIELDS) {

    		// check if FST_XCUSTOM_FIELDS is defined
    		// check if form field array is actually an array
    		$xerror = array();
    		if (!is_array(FST_XCUSTOM_FIELDS)) {$xerror[] = 'FATAL ERROR - FST_FORM_FIELDS not correctly defined as array. Program terminated.';
    			fst_die_fatal($xerror);}

    		foreach (FST_XCUSTOM_FIELDS as $form_field) {
    			// check for all parameters exist
    			if (!isset($form_field["ORDER"])) {$xerror[] = 'Missing or invalid ORDER value in the field array';}
    			if (!isset($form_field["NAME"])) {$xerror[] = 'Missing  or invalid NAME value in the field array';}
    			if (!isset($form_field["TYPE"])) {$xerror[] = 'Missing  or invalid TYPE value in the field array';}
    			if (!isset($form_field["MAXCHARS"])) {$xerror[] = 'Missing  or invalid MAXCHARS value in the field array';}
    			if (!isset($form_field["MAXLENGTH"])) {$xerror[] = 'Missing  or invalid MAXLENGTH value in the field array';}
    			if (!isset($form_field["REQUIRED"])) {$xerror[] = 'Missing  or invalid REQUIRED value in the field array';}
    			if (!isset($form_field["ORDER"])) {$xerror[] = 'Missing  or invalid ORDER value in the field array';}
    			if (!isset($form_field["PLACEHOLDER"])) {$xerror[] = 'Missing  or invalid PLACEHOLDER value in the field array';}

    			// check for type = input or checkbox
    			if (in_array($form_field["TYPE"], array('TEXT', 'CHECKBOX')) && false) {
    				$xerror[] = 'Missing  or invalid TYPE value in the field array';
    			}
    			if (count($xerror)) {fst_sanity_check($xerror);}
    		} // end foreach
    		return;
    	}

    	// --------------------------------------------------------------------------
    	// loops through the FST_CODE_INSERT code and outputs code blocks
    	//   FST_CODE_INSERT array(
    	//      'codeblock' => 'html code')
    	// Since version 17 : the FST_CODE_INSERT can be a string
    	//  Note there is no checking for valid code, so "Danger, Wil Robinson!"

    	// --------------------------------------------------------------------------

    	function fst_insert_codeblock($codeblock = "") {
    		if (is_array($codeblock)) {
    			foreach ($codeblock as $item) {
    				echo $item['CODEBLOCK'];
    			}
    		} else {
    			echo htmlspecialchars_decode($codeblock);
    		}
    		return;
    	}

    	// --------------------------------------------------------------------------
    	// display the fatal errors, and die unmercifully
    	// --------------------------------------------------------------------------
    	function fst_die_fatal($xerror) {
    		$msg = "<br>Error in Customization Values! Program aborted.<br>";
    		echo $msg;
    		if (is_array($xerror)) {$msg .= implode('<br>', $xerror);} else { $msg = $xerror;}
    		echo $msg;
    		die($msg);

    		return;
    	}

    	// --------------------------------------------------------------------------
    	// check for proper minimal required field values of valid email domain
    	//  note that displayed messages are not part of the new language options in version 14
    	// --------------------------------------------------------------------------

    	function fst_sanity_check($x = array()) {

    		// check for mail() function or alternative
    		if ((!function_exists('mail')) and (!function_exists('FST_MAIL_ALT'))) {
    			$x[] = "The PHP mail() function is not enabled on your server. You will need to add a FST_MAIL_ALT() function to your form that will process the mail. Or you need to specify an alternate mail server process with the FST_MAIL_ALT setting.";
    		}

    		// check for recipient email address set
    		if (!strlen(trim(FST_XEMAIL_ON_DOMAIN))) {
    			$x[] = 'Need to specify the recipient email address (FST_XEMAIL_ON_DOMAIN ) in your form\'s coe file.';
    		}

    		// check for recipient email address set
    		if (!fst_is_ssl()) {
    			$x[] = 'HTTPS/SSL is not sensed on your site. FormSpammerTrap requires SSL is enabled on contact pages. (And it should be on your entire site.) Contact your hosting place to get SSL enabled. ';
    		}

    		// check if $FST_FROM_EMAIL is set for the current domain (improved since v16)
    		$mail_domain = explode("@", FST_XEMAIL_ON_DOMAIN);
    		$mail_domain = strtolower(trim($mail_domain[1]));
    		$this_domain = fst_get_domain($_SERVER['HTTP_HOST']); // since version 17
    		$same_domain = ($this_domain == $mail_domain) ? true : false;
    		if (!$same_domain) {
    			$x[] = 'The FST_FROM_EMAIL value (set to <b>' . FST_XEMAIL_ON_DOMAIN . '</b>) needs to match your domain. You specified an email on <b>' . $mail_domain . '</b> , it should be an email on <b>' . $this_domain . '</b> . This value is set in your form\'s code.';
    		}
    		// check for CURL if Recaptcha keys found
    		if ((FST_RECAPTCHA_SITEKEY or FST_RECAPTCHA_SECRET) and (!fst_check_for_curl())) {
    			$x[] = "ReCaptcha keys specified, but CURL doesn't work on your system, so ReCaptcha will not work.";
    		}

    		// allowed extensions must be array
    		if (!is_array(FST_UPLOAD_EXTENSIONS)) {
    			$x[] = 'Allowed file extensions setting not an array. Check FST_UPLOAD_EXTENSIONS settings value in your contact form.';
    		}
    		// if allowed extensions array is not empty, then check for the uploads folder
    		if ((count(FST_UPLOAD_EXTENSIONS)) and (is_array(FST_UPLOAD_EXTENSIONS)) and (!is_dir(FST_UPLOAD_FOLDER))) {
    			$x[] = 'You have enabled the file upload options. The FST_UPLOAD_FOLDER value (set to <b>' . FST_UPLOAD_FOLDER . '</b> ) does not exist or is not defined in your form . Create that folder to allow uploaded files to be stored. The "FST_UPLOAD_FOLDER" setting should not have leading or trailing slash, and must be relative to the form\'s folder. (On WordPress sites, the folder is relative to the site root.)';
    		}
    		if (count(FST_UPLOAD_EXTENSIONS) < 0) {
    			$x[] = "You have enabled file upload extensions, but have not specified any allowable extensions in the FST_UPLOAD_EXTENSIONS setting.";
    		}
    		// checking for proper extensions per the MIME types in fst_mime2ext
    		if (count(FST_UPLOAD_EXTENSIONS) > 0) {
    			$mime_array = fst_mime2ext("*"); // asterisk gives us the entire mime-type array
    			foreach (FST_UPLOAD_EXTENSIONS as $item) {
    				// check for leading period in  FST_UPLOAD_EXTENSIONS
    				if (substr($item, 0, 1) != ".") {
    					$x[] = 'You have specified file upload extensions, but the <b>' . $item . '</b> file extension must have a leading period character, as in "<b>.' . $item . '</b>" . This allows proper checking of uploaded files.';
    				}
    				$item = substr($item, 1);
    				if (!array_search($item, $mime_array)) {
    					$x[] = 'You have specified file upload extensions, but the <b>' . $item . '</b> extension is not a valid MIME type extension. See <a href="https://stackoverflow.com/a/53662733/1466973" target="_blank">https://stackoverflow.com/a/53662733/1466973</a> and <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types</a> for a list of valid MIME-type extensions.';
    				}
    			}
    		}
    		// check database settings if enabled, or if the short_form (only email/name) is enabled
    		if ((FST_SAVE_CONTACT_INFO) OR (FST_SHORT_FORM)) { // for contact database settings
    			$dbok = fst_contact_db_check();
    			if ($dbok) {$x[] = $dbok;}
    		}
    		// check for PHP version 7+ (or some constants will cause errors - v10+)
    		if (!version_compare(PHP_VERSION, '7.2.0', '>=')) { // returns true if less than 7.2.0
    			$x[] = 'PHP version 7.2.0 + is required. You have version ' . PHP_VERSION;
    		}

    		// checks if FST_CUSTOM_FIELDS_ONLY true
    		if (FST_CUSTOM_FIELDS_ONLY) {
    			/* checks to make
    			- make sure there is at least one FST_XCUSTOM_FIELDS array; otherwise error
    			- check for FST_MAIL_ALT function to email things (sanity check)
    			- check for FST_CUSTOM_CHECK function for field checking (sanity check)
    			- check at least one required field (sanity check)

    			 */
    			if ((!count(FST_XCUSTOM_FIELDS)) or (!is_array(FST_XCUSTOM_FIELDS))) {
    				$x[] = "Custom Fields enabled: you must have at least one FST_XCUSTOM_FIELDS array defined in your form, and it must be a properly defined array.";
    			}

    			if (!function_exists("FST_CUSTOM_CHECK")) {
    				$x[] = "Custom Fields enabled: you must define the FST_CUSTOM_CHECK function in your form page code to check fields on the form (even if it doesn't do anything).";
    			}
    			if ((is_array(FST_XCUSTOM_FIELDS)) AND (count(FST_XCUSTOM_FIELDS))) {
    				foreach (FST_XCUSTOM_FIELDS as $item) {
    					if (!array_key_exists('REQUIRED', $item)) {
    						$x[] = "Custom Fields enabled: there must be a 'required' element in each FST_XCUSTOM_FIELDS array element. Element name is " . $item['NAME'];
    					}
    				}
    			}
    			// check for all keys in the FST_XCUSTOM_FIELDS array
    			// see https://www.php.net/manual/en/function.array-key-exists.php#83896
    			$FST_XCUSTOM_FIELDS_keys = array('ORDER', 'NAME', 'TYPE', 'MAXCHARS', 'MAXLENGTH', 'REQUIRED', 'PLACEHOLDER', 'LABELMSG', 'AFTERMSG', 'CLASS', 'ID'
    			);
    		} // end of custom field checking

    		// check for sessions active (required, or fst_go_away aborts form submittal
    		if (!extension_loaded('session')) {
    			$x[] = 'You must enable PHP session support for FormSpammerTrap to work. Contact your hosting company, or fix the php.ini file (advanced).';
    		}

    		// check for the PHPMailer namespace properly loaded
			// Platform-aware PHPMailer sanity check
		if (FST_USE_PHPMAILER ) {
			$platform_phpmailer = (defined('ABSPATH') && file_exists(ABSPATH . WPINC . '/PHPMailer/PHPMailer.php'));
			$plugin_phpmailer = (file_exists(__DIR__ . "/phpmailer/PHPMailer.php") && file_exists(__DIR__ . "/phpmailer/Exception.php"));
			
			if (!$platform_phpmailer && !$plugin_phpmailer) {
				$x[] = 'PHPMailer is required but not found in WordPress/ClassicPress core or plugin folder. Please ensure PHPMailer files are available.';
			}
			
			if (!$platform_phpmailer && !file_exists(__DIR__ . "/phpmailer/SMTP.php") && FST_SMTP_ENABLE) {
				$x[] = 'SMTP is enabled but SMTP.php file is not available in WordPress/ClassicPress core or plugin folder.';
			}
		}
			// check for PHPMailer, unless FST_MAIL_ALT function is defined in the form
    		if ((! FST_USE_PHPMAILER) and (!function_exists('FST_MAIL_ALT'))) {
				$x[] = "PHPMailer is not installed in the \'phpmailer\' folder, and you have not specified the FST_MAIL_ALT() function in your form. FST must use PHPMailer to send mail, unless your FST_MAIL_ALT() function exists for your own mailing process. Please check the documentation for details on how to properly install the PHPMailer files for your site.";
 			}

    		if (count($x)) {
    			$msg = "<div class='fst_yellow_box' style='background-color:yellow'><p>- Remember to GLOBAL all customized settings in your FST_MORE_FIELDS function. Then set the value as needed.</p><p>Note: using FormSpammerTrap version " . FST_VERSION . " . </p>";
    			foreach ($x as $item) {
    				$msg .= "<p>- " . $item . "</p>";
    			}
    			$msg .= "</div>";
    			//$msg = implode("<br> - ", $x);
    			fst_display_fatal($msg);
    			return false;
    		}
    		return true;
    	}

    	// --------------------------------------------------------------------------
    	// supporting function for array keys checking
    	// --------------------------------------------------------------------------
    	function fst_array_keys_exist($keys, $array) {
    		$keys = strtoupper($keys);
    		if (count(array_intersect($keys, array_keys($array))) == count($keys)) {
    			return true;
    		}
    	}

    	// --------------------------------------------------------------------------
    	// contact database sanity check if needed
    	// Note that displayed messages are not part of the new language options in version 14
    	// --------------------------------------------------------------------------
    	function fst_contact_db_check() {
    		// ensure database settings are an array
    		if (!is_array(FST_CONTACT_DATABASE)) {
    			$x[] = 'Contact Database setting not an array. Check FST_CONTACT_DATABASE settings value.';
    		}
    		// ensure database table exists; it should be defined like this
    		/*
    		$FST_CONTACT_DATABASE = array(
    		"DATABASE_LOC" => "localhost",  // database location; normally localhost
    		"DATABASE_NAME" => "",      // the database name on your site. Required, must exist
    		"DATABASE_USER" => "",      // the user name for that database. Required. Must be valid.
    		"DATABASE_PASS" => "",      // the secure/strong password for the database. Required. Password strength not checked.
    		"DATABASE_TABLE" => "",     // the table in the database to store contact information
    		"FIELD_EMAIL"   => "",      // the field name for the email address. Required. Must be at least 25 length.
    		"FIELD_FULLNAME" => "",     // the field name to store the name entered into the form. Required. Must be at least 50 length
    		"FIELD_DATESTAMP" => ""     // the datestamp field, used to store date/time of last update
    		"FIELD_GUID" => ""          // field name for the GUID field used for verification emails (if inabled)
    		"FIELD_STATUS" => 'status'  // field name for status of the record: 10=unverified, 20=verified
    		NEW in version 16
    		"FIELD_DEFAULTS => array("fieldname1" => "field1_value", "fieldname2" => "field2_value")  // default values for indicated field names. There is mimimal valiation of the field or value, so test with your database to ensure data is corretly stored for those fields.
    		);

    		 */
    		$xdataloc   = FST_CONTACT_DATABASE['DATABASE_LOC'];
    		$xdatabase  = FST_CONTACT_DATABASE['DATABASE_NAME'];
    		$xdatatable = FST_CONTACT_DATABASE['DATABASE_TABLE'];
    		$xuser      = FST_CONTACT_DATABASE['DATABASE_USER'];
    		$xpass      = FST_CONTACT_DATABASE['DATABASE_PASS'];
    		$xemail     = FST_CONTACT_DATABASE['FIELD_EMAIL'];
    		$xname      = FST_CONTACT_DATABASE['FIELD_FULLNAME'];
    		$xdate      = FST_CONTACT_DATABASE['FIELD_DATESTAMP'];
    		$xguid      = FST_CONTACT_DATABASE['FIELD_GUID'];
    		$xstatus    = FST_CONTACT_DATABASE['FIELD_STATUS'];
    		$xdefault   = FST_CONTACT_DATABASE['FIELD_DEFAULTS'];
    		try {
    			$con = mysqli_connect($xdataloc, $xuser, $xpass, $xdatabase);
    		} catch (Exception $e) {
    			$data_sanity_error = "Failed to connect to database; error message is:<b> " . mysqli_connect_error() . "</b>. Check FST_CONTACT_DATABASE settings: database location incorrect, database does not exist, or database credentials are incorrect. (1)";
    			return $data_sanity_error;
    		}
    		if (!$con) {
    			$data_sanity_error = "Failed to connect to database; error message is: <b>" . mysqli_connect_error() . "</b>. Check FST_CONTACT_DATABASE settings: database location incorrect, or database does not exist, or database credentials are incorrect. (2)";
    			return $data_sanity_error;
    		}
    		// connect to table in the database
    		$sql = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$xdatabase' AND TABLE_NAME = '$xdatatable'";
    		// "Checking database table $xdatatable exists ... ";
    		mysqli_query($con, $sql);
    		if (mysqli_connect_error()) {
    			$data_sanity_error = "Failed to connect to the $xdatatable table: error message is: <b>" . mysqli_connect_error() . "</b> . Check FST_CONTACT_DATABASE settings.Incorrect table name, or table doesn't exist.";
    			return $data_sanity_error;
    		}
    		$data_sanity_error = ""; // reset for further use in field checking
    		mysqli_close($con);
    		$mysqli = new mysqli($xdataloc, $xuser, $xpass, $xdatabase);
    		if ($mysqli->connect_errno) {echo "Error connecting to database $xdatabase . (6)";}
    		// check field names (type and length)
    		$query  = "DESCRIBE " . $xdatatable;
    		$result = $mysqli->query($query);
    		if ($result) {;
    		} else {
    		}
    		$columns = $result->fetch_all(MYSQLI_ASSOC);
    		$mysqli->close();
    		$xemail_current  = "(Does not exist or named incorrectly.)";
    		$xdate_current   = "(Does not exist or named incorrectly.)";
    		$xguid_current   = "(Does not exist or named incorrectly.)";
    		$xstatus_current = "(Does not exist or named incorrectly.)";
    		$xname_current   = "(Does not exist or named incorrectly.)";
    		foreach ($columns as $item) {
    			switch ($item['Field']) {
    				case $xemail:
    					$xemail_ok = true;
    					if ($item['Type'] == 'char(50)') {$xemail_length_ok = true;
    					}
    					$xemail_current = $item['Field'] . " " . $item['Type'];
    					break;

    				case $xname:
    					$xname_ok = true;
    					if ($item['Type'] == 'char(50)') {$xname_length_ok = true;
    					}
    					$xname_current = $item['Field'] . " " . $item['Type'];
    					break;

    				case $xguid:
    					$xguid_ok = true;
    					if ($item['Type'] == 'char(50)') {$xguid_length_ok = true;
    					}
    					$xguid_current = $item['Field'] . " " . $item['Type'];
    					break;

    				case $xstatus:
    					$xstatus_ok = true;
    					if ($item['Type'] == 'char(2)') {$xstatus_length_ok = true;
    					}
    					$xstatus_current = $item['Field'] . " " . $item['Type'];
    					break;

    				case $xdate:
    					$xdate_ok = true;
    					if ($item['Type'] == 'timestamp') {$xdate_ok = true;}
    					$xemail_current = $item['Field'] . " " . $item['Type'];
    					break;
    			}

    			// now check the additional field default value
    		}
    		// check added default fields, if any define
    		//echo "checking table for proper field names<br>";
    		$xfields_ok        = true;
    		$data_fields_error = "";
    		if (is_array($xdefault)) {
    			$field_names = array_column($columns, "Field"); // array of table field names only
    			foreach ($xdefault as $item => $value) { // loop through the added default fields
    				$found = array_search($item, $field_names);
    				if (!$found) {
    					$data_fields_error .= "<li>The <b>" . $item . "</b> field needs to exist that table, or be named properly. That field doesn't appear to exist in the table, or the field name you specified in the FST_CONTACT_DATABASE array is not correct.</li>";
    					$xfields_ok = false;
    				}
    			}
    		}
    		if ((!$xemail_ok) or (!$xemail_length_ok) or (!$xname_ok) or (!$xdate_ok) or (!$xguid_ok) or (!$xstatus_ok) or (!$xname_length_ok) or (!$xfields_ok)) {
    			$data_sanity_error .= "<p>The structure of the FST_CONTACT_DATABASE does not include the proper fields or field type/length that are required. These are the probable errors - although not a complete list of issues.</p>
<p>Your FST_CONTACT_DATABASE database name is defined as <b>$xdatabase</b> , with the table name of <b>$xdatatable</b> .</p>
   <ul> ";
    			$data_sanity_error .= $data_fields_error;
    			if ((!$xemail_ok) OR (!$xemail_length_ok)) {
    				$data_sanity_error .= "<li>The <b>$xemail</b> field needs to exist, and defined as <b>CHAR/50</b>. Current setting is <b>$xemail_current</b> .</li>";
    			}
    			if ((!$xname_ok) OR (!$xname_length_ok)) {
    				$data_sanity_error .= "<li>The <b>$xname</b> field needs to exist, and defined as <b>CHAR/50</b>. Current setting is <b>$xname_current</b> .</li>";
    			}
    			if (!$xdate_ok) {
    				$data_sanity_error .= "<li>The <b>$xdate</b> field needs to exist, and defined as <b>TIMESTAMP</b>. Current setting is <b>$xdate_current</b> . </li> ";
    			}
    			if ((!$xguid_ok) OR (!$xguid_length_ok)) {
    				$data_sanity_error .= "<li>The <b>$xguid</b> field needs to exist, and defined as <b>CHAR/50</b>. Current setting is <b>$xguid_current</b> . </li> ";
    			}
    			if ((!$xstatus_ok) OR (!$xstatus_length_ok)) {
    				$data_sanity_error .= "<li>The <b>$xstatus</b> field needs to exist, and defined as <b>CHAR/2</b>. Current setting is <b>$xstatus_current</b> . </li> ";
    			}

    			// version 15 - check if $field_defaults is an array, if not, show an error
    			if ((isset($xdefault)) AND (!is_array($xdefault))) {
    				$data_sanity_error .= "<li>The <b>FST_CONTACT_DATABASE field_defaults array element</b> needs to contain an array of default values, if used.</li> ";
    			}
    			$data_sanity_error .= "</ul>
<p>Please ensure your FST_CONTACT_DATABASE settings and table structure are defined correctly to match the actual database.  Please check the documentation for the proper settings and structure of the <b>$xdatatable</b> table used in the <b>$xdatabase</b> database.</p>
<p><b>The form has been entirely disabled.</b></p>";
    			return $data_sanity_error;
    		}

    		return; // all is well !
    	}

    	// --------------------------------------------------------------------------
    	// used to display fatal errors found by the fst_sanity_check of misconfiguration
    	//  - version 9: use FST_SANITY_CHECK_EMAIL flag to determine if displayed or emailed
    	// Note that displayed messages are not part of the new language options in version 14
    	// --------------------------------------------------------------------------
    	function fst_display_fatal($msg = "") {
    		$fatal_message = "<h2 align='center'>FormSpammerTrap Contact Page on your web site - Configuration Error!</h2>
<p class='fst_red_box' >Please contact the site web programmer or administrator.</p>
  <p class='fst_yellow_box' ><b>Configuration errors found:</b><br>$msg</p>
  <p class='fst_red_box' >Fatal Error - process aborted!</p>
    <p class='fst_green_box' >Please review the FormSpammerTrap documentation to ensure that all is properly configured. Use the <a href='https://www.formspammertrap.com/contact.php' target='_blank' class='fst_text_black'>Contact</a> page on the FormSpammerTrap site for help at <a href='https://www.FormSpammerTrap.com/contact.php' target='_blank' class='fst_text_black'>https://www.FormSpammerTrap.com/contact.php</a>.</p>";
    		// Always set content-type when sending HTML email
    		$headers = array(
    			'MIME-Version' => '1.0',
    			'Content-type' => 'text/html; charset=iso-8859-1',
    			'From' => FST_FROM_EMAIL,
    			'Sender' => FST_FROM_EMAIL,
    			'MIME-Version' => '1.0',
    			'Return-Path' => FST_FROM_EMAIL,
    			'X-Sender' => FST_FROM_EMAIL,
    			'X-Mailer' => 'PHP ' . phpversion(),
    		);
    		if (FST_XCC_EMAIL) {
    			$headers['BCC'] = FST_XCC_EMAIL;
    		}
    		$html_start = "<html><body>";
    		$html_end   = "</body></html>";
    		$sendmsg    = $html_start . $fatal_message . $html_end;
    		if (FST_SANITY_CHECK_EMAIL) {
    			$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

    			$referring_page = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    			$sendmsg .= "<p>The problem contact page is at $referring_page . </p>";
    			$message_elements = array(
    				'recipient' => FST_XEMAIL_ON_DOMAIN,
    				'subject' => "FST SANITY CHECK MESSAGE",
    				'message' => $sendmsg,
    				'headers' => $headers,
    			);
    			///fst_print_array($message_elements['headers']);die("3101");
    			fst_send_mail_phpmailer($message_elements); // config error message
    		} else {
    			echo "<div class='fst_fatal_message' >";
    			echo $fatal_message;
    			echo "</div>";
    		}
    		// display nicely formatted message on the screen in either case
    		echo '<hr><div class= "fst_yellow_box fst_text_center" >';
    		echo "<h3 align='center'>Yikes!</h3>";
    		echo "<p align='center'>There was a problem displaying the contact form. An email has been sent to the site administrator about this error.</p>
<p align='center'>Please try again later. Sorry for the problem.</p>
</div><hr>";
    		return true;
    	}

    	// --------------------------------------------------------------------------
    	// insert the save-contact checkbox if enabled
    	// uses the FST_SAVE_CONTACT_MESSAGE text
    	// --------------------------------------------------------------------------
    	function fst_insert_save_checkbox() {; // need the save contact field hidden/enabled if short form, so data will get saved  <br>
    		$xinput_id = fst_shortnumber();
    	?>
        <div id="savecontact" class="fst_label_checkbox">
                <p><label for="<?php echo $xinput_id; ?>"><?php echo FST_SAVE_CONTACT_LABEL; ?></label></p>
            </div>
            <div class='fst_column_2 fst_checkbox_left fst_aftermsg' >
                <p style='margin-top:0;'><input type="checkbox" id="fst_save_contact" name="fst_save_contact" value="send" />

                    <?php if (FST_SAVE_CONTACT_MESSAGE) {echo FST_SAVE_CONTACT_MESSAGE;}?>
<?php if (FST_XPRIVACY_PAGE_LINK) {echo FST_XPRIVACY_PAGE_LINK;}?></p>
            </div>
        <?php
        	return;
        	}

        	// --------------------------------------------------------------------------
        	// save the contact information into the FST_CONTACT_DATABASE contact table
        	// Note that displayed messages are not part of the new language options in version 14
        	// --------------------------------------------------------------------------

        	function fst_contact_db_save($xyour_name, $xyour_email) {
        		if (!is_array(FST_CONTACT_DATABASE)) {
        			return '<hr><b>Configuration error: Contact Database setting not an array. Check FST_CONTACT_DATABASE settings value.</b><hr>';
        		}
        		$xdataloc       = FST_CONTACT_DATABASE['DATABASE_LOC'];
        		$xdatabase      = FST_CONTACT_DATABASE['DATABASE_NAME'];
        		$xdatatable     = FST_CONTACT_DATABASE['DATABASE_TABLE'];
        		$xuser          = FST_CONTACT_DATABASE['DATABASE_USER'];
        		$xpass          = FST_CONTACT_DATABASE['DATABASE_PASS'];
        		$xemail         = FST_CONTACT_DATABASE['FIELD_EMAIL'];
        		$xname          = FST_CONTACT_DATABASE['FIELD_FULLNAME'];
        		$xdate          = FST_CONTACT_DATABASE['FIELD_DATESTAMP'];
        		$field_guid     = FST_CONTACT_DATABASE['FIELD_GUID'];
        		$field_status   = FST_CONTACT_DATABASE['FIELD_STATUS'];
        		$field_defaults = FST_CONTACT_DATABASE['FIELD_DEFAULTS'];

        		$guid = fst_guid();
        		$con  = fst_connect_db(); // connect to the database
        		// set status: 10=verify pending, 20=active/verified based on verify flag
        		if (FST_CONTACT_VERIFY) {$status = 10;} else { $status = 20;}
        		// database and table OK. look for a existing entry by email name
        		$query  = "SELECT * from $xdatatable where {$xemail} = '$xyour_email'";
        		$result = mysqli_query($con, $query);

        		// build the field defaults from the $field_defaults array
        		$default_update  = "";
        		$default_insert1 = "";
        		$default_insert2 = "";
        		if (is_array($field_defaults)) { // make sure it's an array
        			// loops through $field_default, creates string to add to query if values are there
        			foreach ($field_defaults as $fieldname => $fieldvalue) {
        				$default_update .= ($fieldname AND $fieldvalue) ? ", " . $fieldname . " = '" . $fieldvalue . "' " : ""; // update string if exists, otherwise blank
        				$default_insert1 .= ($fieldname AND $fieldvalue) ? ", " . $fieldname . " " : ""; // insert for fieldname  if exists, otherwise blank
        				$default_insert2 .= ($fieldname AND $fieldvalue) ? ", '" . $fieldvalue . "' " : ""; // insert for fieldvalue  if exists, otherwise blank
        				/* echo $default_update . "<br>";
        			echo $default_insert1 . "<br>";
        			echo $default_insert2 . "<br>";*/
        			}
        		}
        		if ($result->num_rows) {
        			// update it
        			$query = "UPDATE $xdatatable SET $xname = '$xyour_name', $xemail = '$xyour_email', $xdate = now(), $field_guid = '$guid', $field_status = '$status' " . $default_update . " WHERE $xemail = '$xyour_email'";
        		} else {
        			// insert it
        			$query = "INSERT INTO $xdatatable ($xname, $xemail,$field_guid, $field_status" . $default_insert1 . " ) VALUES ('$xyour_name', '$xyour_email', '$guid', '$status'" . $default_insert2 . " )";
        		}
        		$result = mysqli_query($con, $query); // do the query, then check result
        		if (!$result) { // bad result
        			return " <hr> <b><i>Error inserting name / email into the contact database. The FST_CONTACT_DATABASE Database setting not an array .</i></b> Check FST_CONTACT_DATABASE settings value .  <hr> ";
        		}
        		// if verify required, send out the email
        		if (FST_CONTACT_VERIFY) {
        			fst_mail_verify($xyour_email, $guid);
        		}
        		// all done, so return indicating all ok (this text shown in the sent message)
        		return '<hr><b>Contact Database setting updated for this person by their request.</b><hr>';
        	}

        	// --------------------------------------------------------------------------
        	//  connect to the database, return database info
        	// Note that displayed messages are not part of the new language options in version 14
        	// --------------------------------------------------------------------------

        	function fst_connect_db() {
        		// get the values
        		$xdataloc   = FST_CONTACT_DATABASE['DATABASE_LOC'];
        		$xdatabase  = FST_CONTACT_DATABASE['DATABASE_NAME'];
        		$xdatatable = FST_CONTACT_DATABASE['DATABASE_TABLE'];
        		$xuser      = FST_CONTACT_DATABASE['DATABASE_USER'];
        		$xpass      = FST_CONTACT_DATABASE['DATABASE_PASS'];
        		$xemail     = FST_CONTACT_DATABASE['FIELD_EMAIL'];
        		$xname      = FST_CONTACT_DATABASE['FIELD_FULLNAME'];
        		$xdate      = FST_CONTACT_DATABASE['FIELD_DATESTAMP'];
        		// check credentials to the database
        		try {
        			$con = mysqli_connect($xdataloc, $xuser, $xpass, $xdatabase);
        		} catch (Exception $e) {
        			return "<hr><i>Failed to connect to the contact database; error message is:<b> " . mysqli_connect_error() . "</b></i>. Check FST_CONTACT_DATABASE settings: database location incorrect, database does exist, or database credentials are incorrect. (7)</hr>";
        		}
        		if ($con === false) { // failed, return with error
        			return "<hr><i>Failed to connect to database; error message is: <b>" . mysqli_connect_error() . "</b>.</i> Check FST_CONTACT_DATABASE settings: database location incorrect, or database does not exist, or database credentials are incorrect. (8)<hr>";
        		}
        		return $con;
        	}

      	// --------------------------------------------------------------------------
      	// grab a number betweek 10K and 99K for use as ID in the first two form fields
      	//  those IDs will be replaced with actual values on form delay
      	// --------------------------------------------------------------------------
      	function fst_shortnumber() {
      		$shortnumber = rand(10000, 99000);

      		return $shortnumber;
      	}

      	// --------------------------------------------------------------------------
      	// check all required fields for values
      	// v14 = consolidated into fst_check_fields function, but left here for backwards compatibility
      	// --------------------------------------------------------------------------
      	function fst_check_required_fields() {
      		return;
      	}

      	// --------------------------------------------------------------------------
      	// stops back page so no resubmit
      	// --------------------------------------------------------------------------
      	function fst_stop_backpage() {
      	?>
              <script>
              if ( window.history.replaceState ) {
              window.history.replaceState( null, null, window.location.href );
              }
              </script>
          <?php
          	return;
          	}

          	// --------------------------------------------------------------------------
          	// show the noscript message
          	// --------------------------------------------------------------------------
          	function fst_no_script() {
          	?>
              <noscript>
                        <div class="fst_red_box fst_noscript" align="center">
                        <p class='fst_nojava' align="center"><?php echo FST_LANG_TEXT['javascript_enabled_1']; ?></p>
                        <p  class='fst_nojava' align="center"><?php echo FST_LANG_TEXT['javascript_enabled_2']; ?></p>
                        </div>
                        </noscript>
                        <?php
           	return;
           	}

	// --------------------------------------------------------------------------
	// redirect to the thanks page, and die since we're done
	// --------------------------------------------------------------------------
	function fst_redirect_thanks() {
	?>
	<script>
                var url = '<?php echo FST_XTHANKS_URL; ?>';
                window.location.replace(url);
                </script>
                <?php
                	// kill this page code since we don't need the rest of it after the redirect
                		die();
                		return false;
                	}

	// --------------------------------------------------------------------------
	// process the verification guid
	// --------------------------------------------------------------------------
	function fst_signup_verify($xguid = "") {
		if (!$xguid) {
			return false;
		}
		$errmsg       = "";
		$con          = fst_connect_db();
		$xdataloc     = FST_CONTACT_DATABASE['DATABASE_LOC'];
		$xdatabase    = FST_CONTACT_DATABASE['DATABASE_NAME'];
		$xdatatable   = FST_CONTACT_DATABASE['DATABASE_TABLE'];
		$xuser        = FST_CONTACT_DATABASE['DATABASE_USER'];
		$xpass        = FST_CONTACT_DATABASE['DATABASE_PASS'];
		$xemail       = FST_CONTACT_DATABASE['FIELD_EMAIL'];
		$xname        = FST_CONTACT_DATABASE['FIELD_FULLNAME'];
		$xdate        = FST_CONTACT_DATABASE['FIELD_DATESTAMP'];
		$field_guid   = FST_CONTACT_DATABASE['FIELD_GUID'];
		$field_status = FST_CONTACT_DATABASE['FIELD_STATUS'];
		$query        = "SELECT * from $xdatatable where {$field_guid} = '$xguid'";
		$result       = mysqli_query($con, $query);
		$rowcount     = $result->num_rows;
		switch ($rowcount) {
			case 1:
				break;

			case 0:
			default:
				$errmsg = "<p align='center'>" . FST_LANG_TEXT['verify_link_wrong'] . "</p>";
				break;
		}
		$status = 20; // indicates verified
		$query  = "UPDATE $xdatatable SET $field_status = '$status' where {$field_guid} = '$xguid'";
		$result = mysqli_query($con, $query); // do the query, then check result
		if (!$result) { // bad result
			$errmsg = " <hr> <b><i>" . FST_LANG_TEXT['data_update_error'] . "</i></b><hr> ";
			echo $msg;
		}
		if ($errmsg) {$msgcolor = 'yellow'; // setup the message and colors
			$msgtitle                            = FST_LANG_TEXT['signup_form_error'];} else { $msgcolor = '#90EE90';
			$msgtitle                             = FST_LANG_TEXT['signup_form_thanks_from'];}
		$msg = "<div style='background-color:$msgcolor; padding:8px;    color:black ;'><h2 align='center'>$msgtitle " . FST_SITE_NAME . "!</h2>";
		if ($errmsg) {$msg .= $errmsg;} else {
			$msg .= "<h3 align='center' >" . FST_LANG_TEXT['signup_form_ok'] . "</h3>";
		}
		if (FST_XTHANKS_URL) {
			$msg .= "<p align='center'><a href='" . FST_CONTACT_VERIFY_REDIRECT . "' class = 'fst_text_black'>" . FST_LANG_TEXT['signup_form_return'] . "</a></p>";
		}
		$msg .= "</div>";
		// display verified message, then abort the message box
		echo $msg;

		// kill this page code since we don't need the rest of it after the redirect
		die();

		return $msg;
	}

	// --------------------------------------------------------------------------
	// these are the four standard fields that can be overridden via setting the $FST_XCUSTOM_FILEDS array in the FST_MORE_FIELDS function
	// Note: doesn't use the new version 15 VALUE and READONLY array parameters, since these fields need input
	// --------------------------------------------------------------------------
	function fst_four_fields() {
		// first four items are standard fields
		$FST_XCUSTOM_FIELDS   = array();
		$FST_XCUSTOM_FIELDS[] = array(
			'ORDER' => '1', // order number for the fields, start at 1, determines placement of the additional field in the form
			'NAME' => 'your_name', // name/id/label of the field
			'TYPE' => 'TEXT', // type of field
			'MAXCHARS' => '50', // character width of field
			'MAXLENGTH' => '50', // max number of characters for the field
			'REQUIRED' => true, // true if required, false if not required
			'PLACEHOLDER' => 'Enter your name', // placeholder text for  input area
			'LABELMSG' => 'Your Name', // text to display to the left (label area) of the field
			'AFTERMSG' => 'Please enter full name',
			'CLASS' => '', // class name for the field, optional
			'ID' => 'your_name', // id name for the field, required

		);

		$FST_XCUSTOM_FIELDS[] = array(
			'ORDER' => '2', // order number for the fields, start at 1, determines placement of the additional field in the form
			'NAME' => 'your_email', // name/id/label of the field
			'TYPE' => 'EMAIL', // type of field
			'MAXCHARS' => '100', // character width of field
			'MAXLENGTH' => '100', // max number of characters for the field
			'REQUIRED' => true, // true if required, false if not required
			'PLACEHOLDER' => 'Enter your email', // placeholder text for  input area
			'LABELMSG' => 'Your Email', // text to display to the left (label area) of the field
			'AFTERMSG' => 'Please enter your email so we can contact you as needed',
			'CLASS' => '', // class name for the field, optional
			'ID' => 'your_email', // id name for the field, required

		);

		// since version 17
		//  - if $FST_SHORT_FORM is true, then don't do these fields below
		//      - Note : FST_SHORT_FORM was introduced in version 10, then broken starting with version 14
		if (FST_SHORT_FORM) {
			return $FST_XCUSTOM_FIELDS;
		}

		$FST_XCUSTOM_FIELDS[] = array(
			'ORDER' => '3', // order number for the fields, start at 1, determines placement of the additional field in the form
			'NAME' => 'your_subject', // name/id/label of the field
			'TYPE' => 'TEXT', // type of field
			'MAXCHARS' => '50', // character width of field
			'MAXLENGTH' => '50', // max number of characters for the field
			'REQUIRED' => true, // true if required, false if not required
			'PLACEHOLDER' => 'Enter Subject', // placeholder text for  input area
			'LABELMSG' => 'Subject', // text to display to the left (label area) of the field
			'AFTERMSG' => 'Message Subject',
			'CLASS' => '', // class name for the field, optional
			'ID' => 'your_subject', // id name for the field, required

		);

		$FST_XCUSTOM_FIELDS[] = array(
			'ORDER' => '4', // order number for the fields, start at 1, determines placement of the additional field in the form
			'NAME' => 'message', // name/id/label of the field
			'TYPE' => 'TEXTAREA', // type of field
			'MAXCHARS' => '80', // character width of field  (or columns in textarea)
			'MAXLENGTH' => '6', // max number of characters for the field  (or rows in textarea
			'REQUIRED' => true, // true if required, false if not required
			'PLACEHOLDER' => 'Enter Message', // placeholder text for  input area
			'LABELMSG' => 'Message', // text to display to the left (label area) of the field
			'AFTERMSG' => 'Please enter full details so we can understand your issue.',
			'CLASS' => '', // class name for the field, optional
			'ID' => 'message', // id name for the field, required

		);

		return $FST_XCUSTOM_FIELDS;
	}

	// --------------------------------------------------------------------------
	//  **** WARNING ****
	//      DO NOT CHANGE MESSAGES HERE - ONLY IN YOUR FST_MORE_FIELDS() FUNCTION
	//  **** WARNING ****
	//
	// loads default user-facing messages (English)
	//      - you can override these messages in your FST_MORE_FIELDS function of your form for your language. Lauguage support files may be in a future version, depending on demand.

	//      - make sure you GLOBAL the $FST_LANG_TEXT array
	//      - then change individual elements of message(s) as needed
	//      - Example in English language
	//          $FST_LANG_TEXT['incomplete_message'] = "Pay attention to the required fields!";
	//      - Example in Spanish language (via google translate)
	//          $FST_LANG_TEXT['incomplete_message'] = "¡Presta atención a los campos obligatorios!";
	//          That will put your new or alternate language message where that $FST_LANG_TEXT element is used
	//      - Don't use Constant names in your messages; use the variable version of the constant, as in
	//          $FST_SITE_NAME . Fields are turned into constants by FST.
	//
	// --------------------------------------------------------------------------

	function fst_language_text() {
		$FST_LANG_TEXT = array(
			"incomplete_message" => "Whoops! There is some incomplete or incorrect information",
			"verify_link_text" => "<p>Verification link: ",
			"verify_subject" => "Verification subject for the site",
			"verify_message" => "Verification message for the site",
			"file_upload_ok" => "File uploaded OK",
			"file_upload_error" => "File upload error",
			"file_upload_wrong_ext" => "Sorry, only " . implode(" - ", FST_UPLOAD_EXTENSIONS) . " files are allowed to upload.",
			"allowed_file_types" => "Allowed file types are:",
			"curl_not_enabled" => "reCAPTCHA failures - CURL not running here!",
			"recaptcha_notice" => "Goggle's Invisible reCAPTCHA, in addition to other bot-blocking techniques, is used on this form. Don't forget the required fields before sending! ",
			"javascript_enabled_1" => ">Note: JavaScript must be enabled to use this comment form.",
			"javascript_enabled_2" => "If not enabled, your message will not be sent.",
			"verify_link_wrong" => "Sorry - incorrect information. Please click the link in your newsletter signup verification message. If you still have problems, use our site's Contact page",
			"data_update_error" => "Error updating your information.</i></b> Site administrators have been notified.",
			"signup_form_error" => "Whoops! Problem verifying your signup from",
			"signup_form_ok" => "Your newsletter signup has been activated. Thanks!",
			"signup_form_thanks_from" => "Thanks from",
			"signup_form_return" => "Click here to return to the " . FST_SITE_NAME . " site.",
			"thumbnail_shown_here" => "List of selected files will be shown here",
			"no_file_long" => "No file selected; use the Browse button to include a file with your message",
			"show_version_message" => "This contact form protected against spambots by using special code from FormSpammerTrap.com .<br> FormSpammerTrap Version Information: Version " . FST_VERSION . " released " . FST_VERSION_DATE . ". PHPMailer version is " . FST_PHPMAILER_VERSION . ".<br> See the site for details: ",
			"show_required_message" => "required field",
			"reset_button_text" => "Reset Form",
			"close_button_text" => "Close Form",
			"required" => "(Required)", // for the 'required' text optionally used on required fields (v16+)
			"invalid_missing" => "Invalid or missing required field", // shows when required fields not filled out, or field contains invalid data (since version 16.0)
		);
		return $FST_LANG_TEXT;
	}

	// --------------------------------------------------------------------------
	// delay the click inside the form if there are errors
	// --------------------------------------------------------------------------

	function fst_delay_the_click_for_errors() {
	?>
<script>
    window.onload = fst_delaytheclick(event);
</script>
<?php
	return;
	}

	// --------------------------------------------------------------------------
	// get the domain of the URL, will remove subdomains (disabled since version 16)
	//      function left here and will return false if used  (since version 17)
	// --------------------------------------------------------------------------
	function fst_get_domain($url = "") {
		$strToLower       = strtolower(trim($url));
		$httpPregReplace  = preg_replace('/^http:\/\//i', '', $strToLower);
		$httpsPregReplace = preg_replace('/^https:\/\//i', '', $httpPregReplace);
		$wwwPregReplace   = preg_replace('/^www\./i', '', $httpsPregReplace);
		$explodeToArray   = explode('/', $wwwPregReplace);
		$finalDomainName  = trim($explodeToArray[0]);
		return $finalDomainName;
	}

	// --------------------------------------------------------------------------
	// get the mime_type of the file
	// --------------------------------------------------------------------------
	function fst_get_mime_type(string $filename) {
		$info = finfo_open(FILEINFO_MIME_TYPE);
		if (!$info) {
			return false;
		}

		$mime_type = finfo_file($info, $filename);
		finfo_close($info);

		return $mime_type;
	}

	// --------------------------------------------------------------------------
	/*creates an array of mime types => file extension
	from https://stackoverflow.com/a/53662733/1466973
	valid image types are here
	https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types
	 */

	// --------------------------------------------------------------------------
	function fst_mime2ext($mime_type = "") {
		$mime_map = array(
			'video/3gpp2' => '3g2',
			'video/3gp' => '3gp',
			'video/3gpp' => '3gp',
			'application/x-compressed' => '7zip',
			'audio/x-acc' => 'aac',
			'audio/ac3' => 'ac3',
			'application/postscript' => 'ai',
			'audio/x-aiff' => 'aif',
			'audio/aiff' => 'aif',
			'audio/x-au' => 'au',
			'video/x-msvideo' => 'avi',
			'video/msvideo' => 'avi',
			'video/avi' => 'avi',
			'application/x-troff-msvideo' => 'avi',
			'application/macbinary' => 'bin',
			'application/mac-binary' => 'bin',
			'application/x-binary' => 'bin',
			'application/x-macbinary' => 'bin',
			'image/bmp' => 'bmp',
			'image/x-bmp' => 'bmp',
			'image/x-bitmap' => 'bmp',
			'image/x-xbitmap' => 'bmp',
			'image/x-win-bitmap' => 'bmp',
			'image/x-windows-bmp' => 'bmp',
			'image/ms-bmp' => 'bmp',
			'image/x-ms-bmp' => 'bmp',
			'application/bmp' => 'bmp',
			'application/x-bmp' => 'bmp',
			'application/x-win-bitmap' => 'bmp',
			'application/cdr' => 'cdr',
			'application/coreldraw' => 'cdr',
			'application/x-cdr' => 'cdr',
			'application/x-coreldraw' => 'cdr',
			'image/cdr' => 'cdr',
			'image/x-cdr' => 'cdr',
			'zz-application/zz-winassoc-cdr' => 'cdr',
			'application/mac-compactpro' => 'cpt',
			'application/pkix-crl' => 'crl',
			'application/pkcs-crl' => 'crl',
			'application/x-x509-ca-cert' => 'crt',
			'application/pkix-cert' => 'crt',
			'text/css' => 'css',
			'text/x-comma-separated-values' => 'csv',
			'text/comma-separated-values' => 'csv',
			'application/vnd.msexcel' => 'csv',
			'application/x-director' => 'dcr',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
			'application/x-dvi' => 'dvi',
			'message/rfc822' => 'eml',
			'application/x-msdownload' => 'exe',
			'video/x-f4v' => 'f4v',
			'audio/x-flac' => 'flac',
			'video/x-flv' => 'flv',
			'image/gif' => 'gif',
			'application/gpg-keys' => 'gpg',
			'application/x-gtar' => 'gtar',
			'application/x-gzip' => 'gzip',
			'application/mac-binhex40' => 'hqx',
			'application/mac-binhex' => 'hqx',
			'application/x-binhex40' => 'hqx',
			'application/x-mac-binhex40' => 'hqx',
			'text/html' => 'html',
			'image/x-icon' => 'ico',
			'image/x-ico' => 'ico',
			'image/vnd.microsoft.icon' => 'ico',
			'text/calendar' => 'ics',
			'application/java-archive' => 'jar',
			'application/x-java-application' => 'jar',
			'application/x-jar' => 'jar',
			'image/jp2' => 'jp2',
			'video/mj2' => 'jp2',
			'image/jpx' => 'jp2',
			'image/jpm' => 'jp2',
			// next two changed from jpeg extension to jpg RGH
			'image/jpeg' => 'jpg',
			'image/pjpeg' => 'jpeg',
			'application/x-javascript' => 'js',
			'application/json' => 'json',
			'text/json' => 'json',
			'application/vnd.google-earth.kml+xml' => 'kml',
			'application/vnd.google-earth.kmz' => 'kmz',
			'text/x-log' => 'log',
			'audio/x-m4a' => 'm4a',
			'application/vnd.mpegurl' => 'm4u',
			'audio/midi' => 'mid',
			'application/vnd.mif' => 'mif',
			'video/quicktime' => 'mov',
			'video/x-sgi-movie' => 'movie',
			'audio/mpeg' => 'mp3',
			'audio/mpg' => 'mp3',
			'audio/mpeg3' => 'mp3',
			'audio/mp3' => 'mp3',
			'video/mp4' => 'mp4',
			'video/mpeg' => 'mpeg',
			'application/oda' => 'oda',
			'audio/ogg' => 'ogg',
			'video/ogg' => 'ogg',
			'application/ogg' => 'ogg',
			'application/x-pkcs10' => 'p10',
			'application/pkcs10' => 'p10',
			'application/x-pkcs12' => 'p12',
			'application/x-pkcs7-signature' => 'p7a',
			'application/pkcs7-mime' => 'p7c',
			'application/x-pkcs7-mime' => 'p7c',
			'application/x-pkcs7-certreqresp' => 'p7r',
			'application/pkcs7-signature' => 'p7s',
			'application/pdf' => 'pdf',
			'application/octet-stream' => 'pdf',
			'application/x-x509-user-cert' => 'pem',
			'application/x-pem-file' => 'pem',
			'application/pgp' => 'pgp',
			'application/x-httpd-php' => 'php',
			'application/php' => 'php',
			'application/x-php' => 'php',
			'text/php' => 'php',
			'text/x-php' => 'php',
			'application/x-httpd-php-source' => 'php',
			'image/png' => 'png',
			'image/x-png' => 'png',
			'application/powerpoint' => 'ppt',
			'application/vnd.ms-powerpoint' => 'ppt',
			'application/vnd.ms-office' => 'ppt',
			'application/msword' => 'doc',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
			'application/x-photoshop' => 'psd',
			'image/vnd.adobe.photoshop' => 'psd',
			'audio/x-realaudio' => 'ra',
			'audio/x-pn-realaudio' => 'ram',
			'application/x-rar' => 'rar',
			'application/rar' => 'rar',
			'application/x-rar-compressed' => 'rar',
			'audio/x-pn-realaudio-plugin' => 'rpm',
			'application/x-pkcs7' => 'rsa',
			'text/rtf' => 'rtf',
			'text/richtext' => 'rtx',
			'video/vnd.rn-realvideo' => 'rv',
			'application/x-stuffit' => 'sit',
			'application/smil' => 'smil',
			'text/srt' => 'srt',
			'image/svg+xml' => 'svg',
			'application/x-shockwave-flash' => 'swf',
			'application/x-tar' => 'tar',
			'application/x-gzip-compressed' => 'tgz',
			'image/tiff' => 'tiff',
			'text/plain' => 'txt',
			'text/x-vcard' => 'vcf',
			'application/videolan' => 'vlc',
			'text/vtt' => 'vtt',
			'audio/x-wav' => 'wav',
			'audio/wave' => 'wav',
			'audio/wav' => 'wav',
			'application/wbxml' => 'wbxml',
			'video/webm' => 'webm',
			'audio/x-ms-wma' => 'wma',
			'application/wmlc' => 'wmlc',
			'video/x-ms-wmv' => 'wmv',
			'video/x-ms-asf' => 'wmv',
			'application/xhtml+xml' => 'xhtml',
			'application/excel' => 'xl',
			'application/msexcel' => 'xls',
			'application/x-msexcel' => 'xls',
			'application/x-ms-excel' => 'xls',
			'application/x-excel' => 'xls',
			'application/x-dos_ms_excel' => 'xls',
			'application/xls' => 'xls',
			'application/x-xls' => 'xls',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
			'application/vnd.ms-excel' => 'xlsx',
			'application/xml' => 'xml',
			'text/xml' => 'xml',
			'text/xsl' => 'xsl',
			'application/xspf+xml' => 'xspf',
			'application/x-compress' => 'z',
			'application/x-zip' => 'zip',
			'application/zip' => 'zip',
			'application/x-zip-compressed' => 'zip',
			'application/s-compressed' => 'zip',
			'multipart/x-zip' => 'zip',
			'text/x-scriptzsh' => 'zsh',
		);
		// look at file-extension to get the mime-type?
		if ($mime_type == "*") {return $mime_map;} // to get the array returned
		return $mime_map[$mime_type];
	}

	// --------------------------------------------------------------------------
	// for debugging array contents
	// --------------------------------------------------------------------------
	function fst_print_array($the_array = array()) {
		echo "<div style='color:black;background-color:white;width=90%;display:block;'>";
		echo "<pre> ";
		print_r($the_array);
		echo "</pre> ";
		echo "</div>";
		return;
	}

	// --------------------------------------------------------------------------
	// we use this for debugging - shows info in formatted output
	// --------------------------------------------------------------------------
	function fst_show_globals() {
		echo "<table border='1'><tr><td>GET</td><td>POST</td><td>FILES</td><td>REQUEST</td><td>SESSION</td></tr>";
		echo "<tr><td><pre>";
		print_r($_GET);
		echo "</pre></td>";
		echo "<td><pre>";
		print_r($_POST);
		echo "</pre></td>";
		echo "<td><pre>";
		print_r($_FILES);
		echo "</pre></td>";
		echo "<td><pre>";
		print_r($_REQUEST);
		echo "</pre></td>";
		echo "<td><pre>";
		print_r($_SESSION);
		echo "</pre></td></tr></table>";
		// for server variables
		// echo '<table border="1">';
		//foreach ($_SERVER as $k => $v){
		// echo "<tr><td>" . $k ."</td><td>" . $v . "</td></tr>";
		//}
		//
		//echo "</table>" ;
		return;
	}

	// --------------------------------------------------------------------------
	// write to debug log file, the 'logs' folder must exist or it will be created in the sanity check
	// --------------------------------------------------------------------------
	function fst_write_debug_log($logmsg = "") {
		// Write the contents to the file,
		// using the FILE_APPEND flag to append the content to the end of the file
		// and the LOCK_EX flag to prevent anyone else writing to the file at the same time
		if ((!FST_XWRITELOG) or (!$logmsg)) {return;} // bypass if option is not set or no message
		// check for log file folder exists, create it if needed
		$log_folder = getcwd() . "/logs/";
		if (!is_dir($log_folder)) {
			if (!mkdir($log_folder, 0777, true)) {
				return;
			}
		}
		// get ready to write the data in the folder (that is ensured to exist)
		$now     = date('Y-m-d H:i:s');
		$nowfile = getcwd() . "/logs/" . date('Y-m-d') . ".log";
		$msg     = str_repeat('-', 80) . PHP_EOL . $now . PHP_EOL . $logmsg;
		file_put_contents($nowfile, $msg . PHP_EOL, FILE_APPEND | LOCK_EX);
		return;
	}

	// --------------------------------------------------------------------------
	// end of the formspammertrap-contact-functions.php
	// --------------------------------------------------------------------------


	/* ----------------------------------------------------
	check for bots, die if found
	 */
	function fst_bot_check() {
		// check for bots
		// check for submit value is correct (maybe redundant)
		if ((isset($_POST["submitform"]) ?? null) === 'FormSpammerTrap') {
			$bot_msg = FST_XWRITELOG ? "Submit Form Invalid" : "201";
			fst_go_away($bot_msg);die();}

		// check for session->formspammertrap
		if (!isset($_SESSION['formspammertrap'])) {
			$bot_msg = FST_XWRITELOG ? "Session FST Invalid" : "202";
			fst_go_away($bot_msg);die();}

		// this next field should have been changed when valid form submitted
		if (!($_POST['fst_idnumber'] ?? 'human')) {
			// didn't change that hidden field, so "die, spambot!"
			$bot_msg = FST_XWRITELOG ? "Human Not Found" : "203";
			fst_go_away($bot_msg);die();}
		return;
	}

	// new since version 17
	function fst_process_form() {
		fst_bot_check(); // check for bots, die if found
		$mail_message = "";
		$after_submit = fst_validate_form(); // will contain array if valid, otherwise false indicate error on form
		if (!is_array($after_submit)) {return false;} // invalid form, so no processing - return
		// load phpmailer if enabled

		// Platform-aware PHPMailer loading - prevents class conflicts
		if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
			// Try WordPress/ClassicPress PHPMailer first
			if (defined('ABSPATH') && file_exists(ABSPATH . WPINC . '/PHPMailer/PHPMailer.php')) {
				// WordPress/ClassicPress has PHPMailer - use it
				require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
				require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
				require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
			} else {
				// Fallback to plugin's bundled PHPMailer
				require_once __DIR__ . '/phpmailer/Exception.php';
				require_once __DIR__ . '/phpmailer/PHPMailer.php';
				require_once __DIR__ . '/phpmailer/SMTP.php';
			}
		}

		// at this point, the form has been submitted and all form info / bot checking is OK

		// build the various values from the form and defaults
		$uid                    = md5(uniqid(time())); // define boundary with a md5 hashed value

		$after_submit['headers']      = fst_build_mailheader($after_submit);
		$after_submit                 = fst_build_message($after_submit); // build the mail message
		$morefields = $after_submit['more_fields'];

		// build the rest of the after_submit array

		if (function_exists('FST_CUSTOM_AFTER_SUBMIT')) {
			$after_submit['raw_from_name']   = $after_submit['your_name'];
			$after_submit['raw_from_email']  = $after_submit['your_email'];
			$after_submit['raw_subject']     = $after_submit['your_subject'];
			$after_submit['raw_message']     = $after_submit['your_message'];
			$after_submit['raw_more_fields'] = $after_submit['more_fields'];
		}

		if (FST_XCUSTOM_FIELDS and is_array($morefields)) { // add extra fields so that FST_CUSTOM_AFTER_SUBMIT can process them if needed
			// fill in an empty subject for the message
			if (!$after_submit['subject']) {$after_submit['subject'] = "Contact Message";}
			// $morefields is the array of added fields
			$newarray     = $after_submit + $morefields; // adds together into new one
			$after_submit = $newarray;
			}
		if (function_exists('FST_CUSTOM_AFTER_SUBMIT')) {
			FST_CUSTOM_AFTER_SUBMIT($after_submit);
 		}
		// save name/email into database (version 9.00+)
		// put status results into the message
		if ((FST_SAVE_CONTACT_INFO) and (isset($_POST['fst_save_contact']))) {
			$xdb_save_message = fst_contact_db_save($after_submit['your_name'], $after_submit['your_email']); // stores into db
			if (strlen($xdb_save_message)) {
				$mail_message .= $xdb_save_message; // add contact db success/fail message
			}
			$extra = array();
			if ((is_array(FST_XCUSTOM_FIELDS)) and (FST_XSHOW_SUBMIT)) {
				foreach (FST_XCUSTOM_FIELDS as $extra) {
					$xname = ($extra["NAME"]) ? $extra["NAME"] : "";
					$xvalue = ($xname) ? $xname : "" ;
					$xlabel = ($extra["LABELMSG"]) ? $extra["LABELMSG"] : "";
					$mail_message .= "$xlabel : $xvalue <br>";
				}
			}
		} // end of savemail/savecontact

		if (FST_XSHOW_SERVER) { // show SERVER values if enabled
			$mail_message .= '<hr>All $_SERVER values<hr>';
			foreach ($_SERVER as $key => $value) {
				$mail_message .= " '$key' = '$value' <br>";
			}
			$mail_message .= "<hr>";
		}

		$after_submit['your_message'] .= $mail_message;

	 // and change message back to html if FST_XSEND_HTML = false becase a file needs to be attached to a html message
		if (!FST_XSEND_HTML) {
			$after_submit['your_message'] = preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $after_submit['your_message']);
		}

		// at this point, the message should include everything plus the file content
		// including to/subject/message/headers
		// check for alternate mail process
		// send the message out
		if (function_exists("FST_MAIL_ALT")) {
			$mailstatus = FST_MAIL_ALT($after_submit);  // use alt mail process from form
		} else
		{
			if (!FST_NO_MAIL) {
				$mailstatus = fst_send_mail_phpmailer($after_submit);
			 }
		}

		// if FST_POP_THANKS
		// output FST_POP_THANKS_MESSAGE via script
		// redirect to the Thanks page, if specified in customization
		// if WP enviroment, use wp_redirect
		if (FST_XTHANKS_URL) {
			fst_redirect_thanks();
		} else {
			echo '<hr><div class= "fst_blue_box fst_text_center" >' . FST_XTHANKS_MESSAGE;
			echo '</div><hr>';
		}

		// if FST_THANKS_URL, redirect to that URL via fst_redirect_thanks(), which should
		// if not WP envirment
		if (FST_POP_THANKS) {
			fst_pop_thanks();
		}
		// cleanup
		// clear all form fields
		?>
    <script>
            // Get all form elements by their name and clear their values
            document.getElementById("formspammertrapcontactform").reset();
    </script>
	<?php

		// stop back button via fst_stop_backpage
		// next script prevents resubmit by clearing the history for the form
		fst_stop_backpage();
		// set form display false via return value?
		$show_form = false; // success - don't show the form

		// end of sending function
		return;
	}

	// --------------------------------------------------------------------------
	// validate form fields as needed
	/* returns
	$after_submit['your_name']
	$after_submit['your_email']
	$after_submit['your_subject']
	$after_submit['your_message']
	$after_submit['from_email']
	 */
	// --------------------------------------------------------------------------
	function fst_validate_form() {
		//fst_show_globals();
		$missing_info = "";
		$after_submit = array(); // will hold valid values if no errors
		// --- Normalize WP/CP "magic quotes" so values don't contain backslashes ---
if (function_exists('wp_unslash')) {
    $_POST = wp_unslash($_POST);  // WordPress / ClassicPress way
} else {
    // Fallback for non-WordPress environments
    $fst_unslash_deep = function ($v) use (&$fst_unslash_deep) {
        if (is_array($v)) return array_map($fst_unslash_deep, $v);
        return is_string($v) ? stripslashes($v) : $v;
    };
    $_POST = $fst_unslash_deep($_POST);
}

// put all form fields into the $after_submit array
// Initialize array to store field name-value pairs
$after_submit = fst_sanitize_field();

		// Loop through required fields (since version 17.10)
		// allows for custom required fields
foreach (FST_REQUIRED_FIELDS as $field) {
    // Check if field is set and not empty using ternary operator
    isset($_POST[$field]) && !empty($_POST[$field]) ? $after_submit[$field] = trim($_POST[$field]) : $missing_info .= "Please enter required information: $field.<br>";
}


	// check for missing name
		if (isset($_POST['your_name'])) {$after_submit['your_name'] = trim($_POST["your_name"]);} else { $missing_info .= "Please enter your name.";}
		// Check for URLs in name field
		if (isset($_POST['your_name']) && !empty(trim($_POST['your_name']))) {
			$name_urls = fst_count_urls(trim($_POST['your_name']));
			if ($name_urls > FST_XURLS_ALLOWED) {
				$missing_info .= "URLs not allowed in name field. Found: " . $name_urls . " URLs.<br>";
			}
		}
		// check for missing email
		if (isset($_POST['your_email'])) {$after_submit['your_email'] = trim($_POST["your_email"]);} else { $missing_info .= "Please enter your email address so we can contact you if needed.";}
		// check for missing subject
		if (isset($_POST['your_subject'])) {$after_submit['your_subject'] = trim($_POST["your_subject"]);} else { $missing_info .= "Please enter a Subject for your message.";}
		// Check for URLs in subject field
		if (isset($_POST['your_subject']) && !empty(trim($_POST['your_subject']))) {
			$subject_urls = fst_count_urls(trim($_POST['your_subject']));
			if ($subject_urls > FST_XURLS_ALLOWED) {
				$missing_info .= "URLs not allowed in subject field. Found: " . $subject_urls . " URLs.<br>";
			}
		}

	// check for empty message
		if (isset($_POST['message'])) {
			$xyourcomment                 = trim($_POST["message"]);
			$after_submit['your_message'] = $xyourcomment;
		} else { $xyourcomment = "";
			$missing_info .= "Please enter your message";}
		// check for max urls in message
		$urls_found = fst_count_urls($xyourcomment);
		if ($urls_found > FST_XURLS_ALLOWED) {
			$missing_info .= "Too many URLs in your message; only " . FST_XURLS_ALLOWED . " are allowed.<br>";
		}

		// check any other required fields (although in-line validation should prevent that
		$missing_info .= fst_check_fields($missing_info);

		// check for FST_CUSTOM_CHECK function and process, look for return of 'true' for all OK ???
		// additional field checking if custom function is loaded
		if (function_exists('FST_CUSTOM_CHECK')) {
			$custom_message = FST_CUSTOM_CHECK();
			foreach ($custom_message as $item) {
				$missing_info .= $item . "<br>";
			}
		}

		// build a valid email from the domain; email address doesn't need to exist, just to ensure that sender is in the same domain to ensure mail not sensed as spam
		if (!FST_XEMAIL_ON_DOMAIN) {
			$domain = (isset($_SERVER['HTTP_HOST'])) ? fst_get_domain($_SERVER['HTTP_HOST']) : "";
			if (!$domain) {
				$missing_info .= "Incorrect or missing domain name in the FST_XEMAIL_ON_DOMAIN value.";
			}
			$after_submit['from_email'] = "noreply@" . $domain;
		} else {
			$after_submit['from_email'] = FST_XEMAIL_ON_DOMAIN;}
		// any other checking

		// need to create the more_fields if defined, otherwise blank
		$after_submit['more_fields'] = "";

		// if $missing_info contains anything, then show the mssage and return
		// display any errors
		if ($missing_info) { // call the fst_delaytheclick() JS function to allow a resubmit to not get trapped if they don't fix a required field that has the fst_delaytheclick() function call
			fst_delay_the_click_for_errors();

			echo "<div align='center' class='fst_red_box'>";
			echo "<div align='center'><strong>" . FST_LANG_TEXT['incomplete_message'] . "(1)</strong></div><hr>";
			echo $missing_info;
			echo "</div><br>";
			return false;
		}
		return $after_submit; // form data is valid, so return true
	}

	// --------------------------------------------------------------------------
// sanitize each type of field according to field type (since version 17.10)
 function fst_sanitize_field() {
	 // Initialize array to store field name-value pairs
	$after_submit = array();

	// Loop through all fields in the form
	foreach ($_POST as $key => $value) {
	    // Sanitize input based on field type
	    switch ($key) {
	        case 'text':
	        case 'textarea':
	        case 'email':
	        case 'password':
	        case 'date':
	        case 'datetime-local':
	        case 'number':
	        case 'range':
	        case 'search':
	        case 'tel':
	        case 'url':
	        case 'hidden':
	 		case 'color':
	            // For text-like fields, sanitize HTML and script commands
	            $sanitized_value = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
	            break;
	        case 'checkbox':
	        case 'radio':                     ;

	            // For checkbox and radio fields, sanitize as integer (0 or 1)
	            $sanitized_value = $value ? 1 : 0;
	            break;
	        case 'file':
	            // For file uploads, no specific sanitization needed
	            // You may want to handle file uploads differently (e.g., move uploaded files to a secure location)
	            $sanitized_value = $value;
	            break;
	        default:
	            // For other field types, no specific sanitization needed
	            $sanitized_value = $value;
	            break;
	    }

	    // Assign field name and sanitized value to array
	    $after_submit[$key] = $sanitized_value;
	}


	return $after_submit;
}
	// --------------------------------------------------------------------------
	function fst_build_message($after_submit) {
		// --- Final cleanup for display: remove any backslashes before quotes/parentheses ---
foreach (['your_name', 'your_email', 'your_subject', 'your_message'] as $k) {
    if (isset($after_submit[$k]) && is_string($after_submit[$k])) {
        // remove backslashes only when they precede quotes or parentheses
        $after_submit[$k] = preg_replace('/\\\\([\'"()])/', '$1', $after_submit[$k]);
    }
}
		// build the mail message text, start with message from form
		$mail_message = "<b>From:</b> " . $after_submit['your_name'] . ", <br><b>Sender Email :</b> " . $after_submit['your_email'] . "<br><br><b>Sender Message </b><br>" . $after_submit['your_message'] . "<br>";
		// add 'content of all form fields' using FST_XCUSTOM_FIELDS
		$morefields = array(); // used to hold field names array used later

		// add all submit fields if FST_SHOW_SUBMIT true
		if (FST_XSHOW_SUBMIT OR FST_XCAPTURE) { // show POST values if enabled
			$mail_message .= "<hr>Form POST values<hr>";
			foreach ($_POST as $key => $value) {
				$mail_message .= " '$key' = '$value' <br>";
			}
			$mail_message .= "<hr>";
		}

		// add server array if FST_XSHOW_SERVER true
		if (FST_XSHOW_SERVER) { // show SERVER values if enabled
			$mail_message .= '<hr>All $_SERVER values<hr>';
			foreach ($_SERVER as $key => $value) {
				$mail_message .= " '$key' = '$value' <br>";
			}
			$mail_message .= "<hr>";
		}
		$after_submit['your_message'] = $mail_message;

		// set a your_subject value if it doesn't exist in the form  (since version 17.10)
		$after_submit['your_subject'] = isset($_POST['your_subject']) ? FST_XEMAIL_SUBJECT . $_POST['your_subject'] : FST_XEMAIL_SUBJECT;

		return $after_submit;
	}

	// end of fst_build_mail_message
	// --------------------------------------------------------------------------


	// --------------------------------------------------------------------------
	function fst_build_mailheader($after_submit) {
		// build the basic mail parts header parts as needed
		$mailheader           = array();
		$mailheader["From"]   = $after_submit['from_email']; // a valid email for your domain
		$mailheader["Sender"] = $after_submit['from_email']; // a valid email for your domain
		// build header  CC email if defined
		if (defined("FST_XCC_EMAIL")) {
			$mailheader["Cc"] = FST_XCC_EMAIL; // note case of 'Cc', per specs
		}
		// build header BCC email if defined
		if (defined("FST_XBCC_EMAIL")) {
			$mailheader["Bcc"] = FST_XBCC_EMAIL; // note case of 'Bcc', per specs
		}
		// build header reply to form email
		// override via FST_REPLY_TO_OVERRIDE ????
		if (FST_REPLY_TO_OVERRIDE) {
			$mailheader["Reply-To"] = FST_REPLY_TO_NAME . "<" . FST_REPLY_TO_EMAIL . ">";} else {
			$mailheader["Reply-To"] = $after_submit['your_email'];
		}
		// set header return path to RST_FROM_EMAIL
		$mailheader["Return-Path"] = FST_FROM_EMAIL; // Return path for errors  (since 16.0)
		// set header -x-sender to FST_FROM_EMAIL
		$mailheader["X-Sender"] = FST_FROM_EMAIL; // since 16.0
		// set header x-mailer to php version
		$mailheader["X-Mailer"] = 'PHP ' . phpversion(); // since 16.0

		// set content type of message (always HTML?)
		$mailheader["MIME-Version"] = "1.0";
		$mailheader["Content-Type"] = "text/html; charset=iso-8859-1";
		// not needed, already define
		// $mailheader['boundary']     = $after_submit['boundary'];
		/*   $mailheader['headers']['Content-Type'] = array(
		'Content-Type' => 'multipart/mixed',
		'boundary' => $uid* );                       */
		//   fst_print_array($mailheader['headers']); die("mailheader at 3887");
		return $mailheader;
	}


	// --------------------------------------------------------------------------
	function fst_pop_thanks() {
	?>
    <script>
    window.confirm("<?php echo FST_POP_THANKS_MESSAGE; ?>");
    </script>
    <?php
    	return;
    	}

    	// --------------------------------------------------------------------------

    	function fst_send_mail_phpmailer($after_submit = array()) {
    		/*
    		Sends mail using phpmailer
    		- x
    		- checks for phpmailer class already installed, otherwise an external request
    		- checks for sending via SMTP; contants must exist
    		- SMTP_USER
    		- SMTP_PASS
    		- SMTP_PORT
    		- SMTP_SECURE
    		- if constants do not exist, default to non-SMTP
    		- checks for file uploaded; adds to mail if exists
    		- uses flags to save/delete uploaded file
    		- SEND_TO_EMAIL         = is the FST_EMAIL_ON_DOMAIN
    		- SEND_TO_NAME          = name for email
    		- REPLY_TO_EMAIL        = email on form (your_email)
    		- REPLY_TO_NAME         = name from from (your_name)
    		- SUBJECT               = subject from form
    		- MESSAGE               - message from form
    		- MESSAGE_MORE          = any other data to add to message
    		-

    		Returns true (email sent OK) or false (email not sent or data error)

    		 */
    		/*  Settings for using PHPMailer - since version 17.00 . Note that you should set values in your form's FST_MORE_FIELDS() function (don't forget to GLOBAL them)
    		- note that SMTP is disabled by default; if used, you must also provide SMTP user/pass/port values. Those values are not validated. Test SMTP useage prior to production.

    		$FST_USE_PHPMAILER = true;  // enabled by default; set to false to use PHP mail() function (although if false, you must supply an FST_MAIL_ALT() function in your form to send mail.
    		$FST_SMTP_ENABLE        = false;    // enable if using SMTP capabilities in PHPMailer
    		$FST_SMTP_USER          = NULL;     // for SMTP user name; not validated (filtered with htmlspecialchars, so watch for possible filtered characters)
    		$FST_SMTP_PASS          = NULL;     // for SMTP password; not validated (filtered with htmlspecialchars, so watch for possible filtered characters)
    		$FST_SMTP_PORT          = NULL;     // for SMTP port number; not validated
    		$FST_SMTP_SECURE        = NULL;     // for SMPT Secure setting; not validated


    		 */

    		if (!count($after_submit)) {return false;} // no data to send

    		// Create a new PHPMailer instance or fallback to mail() function
			$mail = false;
    		if ((FST_USE_PHPMAILER) AND (class_exists('PHPMailer\PHPMailer\PHPMailer'))){
				// using try/catch in case class doesn't exist
				try {
				    $mail = new PHPMailer\PHPMailer\PHPMailer();
				} catch (Exception $e) {
				    $mail = false;
				}
			}
    		// Check if the instance creation failed
    		if (! $mail ) {
    			// Fallback to mail() function
    			$to      = FST_FROM_EMAIL;
    			$subject = $after_submit['your_subject'];
    			$message = $after_submit['your_message'];
				$message .= PHP_EOL . "(Note that PHPMailer not active, so message may be incomplete. Please read FormSpammerTrap documentation to properly enable PHPMailer so that your messages are properly sent. You can bypass PHPMailer use if your form has an FST_MAIL_ALT() function to send messages.)" . PHP_EOL;

    			// Set additional headers if needed
    			$headers = 'From: ' . FST_FROM_EMAIL . "\r\n" .
    			'Reply-To: ' . FST_REPLY_TO_EMAIL . "\r\n" .
    			'X-Mailer: PHP/' . phpversion();

    			// Send email using mail() function
    			if (mail($to, $subject, $message, $headers)) {
    				echo 'Email sent successfully using mail() function';
					return true;
    			} else {
    				echo 'Error: Unable to send email using mail() function';
					return false;
    			}

    			// Exit the function
    			exit;
    		}
	  //		echo "PHP Mailer Active <br>";
    		// Check if SMTP should be used
    		if (FST_SMTP_ENABLE) {
    			// Your SMTP configuration
    			$mail->isSMTP();
    			$mail->Host       = FST_SMTP_HOST;
    			$mail->SMTPAuth   = FST_SMTP_AUTH;
    			$mail->Username   = FST_SMTP_USER;
    			$mail->Password   = FST_SMTP_PASS;
    			$mail->SMTPSecure = FST_SMTP_SECURE;
    			$mail->Port       = FST_SMTP_PORT;
    		}

    		// Set the sender and recipient
    		$mail->setFrom(FST_FROM_EMAIL, FST_FROM_NAME);

			// recipient $FST_XEMAIL_ON_DOMAIN
    		$mail->addAddress(FST_XEMAIL_ON_DOMAIN);
    		$mail->addAddress(FST_FROM_EMAIL, FST_FROM_NAME);
			$mail->addReplyTo($after_submit['your_email'], $after_submit['your_name']);

			// Add CC recipients if specified
if (FST_XCC_EMAIL && !empty(FST_XCC_EMAIL)) {
    $cc_emails = explode(',', FST_XCC_EMAIL);
    foreach ($cc_emails as $cc_email) {
        $cc_email = trim($cc_email);
        if (filter_var($cc_email, FILTER_VALIDATE_EMAIL)) {
            $mail->addCC($cc_email);
        }
    }
}

// Add BCC recipients if specified  
if (FST_XBCC_EMAIL && !empty(FST_XBCC_EMAIL)) {
    $bcc_emails = explode(',', FST_XBCC_EMAIL);
    foreach ($bcc_emails as $bcc_email) {
        $bcc_email = trim($bcc_email);
        if (filter_var($bcc_email, FILTER_VALIDATE_EMAIL)) {
            $mail->addBCC($bcc_email);
        }
    }
}

// add text alternate
    		// Set email content
    		$mail->Subject = $after_submit['your_subject'];
    		$mail->Body    =  $after_submit['your_message'];
			 $mail->isHTML(true);

			 //Replace the plain text body with one created manually
			$mail->AltBody = 'This is a plain-text message body';


    		// Check if a file is uploaded
			$status_msg = "";	// set up empty one for later
    		if (isset($_FILES['fst_uploadfile']['tmp_name']) ) {
				fst_attach_multiple_files($mail) ;    // since version 17 - to allow multiple files attached
			 }
//fst_show_globals();
//fst_print_array($mail);
// fst_print_array($after_submit);
//die("4361");

    		// Send the email
    		if ($mail->send()) {
				if (FST_XWRITELOG) {
				  fst_write_log_file("Mail sent OK - from " . $after_submit['your_email']);
				}
				return true;

    		} else {
    				$status= 'Error: ' . $mail->ErrorInfo;
				if (FST_XWRITELOG) {
				  fst_write_log_file("Mail failed - from " .  $after_submit['your_email']  . " , $status ");
				}
					return false;
    		}
    		return;
    	}

	function fst_attach_multiple_files($mail) {
	 //Attach multiple files one by one
	 $filecount = count($_FILES['fst_uploadfile']['name']);
	 $status_msg = "";
	// Ensure that the fst_uploadfile index is set and it's an array
    // Loop through each uploaded file
    for ($ct = 0; $ct < count($_FILES['fst_uploadfile']['tmp_name']); $ct++) {
        // Extract an extension from the provided filename
        $ext = pathinfo($_FILES['fst_uploadfile']['name'][$ct], PATHINFO_EXTENSION);
        // Define a safe location to move the uploaded file to, preserving the extension
        $filename = $_FILES['fst_uploadfile']['name'][$ct];
        $uploadfile = fst_new_file_name(FST_UPLOAD_FOLDER, $filename);
		if (!FST_UPLOADS_DELETE) { // delete after upload not set, so save the file
			if (move_uploaded_file($_FILES['fst_uploadfile']['tmp_name'][$ct], $uploadfile)) {
				$status_msg .= FST_LANG_TEXT['file_upload_ok'] . "<br>";
			} else {
				$status_msg .= FST_LANG_TEXT['file_upload_error'] . "<br>";
			}
		}
        if (!$mail->addAttachment($uploadfile, $filename)) {
            $status_msg .= 'Failed to attach file ' . $filename;
        }
    }
	$mail->Body .= $status_msg;
	return $mail;
	}
    // --------------------------------------------------------------------------
    // --------------------------------------------------------------------------
	// END of formspammertrap-contact-functions.php
    // --------------------------------------------------------------------------


