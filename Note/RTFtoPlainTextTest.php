<?php
require_once __DIR__.'/RTFToPlainText.php';
class JoshRibakoff_Note_RTFToPlainTextTest extends PHPUnit_Framework_TestCase
{
    function testShouldConvertParTokenToNewline()
    {
        $converter = new JoshRibakoff_Note_RTFToPlainText();
        $output = $converter->convert('foo\\parbar');
        $expected = "foo\nbar";
        $this->assertEquals($expected, $output, 'should convert par token to newline');
    }

    function testShouldIgnoreLinefeed()
    {
        $converter = new JoshRibakoff_Note_RTFToPlainText();
        $output = $converter->convert("foo\nbar");
        $expected = "foobar";
        $this->assertEquals($expected, $output, 'should ignore linefeed');
    }

    function testShouldStripBRTFTokens()
    {
        $converter = new JoshRibakoff_Note_RTFToPlainText();
        $output = $converter->convert('\\bfoo');
        $this->assertEquals('foo', $output, 'should strip other RTF tokens');
    }

    function testShouldStripUC1RTFTokens()
    {
        $converter = new JoshRibakoff_Note_RTFToPlainText();
        $output = $converter->convert('\\uc1foo');
        $this->assertEquals('foo', $output, 'should strip \\uc1 RTF tokens');
    }

    function testShouldStripCF0RTFTokens()
    {
        $converter = new JoshRibakoff_Note_RTFToPlainText();
        $output = $converter->convert('\\cf0foo');
        $this->assertEquals('foo', $output, 'should strip \\cf0 RTF tokens');
    }

    function testShouldStripPardTokens()
    {
        $converter = new JoshRibakoff_Note_RTFToPlainText();
        $output = $converter->convert('\\pardfoo');
        $this->assertEquals('foo', $output, 'should strip \\pard RTF tokens');
    }

    function testShouldStripTokensOnMultipleLines()
    {
        $converter = new JoshRibakoff_Note_RTFToPlainText();
        $output = $converter->convert("foo\nbaz\bbat");
        $this->assertEquals("foobazbat", $output, 'should strip RTF tokens on multiple lines');
    }

    function testShouldUseNewline()
    {
        $converter = new JoshRibakoff_Note_RTFToPlainText();
        $converter->setNewline("\n");
        $output = $converter->convert("foo\parbar");
        $this->assertEquals("foo\nbar", $output, 'should use \n as newline');
    }

    function testShouldUseCarriageReturnNewline()
    {
        $converter = new JoshRibakoff_Note_RTFToPlainText();
        $converter->setNewline("\r\n");
        $output = $converter->convert("foo\parbar");
        $this->assertEquals("foo\r\nbar", $output, 'should use \r\n as newline');
    }

    function testShouldTrimPrefixingWhitespace()
    {
        $converter = new JoshRibakoff_Note_RTFToPlainText();
        $output = $converter->convert(" test");
        $this->assertEquals("test", $output, 'should trim prefixing whitespace');
    }
}