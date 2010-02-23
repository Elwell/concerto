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
<a href="<?php echo ADMIN_URL.'/groups/new' ?>"><span class="buttonsel"><div class="buttonleft"><img src="<?php echo ADMIN_BASE_URL ?>/images/buttonsel_left.gif" border="0" alt="" /></div><div class="buttonmid"><div class="buttonmid_padding">New Group</div></div><div class="buttonright"><img src="<?php echo ADMIN_BASE_URL ?>/images/buttonsel_right.gif" border="0" alt="" /></div></span></a><div style="clear:both;height:12px;"></div>
<?php } ?>
<h2>Click on a group for more information.</h2>
<table class="edit_win" cellpadding="6" cellspacing="0">
<?php
$notfirst = 0; //Indicate that this isn't not the first group, until we display a group 
if(is_array($this->groups))
foreach($this->groups as $groupid => $group){
   ?>
<tr>
   <td<?php if (!$notfirst) {$notfirst =1;  echo ' class="firstrow"';} ?>>
   <h1><a href="<?php echo ADMIN_URL?>/groups/show/<?php echo $groupid ?>"><?php echo $group['name'] ?></a></h1>
      <p><?php echo $group['members'] ?> member<?php echo $group['members']!=1?"s":""?></p>
      <?php if(is_array($group['controls']))
        echo "<p>Controls ".join(" and ", $group['controls']).'</p>';
      ?>
   </td>
</tr>

<?php
}
?>
</table>
