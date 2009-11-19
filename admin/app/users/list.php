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
?><?php if(isAdmin()) { ?>
<a href="<?=ADMIN_URL.'/users/new' ?>"><span class="buttonsel"><div class="buttonleft"><img src="<?= ADMIN_BASE_URL ?>/images/buttonsel_left.gif" border="0" alt="" /></div><div class="buttonmid"><div class="buttonmid_padding">New User</div></div><div class="buttonright"><img src="<?= ADMIN_BASE_URL ?>/images/buttonsel_right.gif" border="0" alt="" /></div></span></a><div style="clear:both;height:12px;"></div>
<? } ?>
<h2>Click on a user to view his or her profile.</h2>
<table class="edit_win" cellpadding="6" cellspacing="0">
<?php
$notfirst = 0; // Indicate that we haven't displayed the first row yet (for top border)
foreach($this->users as $user){
   ?>
<tr>
  <td<? if (!$notfirst) {$notfirst =1;  echo ' class="firstrow"';} ?>>
  	<h1><a href="<?= ADMIN_URL?>/users/show/<? echo $user->username ?>"><?= $user->name ?></a></h1>
  </td>
	<td>
   	<h4>&nbsp;<?php
     $groups=array();
     if($user->admin_privileges) 
        $groups[]= "<strong>Concerto Administrators</strong>";
     $group_objs=$user->list_groups();
     if(is_array($group_objs))
        foreach($user->list_groups() as $group) 
           $groups[] = '<a href="'.ADMIN_URL."/groups/show/$group->id\">$group->name</a>";
     if(count($groups)>0)
        echo 'Member of: '.join(", ", $groups);
   ?> 	
   	</h4>
  </td>
</tr>

<?php
}
?>
</table>
