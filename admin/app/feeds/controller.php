<?php
class feedsController extends Controller
{
   public $actionNames = Array( 'list'=> 'Feeds Listing', 'show'=>'Details',
                                'edit'=> 'Edit', 'moderate'=>'Moderate', 'delete'=>'Delete', 'request'=>'Feed Request');

   public $require = Array( 'require_login'=>1,
                            'require_action_auth'=>Array('edit','create',
                                                         'new', 'update', 'deny',
                                                         'delete', 'destroy',
                                                         'moderate', 'approve' ) );

   function setup()
   {
      $this->setName("Feeds");
   }

   function indexAction()
   {
      $this->listAction();
      $this->renderView("feeds", "list");
   }

   function listAction()
   {
      $this->feeds=Feed::priv_get($_SESSION['user'], 'list');
   }


   function showAction()
   {
      $access_check = new Feed();
      if(!($access_check->priv_test($_SESSION['user'], $this->args[1]))) {
         $this->flash('Error: The feed you requested could not be found. '.
                      'You may have found a bad link, or the feed may have been removed. ',
                      'error');
         redirect_to(ADMIN_URL.'/feeds');
      }
      $this->feed = new Feed($this->args[1]);

      $this->group = new Group($this->feed->group_id);
      //      $this->contents=$this->feed->content_list("1");
      $waiting_arr=$this->feed->content_list('NULL');
      if(is_array($waiting_arr)) $waiting = count($waiting_arr);
      else $waiting = "No";
      $this->waiting = "$waiting item".($waiting!=1?'s':'')." awaiting moderation";


      $types = sql_select('type',Array('id','name'), NULL, 'ORDER BY name');
      foreach($types as $type) {
         $contentids = sql_select('feed_content','DISTINCT content_id', '', 'INNER JOIN `content`'.
                                  ' ON content_id=content.id AND moderation_flag=1'.
                                  ' AND feed_id = '.$this->feed->id.' AND type_id = '.$type['id'].
                                  ' ORDER BY name');
         if(is_array($contentids))
            foreach($contentids as $id)
               $this->contents[$type['name']][] = new Content($id['content_id']);
      }


      $this->setTitle($this->feed->name);
      $this->setSubject($this->feed->name);
      $this->canEdit = $_SESSION['user']->can_write('feed',$this->args[1]);
   }

   function moderateAction()
   {
      $this->feed = new Feed($this->args[1]);
      $this->setTitle('Moderating '.$this->feed->name);
      $this->setSubject($this->feed->name);
      $types = sql_select('type',Array('id','name'), NULL, 'ORDER BY name');
      foreach($types as $type) {
         $contentids = sql_select('feed_content', 'content_id', NULL,
                                  'LEFT JOIN content ON content.id=content_id WHERE type_id = '.$type['id'].
                                  ' AND moderation_flag IS NULL AND feed_id = '.$this->feed->id.' ORDER BY name');
         if(is_array($contentids))
            foreach($contentids as $id)
               $this->contents[$type['name']][] = new Content($id['content_id']);
      }
   }

   function editAction()
   {
      $this->feed = new Feed($this->args[1]);
      $this->setSubject($this->feed->name);
      $this->setTitle("Editing ".$this->feed->name);
   }

   function newAction()
   {
      $this->setTitle("Create new feed");
   }

   function deleteAction()
   {
      $this->showAction();
      $this->renderView('show');
      $this->setTitle('Deleting '.$this->feed->name);
      $this->flash("Do you really want to remove <strong>{$this->feed->name}</strong>? <br />".
                   '<a href="'.ADMIN_URL.'/feeds/destroy/'.$this->feed->id.'">Yes</a> | '.
                   '<a href="'.ADMIN_URL.'/feeds/show/'.$this->feed->id.'">No</a>','warn');
   }

   function requestAction()
   {
      if(isset($_POST['submit'])) {
         $group=new Group(2);
         $dat = $_POST['feed'];
         $nm = escape($_SESSION['user']->name);
         $id = $_SESSION['user']->id;
         $email = escape($_SESSION['user']->email);
         $msg ="There has been a new feed request from {$nm} - {$email} (".ADMIN_URL."/users/show/{$id})\n";
         $msg.='Name: '.escape($dat['name'])."\n";
         $msg.='Organization: '.escape($dat['org'])."\n";
         $msg.='Description: '.escape($dat['desc'])."\n";

         $group->send_mail('New Concerto Feed Request: '.escape($dat['name']), $msg,escape($_SESSION['user']->email));

         $this->flash("Your request is being processed. We'll be contacting you about the feed soon!");
         redirect_to(ADMIN_URL.'/feeds/');
      }
   }

   function createAction()
   {
      $this->Settitle('Feed Creation');
      $feed=new Feed();

      if($feed->create_feed($_POST[feed][name],$_POST[feed][group])) {
         $this->flash($feed->name.' was created successfully.');
         redirect_to(ADMIN_URL.'/feeds/show/'.$feed->id);
      } else {
         $this->flash('Your feed creation failed. '.
                      'Please check all fields and try again; contact an administrator if all else fails.','error');
         redirect_to(ADMIN_URL.'/feeds/new');
      }
   }

   function updateAction()
   {
      $feed = new Feed($this->args[1]);
      $dat = $_POST['feed'];
      $feed->name = $dat['name'];
      $feed->group_id = $dat['group'];

      if($feed->set_properties()) {
         $this->flash('Feed Updated Successfully');
         redirect_to(ADMIN_URL.'/feeds/show/'.$feed->id);
      } else {
         $this->flash('Feed update failed. Please try again.','error');
         redirect_to(ADMIN_URL.'/feeds/show/'.$this->args[1]);
      }
   }

   function approveAction()
   {
      $feed = new Feed($this->args[1]);
      $cid = $this->args[2];
      if($feed->content_mod($cid, 1, $_SESSION['user']->id)) {
         $this->flash('Content approved successfully.');
         redirect_to(ADMIN_URL.'/feeds/moderate/'.$feed->id);
      } else {
         $this->flash('Content approval failed.','error');
         redirect_to(ADMIN_URL.'/feeds/moderate/'.$this->args[1]);
      }
   }

   function denyAction()
   {
      $feed = new Feed($this->args[1]);
      $cid = $this->args[2];
      if($feed->content_mod($cid, 0, $_SESSION['user']->id)) {
         $this->flash('Content denied successfully.');
         redirect_to(ADMIN_URL.'/feeds/moderate/'.$feed->id);
      } else {
         $this->flash('Content denial failed.','error');
         redirect_to(ADMIN_URL.'/feeds/moderate/'.$this->args[1]);
      }
   }

   function destroyAction()
   {
      $feed = new Feed($this->args[1]);
      if($feed->destroy()) {
         $this->flash('Feed destroyed successfully');
         redirect_to(ADMIN_URL.'/feeds');
      } else {
         $this->flash('There was an error removing the feed.','error');
         redirect_to(ADMIN_URL.'/feeds/show/'.$this->args[1]);
      }
   }
}
?>
