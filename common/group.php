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
Class: Group
Status: Working
Functionality: 
       create_group       		Creates a new group
       get_members			Gets all the members in a group
       list_members			Lists all members in a group
       destroy				Destroys a group, can 'remove' or 'reown' everything owned by that group
Comments: 
  
*/
class Group{
	var $id;
	var $name;
	
	var $set;
	
	function __construct($id_in = ''){
		if($id_in !== '' && is_numeric($id_in)){
			$sql = "SELECT * FROM `group` WHERE id = $id_in LIMIT 1";
			$res = sql_query($sql);
			if($res && ($data = sql_row_keyed($res,0))){
				$this->id = $data['id'];
				$this->name = $data['name'];
				
				$this->set = true;
				return true;
			} else {
			  $this->set = false;
				return false;
			}
		} else {
			$this->set = false;
			return true;
		}
	}
	
	function create_group($name_in){
		if($this->set == true){
			return false; //Someone isn't wearing a seatbelt
		} else {
			$name_in = escape($name_in);
			$sql = "INSERT INTO `group` (name) VALUES ('$name_in')";
			$res = sql_query($sql);
			if($res){
				$sql_id = sql_insert_id();
				
				$this->id = $sql_id;
				$this->name = $name_in;
				
				$this->set = true;
				return true;
			} else {
				return false;
			}
		}
	}
	
	function get_members(){
		$sql = "SELECT user_id FROM user_group WHERE group_id = $this->id";
		$res = sql_query($sql);
		$i = 0;
		$found = false;
		while($row = sql_row_keyed($res,$i)){
			$found = true;
			$user_id = $row['user_id'];
			$sql2 = "SELECT username FROM user WHERE id = $user_id LIMIT 1";
			$res2 = sql_query($sql2);
			$user_row = sql_row_keyed($res2,0);
			$username = $user_row['username'];
			$data[] = new User($username);
			$i++;
		}
		if($found){
			return $data;
		} else {
			return false;
		}
	}

	function list_members(){
	        $sql = "SELECT username FROM user_group LEFT JOIN user ON user_group.user_id = user.id WHERE group_id = $this->id";
                $res = sql_query($sql);
                $i = 0;
                $found = false;
                while($row = sql_row_keyed($res,$i)){
                        $found = true;
                        $username = $row['username'];
                        $data[] = $username;
                        $i++;
                }
                if($found){
                        return $data;
                } else {
                        return false;
                }
	}
	
	function destroy($type = 'reown', $new_owner = 0){
		$ret = true;
		if(!is_numeric($new_owner)){
		  return false;
		}
		if($users = $this->get_members()){
			foreach($users as $user){
				$ret = $ret * $user->remove_from_group($this->id);
			}
		}
		//This code is poorly implemented, I want to clean it up at a later point.
		if($type == 'reown'){
			if($ret){
				$sql = "UPDATE feed SET group_id = $new_owner WHERE group_id = $this->id"; //Handle all feeds that were owned by that group
				$res = sql_query($sql);
				if(!$res){
					return false; //Error updating feeds
				}
				$sql = "UPDATE screen SET group_id = $new_owner WHERE group_id = $this->id"; //Handle all screens owned by that group
				$res = sql_query($sql);
				if(!$res){
					return false; //Error screens feeds
				}
			} else {
				return false; //Errore removing all users from that group!
			}
		} elseif($type == 'remove'){
			if($ret){
				$base = new Feed();
				$ret = true;
				if($feeds = $base->get_all("WHERE group_id = $this->id")){
					foreach($feeds as $feed){
						$ret = $ret * $feed->destroy();
					}
					if(!$ret){
						return false; //Error with the feeds
					}
				}
				$base2 = new Screen();
				if($screens = $base2->get_all("WHERE group_id = $this->id")){
					foreach($screens as $screen){
						$ret = ret * $screen->destroy();
					}
					if(!$ret){
						return false; //Error with the screens
					}
				}
			} else {
				return false; //Errore removing all users from that group!
			}
		}
		$sql = "DELETE FROM `group` WHERE id = $this->id LIMIT 1";
		$res = sql_query($sql);
		if(!$res){
			return false; //Error with the last delete
		}
		
		$this->id = '';
		$this->name = '';
		$this->set = false;

		return true;
	}

	function send_mail($subject, $msg, $from='', $forward=false){
		$users = $this->get_members();
		$retval = true;
		foreach($users as $user){
			$retval = $retval * $user->send_mail($subject, $msg, $from, $forward);
		}
		return $retval;
	}
}
?>
