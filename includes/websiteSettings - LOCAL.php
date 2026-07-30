<?php
/*
*******************************************************************
WebsiteSettings.php (LOCAL VERSION)
This PHP file defines common values for the web site 
NOTES
Date        Change
-------------------------------------------------------------
2015-08-31  Changed hostname from IP address to name 
2015-10-18  Added Data values for Performance Category and CD Sale 
			Revenue Type
2015-12-18	Moved classes directory to common location
*******************************************************************
*/	
define("ENVIRONMENT", "LOCAL");

//Set timezone for Production Server (PHP 5.1)
date_default_timezone_set('America/Denver');

// Web Site Constants
 define("IMG_DIR",$ROOT . "/images");
 define("VIDEO_DIR",$ROOT . "/video");
 define("MP3_DIR",$ROOT . "/mp3");
 define("CSS_DIR",$ROOT . "/css");
 define("PDF_DIR",$ROOT . "/pdf");
 define("DOC_DIR",$ROOT . "/doc");
 define("XML_DIR",$ROOT . "/xml");
 define("JS_DIR",$ROOT . "/js");
 define("INCLUDE_DIR",$ROOT . "/includes"); 
 define("CLASS_DIR",$ROOT . "/../offthekitchen.com/classes");
 define("DATALAYER_DIR",$ROOT . "/../offthekitchen.com/datalayer");
 define("ADMIN_DIR",$ROOT . "/admin");
 define("LYRICS_DIR",$ROOT . "/lyrics");
 define("NEWS_DIR",$ROOT . "/news");
 define("SETTINGS_DIR",$ROOT . "/../settings");
 define("DROP_IMG_DIR","http://thurdy.com/images");

// Database Constants
// define("DB_HOST","127.0.0.1");
// define("DB_LOGIN","musician_db");
// define("DB_PASSWORD","musician_db");
// define("DB_NAME","musician_db");
 
  //Order By Values
  define("TRACK_ORDER","Track");
  define("UPDATE_ORDER","Update");
  define("NAME_ORDER","Name");
  define("DATE_ORDER","Date");
  define("RELEASE_ORDER","Release");

 define("DEFAULT_PAGE_TITLE","...for families who LOVE music!"); 

  //Data Values
  //Singles are not on the DB 
  define("DEFAULT_CD_ID",5);
  define("SINGLES_CD_ID",0);
  define("STEVE_WEEKS_FAMILY_ARTIST_ID",1);
  define("TRAVEL_BUGS_ARTIST_ID",2);
  define("THURDY_ARTIST_ID",3);

  //ID representing whatever performance is next
  define("NEXT_PERFORMANCE",0);

  //ID representing most recent News Items
  define("LATEST_NEWS_ITEM",0);
  
  //REVIEW TYPES
  define("REVIEW_TYPE_GENERAL",1);
  define("REVIEW_TYPE_PERFORMANCE",2);
  define("REVIEW_TYPE_CD",3);
  define("REVIEW_TYPE_SONG",4);
  define("DEFAULT_REVIEW_ID",3);
  
  //AWARDS 
  define("AWARD_TYPE_GENERAL",1);
  define("AWARD_TYPE_PERFORMANCE",2);
  define("AWARD_TYPE_CD",3);
  define("AWARD_TYPE_SONG",4); 
  define("DEFAULT_AWARD_IMAGE","default-award.png"); 
  define("AWARD_SIZE_LARGE","large"); 
  define("AWARD_SIZE_SMALL","small"); 
  define("AWARD_SIZE_XSMALL","xsmall"); 
  
   //LYRICS TYPES
  define("LYRICS_TYPE_CD",1);
  define("LYRICS_TYPE_SONG",2); 
  
  //CD IDs
  define("VOL1_CD_ID",1);
  define("VOL2_CD_ID",2);
  define("VOL3_CD_ID",3);
  define("DANDELION_CD_ID",4);
  define("ONCE_CD_ID",5);
  define("WYWH_CD_ID",7);
  
   
   //ACTIVE NAV MENU ITEM
  define("HOME_ACTIVE","home"); 
  define("SCHEDULE_ACTIVE","schedule"); 
  define("MUSIC_ACTIVE","music"); 
  define("CONTACT_ACTIVE","contact"); 
  define("ABOUT_ACTIVE","about"); 
  ?>
  