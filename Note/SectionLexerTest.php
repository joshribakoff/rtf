<?php
class JoshRibakoff_Note_SectionLexerTest extends PHPUnit_Framework_TestCase
{
    function testShouldLexFontTable()
    {
        $lexer = new JoshRibakoff_Note_SectionLexer;

        $lexer->setInput(array(
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

        ));

        $expected = array(
            'f0' => '\\froman Tms Rmn',
            'f1' => '\\fdecor Symbol',
            'f2' => '\\fswiss Helv',
        );

        $this->assertEquals($expected, $lexer->fontTable(), 'should lex out font table');
    }

    function testShouldLexBiggerFontTable()
    {
        $lexer = new JoshRibakoff_Note_SectionLexer;

        $lexer->setInput(array(
            '\\rtf\\ansi\\deff0',
            array('\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset0 fnilMicrosoft Sans Serif;}{\\f2\\fnil\\fcharset204 fnilMicrosoft Sans Serif;}'),
            array('\\colortbl;\\red0\\green0\\blue0;
\\red0\\green0\\blue255;\\red0\\green255\\blue255;\\red0\\green255\\
blue0;\\red255\\green0\\blue255;\\red255\\green0\\blue0;\\red255\\
green255\\blue0;\\red255\\green255\\blue255;'),
            array('\\stylesheet{\\fs20 \\snext0Normal;}'),
            array('\\info{\\author John Doe}
{\\creatim\\yr1990\\mo7\\dy30\\hr10\\min48}{\\version1}{\\edmins0}
{\\nofpages1}{\\nofwords0}{\\nofchars0}{\\vern8351}'),
            '\\widoctrl\\ftnbj \\sectd\\linex0\\endnhere \\pard\\plain \\fs20 This is plain text.\\par'

        ));

        $expected = array(
            'f0' => '\\fnil\\fcharset0 Microsoft Sans Serif',
            'f1' => '\\fnil\\fcharset0 fnilMicrosoft Sans Serif',
            'f2' => '\\fnil\\fcharset204 fnilMicrosoft Sans Serif',
        );

        $this->assertEquals($expected, $lexer->fontTable(), 'should lex out more complex font table');
    }

    function testShouldLexColorTable()
    {
        $lexer = new JoshRibakoff_Note_SectionLexer;

        $lexer->setInput(array(
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

        ));

        $expected = array('000000', '000000', '0000FF', '00FFFF', '00FF00', 'FF00FF', 'FF0000', 'FFFF00', 'FFFFFF');

        $this->assertEquals($expected, $lexer->colorTable(), 'should lex out color table');
    }

    function testShouldLexOutFirstLineOfText()
    {
        $lexer = new JoshRibakoff_Note_SectionLexer;
        $lexer->setInput(array(
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
        ));

        $expected = array(
            'fontsize' => '17',
            'font' => '0',
            'text' => '4/4/2013 11:17:03 AM - jribakoff: black',
            'color' => 0,
            'rtf' => '\viewkind4\uc1\pard\lang1033\f0\fs17 4/4/2013 11:17:03 AM - jribakoff: black'
        );

        $actual = $lexer->textPieces();
        $this->assertEquals($expected, $actual[0], 'should lex out text');
    }

    function testShouldLexOutSecondLineOfText()
    {
        $lexer = new JoshRibakoff_Note_SectionLexer;
        $lexer->setInput(array(
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
        ));

        $expected = array(
            'color' => '1',
            'text' => '4/4/2013 11:16:59 AM - jribakoff: note 8 (green for money)',
            'rtf' => '\\cf1 4/4/2013 11:16:59 AM - jribakoff: note 8 (green for money)'
        );

        $actual = $lexer->textPieces();
        $this->assertEquals($expected, $actual[1], 'should lex out text');
    }

    function testShouldLexOutMultipleColorsOnSingleLine()
    {
        $lexer = new JoshRibakoff_Note_SectionLexer;
        $lexer->setInput(array(
            '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049',
            array('\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}'),
            "\n",
            array('\\colortbl ;\\red0\\green255\\blue0;\\red0\\green128\\blue192;\\red255\\green0\\blue0;'),
            '\viewkind4\uc1\pard\cf1\lang1033\b\f0\fs17 4/4/2013 1:40:28 PM - jribakoff: multi\cf0  \cf2\b0\f1\fs23 color \cf3\b\i\f2\fs29 test\par'
        ));

        $actual = $lexer->textPieces();
        $this->assertEquals('4/4/2013 1:40:28 PM - jribakoff: multi\\cf0  \\cf2\\b0\\f1\\fs23 color \\cf3\\b\\i\\f2\\fs29 test', $actual[0]['text'], 'should preserve inline formatting & colors');
    }

    function testShouldLexOutText()
    {
        $lexer = new JoshRibakoff_Note_SectionLexer;
        $lexer->setInput(array(
            '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049',
            array('\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}'),
            "\n",
            array('\\colortbl ;\\red0\\green255\\blue0;\\red0\\green128\\blue192;\\red255\\green0\\blue0;'),
            "foo\nbar"
        ));

        $this->assertEquals("foo\nbar", $lexer->text(), 'should lex out full text');
    }

    function testShouldLexOutAllTextPieces()
    {
        $lexer = new JoshRibakoff_Note_SectionLexer;
        $lexer->setInput(array(
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
        ));


        $actual = $lexer->textPieces();
        $this->assertEquals(11, count($actual), 'should lex out 8 lines of text total');
    }

    function testShouldIgnoreBlankLines()
    {
        $lexer = new JoshRibakoff_Note_SectionLexer;
        $lexer->setInput(array(
            '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049',
            array('\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}'),
            "\n",
            array('\\colortbl ;\\red0\\green255\\blue0;\\red0\\green128\\blue192;\\red255\\green0\\blue0;'),
            "\n" . '\\viewkind4\\uc1\\pard\\lang1033\\f0\\fs17 4/4/2013 11:17:03 AM - jribakoff: black\\par
\\lang1049\\f1\\par'
        ));


        $actual = $lexer->textPieces();
        $this->assertEquals('', $actual[1]['text'], 'should ignore blank lines of text');
    }

    function testShouldDefaultToBlack()
    {
        $lexer = new JoshRibakoff_Note_SectionLexer;
        $lexer->setInput(array(
            '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049',
            array('\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}'),
            "\n",
            array('\\colortbl ;\\red255\\green0\\blue0;'),
            "\n" . '\\viewkind4\\uc1\\pard\\lang1033\\f0\\fs17 4/4/2013 11:17:03 AM - jribakoff: black\\par'
        ));


        $actual = $lexer->textPieces();
        $this->assertEquals(0, $actual[0]['color'], 'should lex out text');
    }

    function testShouldDefaultToBlackWithInlineColor()
    {
        $lexer = new JoshRibakoff_Note_SectionLexer;
        $lexer->setInput(array(
            '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049',
            array('\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}'),
            "\n",
            array('\\colortbl ;\\red255\\green0\\blue0;'),
            "\n" . '\\viewkind4\\uc1\\pard\\lang1033\\f0\\fs17 4/4/2013 11:17:03 AM - jribakoff: black \\cf1 red \\cf0 black\\par'
        ));


        $actual = $lexer->textPieces();
        $this->assertEquals(0, $actual[0]['color'], 'should lex out text');
    }

    function testShouldEscapeBackslashes()
    {
        $lexer = new JoshRibakoff_Note_SectionLexer;
        $lexer->setInput(array(
            '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049',
            array('\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}'),
            "\n",
            array('\\colortbl ;\\red255\\green0\\blue0;'),
            "\n" . '\\viewkind4\\uc1\\pard\\lang1033\\f0\\fs17 4/4/2013 11:17:03 AM - jribakoff: black \\\cf1 escaped to black \\cf0 black\\par'
        ));


        $actual = $lexer->textPieces();
        $this->assertEquals('4/4/2013 11:17:03 AM - jribakoff: black \\\cf1 escaped to black \cf0 black', $actual[0]['text'], 'should lex out text');
    }

    function testShouldKeepEscapedCurlyBraces()
    {
        $lexer = new JoshRibakoff_Note_SectionLexer;
        $lexer->setInput(array(
            '\{\}'
        ));
        $textPieces = $lexer->textPieces();
        $this->assertEquals('\{\}', $textPieces[0]['rtf']);

        $text = $lexer->text();
        $this->assertEquals('\{\}', $text);
    }

    function testShouldPreserveTrailingWhitespaceInTextPieces()
    {
        $lexer = new JoshRibakoff_Note_SectionLexer;
        $lexer->setInput(array(
            "\n" . 'test \\par'
        ));


        $actual = $lexer->textPieces();
        $this->assertEquals('test ', $actual[0]['text'], 'should lex out text');
    }
}