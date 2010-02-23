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
/*
Class: Upload
Status: Good to go
Functionality: Uploads content
	contructor		Takes the content and starts the upload process
	filer			Identify what a content is a route it the appropriate way
	typer			Read mime type (lame)
	xxx_cleaner		Applies any rules we need to filetype xxx, may pass off to another xxx cleaner
	mover			Moves the content to the right directory, and adds it to the system
	submit_tofeeds		Submits the content to the feeds, auto approving for owners
Comments: 
	The goal of upload is to process/clean things up before sending them to Content to be created.
	And then clean them up after content has had a chance to play.
	Cleaned.  Also added ppt and gif support, feeds now are a function
*/


//Reject Limits
define('MIN_W','400'); //Min width before we reject an image
define('MIN_H','400'); //Min height before we reject an image
//Resize Limits
define('MAX_W','1920'); //Max width before we resize an image
define('MAX_H','1280'); //Max height before we resize an image

//If you're using PHP < 5.2.1, we cannot figure out the temp directory automagically.
//This parameter will ONLY be used if the sys_get_temp_dir function does not exist (i.e. older PHP)
define('TEMP_DIR','/tmp/'); 

class Uploader{
	/*How to decypher this:
	U = Sent by upload form
	C = Used by content class
	I = Internal use only
	*/
	var $name; //UC
	var $start_date; //UC
	var $end_date; //UC
	var $feeds; //U
	var $type; //C
	var $duration; //UC
	var $content_i; //U
	var $content_o; //C
	var $mime_type; //C
	var $user_id; //UC
	
	var $ctype; //UI
	var $auto; //I
	var $status; //I  //The status, any errors or messages we want to pass on to users
	var $retval; //I  //Since a contructor can't return
	var $cid; //I //The ID of the content created (if you get that far)
	
	function __construct($name_in, $start_date_in, $end_date_in, $feeds_in, $duration_in, $content_i_in, $ctype_in, $user_id_in, $auto_in = 1){
	
		$this->name = $name_in;
		$this->start_date = $start_date_in;
		$this->end_date = $end_date_in;
		$this->duration = $duration_in;
		$this->content_i = $content_i_in;
		$this->ctype = $ctype_in;
		$this->user_id = $user_id_in;
		
		$this->feeds = $feeds_in;
		
		$this->auto = $auto_in; //This field specificies if the uploader should run in automatic mode or manual processing.  I like auto mode, but thats just me
		
		$this->status = "";

		if($this->auto){
			$this->filer();
		} else {
			$this->retval = true;
			return true;
		}
	}
	//Determines which steps need to be applied to the content
	function filer(){
		if(empty($this->feeds)){
		  $this->status = "No feeds selected";
		  $this->retval = false;
			return false;
		}
		if($this->ctype == 'text'){
			//Awsome, this is easy to handle!
                        
                        //Let's filter out any html/script injection.
                        $this->content_o = htmlspecialchars($this->content_i);
                        $this->name = htmlspecialchars($this->name);

                        $this->mime_type = 'text/plain';
			$this->type_id = 2; //SELF: THIS IS BAD AND DUMB AND STUPID
			$content = new Content();
			if($content->create_content($this->name, $this->user_id, $this->content_o, $this->mime_type, $this->type_id, $this->start_date, $this->end_date)){

				$this->cid = $content->id;
				
				$this->submit_tofeeds();
				
				$this->status = "";
				$this->retval = true;
				return true; //The content is finished uploading
			} else {
				$this->retval = false;
				$this->status = $this->status . 'Content Failed: ' . $content->status;
				return false; //Failure making a content isn't a good thing
			}
		
		} elseif($this->ctype == 'html'){
			//Awsome, this is easy to handle as well
			$this->content_o = $this->content_i;
			$this->mime_type = 'text/html';
			$this->type_id = 2; //SELF: THIS IS BAD AND DUMB AND STUPID
			$content = new Content();
			if($content->create_content($this->name, $this->user_id, $this->content_o, $this->mime_type, $this->type_id, $this->start_date, $this->end_date)){

				$this->cid = $content->id;
				
				$this->submit_tofeeds();
				
				$this->status = "";
				$this->retval = true;
				return true; //The content is finished uploading
			} else {
				$this->retval = false;
				$this->status = $this->status . $content->status;
				return false; //Failure making a content isn't a good thing
			}
		} elseif($this->ctype == 'dynamic'){
			//Awsome, this is equi easy to handle as well
			$this->content_o = $this->content_i;
			$this->mime_type = 'text/html';
			$this->type_id = 4; //SELF: THIS IS BAD AND DUMB AND STUPID
			$content = new Content();
			if($content->create_content($this->name, $this->user_id, $this->content_o, $this->mime_type, $this->type_id, $this->start_date, $this->end_date)){

				$this->cid = $content->id;
				
				$this->submit_tofeeds();
				
				$this->status = "";
				$this->retval = true;
				return true; //The content is finished uploading
			} else {
				$this->retval = false;
				$this->status = $this->status . $content->status;
				return false; //Failure making a content isn't a good thing
			}
		} elseif($this->ctype == 'file'){
			//echo "Identified a file upload";
			if($this->content_i['error'] == 0 && is_uploaded_file($this->content_i['tmp_name'])){
				$pre_type = $this->typer();
				
				//echo "Type: $pre_type   ";
				if($pre_type == "image/jpeg" || $pre_type == "image/pjpeg" || $pre_type == "image/jpg"){
					//echo "Bananas";
					$this->jpeg_cleaner();
				} elseif ($pre_type == "image/png" || $pre_type == "image/x-png"){ //Wierd IE sends x-png
					$this->png_cleaner();
				} elseif ($pre_type == "image/gif"){
					$this->gif_cleaner();
				} elseif ($pre_type == "application/vnd.ms-powerpoint"){
					$this->ppt_cleaner();
				} elseif ($pre_type == "application/pdf"){
					$this->pdf_cleaner(); 
				} else {
					unlink($this->content_i['tmp_name']); //Delete it since its def a virus duh!
					$this->status = $this->status ."We could not recognize the type of file you submitted. ";
					$this->retval = false;
					return false; //Unknown filetype
				}
			} else {
				$this->retval = false;
				$this->status = "Error receiving your file.  Please contact an administrator if this error repeats. ";
				return false;
			}
		} else {
			$this->status = $this->status . "We could not recognize the uploader used. ";
			//Unknown ctype == bad
			$this->retval = false;
			return false;
		}
	}
	function typer(){
		//We could add enchanted MIME typing here, but for now we'll trust browsers
		return $this->content_i['type'];
	}
	function jpeg_cleaner($loc = ''){
		//echo "Starting JPEG cleaner";
		$temp_dir = $this->get_temp_dir();
		$temp_name = $this->user_id . "-" . time() . ".jpg";
		$temp_dest = $temp_dir . $temp_name;
		if($loc != ''){
			$temp_dest = $loc;
		} else {
			if(!move_uploaded_file($this->content_i['tmp_name'], $temp_dest)){
				$this->retval = false;
				$this->status = $this->status . "Permissions error, contact an administrator. [Type: J]";
				return false;
			}
		}
		chmod($temp_dest, 0644);
		//Now that we have the file and we know where it is, lets mess it up
		$src_img=imagecreatefromjpeg($temp_dest);

		$width=imageSX($src_img);
		$height=imageSY($src_img);
		//echo "Source $width x $height";
		if($width < MIN_W || $height < MIN_H){ //The image isn't big enough!
			unlink($temp_dest);
			$this->status = $this->status . "The image you submitted was too small. ";
			$this->retval = false;
			return false;
		} elseif($width > MAX_W || $height > MAX_H){  //The image is too large, resize it!
			//echo "Too large";
			$scale_x = MAX_W / $width;
			$scale_y = MAX_H / $height;
			
			if($scale_x >= $scale_y){ //Find the dimension that needs the most help
				$scale = $scale_y;
			} else {
				$scale = $scale_x;
			}
			$new_x = $width * $scale;
			$new_y = $height * $scale;
				
			$dest_img=ImageCreateTrueColor($new_x,$new_y);
        		imagecopyresampled($dest_img,$src_img,0,0,0,0,$new_x,$new_y,$width,$height);
        		imagejpeg($dest_img, $temp_dest, 100);
        		imagedestroy($dest_img);
        		imagedestroy($src_img);
        		
        		$this->mime_type = 'image/jpeg';
        		$this->type_id = 3; //SELF: THIS IS BAD AND DUMB AND STUPID
        		$this->status = $this->status . "Your image was successfully resized. ";
        		//echo "But we shrunk it!";
        		if($this->auto){
        			return $this->mover($temp_dest);
        		} else {
				$this->retval = true;
        			return true;
        		}
		} else {
			$this->mime_type = 'image/jpeg';
               		$this->type_id = 3; //SELF: THIS IS BAD AND DUMB AND STUPID
			//echo "Did not require resizing";
			if($this->auto){
        			return $this->mover($temp_dest);
	       		} else {
				$this->retval = true;
        			return true;
	       		}
		}
	}
	function png_cleaner($loc = ''){
		//echo "Starting PNG cleaner";
		$temp_dir = $this->get_temp_dir();
		$temp_name = $this->user_id . "-" . time() . ".png";
		$temp_dest = $temp_dir . $temp_name;
		if($loc != ''){
			$temp_dest = $loc;
		} else {
			if(!move_uploaded_file($this->content_i['tmp_name'], $temp_dest)){
				$this->retval = false;
				$this->status = $this->status . "Permissions error, contact an administrator. [Type: P]";
				return false;
			}
		}
		chmod($temp_dest, 0644);
		//Now that we have the file and we know where it is, lets mess it up
		$src_img=imagecreatefrompng($temp_dest);

		$width=imageSX($src_img);
		$height=imageSY($src_img);
		//echo "Source $width x $height";
		if($width < MIN_W || $height < MIN_H){ //The image isn't big enough!
			unlink($temp_dest);
			$this->status = $this->status . "The image you submitted was too small. ";
			$this->retval = false;
			return false;
		} elseif($width > MAX_W || $height > MAX_H){  //The image is too large, resize it!
			//echo "Too large";
			$scale_x = MAX_W / $width;
			$scale_y = MAX_H / $height;
			
			if($scale_x >= $scale_y){ //Find the dimension that needs the most help
				$scale = $scale_y;
			} else {
				$scale = $scale_x;
			}
			$new_x = $width * $scale;
			$new_y = $height * $scale;
				
			$dest_img=ImageCreateTrueColor($new_x,$new_y);
			
			//Respect transparency
			$alpha = imagecolortransparent($src_img);
			if($alpha >= 0){
				$color = imagecolorsforindex($dest_img, $alpha);
				$alpha = imagecolorallocate($dest_img, $color['red'], $color['green'], $color['blue']);
				imagefill($dest_img, 0, 0, $alpha);
				imagecolortransparent($dest_img, $alpha);
			} else {
				imagealphablending($dest_img, false);
				$color = imagecolorallocatealpha($dest_img, 0, 0, 0, 127);
				imagefill($dest_img, 0, 0, $color);
				imagesavealpha($dest_img, true);
			}
			//end respect
			
			
        		imagecopyresampled($dest_img,$src_img,0,0,0,0,$new_x,$new_y,$width,$height);
        		imagepng($dest_img, $temp_dest, 1);
        		imagedestroy($dest_img);
        		imagedestroy($src_img);
        		
        		$this->mime_type = 'image/png';
        		$this->type_id = 3; //SELF: THIS IS BAD AND DUMB AND STUPID
        		$this->status = $this->status . "Your image was successfully resized. ";
        		//echo "But we shrunk it!";
        		if($this->auto){
        			return $this->mover($temp_dest);
        		} else {
				$this->retval = true;
        			return true;
        		}
		} else {
			$this->mime_type = 'image/png';
               		$this->type_id = 3; //SELF: THIS IS BAD AND DUMB AND STUPID
			//echo "Did not require resizing";
			if($this->auto){
        			return $this->mover($temp_dest);
	       		} else {
				$this->retval = true;
        			return true;
	       		}
		}
	}	
	function gif_cleaner($loc = ''){
		//echo "Starting GIF cleaner";
		$temp_dir = $this->get_temp_dir();
		$temp_name = $this->user_id . "-" . time() . ".gif";
		$temp_dest = $temp_dir . $temp_name;
		if($loc != ''){
			$temp_dest = $loc;
		} else {
			if(!move_uploaded_file($this->content_i['tmp_name'], $temp_dest)){
				$this->retval = false;
				$this->status = $this->status . "Permissions error, contact an administrator. [Type: G]";
				return false;
			}
		}
		chmod($temp_dest, 0644);
		//Now that we have the file and we know where it is, lets mess it up
		$src_img=imagecreatefromgif($temp_dest);

		$width=imageSX($src_img);
		$height=imageSY($src_img);
		//echo "Source $width x $height";
		if($width < MIN_W || $height < MIN_H){ //The image isn't big enough!
			unlink($temp_dest);
			//echo "Too Small!";
			$this->status = $this->status . "The image you submitted was too small. ";
			$this->retval = false;
			return false;
		} elseif($width > MAX_W || $height > MAX_H){  //The image is too large, resize it!
			//echo "Too large";
			$scale_x = MAX_W / $width;
			$scale_y = MAX_H / $height;
				
			if($scale_x >= $scale_y){ //Find the dimension that needs the most help
				$scale = $scale_y;
			} else {
				$scale = $scale_x;
			}
			$new_x = $width * $scale;
			$new_y = $height * $scale;
			
			$dest_img=ImageCreateTrueColor($new_x,$new_y);
	        	imagecopyresampled($dest_img,$src_img,0,0,0,0,$new_x,$new_y,$width,$height);
        		imagegif($dest_img, $temp_dest);
       			imagedestroy($dest_img);
       			imagedestroy($src_img);
       		
       			$this->mime_type = 'image/gif';
       			$this->type_id = 3; //SELF: THIS IS BAD AND DUMB AND STUPID
       			//echo "But we shrunk it!";
       			$this->status = $this->status . "Your image was successfully resized. ";
        		if($this->auto){
       				return $this->mover($temp_dest);
       			} else {
				$this->retval = true;
       				return true;
       			}
		} else {
			$this->mime_type = 'image/gif';
          		$this->type_id = 3; //SELF: THIS IS BAD AND DUMB AND STUPID
			//echo "Did not require resizing";
			if($this->auto){
        			return $this->mover($temp_dest);
        		} else {
				$this->retval = true;
        			return true;
        		}
		}
	}

	function pdf_cleaner(){
		$temp_dir = $this->get_temp_dir();
		$temp_name = $this->user_id . "-" . time() . ".pdf";
		$temp_dest = $temp_dir . $temp_name;
		if(move_uploaded_file($this->content_i['tmp_name'], $temp_dest)){
			$source = $temp_dest;
			$target = $temp_dir . $this->user_id . "-" . time() . ".png";
			$command = "convert " . $source . " " . $target; //This command relies on Image Magick & GS to be installed
        		exec($command, $output, $return);
        		unlink($source);
        		if($return != 0){
				$this->status = $this->status . "Your PDF couldn't be converted to an image. ";
				$this->retval = false;
				return false;
			}
			$this->content_i['temp_name'] = $target;
			$this->content_i['type'] = "image/png";
			if($this->auto){
        			$this->png_cleaner($target);
        		} else {
				$this->retval = true;
        			return true;
        		}
		} else {
			$this->status = $this->status . "Permissions error, contact an administrator. [Type: P]";
			$this->retval = false;
			return false;
		}
	}
	function ppt_cleaner(){
		$temp_dir = $this->get_temp_dir();
		$temp_name = $this->user_id . "-" . time() . ".ppt";
		$temp_dest = $temp_dir . $temp_name;
		if(move_uploaded_file($this->content_i['tmp_name'], $temp_dest)){
			$source = $temp_dest;
			$target = $temp_dir . $this->user_id . "-" . time() . ".png";
			$command = COMMON_DIR . "scripts/DocumentConverter.py " . $source . " " . $target; //This command relies on open office
        		//echo $command;
			exec($command, $output, $return);
	        	//print_r($output); echo "RETURNING: $return..";
			unlink($source);
       		 	if($return != 0){
				$this->status = $this->status . "Your PPT couldn't be converted to an image. ";
				$this->retval = false;
				return false;
			}
			$this->content_i['temp_name'] = $target;
			$this->content_i['type'] = "image/png";
			$this->status = $this->status . "Please check to ensure your powerpoint was correctly converted. ";
			if($this->auto){
        			$this->png_cleaner($target);
        		} else {
				$this->retval = true;
        			return true;
        		}	
		} else {
                        $this->status = $this->status . "PPT permission overflow.  Please contact an administrator. ";
                        $this->retval = false;
                        return false;
		}
	} 
	function mover($current_loc){
		$this->content_o = $current_loc;
		$ext = substr(strrchr($current_loc, "."), 1);
		$content = new Content();
		//print_r($this);
		if($content->create_content($this->name, $this->user_id, $this->content_o, $this->mime_type, $this->type_id, $this->start_date, $this->end_date)){
			$this->cid = $content->id;
			
			$target_loc = IMAGE_DIR . $this->cid . "." . $ext;
			rename($current_loc, $target_loc);
			$content->content = $this->cid . "." . $ext;
			$content->set_properties();
			
			$this->submit_tofeeds();
			
			$this->retval = true;
			return true; //The content is finished uploading
		} else {
			$this->retval = false;
			$this->status = $this->status . $content->status;
			return false; //Failure making a content isn't a good thing
		}
	}
	function submit_tofeeds(){ //Submits the content to feeds, addressed the auto-approve issue for moderators
		foreach($this->feeds as $fid){
			$f = new Feed($fid);
			$u = new User($this->user_id);
			if($u->in_group($f->group_id)){
				$f->content_add($this->cid, 1, $u->id, $this->duration);
			} else {
				$f->content_add($this->cid, 'NULL', 'NULL', $this->duration);
			}
		}
	}
	
	//Small helper function to a temporary location
	function get_temp_dir(){
	  if(!function_exists('sys_get_temp_dir')){
	    $dir = TEMP_DIR; //The constant from the top of the file
	  } else {
	    $dir = sys_get_temp_dir();
	  }
	  $path = realpath($dir);
	  if(!$path){
	    $this->retval = false;
		$this->status = $this->status . " Unable to find a temporary directory. ";
		return false; //Failure to find a temp directory
	  } else {
	    return $path . '/';
	  }
	}
}
