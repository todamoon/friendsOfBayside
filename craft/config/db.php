<?php

/**
 * Database Configuration
 *
 * All of your system's database configuration settings go in here.
 * You can see a list of the default settings in craft/app/etc/config/defaults/db.php
 */

return array(

	'*' => array(
		'tablePrefix' => 'craft',
    'database' => 'lHenry_craft',
		'server' => 'localhost'

  ),

  '.dev' => array(
    'user' => 'root',
    'password' => 'root'
  ),

	'.studio' => array(
		'user' => 'forge',
		'password' => 'LJew7dPmlBpD3Vmlx4u0'
  )


);
