<?php
require_once(dirname(__FILE__) . '/BraceLexer.php');

class JoshRibakoff_Note_BraceLexerTest extends PHPUnit_Framework_TestCase
{
    function testShouldTokenize0()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("1");
        $expected = array(
            '1'
        );
        $this->assertEquals($expected, $note->tokenize(), 'should tokenize {} blocks');
    }

    function testShouldParseBlock()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("{1}");
        $expected = array(
            array('1')
        );
        $this->assertEquals($expected, $note->tokenize(), 'should parse out content from a block');
    }

    /**
     * @expectedException Exception
     */
    function testShouldThrowExceptionWhenBraceNotClosed()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("{1");
        $note->tokenize();
    }

    function testShouldEscapeTokens()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("\{1\}");
        $expected = array('{1}');
        $this->assertEquals($expected, $note->tokenize(), 'should escape tokens');
    }

    function testShouldParseNestedBlock()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("11{2}33");
        $expected = array(
            '11',
            array('2'),
            '33'
        );
        $this->assertEquals($expected, $note->tokenize(), 'should parse out a nested block');
    }

    function testShouldParseTwoLevelsOfNesting()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("11{2{3}}44");
        $expected = array(
            '11',
            array('2{3}'),
            '44'
        );
        $this->assertEquals($expected, $note->tokenize(), 'should parse two levels of nested blocks');
    }

    function testShouldEscapeTokensInNestedBlock()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("11{2{\{3}}44");
        $expected = array(
            '11',
            array('2{\{3}'),
            '44'
        );
        $this->assertEquals($expected, $note->tokenize(), 'should escape tokens in nested block');
    }

    /**
     * @expectedException Exception
     */
    function testShouldThrowExceptionWhenMultipleBracesNotClosed()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("{{1}");
        $note->tokenize();
    }

    function testShouldParseMultipleNestedBlocks()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("11{2}{3}44");
        $expected = array(
            '11',
            array('2'),
            array('3'),
            '44'
        );
        $this->assertEquals($expected, $note->tokenize(), 'should parse multiple nested blocks');
    }

    function testShouldParseMultipleDoubleNestedBlocks()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("11{2{hello}}{3{world}}44");
        $expected = array(
            '11',
            array('2{hello}'),
            array('3{world}'),
            '44'
        );
        $this->assertEquals($expected, $note->tokenize(), 'should parse multiple double nested blocks');
    }

    function testShouldParseMultipleTripleNestedBlocks()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("11{2{hello{world}}}{3{{goodbye}world}}44");
        $expected = array(
            '11',
            array('2{hello{world}}'),
            array('3{{goodbye}world}'),
            '44'
        );
        $this->assertEquals($expected, $note->tokenize(), 'should parse multiple triple nested blocks');
    }

    function testShouldTokenizeBracesInMiddleOfString()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("hello{goodbye}world");
        $expected = array(
            'hello',
            array('goodbye'),
            'world'
        );
        $this->assertEquals($expected, $note->tokenize(), 'should tokenize braces in middle of string');
    }

    function testShouldInterpretClosingBraceAsLiteralDataIfNested()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("3{{goodbye}world}");
        $expected = array(
            '3',
            array('{goodbye}world')
        );
        $this->assertEquals($expected, $note->tokenize(), 'should interpret closing brace as literal data if nested');
    }

    function testShouldLexOverNewlines()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("3{{goodbye\r\n}world}");
        $expected = array(
            '3',
            array("{goodbye\r\n}world")
        );
        $this->assertEquals($expected, $note->tokenize(), 'should lex over new lines');
    }

    function testShouldLexValidRTFData()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF("\\rtf\\ansi\\deff0{\\fonttbl{\\f0\\froman Tms Rmn;}{\\f1\\fdecor 
Symbol;}{\\f2\\fswiss Helv;}}{\\colortbl;\\red0\\green0\\blue0;
\\red0\\green0\\blue255;\\red0\\green255\\blue255;\\red0\\green255\\
blue0;\\red255\\green0\\blue255;\\red255\\green0\\blue0;\\red255\\
green255\\blue0;\\red255\\green255\\blue255;}{\\stylesheet{\\fs20 \\snext0Normal;}}{\\info{\\author John Doe}
{\\creatim\\yr1990\\mo7\\dy30\\hr10\\min48}{\\version1}{\\edmins0}
{\\nofpages1}{\\nofwords0}{\\nofchars0}{\\vern8351}}\\widoctrl\\ftnbj \\sectd\\linex0\\endnhere \\pard\\plain \\fs20 This is plain text.\\par");

        $expected = array(
            '\\rtf\\ansi\\deff0',
            array('\\fonttbl{\\f0\\froman Tms Rmn;}{\\f1\\fdecor 
Symbol;}{\\f2\\fswiss Helv;}'),
            array('\\colortbl;\\red0\\green0\\blue0;
\\red0\\green0\\blue255;\\red0\\green255\\blue255;\\red0\\green255\\
blue0;\\red255\\green0\\blue255;\\red255\\green0\\blue0;\\red255\\
green255\\blue0;\\red255\\green255\\blue255;'),
            array('\\stylesheet{\\fs20 \\snext0Normal;}'),
            array('\\info{\\author John Doe}
{\\creatim\\yr1990\\mo7\\dy30\\hr10\\min48}{\\version1}{\\edmins0}
{\\nofpages1}{\\nofwords0}{\\nofchars0}{\\vern8351}'),
            '\\widoctrl\\ftnbj \\sectd\\linex0\\endnhere \\pard\\plain \\fs20 This is plain text.\\par'

        );

        $actual = $note->tokenize();

        $this->assertEquals($expected, $actual, 'should lex valid RTF data');
    }

    function testShouldLexRTFDataFromJoshRibakoff()
    {
        $note = new JoshRibakoff_Note_BraceLexer;

        $note->setRTF('\rtf1\ansi\ansicpg1251\deff0\deflang1049{\fonttbl{\f0\fnil\fcharset0 Microsoft Sans Serif;}{\f1\fnil\fcharset204 Microsoft Sans Serif;}}
{\colortbl ;\red0\green255\blue0;\red0\green128\blue192;\red255\green0\blue0;}
\viewkind4\uc1\pard\lang1033\f0\fs17 4/4/2013 11:17:03 AM - jribakoff: black\par
\cf1 4/4/2013 11:16:59 AM - jribakoff: note 8 (green for money)\par
\cf0 4/4/2013 11:16:50 AM - jribakoff: note 7\par
\cf2 4/4/2013 11:16:47 AM - jribakoff: note 6 (really blue)\par
\cf0 4/4/2013 11:16:34 AM - jribakoff: note 6 ( blue)\par
4/4/2013 11:15:36 AM - jribakoff: note 5\par
\cf3 4/4/2013 11:15:33 AM - jribakoff: note 4 (red)\par
\cf0 4/4/2013 11:15:23 AM - jribakoff: note 3\par
4/4/2013 11:15:21 AM - jribakoff: note 2\par
4/3/2013 2:47:03 PM - jribakoff: simple text\par
\lang1049\f1\par');

        $expected = array(
            '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049',
            array('\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}'),
            "\n",
            array('\\colortbl ;\\red0\\green255\\blue0;\\red0\\green128\\blue192;\\red255\\green0\\blue0;'),
            "\n" . '\\viewkind4\\uc1\\pard\\lang1033\\f0\\fs17 4/4/2013 11:17:03 AM - jribakoff: black\\par
\\cf1 4/4/2013 11:16:59 AM - jribakoff: note 8 (green for money)\\par
\\cf0 4/4/2013 11:16:50 AM - jribakoff: note 7\\par
\\cf2 4/4/2013 11:16:47 AM - jribakoff: note 6 (really blue)\\par
\\cf0 4/4/2013 11:16:34 AM - jribakoff: note 6 ( blue)\\par
4/4/2013 11:15:36 AM - jribakoff: note 5\\par
\\cf3 4/4/2013 11:15:33 AM - jribakoff: note 4 (red)\\par
\\cf0 4/4/2013 11:15:23 AM - jribakoff: note 3\\par
4/4/2013 11:15:21 AM - jribakoff: note 2\\par
4/3/2013 2:47:03 PM - jribakoff: simple text\\par
\\lang1049\\f1\\par'
        );

        $actual = $note->tokenize();

        $this->assertEquals($expected, $actual, 'should lex valid RTF data');
    }

}