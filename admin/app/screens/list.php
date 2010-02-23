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
?><?php if (isAdmin()) {?>
<a href="<?php echo ADMIN_URL.'/screens/new' ?>"><span class="buttonsel"><div class="buttonleft"><img src="<?php echo ADMIN_BASE_URL ?>/images/buttonsel_left.gif" border="0" alt="" /></div><div class="buttonmid"><div class="buttonmid_padding">New Screen</div></div><div class="buttonright"><img src="<?php echo ADMIN_BASE_URL ?>/images/buttonsel_right.gif" border="0" alt="" /></div></span></a><div style="clear:both;height:12px;"></div>
<?php } ?>
<h2>Click on the name of a screen to view its details. <a href="<?php echo ADMIN_URL ?>/pages/show/docs/19#s1"><img class="icon" border="0" src="<?php echo ADMIN_BASE_URL ?>images/help_button.gif" alt="Extra Help" title="Extra Help" /></a></h2>
<?php
foreach($this->screens as $screen){
   if ($screen->width/$screen->height==(16/9)){
      if ($screen->is_connected()) {
      	if (!$screen->get_powerstate()) {
					$status = "Asleep";
					$statcolor = "#aa0";
					$scrimg="screen_169_asleep.png";
				}
				else {
					$status = "Online";
					$statcolor = "green";
					$scrimg="screen_169_on.png";
				}
      } else {
      	$statcolor = "red";
      	$status = "Offline";
      	$scrimg="screen_169_off.png";
      }
   } else if ($screen->width/$screen->height==(16/10)) {
      if ($screen->is_connected()) {
      	if (!$screen->get_powerstate()) {
					$status = "Asleep";
					$statcolor = "#aa0";
					$scrimg="screen_169_asleep.png";
				}
				else {
					$status = "Online";
					$statcolor = "green";
					$scrimg="screen_169_on.png";
				}
      } else {
      	$statcolor = "red";
      	$status = "Offline";
      	$scrimg="screen_169_off.png";
      }
   } else {
      if ($screen->is_connected()) {
      	if (!$screen->get_powerstate()) {
					$status = "Asleep";
					$statcolor = "#aa0";
					$scrimg="screen_43_asleep.png";
      	} 
      	else {
					$status = "Online";
					$statcolor = "green";
					$scrimg="screen_43_on.png";
      	}
      } else {
      	$statcolor = "red";
      	$status = "Offline";
      	$scrimg="screen_43_off.png";
      }
   }
   
?>
  <a href="<?php echo ADMIN_URL?>/screens/show/<?php echo $screen->id ?>">
    <div class="roundcont roundcont_sf">
			<div class="roundtop"><span class="rt"><img src="<?php echo ADMIN_BASE_URL ?>/images/blsp.gif" height="6" width="1" alt="" /></span></div>
			<div class="roundcont_main sf">
				<img src="<?php echo ADMIN_BASE_URL?>/images/<?php echo $scrimg?>" 
                 height="100" alt="" onclick="window.location='<?php echo ADMIN_URL?>/screens/show/<?php echo $screen->id?>'" /><br />
				<div class="sf_header">
					<p style="color:<?php echo $statcolor ?>;"><?php echo $status ?></p>
					<h1><?php echo $screen->name?></h1>
					<h2><?php echo $screen->location?></h2>
				</div>
				<div style="clear:both;"></div>
			</div>
			<div class="roundbottom"><span class="rb"><img src="<?php echo ADMIN_BASE_URL ?>/images/blsp.gif" height="6" width="1" alt="" /></span></div>
		</div>
  </a>

<?php
}
?>
