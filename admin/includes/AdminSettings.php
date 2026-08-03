<?php
/*
**********************************************************************
*  AdminSettings.php
* 
*   This file contains the Settings for the Admininstrative application
Date        Change
-------------------------------------------------------------
2016-01-05  Added Admin CSS directory
**********************************************************************
*/

// APPLICATION CONSTANTS
 define("ADMIN_IMG_DIR", ADMIN_DIR . "/images");
 define("ADMIN_JS_DIR", ADMIN_DIR . "/javascript");
 define("ADMIN_INCLUDE_DIR", ADMIN_DIR . "/includes");
 define("ADMIN_CSS_DIR", ADMIN_DIR . "/css");

//FORM MODE CONSTANTS 
 define("FORM_MODE_NEW",0);
 define("FORM_MODE_EDIT",1);
 define("FORM_MODE_SELECT",2);
 define("FORM_MODE_EDIT_NO_DELETE",3);
 define("FORM_MODE_SEARCH",4);

 //Message Constants
 define("ERROR_SEVERITY_NONE",0);
 define("ERROR_SEVERITY_INFO",1);
 define("ERROR_SEVERITY_WARNING",2);
 define("ERROR_SEVERITY_ERROR",3);

 //Style Constants
 define("RESULT_STYLE_CLASS","result");
 define("RESULT_STYLE_CLASS_ALT","altresult");
 define("REPORT_STYLE_CLASS","reportrow");
 define("REPORT_STYLE_CLASS_ALT","altreportrow");

//Image Constants
 define("NO_IMAGE","NoImage.gif");
 define("IMAGE_NOT_FOUND","ImageNotFound.gif");
 define("INVALID_IMAGE","InvalidImage.gif");

//DB Constants
 define("CATEGORY_PERFORMANCE",2);
 define("REVENUE_TYPE_CD_SALE",3);
 define("REVENUE_TYPE_PERFORMANCE_FEE",4);
 define("COLORAD_SESSIONS_CAT_ID",22);

 //ERROR CONSTANTS

 //MISC CONSTANTS
 define("START_YEAR",2003);

//ACTIVE NAV MENU ITEM
define("ADMIN_HOME_ACTIVE","home"); 
define("PRODUCTS_ACTIVE","products"); 
define("PERFORMANCES_ACTIVE","performances"); 
define("FINANCES_ACTIVE","finances"); 
define("DISCOGRAPHY_ACTIVE","discography"); 
define("REPORTS_ACTIVE","reports"); 
define("MISC_ACTIVE","misc"); 


 //MESSAGE TYPES
 define("MESSAGE_TYPE_INFO",0);
 define("MESSAGE_TYPE_WARNING",1);
 define("MESSAGE_TYPE_ERROR",2);

// Require admin login for every page that includes AdminSettings,
// unless the page opts out with define('ADMIN_AUTH_SKIP', true).
if (!defined('ADMIN_AUTH_SKIP') || !ADMIN_AUTH_SKIP) {
	include_once(ADMIN_INCLUDE_DIR . '/requireAuth.php');
}

?>
