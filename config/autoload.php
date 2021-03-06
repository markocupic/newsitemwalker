<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
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
    // Modules
    'Markocupic\Newsitemwalker\ModuleNewsitemwalker' => 'system/modules/newsitemwalker/modules/ModuleNewsitemwalker.php',

));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'mod_newsitemwalker' => 'system/modules/newsitemwalker/templates',
));
