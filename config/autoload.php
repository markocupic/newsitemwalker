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
      'Markocupic',
));

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'Markocupic\Newsitemwalker\ModuleNewsitemwalker' => 'system/modules/newsitemwalker/modules/ModuleNewsitemwalker.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_newsitemwalker' => 'system/modules/newsitemwalker/templates',
));
