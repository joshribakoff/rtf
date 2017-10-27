<?php
require_once 'Note.php';
class JoshRibakoff_NoteTest extends PHPUnit_Framework_TestCase
{
    // function testRussian()
    // {
    //     $start = file_get_contents('109.rtf');
    //     //echo $start;
    //     $note = new JoshRibakoff_Note();
    //     $note->setRTF($start);
    //     $note->prependNote(array(
    //         'text' => 'simple note2'
    //     ));

    //     //echo $note->formatRTF();
    // }

    function testPrependsSimpleText()
    {
        $startNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => 'simple note2'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl }
\\f0\\fs17 simple note2\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';

        $this->assertEquals($expectedNote, $note->formatRTF(), 'should prepend note & format as RTF');
    }

    function testPrependsSimpleTextExtraBraces()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => 'simple note2'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl }
\\f0\\fs17 simple note2\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';

        $this->assertEquals($expectedNote, $note->formatRTF(), 'should prepend note & format as RTF');
    }

    function testPrependsBoldTextBTag()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<b>bold note</b>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl }
\\f0\\fs17 \b bold note \b0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should bold note & format as RTF');

    }

    function testPrependsBoldTextBTagWhenHTMLAttribute()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<b style="foo">bold note</b>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl }
\\f0\\fs17 \b bold note \b0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should bold note & format as RTF');

    }

    function testPrependsBoldTextStrongTag()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<strong>bold note</strong>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl }
\\f0\\fs17 \b bold note \b0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should bold note & format as RTF');

    }

    function testPrependsUnderlinedText()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\fs17\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<u>underlined note</u>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl }
\\f0\\fs17 \ul underlined note \ulnone\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\fs17\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should italicize note & format as RTF');

    }

    function testPrependsUnderlinedTextWithHTMLAttribute()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\fs17\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<u style="foo">underlined note</u>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl }
\\f0\\fs17 \ul underlined note \ulnone\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\fs17\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should italicize note & format as RTF');

    }

    function testPrependsItalicsTextITag()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<i>italics note</i>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl }
\\f0\\fs17 \i italics note \i0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should italicize note & format as RTF');

    }

    function testPrependsItalicsTextEMTag()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<em>italics note</em>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl }
\\f0\\fs17 \i italics note \i0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should italicize note & format as RTF');

    }

    function testPrependsItalicsTextWithHTMLAttribute()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<i style="foo">italics note</i>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl }
\\f0\\fs17 \i italics note \i0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should italicize note & format as RTF');

    }

    function testPrependsColorTextNoPreviousColorTable()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<font color="#FF0000">test</font>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;\\red255\\green0\\blue0;}
\\f0\\fs17 \\cf0 \\cf1 test \\cf0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should prepend note with no previous color table');

    }

    function testPrependsColorTextNoPreviousColorTable2()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<font color="#00FF00">test</font>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;\\red0\\green255\\blue0;}
\\f0\\fs17 \\cf0 \\cf1 test \\cf0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should prepend note with no previous color table');

    }

    function testPrependsColorTextColorFromExistingColorTable()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;\\red0\\green255\\blue0;}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<font color="#00FF00">test</font>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;\\red0\\green255\\blue0;\\red0\\green255\\blue0;}
\\f0\\fs17 \\cf0 \\cf2 test \\cf0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should prepend note with color from existing color table');

    }

    function testShouldAddOntoExistingColorTable()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;\\red0\\green255\\blue0;}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<font color="#0000FF">test</font>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;\\red0\\green255\\blue0;\\red0\\green0\\blue255;}
\\f0\\fs17 \\cf0 \\cf2 test \\cf0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should add onto existing color table');
    }

    function testShouldHandleMultipleInlineColors()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';
        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => 'black <font color="#00FF00">green</font> <font color="#0000FF">blue</font> black'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;\\red0\\green255\\blue0;\\red0\\green0\\blue255;}
\\f0\\fs17 \\cf0 \\cf0 black \\cf1 green \\cf0 \\cf2 blue \\cf0 black\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should format multiple inline colors when prepending note');
    }

    function testShouldHandleMultipleInlineColorsWithCSSSpan()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';
        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => 'black <span style="color:#00FF00">green</span> <span style="color:#0000FF">blue</span> black'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;\\red0\\green255\\blue0;\\red0\\green0\\blue255;}
\\f0\\fs17 \\cf0 \\cf0 black \\cf1 green \\cf0 \\cf2 blue \\cf0 black\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should format multiple inline colors when prepending note');
    }

    function testShouldIgnoreSuperfluousAttribsBeforeStyle()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';
        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<span class="foo" style="color:#00FF00">green</span>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;\\red0\\green255\\blue0;}
\\f0\\fs17 \\cf0 \\cf1 green \\cf0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should format multiple inline colors when prepending note');
    }

    function testShouldIgnoreSuperfluousAttribsAfterStyle()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';
        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<span style="color:#00FF00" class="foo">green</span>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;\\red0\\green255\\blue0;}
\\f0\\fs17 \\cf0 \\cf1 green \\cf0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should format multiple inline colors when prepending note');
    }

    function testShouldIgnoreSuperfluousStyles()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';
        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<span style="background-color: red;color:#00FF00;">green</span>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;\\red0\\green255\\blue0;}
\\f0\\fs17 \\cf0 \\cf1 green \\cf0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should format multiple inline colors when prepending note');
    }

    function testShouldFormatOnlyPartOfNewNote()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;\\red0\\green255\\blue0;}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => 'this is black <font color="#0000FF">test</font> this is black'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl \\red0\\green0\\blue0;\\red0\\green255\\blue0;\\red0\\green0\\blue255;}
\\f0\\fs17 \\cf0 this is black \\cf2 test \\cf0 this is black\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should add onto existing color table');
    }

    function testShouldEscapeCurlyBraceInPrependedNote()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '}{'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\colortbl }
\\f0\\fs17 \\}\\{\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';

        $this->assertEquals($expectedNote, $note->formatRTF(), 'should escape curly brace in prepended note');
    }

    function testShouldEscapeCurlyBraceOriginalRTF()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 test \\{\\}\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\colortbl }
\\uc1\\pard\\f0\\fs17 test \\{\\}\\par
\\f1\\par';

        $this->assertEquals($expectedNote, $note->formatRTF(), 'should escape curly brace in original rtf');
    }

    function testShouldEscapeBackslashes()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '\\cf1'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\colortbl }
\\f0\\fs17 \\\cf1\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should escape backslashes');
    }

    function testShouldStripTags()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => '<table><tr><td><b>test</b></td></tr>'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl }
\\f0\\fs17 \b test \b0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should escape backslashes');
    }

    function testShouldPreserveTrailingSpace()
    {
        //return $this->markTestIncomplete();
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 simple note \\par
\\f1\\par
}
';
        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => 'new note'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl }
\\f0\\fs17 new note\\par
\\uc1\\pard\\f0\\fs17 simple note \\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should preserve trailing whitespace in old notes');
    }

    function testShouldDecodeHTMLEntities()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => 'test &sect;&copy;'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl }
\\f0\\fs17 test §©\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should decode html entities');
    }

    function testShouldPreserveExistingFormattingSpanningMultipleLines()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 10/6/2011 6:42:52 PM - kackermann:\\par
\\cf2\\b 10/4/2011 1:06:03 PM - jhorton: TURNED ACH OFF PERM IN ACCI PER ACH FORM\\par
\\cf0\\b0\\par
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => 'test&nbsp;&amp;123'
        ));

        $expectedNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\colortbl }
\\f0\\fs17 test &123\par
\\uc1\\pard\\f0\\fs17 10/6/2011 6:42:52 PM - kackermann:\\par
\\cf2\\b 10/4/2011 1:06:03 PM - jhorton: TURNED ACH OFF PERM IN ACCI PER ACH FORM\\par
\\cf0\\b0\\par';
        $this->assertEquals($expectedNote, $note->formatRTF(), 'should decode html entities');
    }

    function testShouldAssembleNoteAsHTML()
    {
        //return $this->markTestIncomplete();
        $startNote = '\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset0 fnilMicrosoft Sans Serif;}{\\f2\\fnil\\fcharset204 fnilMicrosoft Sans Serif;}}
4/4/2013 3:12:46 PM - jribakoff: test';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);

        $expectedNote = '4/4/2013 3:12:46 PM - jribakoff: test';
        $this->assertEquals($expectedNote, $note->formatHTML(), 'should format note as HTML');
    }

    function testShouldFormatPlaintext()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 10/6/2011 6:42:52 PM - kackermann: \\par
\\cf2\\b 10/4/2011 1:06:03 PM - jhorton: TURNED ACH OFF PERM IN ACCI PER ACH FORM
}
';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);

        $expectedNote = "10/6/2011 6:42:52 PM - kackermann:\n";
        $expectedNote .= "10/4/2011 1:06:03 PM - jhorton: TURNED ACH OFF PERM IN ACCI PER ACH FORM";

        $this->assertEquals($expectedNote, $note->formatPlaintext(), 'should format as plaintext');
    }

    function testShouldSpecifyNewlineForPlaintext()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
test1\\par
test2
}';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);

        $expectedNote = "test1|test2";
        $actual = $note->formatPlaintext(array(
            'newline' => '|'
        ));

        $this->assertEquals($expectedNote, $actual, 'should format as plaintext');
    }

    function testPrependedNoteShouldBeInPlaintext()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 10/6/2011 6:42:52 PM - kackermann: \\par
\\cf2\\b 10/4/2011 1:06:03 PM - jhorton: TURNED ACH OFF PERM IN ACCI PER ACH FORM}';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);
        $note->prependNote(array(
            'text' => 'new note'
        ));


        $expectedNote = "new note\n";
        $expectedNote .= "10/6/2011 6:42:52 PM - kackermann:\n";
        $expectedNote .= "10/4/2011 1:06:03 PM - jhorton: TURNED ACH OFF PERM IN ACCI PER ACH FORM\n";

        $this->assertEquals($expectedNote, $note->formatPlaintext(), 'prepended note should be in the plaintext output');
    }

    function testShouldTrimPrefixingWhitespaceFromPlaintextOutput()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 test}';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);

        $this->assertEquals('test', $note->formatPlaintext(), 'should strip prefixing whitespace');
    }

    function testShouldTrimBlankLinesFromPlaintextOutput()
    {
        $startNote = '{\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
\\uc1\\pard\\f0\\fs17 test
\\cf2\\b0\\par
\\uc1\\pard\\f0\\fs17 test2
}';

        $note = new JoshRibakoff_Note();
        $note->setRTF($startNote);

        $this->assertEquals("test\ntest2", $note->formatPlaintext(), 'should not have blank lines in plaintext output');
    }

}