<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Newsitemwalker
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
      'MCupic',
));

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'MCupic\Newsitemwalker' => 'system/modules/newsitemwalker/modules/Newsitemwalker.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_newsitemwalker_default' => 'system/modules/newsitemwalker/templates',
));
