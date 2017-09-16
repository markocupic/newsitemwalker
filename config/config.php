<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @package Newsitemwalker
 * @author Marko Cupic, <m.cupic@gmx.ch>
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */



 

/**
 * -------------------------------------------------------------------------
 * FRONTEND MODULES
 * -------------------------------------------------------------------------
 */
array_insert($GLOBALS['FE_MOD'], 2, array
(
	'news' => array
	(
		'newsitemwalker'     => 'Markocupic\Newsitemwalker\ModuleNewsitemwalker'
	)
));