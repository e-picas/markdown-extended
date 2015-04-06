<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Util;

use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\API\ContentInterface;

class ContentCollection
    extends \ArrayIterator
{

    public function __construct(array $data = array())
    {
        parent::__construct();
        foreach ($data as $item) {
            $this->append($item);
        }
    }

    public function append(ContentInterface $content)
    {
        parent::append($content);
    }

    public function offsetSet($index, $content)
    {
        if (Kernel::validate($content, Kernel::TYPE_CONTENT)) {
            parent::offsetSet($index, $content);
        }
    }

}

// Endfile
