<?php
/**
 * Copyright (C) 2010 Andrew Elwell, CERN
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
 * @author       CERN, $Author$
 * @license      GPLv2, see www.gnu.org/licenses/gpl-2.0.html
 * @version      $Revision$
 */


// This replaces all the CAS stuff with checks that we're logged in with ADFS from CERN

function check_login($callback)
{
   if(isLoggedIn())
      return true;

   if($callback->controller == 'users' && $callback->action == 'create')
      return true;

}

function require_login()
{
/*Caching login: */
/*
   phpCAS::forceAuthentication();
   if(!isLoggedIn())
      login_login();
*/

/*Re-fetching user for each page that uses it: */
   login_login();

   return true;
}

function require_action_auth($callback)
{
   check_login($callback);
   $target = $callback->controller;
   $id=$callback->currId;

   if(!has_action_auth($target, $id)) {
      $callback->flash("Sorry, you don't have permission to edit $target $id",'error');
      if($callback->action == $callback->defaultAction)
         redirect_to(ADMIN_URL);
      else
         redirect_to(ADMIN_URL.'/'.$callback->controller);
   }

   return true;
}

//these methods are interfaces to logon information.
function isLoggedIn()
{
   if(array_key_exists('user', $_SESSION) && 
    strlen($_SESSION['user']->username)>1) return true;
   return false;
}

function isAdmin()
{
   if(array_key_exists('user', $_SESSION) &&
    $_SESSION['user']->admin_privileges) return true;
   return false;
}

function firstName()
{
   return $_SESSION['user']->firstname;
}

function userName()
{  
   return $_SESSION['user']->username;
}

function has_action_auth($target, $id)
{
   if(!isLoggedIn()) return false;
   $grant=false;

   if($target=='screens') $target='screen';
   elseif($target=='feeds') $target='feed';
   elseif($target=='groups') $target='group';
   elseif($target=='users') $target='user';

   if($_SESSION['user']->can_write($target,$id)) {
      $grant=true;
   }

   return $grant;
}

//login/out functionality

function login_logout()
{
   $_SESSION = array();
   session_destroy();
   session_start();
   header("Cache-control: private"); // IE 6 Fix
      redirect_to("https://login.cern.ch/adfs/ls/?wa=wsignout1.0");
}

function login_login()
{
   $_SESSION = array();
   session_start();
   $rcsid = $_SERVER['ADFS_LOGIN'];

   if(isset($_SESSION['su'])) {
      $rcsid=$_SESSION['su'];
   }
   $rcsid=mysql_escape_string($rcsid);
   $_SESSION['user'] = new user($rcsid);
   //Send the user to signup if the new user was not created
   //Comparison must be case-insensitive. strcasecmp returns 0 if equal
   if(strcasecmp($_SESSION['user']->username,$rcsid)!== 0){
      redirect_to(ADMIN_URL.'/users/signup');
      exit();
   }
}
