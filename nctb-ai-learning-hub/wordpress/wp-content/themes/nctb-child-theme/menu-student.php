<?php
/*
 * Student Dashboard Menu
 */

$defaults = array(
    'theme_location'    => 'student',
    'container'        => 'div',
    'container_class'   => 'student-nav',
    'menu'            => 'student-dashboard',
    'menu_class'       => 'dashboard-nav',
    'items_wrap'      => '<ul id="%menu" class="%nav">%items</ul>',
    'depth'           => 1,
);

wp_nav_menu( $defaults );
?>