<?php

/**
* Add palettes to tl_module
*/
$GLOBALS['TL_DCA']['tl_module']['palettes']['newsitemwalker'] = 'name,type;{config_legend},news_archives;{template_legend},newsItemWalkerTpl;{expert_legend:hide},cssID,space';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['newsItemWalkerTpl'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['newsItemWalkerTpl'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_newsitemwalker', 'getNewsitemwalkerTemplates'),
       'sql'                     => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['news_archives'] = array
(
       'label'                   => &$GLOBALS['TL_LANG']['tl_module']['news_archives'],
       'exclude'                 => true,
       'inputType'               => 'checkbox',
       'options_callback'        => array('tl_module_newsitemwalker', 'getNewsArchives'),
       'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'mandatory'=>true),
       'sql'                     => 'blob NULL'
);

/**
 * Class tl_module_newsitemwalker
 */
class tl_module_newsitemwalker extends Backend
{

    /**
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
     * @return array
     */
    public function getNewsitemwalkerTemplates()
    {
        return $this->getTemplateGroup('mod_newsitemwalker_');
    }

}
