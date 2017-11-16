<?php
/*
Plugin Name: Folding Category List (FoCaL)
Plugin URI: http://timtrott.co.uk/wordpress-folding-category-list/
Description: Enhanced Folding Category List for Wordpress 
Author: Tim Trott
Version: 2.0.7
Author URI: http://timtrott.co.uk/

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    
If you develop a plug-in based on this code I would appreciate 
some credit and a link to the above address if appropriate please.
*/

if ( class_exists('WP_Widget') ) 
{
	require(dirname(__FILE__).'/focal_wp28.php');
} 
else 
{
	require(dirname(__FILE__).'/focal_wp23.php');
} 

?>