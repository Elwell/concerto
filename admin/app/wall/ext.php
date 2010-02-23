<?php if (($this->feed->type == 3) || ($this->content->get_moderation_status($this->feed) != 1)){ ?>
  Invalid request.
<?php } else { ?>
<div id="overlay_graphic" style="text-align: center;">
  <a href="<?php echo ADMIN_URL ?>/content/show/<?php echo $this->content->id ?>" target="_blank"><img src="<?php echo ADMIN_URL ?>/content/image/<?php echo $this->content->id ?>?width=590&height=460" alt="<?php echo $this->content->name ?>"/></a>
</div>
<div id="overlay_details">
<?php
  /*For non-AJAX requests we'll center the text
    We also have to encode things a bit differently 
    for AJAX, like Marc's last name
  */
  if(isset($_REQUEST['ajax'])){
    $width_left = 24;
    $width_right = 74;
    $submitter = utf8_encode($this->submitter->name);
  } else {
    $width_left = 49;
    $width_right = 49;
    $submitter = $this->submitter->name;
  }
?>
	<div style="float:left; width:<?php echo $width_left ?>%;">
		<h2><span class="overlay_start"><?php echo date('M',strtotime($this->content->start_time)) ?> <span class="overlay_date"><?php echo date('j',strtotime($this->content->start_time)) ?></span></span><span class="overlay_to"> to </span><span class="overlay_end"><?php echo date('M',strtotime($this->content->end_time)) ?> <span class="overlay_date"><?php echo date('j',strtotime($this->content->end_time)) ?></span></span></h2>
	</div>
	<div style="float:right; width:<?php echo $width_right ?>%;">
		<h1><span>Feed:</span> <?php echo $this->feed->name ?></h1>
		<h1><span>By:</span> <?php echo htmlspecialchars($submitter) ?></h1>
	</div>
</div>
<?php if(!isset($_REQUEST['ajax'])){ ?>
<div id="bottomstrip">
        <div id="bottomstrip-padding">
                <a href="<?php echo ADMIN_URL ?>/wall/feedgrid/<?php echo $this->feed->id ?>">&lt;&lt; Back to the <?php echo $this->feed->name ?> Feed</a>
        </div>
</div>
<?php } ?>

<?php } ?>
