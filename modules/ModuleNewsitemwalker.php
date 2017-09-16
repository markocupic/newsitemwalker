<?php

namespace Markocupic\Newsitemwalker;


use Contao\BackendTemplate;
use Contao\Input;
use Contao\FrontendTemplate;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use Contao\Module;
use Contao\PageModel;
use Contao\Config;
use Contao\Database;


/**
 * Class ModuleNewsitemwalker
 * @package Markocupic\Newsitemwalker
 */
class ModuleNewsitemwalker extends Module
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_newsitemwalker';

    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {

        if (TL_MODE == 'BE')
        {
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['newsitemwalker'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&table=tl_module&act=edit&id=' . $this->id;
            return $objTemplate->parse();
        }

        // Set the item from the auto_item parameter
        if (Config::get('useAutoItem') && isset($_GET['auto_item']))
        {
            Input::setGet('items', Input::get('auto_item'));
        }

        // Return if no news item has been specified
        if (!Input::get('items'))
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
        $objPageModel = PageModel::findByPk($objPage->id);

        if ($this->newsItemWalkerTpl == "")
        {
            $this->newsItemWalkerTpl = $this->strTemplate;
        }
        $this->Template = new FrontendTemplate($this->newsItemWalkerTpl);


        //get the pid of the current item
        $objCurrentItem = $this->Database->prepare("SELECT date, pid FROM tl_news WHERE id=? OR alias=?")->limit(1)->execute(Input::get('items'), Input::get('items'));

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


        // Get tagged news items and create sql string
        $arrFilter = $this->getTaggedNewsItems();
        $strFilter = '';
        if (count($arrFilter) > 0)
        {
            $strFilter .= ' AND id IN (' . implode(',', $arrFilter) . ')';
        }


        //previous news item
        $objPrevArticle = $this->Database->prepare("SELECT id,alias FROM tl_news WHERE date<? AND (" . $queryStr . ")" . $strFilter . " AND (start='' OR start<?) AND (stop='' OR stop>?) AND published=1 ORDER BY date DESC")->limit(1)->execute($objCurrentItem->date, $time, $time);
        if ($objPrevArticle->numRows > 0)
        {

            $prevHref = $objPageModel->getFrontendUrl((Config::get('useAutoItem') ? '/' : '/items/') . ($objPrevArticle->alias ?: $objPrevArticle->id));
            $this->Template->prevHref = $prevHref;
            $this->Template->prevLink = '<a href="' . $prevHref . '" title="' . $GLOBALS['TL_LANG']['MSC']['prevArticle'][1] . '">' . $GLOBALS['TL_LANG']['MSC']['prevArticle'][0] . '</a>';
            $objNews = NewsModel::findByPk($objPrevArticle->id);
            if ($objNews !== null)
            {
                $this->Template->prevNews = $objNews->row();
                $this->Template->prevId = $objPrevArticle->id;
                $objNewsArchivePrev = NewsArchiveModel::findByPk($objPrevArticle->pid);
                if ($objNewsArchivePrev !== null)
                {
                    $this->Template->prevNewsParentArchive = $objNewsArchivePrev->row();
                }
            }
        }


        //next news item
        $objNextArticle = $this->Database->prepare("SELECT id,alias FROM tl_news WHERE date>? AND (" . $queryStr . ")" . $strFilter . " AND (start='' OR start<?) AND (stop='' OR stop>?) AND published=1 ORDER BY date ASC")->limit(1)->execute($objCurrentItem->date, $time, $time);
        if ($objNextArticle->numRows > 0)
        {
            $nextHref = $objPageModel->getFrontendUrl((Config::get('useAutoItem') ? '/' : '/items/') . ($objNextArticle->alias ?: $objNextArticle->id));
            $this->Template->nextHref = $nextHref;
            $this->Template->nextLink = '<a href="' . $nextHref . '" title="' . $GLOBALS['TL_LANG']['MSC']['nextArticle'][1] . '">' . $GLOBALS['TL_LANG']['MSC']['nextArticle'][0] . '</a>';
            $objNews = NewsModel::findByPk($objNextArticle->id);
            if ($objNews !== null)
            {
                $this->Template->nextNews = $objNews->row();
                $this->Template->nextId = $objNextArticle->id;
                $objNewsArchiveNext = NewsArchiveModel::findByPk($objNextArticle->pid);
                if ($objNewsArchiveNext !== null)
                {
                    $this->Template->nextNewsParentArchive = $objNewsArchiveNext->row();
                }
            }
        }
    }

    /**
     * @return array
     */
    private function getTaggedNewsItems()
    {
        $arrReturn = array();
        if(Database::getInstance()->tableExists('tl_tag'))
        {
            if (strlen($this->tag_filter))
            {
                $tags = preg_split("/,/", $this->tag_filter);
                $placeholders = array();
                foreach ($tags as $tag)
                {
                    array_push($placeholders, '?');
                }
                array_push($tags, 'tl_news');
                $arrReturn = $this->Database->prepare("SELECT tid FROM tl_tag WHERE tag IN (" . join($placeholders, ',') . ") AND from_table = ? ORDER BY tag ASC")
                    ->execute($tags)
                    ->fetchEach('tid');
            }
        }

        return $arrReturn;
    }
}