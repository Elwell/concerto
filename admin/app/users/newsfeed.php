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
?><script type="text/javascript"><!--
$(function()
{
   $("#news_expand").data('items',5);
   
   $("#news_expand").click(function(event) {
         $.post("<?= ADMIN_URL ?>/users/notifications", {'start': $("#news_expand").data('items'), 'num': 5}, function(data) {
               var x = $("<div>"+data+"</div>");
               $("#news_expand").before(x.hide());
               x.slideDown("slow");
               $("#news_expand").data('items',$("#news_expand").data('items')+5);
            });
         return false;
      });
 }); 
//--></script>


<div class="roundcont newsfeed">
  <div class="roundtop"><span class="rt"><img src="<? echo ADMIN_BASE_URL ?>/images/blsp.gif" height="6" width="1" alt="" /></span></div>
  <div class="roundcont_main">
    <div style="text-align:right; float:right; width:240px;">
    <? $num = count($this->notifications) ?>
   	<? if($this->page > 0) { ?>
   		<a href="<?= ADMIN_URL ?>/users/newsfeed/<?= userName() ?>/0"><span class="buttonsel"><div class="buttonleft"><img src="<?= ADMIN_BASE_URL ?>/images/buttonsel_left.gif" border="0" alt="" /></div><div class="buttonmid"><div class="buttonmid_padding">|<</div></div><div class="buttonright" style="width:10px; padding-right:12px;"><img src="<?= ADMIN_BASE_URL ?>/images/buttonsel_right.gif" border="0" alt="" /></div></span></a>
   	<? } ?>
    <? if($this->page > 0) {?>
			<a href="<?= ADMIN_URL ?>/users/newsfeed/<?= userName() ?>/<?= $this->page - 1?>"><span class="buttonsel"><div class="buttonleft"><img src="<?= ADMIN_BASE_URL ?>/images/buttonsel_left.gif" border="0" alt="" /></div><div class="buttonmid"><div class="buttonmid_padding"><<</div></div><div class="buttonright" style="width:10px; padding-right:12px;"><img src="<?= ADMIN_BASE_URL ?>/images/buttonsel_right.gif" border="0" alt="" /></div></span></a>
    <? } ?>
    <? if(($num>1) && $this->page < floor($this->notification_count / 25)) {?>
			<a href="<?= ADMIN_URL ?>/users/newsfeed/<?= userName() ?>/<?= $this->page + 1?>"><span class="buttonsel"><div class="buttonleft"><img src="<?= ADMIN_BASE_URL ?>/images/buttonsel_left.gif" border="0" alt="" /></div><div class="buttonmid"><div class="buttonmid_padding">>></div></div><div class="buttonright" style="width:10px; padding-right:12px;"><img src="<?= ADMIN_BASE_URL ?>/images/buttonsel_right.gif" border="0" alt="" /></div></span></a>
    <? } ?>
   	<? if($this->page < $num && $this->page < floor($this->notification_count / 25)) { ?>
   		<a href="<?= ADMIN_URL ?>/users/newsfeed/<?= userName() ?>/<?= floor($this->notification_count / 25) ?>"><span class="buttonsel"><div class="buttonleft"><img src="<?= ADMIN_BASE_URL ?>/images/buttonsel_left.gif" border="0" alt="" /></div><div class="buttonmid"><div class="buttonmid_padding">>|</div></div><div class="buttonright" style="width:10px; padding-right:12px;"><img src="<?= ADMIN_BASE_URL ?>/images/buttonsel_right.gif" border="0" alt="" /></div></span></a>
   	<? } ?>
    </div>
<? if ($num>0) { ?>
    <h2>Showing items <?= $this->start+1 ?> to <?= $this->start + count($this->notifications) ?> of <?= $this->notification_count ?> total notifications</h2>
    <?php 
    if(is_array($this->notifications)) {
       foreach($this->notifications as $newsfeed) {
    ?>
    	<p class="<?= $newsfeed->type ?>_<?= $newsfeed->msg ?>"><?= $newsfeed->text ?><span class="datesub"><?= date('M j', $newsfeed->timestamp) ?></span>
        <?php
          if($newsfeed->has_extra){
            echo '<br/><span class="newsfeed_reason">'.$newsfeed->additional.'</span>';
          }
        ?>
      </p><?php
       }
    }
    ?>
<? } else { ?>
    <h2>No more items to display.</h2>
<? } ?>
  </div>
  <div class="roundbottom"><span class="rb"><img src="<? echo ADMIN_BASE_URL ?>/images/blsp.gif" height="6" width="1" alt="" /></span></div>
</div>
