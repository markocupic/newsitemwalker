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
* Add palettes to tl_module
*/
$GLOBALS['TL_DCA']['tl_module']['palettes']['newsitemwalker'] = 'name,type;{config_legend},news_archives;{template_legend},newsItemWalkerTpl;{expert_legend:hide},cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['newsItemWalkerTpl'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['newsItemWalkerTpl'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('Mod_NewsItemWalker', 'getTemplates'),
       'sql'                     => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['news_archives'] = array
(
       'label'                   => &$GLOBALS['TL_LANG']['tl_module']['news_archives'],
       'exclude'                 => true,
       'inputType'               => 'checkbox',
       'options_callback'        => array('Mod_NewsItemWalker', 'getNewsArchives'),
       'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'mandatory'=>true),
       'sql'                     => 'blob NULL'
);

/**
 * Class Mod_NewsItemWalker
 *
 * @copyright  Marko Cupic 2011
 * @author     Marko Cupic
 * @package Newsitemwalker
 */
class Mod_NewsItemWalker extends Backend
{
	
	/**
	 * Get all news archives and return them as array
	 * @return array
	 */
	public function getNewsArchives()
	{

		$arrArchives = array();
		$objArchives = $this->Database->execute("SELECT id, title FROM tl_news_archive ORDER BY title");

		while ($objArchives->next())
		{
                     $arrArchives[$objArchives->id] = $objArchives->title;
		}

		return $arrArchives;
	}


	/**
	 * Return all NewsItemWalker templates as array
	 * @param object
	 * @return array
	 */
	public function getTemplates(DataContainer $dc)
	{
		// Get the page ID
		$objArticle = $this->Database->prepare("SELECT pid FROM tl_article WHERE id=?")
									 ->limit(1)
									 ->execute($dc->activeRecord->pid);

		// Inherit the page settings
		$objPage = $this->getPageDetails($objArticle->pid);

		// Get the theme ID
		$objLayout = $this->Database->prepare("SELECT pid FROM tl_layout WHERE id=?")
                                          ->limit(1)
                                          ->execute($objPage->layout);

		// Return all gallery templates
		return $this->getTemplateGroup('mod_newsitemwalker_', $objLayout->pid);
	}

}
