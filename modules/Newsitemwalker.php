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
 * Run in a custom namespace, so the class can be replaced
 */
namespace MCupic;


/**
 * Class Newsitemwalker
 *
 * @copyright  Marko Cupic 2011
 * @author     Marko Cupic
 * @package Newsitemwalker
 */
class Newsitemwalker extends \Module
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_newsitemwalker_default';

    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {

        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### NEWS-ITEM-WALKER ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&table=tl_module&act=edit&id=' . $this->id;
            return $objTemplate->parse();
        }

        // Set the item from the auto_item parameter
        if ($GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
        {
            \Input::setGet('items', \Input::get('auto_item'));
        }

        // Return if no news item has been specified
        if (!\Input::get('items'))
        {
            return '';
        }

        return parent::generate();
    }

    /**
     * Generate module
     */
    protected function compile()
    {

        global $objPage;
        if ($this->newsItemWalkerTpl == "")
        {
            $this->newsItemWalkerTpl = $this->strTemplate;
        }
        $this->Template = new \FrontendTemplate($this->newsItemWalkerTpl);


        //get the pid of the current item
        $objCurrentItem = $this->Database->prepare("SELECT date, pid FROM tl_news WHERE id=? OR alias=?")->limit(1)->execute(\Input::get('items'), \Input::get('items'));

        // backwards compatibility
        $queryStr = "pid=" . $objCurrentItem->pid;

        //build query if other news-archives are selected
        $arrNewsArchives = deserialize($this->news_archives);
        if (is_array($arrNewsArchives) && !empty($arrNewsArchives))
        {
            $queryStr = "pid IN (" . implode(',', $arrNewsArchives) . ")";
        }

        // current time
        $time = time();

        //previous news item
        $objPrevArticle = $this->Database->prepare("SELECT id,alias FROM tl_news WHERE date<? AND (" . $queryStr . ") AND (start='' OR start<?) AND (stop='' OR stop>?) AND published=1 ORDER BY date DESC")->limit(1)->execute($objCurrentItem->date, $time, $time);
        if ($objPrevArticle->numRows > 0)
        {
            $prevHref = $this->generateFrontendUrl($objPage->row(), ($GLOBALS['TL_CONFIG']['useAutoItem'] ? '/' : '/items/') . ($objPrevArticle->alias != "" ? $objPrevArticle->alias : $objPrevArticle->id));
            $this->Template->prevHref = $prevHref;
            $this->Template->prevLink = '<a href="' . $prevHref . '" title="' . $GLOBALS['TL_LANG']['MSC']['prevArticle'][1] . '">' . $GLOBALS['TL_LANG']['MSC']['prevArticle'][0] . '</a>';
            $objNews = \NewsModel::findByPk($objPrevArticle->id);
            if ($objNews !== null)
            {
                $this->Template->prevNews = $objNews->row();
            }
        }


        //next news item
        $objNextArticle = $this->Database->prepare("SELECT id,alias FROM tl_news WHERE date>? AND (" . $queryStr . ") AND (start='' OR start<?) AND (stop='' OR stop>?) AND published=1 ORDER BY date ASC")->limit(1)->execute($objCurrentItem->date, $time, $time);
        if ($objNextArticle->numRows > 0)
        {
            $nextHref = $this->generateFrontendUrl($objPage->row(), ($GLOBALS['TL_CONFIG']['useAutoItem'] ? '/' : '/items/') . ($objNextArticle->alias != "" ? $objNextArticle->alias : $objNextArticle->id));
            $this->Template->nextHref = $nextHref;
            $this->Template->nextLink = '<a href="' . $nextHref . '" title="' . $GLOBALS['TL_LANG']['MSC']['nextArticle'][1] . '">' . $GLOBALS['TL_LANG']['MSC']['nextArticle'][0] . '</a>';
            $objNews = \NewsModel::findByPk($objNextArticle->id);
            if ($objNews !== null)
            {
                $this->Template->nextNews = $objNews->row();
            }
        }

    }
}