<div id="feedgrid">
<?php if(strlen($this->feed->name) <= 0 || $this->feed->type == 3) { ?>
  Invalid request.
<?php } else { ?>
<div id="wallthumbs">
  <?php foreach ($this->contents as $content) { ?>
    <div class="UIWall_thumb">
      <a class="overlayTrigger" href="<?php echo ADMIN_URL ?>/wall/ext/<?php echo $this->feed->id ?>/<?php echo $content->id ?>" rel="#oz">
        <div class="UIWall_wrapper">
          <img class="UIWall_image" src="<?php echo ADMIN_URL ?>/content/image/<?php echo $content->id ?>?width=200&height=150" alt="<?php echo $content->name ?>" />
        </div>
      </a>
    </div>
  <?php } ?>
</div>
<?php if(!isset($_REQUEST['ajax'])){ ?>
<div id="bottomstrip">
        <div id="bottomstrip-padding">
                <a href="<?php echo ADMIN_URL ?>/wall/">&lt;&lt; Back to the Concerto Wall</a>
        </div>
</div>
<?php } ?>

<?php } ?>
</div>
