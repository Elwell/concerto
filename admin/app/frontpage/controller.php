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
 * @author       Web Technologies Group, $Author$
 * @copyright    Rensselaer Polytechnic Institute
 * @license      GPLv2, see www.gnu.org/licenses/gpl-2.0.html
 * @version      $Revision$
 */
class frontpageController extends Controller
{
	public $actionNames = Array( 'index'=> "Front page", 
                                'admin'=>'Admin Utilities',
                                'mailer' =>'Send Mail');

   public $require = Array('check_login'=>Array('dashboard','login','logout'),
                           'require_login'=>Array('admin','login','dashboard','su','phpinfo','mailer','sendmail') );

	function setup()
	{
		$this->setName("Home");
	}

	function indexAction()
	{
      $this->setTitle("Front Page");
      if (isLoggedIn()) {
         $this->dashboardAction();
         $this->renderView('dashboard');
      }
	}
   
	function dashboardAction()
	{
     $this->notifications = Newsfeed::get_for_user($_SESSION['user']->id);
     $group_str = implode(',',$_SESSION['user']->groups);
     $this->setTitle("Dashboard");
     if(count($_SESSION['user']->groups) > 0){
        $group_str = 'OR group_id IN (' . $group_str . ')';
     } else {
        $group_str = "";
     }
     $this->screens= Screen::get_all('WHERE type = 0 ' . $group_str . ' ORDER BY `name`');
     if(!is_array($this->screens)){
       $this->screens = array();
     }
     $this->screen_stats = Screen::screenStats('WHERE type = 0 ' . $group_str . ' ORDER BY `name`');
	}

	function adminAction()
	{
      $user = new User(phpCAS::getUser());
      if(!$user->admin_privileges)
         redirect_to(ADMIN_URL.'/frontpage');

      $this->flash('This is an error.','error');
      $this->flash('This is a warning.','warn');
      $this->flash('Status','stat');
      $this->flash('FYI','info');
      $this->flash('Default message type');

		$this->setTitle("Administrative Utilities");

      if(isset($_REQUEST['stats'])) {
         if($_REQUEST['stats']=='Turn On') {
            $_SESSION['stats']=1;
            $_SESSION['flash']='Page build statistics now on (see page bottom)';
         } else {
            $_SESSION['stats']=0;
            $_SESSION['flash']='Page build statistics now off';
         }
      }
	}
	function mailerAction()
	{
         $user = new User(phpCAS::getUser());
         $this->fromyou = $user->name . ' (' . $user->email . ')';
         if(!$user->admin_privileges)
           redirect_to(ADMIN_URL.'/frontpage');

  	 $this->setTitle("System Mailman");
         //Generate Users
	 $userids = sql_select("user","username",false,"ORDER BY username");
	 $this->users = array();
	 foreach($userids as $username){
	   $this->users[] = new User($username['username']);
	 }
         //Generate Groups
         $groupids = sql_select("group","id",false,"ORDER BY name");
         $this->groups = array();
         foreach($groupids as $groupid){
           $this->groups[] = new Group($groupid['id']);
         }

	}
	function sendmailAction()
	{
	     $curuser = new User(phpCAS::getUser());
	     $message = $_POST['message'];
	     $subject = $_POST['subject'];
             $from = '';
             if($_POST['from'] == 'user'){
	       $from = $curuser->name . ' <' . $curuser->email . '>';
	     }
	     if($message <= "" || $subject <= ""){
	       $this->flash('Emails must have a subject and message.','error');
	       redirect_to(ADMIN_URL.'/frontpage/mailer');
	     }
	     if(isset($_POST['everyone'])){
		$userrows = sql_select('user', 'username');
		foreach($userrows as $row){
		  $users[] = $row['username'];
		}
	     } else { //We are not sending to everyone
	       $users = array();
	       //Handle individual users
	       $usernames = $_POST['user'];
	       if(sizeof($usernames) > 0) {
	         $users = array_merge($users,$usernames);
	       }

	       //Handle groups & special groups
	       $groupids = array();
	       if(isset($_POST['group'])){
	         $groupids = $_POST['group'];
	       }
	       if(isset($_POST['special'])){ //Handle special groups that own stuff
	         $special = $_POST['special'];
	         foreach($special as $table){
		    $members = sql_select($table, 'group_id');
		    foreach($members as $member){
		      $groupids[] = $member['group_id'];
		    }
	         }
	       }
	       $groupids = array_unique($groupids);
	       if(sizeof($groupids) > 0){
	         foreach($groupids as $groupid){
	           $group = new Group($groupid);
	           $group_users = $group->list_members();
	           $users = array_merge($users, $group_users);
	         }
	       }
	       $users = array_unique($users);
	     } //End big block to build the recipients array
	     if(sizeof($users) == 0){
	       $this->flash('Emails must at least one recipient.','error');
	       redirect_to(ADMIN_URL.'/frontpage/mailer');
	     }
	     $status = true;
	     foreach($users as $user){
	       $user = new User($user);
	       $retval = $user->send_mail($subject, $message, $from);
	       $status = $status * $retval; 
	     }
	     if(!$status){
	       $this->flash('The mail function returned false, some messages may not have gotten delivered.','warn');
	     } else {
	       $this->flash('It appears the messages were sucessfully sent.','info');
	     }
	     redirect_to(ADMIN_URL.'/frontpage/mailer');
	}

   function phpinfoAction()
   {
      $user = new User(phpCAS::getUser());
      if(!$user->admin_privileges)
         redirect_to(ADMIN_URL.'/frontpage');
      phpinfo();
      exit();
   }

   function suAction()
   {
      $user = new User(phpCAS::getUser());
      if(isset($_REQUEST['r'])) {
         unset($_SESSION['su']);
         login_login();
      } elseif ($user->admin_privileges  && isset($_REQUEST['su'])) {
         $_SESSION['su']=$_REQUEST['su'];
         login_login();
      }
      redirect_to(ADMIN_URL."/frontpage");
   }

	function loginAction()
	{
      redirect_to(ADMIN_URL."/frontpage");
	}

	function logoutAction()
	{
		login_logout();
		$_SESSION['flash'][] = array('warn','Something went wrong with your logout. Close your browser to end the session securely.');
		self::renderView('frontpage');
	}
}
?>
