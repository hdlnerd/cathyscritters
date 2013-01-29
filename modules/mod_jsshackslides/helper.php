<?php

/**
 * @version   1.x
 * @package   ShackSlides
 * @copyright (C) 2010 Joomlashack / Meritage Assets Corp
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Direct access to files is not permitted');

abstract class ModShackSlidesHelper
{

    protected $images;
    protected $titles;
    protected $links;
    protected $base;
    protected $menu;

    public function __construct($params)
    {
        $this->menu = JMenu::getInstance('site')->getMenu();
        $this->setBase(JURI::base());
    }

    public function getImages()
    {
        return $this->images;
    }

    protected function setImages($images)
    {
        if (is_array($images))
        {
            $this->images = $images;
        }
        else
        {
            $this->images[] = $images;
        }
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function setLinks($links)
    {
        if (is_array($links))
        {
            $this->links = $links;
        }
        else
        {
            $this->links[] = $links;
        }
    }

    public function getBase()
    {
        return $this->base;
    }

    protected function setBase($base)
    {
        $this->base = $base;
    }

    public function getTitles()
    {
        return $this->titles;
    }

    public function setTitles($titles)
    {
        if (is_array($titles))
        {
            $this->titles = $titles;
        }
        else
        {
            $this->titles[] = $titles;
        }
    }

    protected function getFirstImageFromContent($content)
    {
        preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $content, $matches);

        if (isset($matches[1]))
        {
            $image = $matches[1];
            return $image;
        }
        else
            return false;
    }

    protected function getTitleFromContent($content)
    {
        $title = strip_tags($content, '<p><h1><h2><h3><h4><h5><h6><span><b><i><u><strong><em><br>');

		if (trim($title) == '') return false;

		else return $title;
    }

    protected function buildLinkFromFields($fields)
    {
        $query = implode('&', $fields);

		return JRoute::_('index.php?'.$query);
    }

    protected function buildQueryOrder()
    {

    }

    protected function compareQuery($fields)
    {
        foreach ($this->menu as $item)
        {
            $diff = array_diff_assoc($fields, $item->query);
            if (!count($diff)) return $item->id;
            continue;
        }
        return false;
    }

}