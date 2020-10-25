<?php

// $Id: modinfo.php,v 1.1 2006/03/26 21:17:05 mikhail Exp $
// Module Info

// The name of this module
define('_MI_BIZDIR_NAME', 'Yellow Pages');

// A brief description of this module
define('_MI_BIZDIR_DESC', 'Creates a directory listing section where users can search/submit/rate various web sites.');

// Names of blocks for this module (Not all module has blocks)
define('_MI_BIZDIR_BNAME1', 'Recent Listings');
define('_MI_BIZDIR_BNAME2', 'Top Listings');

// Sub menu titles
define('_MI_BIZDIR_SMNAME1', 'Submit');
define('_MI_BIZDIR_SMNAME2', 'Popular');
define('_MI_BIZDIR_SMNAME3', 'Top Rated');

// Names of admin menu items
define('_MI_BIZDIR_ADMENU2', 'Add/Edit Listing');
define('_MI_BIZDIR_ADMENU3', 'Submitted Listings');
define('_MI_BIZDIR_ADMENU4', 'Broken Links');
define('_MI_BIZDIR_ADMENU5', 'Modified Listing');
define('_MI_BIZDIR_ADMENU6', 'Link Checker');

// Title of config items
define('_MI_BIZDIR_POPULAR', 'Select the number of hits for links to be marked as popular');
define('_MI_BIZDIR_NEWLINKS', 'Select the maximum number of new listings displayed on top page');
define('_MI_BIZDIR_PERPAGE', 'Select the maximum number of listings displayed in each page');
define('_MI_BIZDIR_USESHOTS', 'Select yes to display screenshot images for each listing');
define('_MI_BIZDIR_USEFRAMES', 'Would you like to display the linked page withing a frame?');
define('_MI_BIZDIR_SHOTWIDTH', 'Maximum allowed width of each screenshot image');
define('_MI_BIZDIR_ANONPOST', 'Allow anonymous users to post business listings?');
define('_MI_BIZDIR_AUTOAPPROVE', 'Auto approve new listings without admin intervention?');
define('_MI_BIZDIR_DEFAULT_COUNTRY', 'Default Country');
define('_MI_BIZDIR_DEFAULT_PROVINCE', 'Default State or Province');
define('_MI_BIZDIR_DEFAULT_AREA_CODE', 'Default Area Code');
define('_MI_BIZDIR_PREFILL_DEFAULTS', 'Prefill Default Values');

// Description of each config items
define('_MI_BIZDIR_POPULARDSC', '');
define('_MI_BIZDIR_NEWLINKSDSC', '');
define('_MI_BIZDIR_PERPAGEDSC', '');
define('_MI_BIZDIR_USEFRAMEDSC', '');
define('_MI_BIZDIR_USESHOTSDSC', '');
define('_MI_BIZDIR_SHOTWIDTHDSC', '');
define('_MI_BIZDIR_AUTOAPPROVEDSC', '');
define('_MI_BIZDIR_DEFAULT_COUNTRY_DSC', 'The default country used in searches and mapping.');
define('_MI_BIZDIR_DEFAULT_PROVINCE_DSC', 'The default state or province used in searches and mapping.');
define('_MI_BIZDIR_DEFAULT_AREA_CODE_DSC', 'The default telephone area code.');
define('_MI_BIZDIR_PREFILL_DEFAULTS_DSC', 'Prefill the default values when adding a new business?');

// Text for notifications

define('_MI_BIZDIR_GLOBAL_NOTIFY', 'Global');
define('_MI_BIZDIR_GLOBAL_NOTIFYDSC', 'Global business listing notification options.');

define('_MI_BIZDIR_CATEGORY_NOTIFY', 'Category');
define('_MI_BIZDIR_CATEGORY_NOTIFYDSC', 'Notification options that apply to the current business category.');

define('_MI_BIZDIR_LINK_NOTIFY', 'Listing');
define('_MI_BIZDIR_LINK_NOTIFYDSC', 'Notification options that aply to the current listing.');

define('_MI_BIZDIR_GLOBAL_NEWCATEGORY_NOTIFY', 'New Category');
define('_MI_BIZDIR_GLOBAL_NEWCATEGORY_NOTIFYCAP', 'Notify me when a new listing category is created.');
define('_MI_BIZDIR_GLOBAL_NEWCATEGORY_NOTIFYDSC', 'Receive notification when a new listing category is created.');
define('_MI_BIZDIR_GLOBAL_NEWCATEGORY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New business listing category');

define('_MI_BIZDIR_GLOBAL_LINKMODIFY_NOTIFY', 'Modify Listing Requested');
define('_MI_BIZDIR_GLOBAL_LINKMODIFY_NOTIFYCAP', 'Notify me of any listing modification requests.');
define('_MI_BIZDIR_GLOBAL_LINKMODIFY_NOTIFYDSC', 'Receive notification when any listing modification request is submitted.');
define('_MI_BIZDIR_GLOBAL_LINKMODIFY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Listing Modification Requested');

define('_MI_BIZDIR_GLOBAL_LINKBROKEN_NOTIFY', 'Broken Link Submitted');
define('_MI_BIZDIR_GLOBAL_LINKBROKEN_NOTIFYCAP', 'Notify me of any broken link report.');
define('_MI_BIZDIR_GLOBAL_LINKBROKEN_NOTIFYDSC', 'Receive notification when any broken link report is submitted.');
define('_MI_BIZDIR_GLOBAL_LINKBROKEN_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Broken Link Reported');

define('_MI_BIZDIR_GLOBAL_LINKSUBMIT_NOTIFY', 'New Listing Submitted');
define('_MI_BIZDIR_GLOBAL_LINKSUBMIT_NOTIFYCAP', 'Notify me when any new listing is submitted (awaiting approval).');
define('_MI_BIZDIR_GLOBAL_LINKSUBMIT_NOTIFYDSC', 'Receive notification when any new listing is submitted (awaiting approval).');
define('_MI_BIZDIR_GLOBAL_LINKSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New business listing submitted');

define('_MI_BIZDIR_GLOBAL_NEWLINK_NOTIFY', 'New Listing');
define('_MI_BIZDIR_GLOBAL_NEWLINK_NOTIFYCAP', 'Notify me when any new listing is posted.');
define('_MI_BIZDIR_GLOBAL_NEWLINK_NOTIFYDSC', 'Receive notification when any new listing is posted.');
define('_MI_BIZDIR_GLOBAL_NEWLINK_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New listing');

define('_MI_BIZDIR_CATEGORY_LINKSUBMIT_NOTIFY', 'New Listing Submitted');
define('_MI_BIZDIR_CATEGORY_LINKSUBMIT_NOTIFYCAP', 'Notify me when a new listing is submitted (awaiting approval) to the current category.');
define('_MI_BIZDIR_CATEGORY_LINKSUBMIT_NOTIFYDSC', 'Receive notification when a new link is submitted (awaiting approval) to the current category.');
define('_MI_BIZDIR_CATEGORY_LINKSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New link submitted in category');

define('_MI_BIZDIR_CATEGORY_NEWLINK_NOTIFY', 'New Listing');
define('_MI_BIZDIR_CATEGORY_NEWLINK_NOTIFYCAP', 'Notify me when a new listing is posted to the current category.');
define('_MI_BIZDIR_CATEGORY_NEWLINK_NOTIFYDSC', 'Receive notification when a new listing is posted to the current category.');
define('_MI_BIZDIR_CATEGORY_NEWLINK_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New business listing in category');

define('_MI_BIZDIR_LINK_APPROVE_NOTIFY', 'Listing Approved');
define('_MI_BIZDIR_LINK_APPROVE_NOTIFYCAP', 'Notify me when this listing is approved.');
define('_MI_BIZDIR_LINK_APPROVE_NOTIFYDSC', 'Receive notification when this listing is approved.');
define('_MI_BIZDIR_LINK_APPROVE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Listing approved');
