<?php
/**
 * This file was developed as part of the Concerto digital signage project
 * at RPI.
 *
 * Copyright (C) 2009 Rensselaer Polytechnic Institute
 * (Student Senate Web Technolgies Group)
 *
 * This program is free software; you can redistribute it and/or modify it 
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.  You should have received a copy
 * of the GNU General Public License along with this program.
 *
 * @package      Concerto
 * @author       Web Technologies Group, $Author: mike $
 * @copyright    Rensselaer Polytechnic Institute
 * @license      GPLv2, see www.gnu.org/licenses/gpl-2.0.html
 * @version      $Revision: 551 $
 */

/**
 * config.inc.php
 * 
 * Concerto system configuration - where people can't get to it
 */

//Database Connection
$db_host = 'localhost';
$db_login = 'concerto_dev';
$db_password = 'phe7rudr';
$db_database = 'concerto_development';

//Important paths

define('ROOT_DIR', '/var/www/tom_balls/');                    //server-side path where Concerto lives
define('COMMON_DIR', ROOT_DIR.'common/');             //server-side path to dir with resources for
                                                       //  multiple portions of Concerto
define('CONTENT_DIR', ROOT_DIR.'content/');      //server-side path to content images
define('IMAGE_DIR', CONTENT_DIR.'images/');      //server-side path to content images
define('TEMPLATE_DIR', CONTENT_DIR.'templates/');//server-side path to screen templates

//URLS for hyperlinks and the like
define('ROOT_URL', '/tom_balls/');                         //the root location where Concerto lives
define('SCREEN_URL', ROOT_URL.'screen/');        //location of front-end screen program
define('HARDWARE_URL', ROOT_URL.'hardware/');    //location of management for on-location machines
define('ADMIN_BASE_URL', ROOT_URL.'admin/');     //base URL on server for images, css, etc. for interface
define('ADMIN_URL', ADMIN_BASE_URL.'index.php'); //URL that can access this page (may be same as ADMIN_BASE_URL if mod_rewrite configured)

//Various configuration
define('CONCERTO_VERSION', '1.7');               //Version number.
define('DEFAULT_DURATION', 5);                   //Default content duration, in seconds
define('DEFAULT_WEIGHT', 3);                     //Default position weight

define('EMS_FEED_ID', 19);                       //ID of the emergency feed.
define('ADMIN_GROUP_ID', 0);                     //ID of the User Group for admin functions and contact
define('GA_TRACKING', false);                    //Define a Google Tracking id if applicable
define('TICKER_LIMIT', 150);

define('SYSTEM_EMAIL', 'concerto@union.rpi.edu');//Email address used for system emails
?>
