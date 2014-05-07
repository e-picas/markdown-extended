<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2014 Pierre Cassat
 *
 * original MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * original PHP Markdown & Extra
 * Copyright (c) 2004-2012 Michel Fortin  
 * <http://michelf.com/projects/php-markdown/>
 *
 * original Markdown
 * Copyright (c) 2004-2006 John Gruber  
 * <http://daringfireball.net/projects/markdown/>
 */
namespace testsMarkdownExtended;

class MarkdownExtendedOldInterfaceTest
    extends MarkdownExtendedBaseTest
{

    /**
     * Get the `markdown.php` file path
     *
     * @return  string
     */
    public function getOldInterfaceFilepath()
    {
        return __DIR__.'/../../src/markdown.php';
    }

    /**
     * Test the `Markdown()` and `MarkdownFromSource()` aliases
     */
    public function testCreate()
    {
        $this->assertFileExists($this->getOldInterfaceFilepath(), 'getOldInterfaceFilepath failure!');
        require_once $this->getOldInterfaceFilepath();

        // Markdown
        $this->assertEquals(
            $this->getTestExpectedBody(),
            trim(Markdown(file_get_contents($this->getTestFilepath()))),
            'Markdown() failure'
        );

        // MarkdownFromSource
        $this->assertEquals(
            $this->getTestExpectedBody(),
            trim(MarkdownFromSource($this->getTestFilepath())),
            'MarkdownFromSource() failure'
        );

    }
    
}
