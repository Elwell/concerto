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
?><form method="POST" action="<?=ADMIN_URL?>/groups/unsubscribe/<?=$this->group->id?>">
<select id="user" name="user">
<option value=""> </option>
<?php
if(is_array($this->users))
	foreach($this->users as $user)
		echo "   <option value=\"{$user->username}\"".($_REQUEST['user']==$user->username?" SELECTED":"").">$user->username - $user->name</option>\n";
?>
</select>
<input value="Remove User from Group" type="submit" name="submit" />
</form>

