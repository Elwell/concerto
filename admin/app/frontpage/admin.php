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
?><h2>Please use these utilities wisely.</h2>

<div class="roundcont">
  <div class="roundtop"><span class="rt"><img src="<?php echo ADMIN_BASE_URL ?>/images/blsp.gif" height="6" width="1" alt="" /></span></div>
  <div class="roundcont_main">
  <h1>Masquerade (su)</h1>
  <form action="<?php echo ADMIN_URL?>/frontpage/su" method="POST">
    <select name="su">
    <option></option>
<?php
      $userids = sql_select("user","username",false,"ORDER BY username");
      $this->users=Array();
      if(is_array($userids))
         foreach($userids as $user) {
            $user = new User($user[username]);
            echo '<option value="'.$user->username.'">'.$user->username.' - '.$user->name.'</option>';
         }

?>
    </select>
    <input type="submit" value="su" />
  </form>
  </div>
  <div class="roundbottom"><span class="rb"><img src="<?php echo ADMIN_BASE_URL ?>/images/blsp.gif" height="6" width="1" alt="" /></span></div>
</div>
<div class="roundcont">
  <div class="roundtop"><span class="rt"><img src="<?php echo ADMIN_BASE_URL ?>/images/blsp.gif" height="6" width="1" alt="" /></span></div>
  <div class="roundcont_main">
  <h1>Page Load Statisics</h1>
  <form method="POST">
    <input type="submit" name="stats" value="Turn <?php echo (array_key_exists('stats', $_SESSION) && $_SESSION['stats']) ?'Off':'On'?>" />
  </form>
  </div>
  <div class="roundbottom"><span class="rb"><img src="<?php echo ADMIN_BASE_URL ?>/images/blsp.gif" height="6" width="1" alt="" /></span></div>
</div>


<h3>Admin Privs: <span class="emph"><?php echo isAdmin() ?></span></h3>
<h3>Reset Session: <span class="emph"><a href="<?php echo ADMIN_URL?>/frontpage/su?r=1">reset</a></span></h3>
<h3>Admin Revision: <span class="emph"><?php system('svnversion')?></span></h3>
<a href="<?php echo ADMIN_URL ?>/frontpage/phpinfo">PHP Info</a><br />
<a href="<?php echo ADMIN_URL ?>/frontpage/mailer">Send Mail</a><br />
<a href="<?php echo ADMIN_URL ?>/frontpage/addtemplate">Template Importer</a></br>

