<?php
include('includes/mysql.inc');
include('includes/login.php');

define('ADMIN_BASE_URL','/mike_admin');
define('ADMIN_URL','/mike_admin/index.php');

//session variables visible to both controller and view
global $sess;

$sess[breadcrumbs][]='<a href="'.ADMIN_URL.'">Signage Interface</a>';

//parse request
$request = split('/',trim($_SERVER[PATH_INFO],'/'));

//decide what controller we'll be requesting an action from
$controller = $request[0];
if(!isset($controller) || $controller == "") $controller = 'frontpage';

//get the code for the requested controller
include('app/'.$controller.'/controller.php');

$sess[breadcrumbs][]='<a href="'.ADMIN_URL.'/'.$controller.'">'.
	call_user_func(array($controller.'Controller','getName')).
	'</a>';

//figure out what action to preform
$action = $request[1];
if(!isset($action))
	$action = 
call_user_func(array($controller.'Controller','getDefaultAction'));

$layout = call_user_func(array($controller.'Controller','getLayout'),
	$action);

//send additional url parameters to controller through sess
$sess['args'] = array_slice($request,2);

//actually preform the requested action
call_user_func(array($controller.'Controller',$action));

//layout the page.  This will call renderAction when
//  it is time to render the view.
include ($layout.'.php');

//may want to allow controllers to insert extra
// information into the html head at some point.
function renderHeadExtras() {}

function setView($controller, $view)
{
	global $qview;
	//for security, first view that is set will be used.
	if(!is_array($qview))
		$qview=array($controller,$view);
}

//to be called by layout, this renders the main content of the page
function renderAction()
{
	global $qview, $sess;
	include 'app/'.$qview[0].'/'.$qview[1].'.php';
}

//print out the statuse messages saved in $sess
function renderMessages()
{
	global $sess;
	if(is_array($sess[messages]))
		foreach($sess[messages] as $msg)
			echo renderMessage($msg[0], $msg[1]);
}

//ancestor for all controllers
class Controller
{
	protected static $defaultLayout = 'ds_layout';
	protected static $layouts=Array();
	protected static $defaultAction = 'index';
	function getDefaultAction()
	{
		return self::$defaultAction;
	}

	function setLayout($layout, $actions=-1)
	{
		if($actions==-1)
			self::$defaultLayout=$layout;
		if(is_array($actions))
			foreach($actions as $action)
				self::$layouts[$action] = $layout;
		else
			self::$layouts[$actions]=$layout;
	}
	function getLayout($action)
	{	
		if(isset(self::$layouts[$action]))
			return self::$layouts[$action];
		else return self::$defaultLayout;
	}

	function renderView($a1, $a2=-1)
	{
		global $sess;
		if ($a2==-1)
		{
			global $controller;
			$view = $a1;
		}
		else
		{
			$controller = $a1;
			$view = $a2;
		}
		setView($controller, $view);
	}
}
?>
