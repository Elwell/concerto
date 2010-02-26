<?php
/**
 * This file was developed as part of the Concerto digital signage project
 * at RPI.
 *
 * Copyright (C) 2009 Rensselaer Polytechnic Institute
 * (Student Senate Web Technologies Group)
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
/*
File: index.php
Status: Functional (there are still features to implement)
History: Blame Mike D
Functionality: Sets up for output, parses URL, and dispatches controllers
                to preform requested actions.  Also includes base class
                Controller.
*/

//Let's keep track of build time breakdown for debugging and whatnot
global $start_time;
global $render_times;
$render_times=Array();
$start_time=microtime(true);

//DB, system, and miscellaneous configuration:
include('../config.inc.php');

//The version number, and other non-user globals:
include('../version.inc.php');

//Classes and Libraries:
include(COMMON_DIR.'mysql.inc.php');//Tom's sql library interface + db connection settings
include(COMMON_DIR.'user.php');     //Class to represent a site user
include(COMMON_DIR.'screen.php');   //Class to represent a screen in the system
include(COMMON_DIR.'feed.php');     //Class to represent a content feed
include(COMMON_DIR.'field.php');    //Class to represent a field in a template
include(COMMON_DIR.'position.php'); //Class to represent a postion relationship
include(COMMON_DIR.'content.php');  //Class to represent content items
include(COMMON_DIR.'upload.php');   //Helps uploading
include(COMMON_DIR.'group.php');    //Class to represent user groups
include(COMMON_DIR.'dynamic.php');  //Functionality for dynamic content
include(COMMON_DIR.'notification.php');  //Functionality for notifications
include(COMMON_DIR.'newsfeed.php');  //Functionality for notifications
include(COMMON_DIR.'template.php');  //Class to represent a template
include(COMMON_DIR.'image.inc.php'); //Image library, used for resizing images

$render_times[] = Array('Includes', microtime(true));

//Functionality for CAS, logins, and page authorization:
include('includes/login.php');  

$render_times[] = Array('login', microtime(true));

//Constants
// Leaving some of these constants out will make things broken.
// Some of these will likely be definied in the above-included config.inc.php,
// but we'll provide some defaults here just in case.
if(!defined('ADMIN_BASE_URL')) {
    define('ADMIN_BASE_URL','/admin/'); //base directory for images, etc.
}
if(!defined('ADMIN_URL')) {
    //Top URL of site
    //May be same as ADMIN_BASE_URL if mod_rewrite is configured (pretty URLs)
    define('ADMIN_URL','/mike_admin/index.php');
}
if(!defined('DEFAULT_PATH')) {
    //Controller to use when none is specified
    define('DEFAULT_PATH','/frontpage');
}
if(!defined('DEFAULT_TEMPLATE')) {
    //Layout file for actions with none specified 
    define('DEFAULT_TEMPLATE','ds_layout');      
}
if(!defined('HOMEPAGE')) {
    //Name of the homepage
    define('HOMEPAGE','Home');
}
if(!defined('HOMEPAGE_URL')) {
    //relative URL for frontpage (we'll link to ADMIN_URL.'/'.HOMEPAGE_URL)
    define('HOMEPAGE_URL', '');
}
if(!defined('APP_PATH')) {
    define('APP_PATH','app'); //Location of view and controller files
}

set_magic_quotes_runtime(0);

//Enough setup... Let's get to work
//Allow multi-domain setups to point to one single domain
if(defined('PREFERRED_DOMAIN') && PREFERRED_DOMAIN != $_SERVER['HTTP_HOST']) {
  header ('HTTP/1.1 301 Moved Permanently');
  header ('Location: http://' . PREFERRED_DOMAIN . $_SERVER['REQUEST_URI']);
  exit(0);
}

//parse request, go to default page if none requested
if(!array_key_exists('PATH_INFO', $_SERVER) || 
  $_SERVER['PATH_INFO'] == '' || $_SERVER['PATH_INFO'] == '/') {
    //Note: if the server fails to pass PATH_INFO when we get a URL that is not the
    //top level, we will display the defaul page (homepage) regardless of the URL.
    //this should be okay for most configurations, but is a potential deployment hitch.
    $path_info = DEFAULT_PATH;
} else {
    $path_info = $_SERVER['PATH_INFO'];
}
$request = split('/',trim($path_info,'/'));

//decide what controller we'll be requesting an action from
$controller = $request[0];
if(!isset($controller) || $controller == "") 
     $controller = DEFAULT_CONTROLLER;
     
//include the code for the requested controller
if(!file_exists(APP_PATH.'/'.$controller.'/controller.php')) {
   notFound();
} else {
   include(APP_PATH.'/'.$controller.'/controller.php');
   
   // make a reflection object to represent our controller
   $reflectionObj = new ReflectionClass($controller.'Controller');
   
   // use Reflection to create a new instance
   $controllerObj = $reflectionObj->newInstanceArgs(); 

   //have the controller do its thing
   $controllerObj->execute(array_slice($request,1));
}

//Send headers.  They should not be sent before now.
/*switch($httpStatus)
{
	case 404:
	header('HTTP/1.0 404 Not Found'); break;

	case 401:
	header('HTTP/1.0 401 Unauthorized'); break;

	case 403:
	header('HTTP/1.0 403 Forbidden'); beak;

	case 200:
	default:
        header("Cache-control: private"); // IE 6 Fix
        if(!isset($contentType))
                $contentType = "text/html";
        header("Content-Type: {$contentType}; charset=ISO-8859-1");
	break;
}*/

//print out the status messages saved in session
//this will be called in the template to display them to the user
function renderMessages()
{
  if(array_key_exists('flash',$_SESSION) && is_array($_SESSION['flash']))
     foreach($_SESSION['flash'] as $msg)
        echo renderMessage($msg[0], $msg[1]);
  //Once they've been displayed, clear them out.
  $_SESSION['flash']=array();
}

function notFound()
{
   global $sess;
   $status = 404;
   //  setView(BLANK_VIEW,0);
}

function denied($reason=0)
{
   global $sess;
   $status = 403;
   switch ($reason){
   case 0:
      $rtext='Permission denied.'; break;
   case 'login':
      $rtext='You must be logged in to view this page.'; 
      $status = 401; break;
   case 'rights':
      $rtext='You have insufficient rights for this action.'; break;
   default:
      $rtext='Permission denied: '.$reason;
   }
   
   $sess['messages'][] = array('warn',$rtexto);
   if($reason == 'login')
      $sess['messages'][] = 
         array('info',
               '<a href="?login">Log in</a> or <a href= "'.
               ADMIN_BASE_URL.'/docs/">visit the help pages</a> to learn more.');
   setView('frontpage','denied');
}

function redirect_to($url)
{
   header("Location: $url",TRUE,302);
   exit();
}

//Potential future use for better links, these aren't widely used
//for now, ADMIN_URL is visible everywhere to get the top-level url
function url_for($controller, $action='', $id='')
{
   $str = ADMIN_URL.'/'.$controller;
   if($action!='')
      $str.='/'.$action;
   if($id!='')
      $str.='/'.$id;
   return $str;
}
function link_to($label, $controller, $action='', $id='', $a_extra='')
{
   return "<a href=\"".url_for($controller, $action, $id)."\"".
      ($a_extra==''?'':' ').$a_extra.">$label</a>";
}

/*
Class: Controller
Status: Stable
Functionality: Ancestor for all controllers 
This does the heavy lifting of choosing actions, passing parameters,
recording breadcrumbs, and calling views and templates.
*/
class Controller
{
   public $defaultAction = 'index';
   protected $before_execs = array();
   protected $after_execs = array();
   protected $defaultTemplate = DEFAULT_TEMPLATE;
   protected $templates = array();
   protected $args;
   protected $breadcrumbs;
   public $controller;
   public $action;
   public $currId;
   public $pagetitle;
   public $subjectName;
   public $subtitle;
   public $template;     //The template being used by the current action

   function __construct()
   {
      $this->controller = 
         ereg_replace('Controller','',get_class($this));
      $this->setup();
   }
  
   function setup() //meant to be overriden by child
   {
      return false;
   }
   
   function execute($args)
   {
      //frontpage and controller breadcrumbs
      $this->breadcrumb(HOMEPAGE, HOMEPAGE_URL);
      if($this->controller!='DEFAULT_CONTROLLER')
         $this->breadcrumb($this->getName(),$this->controller);
      
      //figure out what action to use
      if(array_key_exists(0,$args) && method_exists($this,$args[0].'Action'))
         $action = $args[0];
      else if(method_exists($this, $this->defaultAction.'Action'))
         $action = $this->defaultAction;
      else
         notFound();
      
      //save arguments for controller use
      $this->args=$args;
      $this->currId= array_key_exists(1,$args) ? $args[1] : NULL;
      $this->action=$action;

      //save information about the view we want to display
      //by default we use the view with the name of the action
      //(may be modified by action)
      $this->renderView($action);
    
      //find the action's human name
      if($action != $this->defaultAction) {
        if(array_key_exists($action, $this->actionNames)) {
          $actionName = $this->actionNames[$action];
        } else {
          $actionName = $action;
        }
      }

      //figure out which template should be used by default
      $this->template = $this->getTemplate($action);

      //take care of any requirements
      $this->doRequirements($action);
      
      //run the action
      global $render_times;
      $render_times[] = Array('Init', microtime(true));
      call_user_func(array($this,$action.'Action'));

      //set breadcrumbs
      if(count($this->breadcrumbs)<=2) {
         if($this->subjectName)
            $this->breadcrumb($this->subjectName,$this->controller.'/show/'.(array_key_exists(1,$this->args) ? $this->args[1] : ''));
         if($action != $this->defaultAction && $action != 'show')
            $this->breadcrumb($actionName,$this->controller.'/'.$action);
         //I don't forsee a case where that breadcrumb URL is shown, btw.
      }
      
      //include the template, which will call back for view
      $render_times[] = Array('Action', microtime(true));
      if($this->template !== false)
         include $this->template;
      else //if this occurs, a 404 will be delivered but the
         //action may still have been completed.
         notFound(); 
      }
   
   //renders (directly outputs) the view; to be called by template
   function render()
   {
      $viewpath=APP_PATH.'/'.
         $this->view['controller'].'/'.
         $this->view['view'].'.php';
      if(file_exists($viewpath))
         include($viewpath);

      //We'll allow admins to take a look at page build times and breakdowns
      if(isset($_SESSION['stats']) && $_SESSION['stats']) {
         global $start_time, $render_times;
         $end = microtime(true);
         $render_times[] = Array('Template', microtime(true));
         $prev_time = $start_time;
         echo '<p>';
         $prev_time = $start_time;
         foreach($render_times as $ar) {
            echo $ar[0].': '.number_format(($ar[1]-$prev_time)*1000,3).' | ';
            $prev_time=$ar[1];
         }
         echo '<strong>Total: '.number_format(($end-$start_time)*1000,3).' ms</strong></p>';
      }
   }
   function setName($name)
   {
      $this->name = $name;
   }
   function getName()
   {
      return $this->name;
   }
   function setTitle($title)
   {
      $this->pageTitle=$title;
   }
   function setSubtitle($sub)
   {
      $this->subtitle = $sub;
   }
   function setSubject($subj)
   {
      $this->subjectName=$subj;
   }
   function getTitle()
   {
      if(isset($this->pageTitle))
         return htmlspecialchars($this->pageTitle);
      if(isset($this->actionNames[$this->view['view']]))
         return htmlspecialchars($this->actionNames[$this->view['view']]);
      if(isset($this->controller))
         return htmlspecialchars($this->controller);
   }
   function getSubtitle()
   {
      if(isset($this->subtitle))
         return $this->subtitle;
      return false;
   }
   function setTemplate($template, $actions='0')
   {
      if($actions == '0')
         $this->defaultTemplate=$template;
      else if(is_array($actions)) 
         foreach ($actions as $action)
            $this->templates[$action] = $template;
      else
         $this->templates[$actions] = $template;
      return true;
   }

   function doRequirements($action)
   {
      if (!isset($this->require))
         return true;
      foreach ($this->require as $method => $actions) {
         if( $actions == 1 || ( is_array($actions) && in_array($action, $actions) ))
            call_user_func($method, $this);
      }
   }

   function getTemplate($action)
   {
      if(isset($this->templates[$action]) && 
         file_exists($this->templates[$action].'.php'))
         return $this->templates[$action].'.php';
      else if(file_exists($this->defaultTemplate).'.php')
         return $this->defaultTemplate.'.php';
      return false;
   }

   //internal; returns the action that is used if none is specified
   function getDefaultAction()
   {
      return $this->defaultAction;
   }   

   //used to set which view will be included in the final page.
   //use renderView(view) to specify a view in the current controller
   //use renderView(controller, view) for a view in a different controller
   function renderView($controller, $view=null)
   {
      if($view==null)
      {
         $view = $controller;
         $controller = $this->controller;
      }
      $this->view['controller']=$controller;
      $this->view['view']=$view;
   }
   
   //display informational messages
   function flash($msg, $type='info')
   {
      $_SESSION['flash'][]= Array($type, $msg);
   }

   function breadcrumb($item, $url=NULL)
   {
      $this->breadcrumbs[]=Array($item,$url);
   }
   function getCrumbs($delim=' > ')
   {
      $vals = array();
      foreach($this->breadcrumbs as $k => $c) {
         if($c!='' && $k<count($this->breadcrumbs)-1) {
            if($c[1]!==NULL)
               $vals[]="<a href=\"".ADMIN_URL."/$c[1]\">".htmlspecialchars($c[0])."</a>";
            else
               $vals[]=htmlspecialchars($c[0]);
         } else {
            $vals[]=htmlspecialchars($c[0]);
         }
      }
      return join($delim,$vals);
   }
}

//Utility function that I wrote
function sql_select($table, $fields="", $conditions="", $extra="", $debug=false)
{
    if($fields && !is_array($fields) ) {
        $fields = Array($fields);
    }
    $query = 'SELECT '.($fields?join(", ",$fields):'*')." FROM `$table`";
    if($conditions) {
        $query .= " WHERE $conditions ";
    }
    if($extra) {
        $query .= ' '.$extra;
    }
    if($debug) {
        echo $query;
    }
    $res=sql_query($query);
    $rows= array();
    $i=0;
    if($debug) {
        echo mysql_error();
    }
    while($row = sql_row_keyed($res,$i++)) {
        if(isset($row[0])) $rows[]=$row;
    }
    if($debug) {
        print_r($rows);
    }
    return $rows;	
}
?>
