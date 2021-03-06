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

require_once("hexdump.php");

function generate_signature($data) {
    # generate base64 encoded RSA signature for data
    $fp = fopen(PRIVATE_KEY_PATH, "r")
        or die("failed to load private key file!");
    $pktext = fread($fp, 65536); # private key should be no larger than this
    $privkey = openssl_get_privatekey($pktext)
        or die("failed to load private key data from file: " 
            . openssl_error_string( ));

    $sig = "";
    if (!openssl_sign($data, $sig, $privkey)) {
        die("Failed to digitally sign data: " . openssl_error_string( ));
    }

    return base64_encode($sig);
    
}

?>
