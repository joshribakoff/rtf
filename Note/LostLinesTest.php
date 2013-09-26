<?php
require_once __DIR__.'/../Note.php';
require_once __DIR__.'/SectionLexer.php';
class JoshRibakoff_Note_LostLinesTest extends PHPUnit_Framework_TestCase
{
    function testSectionLexerShouldParseRTF()
    {
        $sectionLexer = new JoshRibakoff_Note_SectionLexer;
        $actual = $sectionLexer->lineOfText('\\cf1\\f0\\fs17 4/2/2013 9:49:38 AM - bglass: test.\\par');
        $this->assertEquals('4/2/2013 9:49:38 AM - bglass: test.\\par', $actual['text'], 'should return line of text');
    }

    function testSectionLexerShouldParsePlainText()
    {
        $sectionLexer = new JoshRibakoff_Note_SectionLexer;
        $actual = $sectionLexer->lineOfText('3/29/2013 2:19:29 PM - gviguet:  l/vm in re to Counseling..............');
        $this->assertEquals('3/29/2013 2:19:29 PM - gviguet:  l/vm in re to Counseling..............', $actual['text'], 'should return line of text');
    }

    function testPrependsSimpleText()
    {
        $startNote = '\\rtf1\\ansi\\ansicpg1252\\deff0\\deflang1033{\\fonttbl{\\f0\\fswiss\\fprq2\\fcharset0 Arial;}{\\f1\\fnil\\fcharset0 Microsoft Sans Serif;}}
{\\colortbl ;\\red255\\green0\\blue0;\\red0\\green0\\blue255;}
\\viewkind4\\uc1\\pard\\cf1\\ul\\b\\f0\\fs17 4/2/2013 9:49:38 AM - bglass: test.\\par
3/29/2013 2:19:29 PM - gviguet:  l/vm in re to Counseling..............\\par';

        $note = new JoshRibakoff_Note();

        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => 'simple note2'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fswiss\\fprq2\\fcharset0 Arial;}{\\f1\\fnil\\fcharset0 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;\\red255\\green0\\blue0;\\red0\\green0\\blue255;}
\\f0\\fs17 simple note2\\par
\\viewkind4\\uc1\\pard\\cf1\\ul\\b\\f0\\fs17 4/2/2013 9:49:38 AM - bglass: test.\\par
3/29/2013 2:19:29 PM - gviguet:  l/vm in re to Counseling..............\\par';

        $this->assertEquals($expectedNote, $note->formatRTF(), 'should prepend note & format as RTF');
    }


}
