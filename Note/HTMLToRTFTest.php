<?php
require_once __DIR__.'/HTMLToRTF.php';
class JoshRibakoff_Note_HTMLToRTFTest extends PHPUnit_Framework_TestCase
{
    function testShouldNotInsertParTokenForOneHTMLParagraph()
    {
        $input = '<p>test</p>';
        $converter = new JoshRibakoff_Note_HTMLToRTF();
        $output = $converter->convert($input);
        $this->assertEquals('test', $output, 'should not add \par token for single HTML paragraph');
    }

    function testShouldInsertParTokenBetweenHTMLParagraphs()
    {
        $input = '<p>test</p><p>test2</p>';
        $converter = new JoshRibakoff_Note_HTMLToRTF();
        $output = $converter->convert($input);
        $this->assertEquals('test\\partest2', $output, 'should add \par token between HTML paragraphs');
    }
}
