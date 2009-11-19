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
?>
<? if ($this->feed->user_priv($_SESSION['user'], "edit")) { ?>
<a href="<?=ADMIN_URL.'/feeds/edit/'.$this->feed->id ?>"><span class="buttonsel"><div class="buttonleft"><img src="<?= ADMIN_BASE_URL ?>/images/buttonsel_left.gif" border="0" alt="" /></div><div class="buttonmid"><div class="buttonmid_padding">Edit Feed</div></div><div class="buttonright"><img src="<?= ADMIN_BASE_URL ?>/images/buttonsel_right.gif" border="0" alt="" /></div></span></a>
<a href="<?=ADMIN_URL.'/feeds/delete/'.$this->feed->id ?>"><span class="buttonsel"><div class="buttonleft"><img src="<?= ADMIN_BASE_URL ?>/images/buttonsel_left.gif" border="0" alt="" /></div><div class="buttonmid"><div class="buttonmid_padding">Delete Feed</div></div><div class="buttonright"><img src="<?= ADMIN_BASE_URL ?>/images/buttonsel_right.gif" border="0" alt="" /></div></span></a>
<div style="clear:both;height:12px;"></div>
<? } ?>

<?php
if($this->feed->user_priv($_SESSION['user'], "moderate")){
?>
<? if(strlen($this->feed->description)>0) { ?>
   <h3>Description:</h3>
   <p><?= $this->feed->description ?></p>
<? } ?>
<h3>Moderation status: <span class="emph"><a href="<?=ADMIN_URL?>/moderate/feed/<?=$this->feed->id?>"><?= $this->waiting > 0 ? $this->waiting : "No" ?> items awaiting moderation</a></span></h3>
<?
}
?>
<p>This feed is moderated by <a href="<?=ADMIN_URL.'/groups/show/'.$this->group->id?>"><?= htmlspecialchars($this->group->name) ?></a>.</p>
<?
if(($this->feed->type == 4) && ($this->feed->dyn->needs_update() > 0)){
?>
<p class="dyn_stat"><b>Currently Processing:</b>&nbsp;&nbsp;&nbsp;This dynamic feed has <?=$this->feed->dyn->needs_update()?> unprocessed item(s).  It should be ready within a couple minutes.</p>
<?
}
?>
<h3>Content</h3>
<ul>
<? if(is_array($this->feed->get_types())) foreach($this->feed->get_types() as $type_id => $type){ ?>
<li><a href="<?= ADMIN_URL ?>/browse/show/<?= $this->feed->id ?>/type/<?= $type_id ?><?= isset($this->args[2]) ? "/{$this->args[2]}" : "" ?>"><?= $type ?></a></li>
<? } ?>
</ul>

<h3>Feed Statistics</h3>
<ul>
<li>Active and Future Content: <?= $this->active_content ?></li>
<li>Expired Content: <?= $this->expired_content ?></li>
</ul>

<? $screens = $this->feed->get_screens(); ?>
<? if(is_array($screens) && count($screens)>0) { ?>
<h3>Active Screens</h3>
<ul>
<?php
$prev=NULL; //This will be the previuos screen listed's ID.
foreach ($screens as $screen) {
  if($prev!=$screen->id) { 
    if(isset($prev)) {
      echo ')</li>';
    }
    $prev=$screen->id;
?>
<li><?= $screen->name ?> 
(<?
  } else echo ', ';
?>
<?= $screen->field_name ?>
<? } ?>
<?
if(isset($prev)) {
   echo ')</li>';
}
?>
</ul>
<? } ?>
