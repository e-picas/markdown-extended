<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2013 Pierre Cassat
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

class MarkdownExtendedBaseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Get the tests test file path
     * @return string
     */
    public function getTestFilepath()
    {
        return __DIR__.'/test.md';
    }

    /**
     * Create a markdown parser
     * @param array $configuration Optional configuration
     * @return \MarkdownExtended\Parser
     */
    public function createParser($configuration = null)
    {
        return \MarkdownExtended\MarkdownExtended::getInstance()
            ->get('Parser', $configuration);
    }

    /**
     * Create a markdown content
     * @param string $content
     * @return \MarkdownExtended\Content
     */
    public function createContent($content = null)
    {
        return new \MarkdownExtended\Content($content);
    }

    /**
     * Create a markdown content from file
     * @param string $file_path
     * @return \MarkdownExtended\Content
     */
    public function createSourceContent($filepath = null)
    {
        return new \MarkdownExtended\Content(null, $filepath);
    }

    /**
     * Get a trimed content body
     * @param object $content
     * @return string
     */
    public function getBody($content = null)
    {
        return trim($content->getBody());
    }

    /**
     * Validate class methods
     */
    public function testCreate()
    {
        $this->assertInstanceOf('\MarkdownExtended\Parser', $this->createParser(), 'baseTest->createParser failure!');

        $this->assertInstanceOf('\MarkdownExtended\Content', $this->createContent('test'), 'baseTest->createContent failure!');

        $this->assertFileExists($this->getTestFilepath(), 'baseTest->getTestFilepath failure!');

        $this->assertInstanceOf('\MarkdownExtended\Content', $this->createSourceContent($this->getTestFilepath()), 'baseTest->createSourceContent failure!');
    }

}
