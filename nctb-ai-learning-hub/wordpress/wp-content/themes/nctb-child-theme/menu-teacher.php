<?php
/*
 * Teacher Dashboard Menu
 */

$defaults = array(
    'theme_location'    => 'teacher',
    'container'        => 'div',
    'container_class'   => 'teacher-nav',
    'menu'            => 'teacher',
    'menu_class'       => 'dashboard-nav',
    'items_wrap'      => '<ul id="%menu" class="%nav">%items</ul>',
    'depth'           => 1,
);

wp_nav_menu( $defaults );
?>