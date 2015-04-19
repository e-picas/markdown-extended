<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Util\Menu;

/**
 * This class analyzes an HTML content to extract its menu (based on `h...` tags)
 */
class DOMMenu
    extends \DOMDocument
{

    /**
     * @var \MarkdownExtended\Util\Menu\Menu
     */
    protected $toc;

    /**
     * Initialize a builder
     *
     * @param $content
     */
    public function __construct($content)
    {
        @$this->loadHTML($content);
    }

    /**
     * Gets the menu as an array
     *
     * @return array
     */
    public function getMenu()
    {
        $this->_buildMenu();
        return $this->toc->getItems();
    }

    /**
     * Actually builds the menu by extracting `h...` tags from concerned content
     */
    protected function _buildMenu()
    {
        $this->toc  = new Menu;
        $xpath      = new \DOMXPath($this);
        $entries    = $xpath->query('//h1|//h2|//h3|//h4|//h5|//h6');
        foreach ($entries as $header) {
            /* @var $header \DOMElement */
            $level = (int) substr($header->tagName, 1, 1);
            $item = new MenuItem(
                trim($header->nodeValue),
                $level,
                array( 'id' => $header->getAttribute('id') )
            );
            $this->toc->addItem($item);
        }
    }

}
