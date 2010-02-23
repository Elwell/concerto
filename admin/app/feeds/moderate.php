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
?><h2>Use the buttons to approve or deny each piece of content as you see appropriate for this feed.</h2>
<?php
if(is_array($this->contents))
{
foreach(array_keys($this->contents) as $field)
     $urls[]='<a href="#'.$field.'">'.$field.'</a>';
?>
<p>Jump to: <?php echo join(" | ", $urls)?>
</p>
<?php
} else {
	echo "<p>There is no content to be moderated.</p>";
}
if(is_array($this->contents))
foreach($this->contents as $field=>$contents)
{
   echo "<a name=\"$field\"></a><h1>$field</h1>";
?>
<table class="edit_win" cellpadding="6" cellspacing="0">
<?php
   $notfirst=0; //style for first row
   foreach($contents as $content) {
      $submitter = new User($content->user_id);
?>
  <tr>
<?php
      if(preg_match('/image/',$content->mime_type)) {
        $has_imagecol=1;
?>
    <td<?php if (!$notfirst) echo ' class="firstrow"'; ?>>
    <a href="<?php echo ADMIN_URL?>/content/show/<?php echo $content->id ?>"> 
    <img src="<?php echo ADMIN_URL?>/content/image/<?php echo $content->id ?>?width=200&height=150" />
    </a>
    </td>
<?php
      }
?>

    <td class="edit_col<?php if (!$notfirst) {$notfirst =1;  echo ' firstrow';} ?>"
        <?php if(!$has_imagecol) echo "colspan=2";?>>
      <a href="<?php echo ADMIN_URL?>/content/show/<?php echo $content->id ?>">
       <h1><a href="<?php echo ADMIN_URL?>/content/show/<?php echo $content->id ?>"><?php echo $content->name?></a></h1>
       <span style="font-size:1.5em;font-weight:bold;color:#333;margin-bottom:12px;">
<?php
          if($content->mime_type == "text/plain")
             echo "$content->content<br/>\n";
?>
       <?php echo date("m/j/Y",strtotime($content->start_time))?> - <?php echo date("m/j/Y",strtotime($content->end_time))?></span>
       <h2>Submitted by <strong><a href="<?php echo ADMIN_URL.'/users/show/'.$submitter->username?>"><?php echo $submitter->name?></a></strong></h2>
       <p><a href="<?php echo ADMIN_URL.'/feeds/approve/'.$this->feed->id.'/'.$content->id?>">Approve</a> |
          <a href="<?php echo ADMIN_URL.'/feeds/deny/'.$this->feed->id.'/'.$content->id?>">Deny</a></p>

      </a>
    </td>
  </tr>

<?php
   }
?>
</table>
<?php
}
?>
