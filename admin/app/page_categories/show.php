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
?><a href="<?php echo ADMIN_URL.'/page_categories/edit/'.$this->category['id'] ?>"><span class="buttonsel"><div class="buttonleft"><img src="<?php echo ADMIN_BASE_URL ?>/images/buttonsel_left.gif" border="0" alt="" /></div><div class="buttonmid"><div class="buttonmid_padding">Edit Category</div></div><div class="buttonright"><img src="<?php echo ADMIN_BASE_URL ?>/images/buttonsel_right.gif" border="0" alt="" /></div></span></a> 
<a href="<?php echo ADMIN_URL.'/page_categories/delete/'.$this->category['id'] ?>"><span class="buttonsel"><div class="buttonleft"><img src="<?php echo ADMIN_BASE_URL ?>/images/buttonsel_left.gif" border="0" alt="" /></div><div class="buttonmid"><div class="buttonmid_padding">Delete Category</div></div><div class="buttonright"><img src="<?php echo ADMIN_BASE_URL ?>/images/buttonsel_right.gif" border="0" alt="" /></div></span></a><div style="clear:both;height:12px;"></div>

<h3>Category Name: <span class="emph"><?php echo $this->category['name'] ?></span></h3>
<h3>Path: <span class="emph"><a href="<?php echo ADMIN_URL.'/pages/show/'.$this->category['path']?>"><?php echo $this->category['path'] ?></a></span></h3>
<h3>Layout: <span class="emph"><?php echo $this->category['layout']?></span></h3>
<h3>Default Page: <span class="emph"><a href="<?php echo ADMIN_URL.'/pages/show/'.$this->category['path']?>"><?php echo $this->category['default_page_name'] ?></a></span></h3>
<h3>Pages: <span class="emph"><?php echo $this->count ?></span> <a href="<?php echo ADMIN_URL.'/pages'?>">(see listing of pages)</a></h3>
