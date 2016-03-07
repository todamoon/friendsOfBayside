<?php

/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here.
 * You can see a list of the default settings in craft/app/etc/config/defaults/general.php
 */

return array(

	'*' => array(
		'omitScriptNameInUrls' => true,
		'useEmailAsUsername' => true,
		'autoLoginAfterAccountActivation' => true,
		'generateTransformsAfterPageLoad' => false,
		'devMode' => false,
		'extraAllowedFileExtensions' => 'sql'

	),

  '.dev' => array(
    'devMode' => true,
    'backupDbOnUpdate' => false,
    'siteUrl' => 'http://leah.dev/',
		'usePathInfo' => false

  ),

	'.studio' => array(
		'devMode' => false,
		'generateTransformsAfterPageLoad' => false,
		'siteUrl' => 'http://hileah.com/',
		'usePathInfo' => true

	)


);
