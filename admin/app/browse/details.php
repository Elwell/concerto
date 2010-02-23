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
?><table>
<tr>
<td class="preview <?php if(preg_match('/text/',$this->content->mime_type)) { echo " text_bg"; } ?>" style="width:250px">
<?php if(preg_match('/image/',$this->content->mime_type)) { ?>
    <a id="i-preview" href="<?php echo ADMIN_URL ?>/content/image/<?php echo $this->content->id ?>"><img src="<?php echo ADMIN_URL ?>/content/image/<?php echo $this->content->id ?>?width=250&amp;height=200" alt="" /></a>
<?php } elseif(preg_match('/text/',$this->content->mime_type)) { ?>
    <span class="emph"><?php echo $this->content->content ?></span>
<?php } ?>
</td>
<td>
    <h1><a href="<?php echo ADMIN_URL ?>/content/show/<?php echo $this->content->id ?>"><?php echo $this->content->name ?></a>
<?php
        if($this->feed->user_priv($_SESSION['user'], 'moderate')) {
           if($this->status==0) {
              echo '&nbsp;&nbsp;&nbsp;<small><a title="Approve Content for Feed" href="'.ADMIN_URL.'/moderate/confirm/approve/'.$this->feed->id.'/'.$this->content->id.'"><img src="'. ADMIN_BASE_URL . '/images/icon_approve.gif" border="0" alt=""  /> <span style="color:green !important; font-size:0.8em;">Approve</span></a></small>';
           } elseif ($this->status==1) {
              echo '&nbsp;&nbsp;&nbsp;<small><a title="Remove Content from Feed" href="'.ADMIN_URL.'/moderate/confirm/deny/'.$this->feed->id.'/'.$this->content->id.'"><img src="'. ADMIN_BASE_URL . '/images/icon_disapprove.gif" border="0" alt="" /> <span style="color:red !important; font-size:0.8em;">Disapprove</span></a></small>';
           }
        }
?>  </h1>
    <span style="font-size:1.5em;font-weight:bold;color:#333;margin-bottom:12px;"><?php echo date('M j, Y',strtotime($this->content->start_time)) ?> - <?php echo date('M j, Y',strtotime($this->content->end_time)) ?></span> <?php if($this->week_range > 1) echo "({$this->week_range} Weeks)" ?>
    <h2>Display duration: <span class="emph"><img src="<?php echo ADMIN_BASE_URL ?>/images/stopwatch.gif" alt="Duration" /> <?php echo $this->dur_str?></span></h2>
    <h2>Submitted by <strong><a href="<?php echo ADMIN_URL ?>/users/show/<?php echo $this->submitter->id ?>"><?php echo $this->submitter->name ?></a></strong></h2>
<?php if($this->moderator->id != false) { ?>
    <h2>Moderated by <strong><a href="<?php echo ADMIN_URL ?>/users/show/<?php echo $this->moderator->id ?>"><?php echo $this->moderator->name ?></a></strong></h2>
<?php } ?>
</td>
</tr>
</table>
