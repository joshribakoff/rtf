<?php
require_once __DIR__.'/RTFToHTML.php';
class JoshRibakoff_Note_RTFToHTMLTest extends PHPUnit_Framework_TestCase
{
    function testShouldConvertBoldTags()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\bfoo\\parbar\\par\\b0');
        $expected = '<b>foo<br />bar<br /></b>';
        $this->assertEquals($expected, $output, 'should convert opening bold in one line and closing bold in another');
    }

    function testShouldConvertItalicsTags()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\ifoo\\i0');
        $expected = '<i>foo</i>';
        $this->assertEquals($expected, $output, 'should convert italics');
    }

    function testShouldConvertUnderlineTags()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\ulfoo\\ulnone');
        $expected = '<u>foo</u>';
        $this->assertEquals($expected, $output, 'should convert underline	');
    }

    function testShouldCloseBoldTags()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\bfoo\\par\\b0bar');
        $expected = '<b>foo<br /></b>bar';
        $this->assertEquals($expected, $output, 'Should close bold tags');
    }

    function testShouldCloseHangingBoldTags()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\bfoo');
        $expected = '<b>foo</b>';
        $this->assertEquals($expected, $output, 'Should close hanging bold tags');
    }

    function testShouldCloseHangingUnderlineTags()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\ulfoo');
        $expected = '<u>foo</u>';
        $this->assertEquals($expected, $output, 'Should close hanging underline tags');
    }

    function testShouldCloseHangingItalicsTags()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\ifoo');
        $expected = '<i>foo</i>';
        $this->assertEquals($expected, $output, 'Should close hanging italics tags');
    }

    /**
     * @expectedException Exception
     */
    function testShouldThrowExceptionIfTokenIsMissingColorTableEntry_ColorLookup()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $converter->colorHex(1);
    }

    /**
     * @expectedException Exception
     */
    function testShouldThrowExceptionIfTokenIsMissingColorTableEntry_Parsing()
    {
        $input = '\\cf1 test';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $converter->convert($input);
    }

    function testShouldConvertColors()
    {
        $input = '\\cf1red\\cf0black';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $converter->setColorTable(array('000000', 'FF0000'));
        $output = $converter->convert($input);
        $expected = '<span style="color:#FF0000">red</span>black';
        $this->assertEquals($expected, $output, 'should convert colors');
    }

    function testDefaultColorShouldDoNothingPerSe()
    {
        $input = '\\bfoo\\cf0bar';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert($input);
        $expected = '<b>foobar</b>';
        $this->assertEquals($expected, $output, 'default color tag should do nothing per se');
    }

    function testAColorShouldEndPreviousColors()
    {
        $input = '\\cf1red\\cf2green';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $converter->setColorTable(array('000000', 'FF0000', '00FF00'));
        $output = $converter->convert($input);
        $expected = '<span style="color:#FF0000">red</span><span style="color:#00FF00">green</span>';
        $this->assertEquals($expected, $output, 'a color should end previous colors');
    }

    function testAColorShouldEndPreviousColorsWithBoldTags()
    {
        $input = '\cf0black\cf2\b green\cf0black';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $converter->setColorTable(array('000000', 'FF0000', '00FF00'));
        $output = $converter->convert($input);
        $expected = 'black<span style="color:#00FF00"><b> green</b></span><b>black</b>';
        $this->assertEquals($expected, $output, 'a color should end previous colors');
    }

    function testShouldAutomaticallyCloseColorTags()
    {
        $input = '\\cf1red';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $converter->setColorTable(array('000000', 'FF0000'));
        $output = $converter->convert($input);
        $expected = '<span style="color:#FF0000">red</span>';
        $this->assertEquals($expected, $output, 'should convert colors');
    }

    function testShouldNotDetectOutOfOrderRTFCloseTags()
    {
        $input = '\\cf1 color only \\cf0 plaintext';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $converter->setColorTable(array('000000', 'FF0000'));
        $converter->convert($input);
        $this->assertFalse($converter->detectedOutOfOrderRTFCloseTags(), 'should not detect out of order RTF close tags');
    }

    function testShouldDetectOutOfOrderRTFCloseTags()
    {
        $input = '\\cf1 color only \\b bold color \\cf0 bold only';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $converter->setColorTable(array('000000', 'FF0000'));
        $converter->convert($input);
        $this->assertTrue($converter->detectedOutOfOrderRTFCloseTags(), 'should detect out of order RTF close tags');
    }

    // in RTF, unlike HTML, tags can be closed in a different order they were opened
    function testShouldCloseTagsAndReopenToProduceValidHTML()
    {
        $input = '\\iitalics\\bbold italics\\i0bold';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert($input);
        $expected = '<i>italics<b>bold italics</b></i><b>bold</b>';
        $this->assertEquals($expected, $output, 'should close tags & reopen them if RTF closed tags out of order');
    }

    function testShouldCloseTagsAsFewTagsAsNecessaryToCloseCurrentTag()
    {
        $input = '\\ii\\bbi\\ulbiu\\b0iu\\ulnone\\i0';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert($input);
        $expected = '<i>i<b>bi<u>biu</u></b><u>iu</u></i>';
        $this->assertEquals($expected, $output, 'should close tags & reopen them if RTF closed tags out of order');
    }

    function testShouldCloseNestedBoldTagsThatWereLeftOpen()
    {
        $input = '\cf1\bbold\\parmore bold\\par\cf0\b0 not bold';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $converter->setColorTable(array('000000', 'FF0000'));
        $output = $converter->convert($input);
        $expected = '<span style="color:#FF0000"><b>bold<br />more bold<br /></b></span><b></b> not bold';
        $this->assertEquals($expected, $output, 'should close nested bold tags that were left open');
    }

    function testShouldIgnoreUC1()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\uc1test');
        $expected = 'test';
        $this->assertEquals($expected, $output, 'Should ignore \\uc1');
    }

    function testShouldIgnoreUC2()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\uc2test');
        $expected = 'test';
        $this->assertEquals($expected, $output, 'Should ignore \\uc2');
    }

    function testShouldIgnoreF1()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert(' \\f1test');
        $expected = 'test';
        $this->assertEquals($expected, $output, 'Should ignore \\f1');
    }

    function testShouldIgnorePARD()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\pardtest');
        $expected = 'test';
        $this->assertEquals($expected, $output, 'Should ignore \\pard');
    }

    function testShouldIgnorePARD2()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\viewkind4\pard\f0\fs17test');
        $expected = 'test';
        $this->assertEquals($expected, $output, 'Should ignore \\pard');
    }

    function testShouldIgnoreLangTokens()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\lang1033\\lang1049');
        $this->assertEquals('', $output, 'should ignore \\lang tokens');
    }

    function testShouldNotIgnoreTextAfterLangTokens()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\lang1033important text here');
        $this->assertEquals('important text here', $output, 'should not ignore text after \\lang tokens');
    }

    function testRegression()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $converter->setColorTable(array('000000', 'FF0000', 'FFFF00'));
        $output = $converter->convert('\lang1033important\lang1033-test');
        $this->assertEquals('important-test', $output, 'should not remove "important" text sandwiched between noop tokens');
    }

    function testNotCloseUnopenedColorsWhenSpecifyingCF0()
    {
        $input = 'test \\cf0test';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert($input);
        $expected = 'test test';
        $this->assertEquals($expected, $output, 'should not close unopened colors when specifying cf0');
    }

    function testIssueBold()
    {
        $input = '\\bbold\\cf0\\b0not bold';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert($input);
        $expected = '<b>bold</b>not bold';
        $this->assertEquals($expected, $output, 'should turn off bold');
    }

    function testIssueOnlyIgnoreNoop()
    {
        $input = 'foo\f0\fs17 bar \b\f1test';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert($input);
        $this->assertEquals('foo bar <b>test</b>', $output, 'should only ignore noop');
    }

    function testIssueULNone()
    {
        $input = ' \ul\bub\ulnone\b0test';
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert($input);
        $this->assertEquals('<u><b>ub</b></u><b></b>test', $output, 'should handle out of order underline');
    }

    function testTextShouldNotBeRed()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $converter->setColorTable(array('000000', 'FF0000', 'FFFF00'));
        $output = $converter->convert('\cf1red\b\cf2yellow bold');
        $expected = '<span style="color:#FF0000">red<b></b></span><b><span style="color:#FFFF00">yellow bold</span></b>';
        $this->assertEquals($expected, $output, 'should not be red');
    }

    function testShouldCloseMultipleHangingTags()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\ul\btest');
        $expected = '<u><b>test</b></u>';
        $this->assertEquals($expected, $output, 'should close multiple hanging tags');
    }

    function testShouldUnescapeBackslashes()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\\\');
        $expected = '\\';
        $this->assertEquals($expected, $output, 'should unescape backslashes');
    }

    function testShouldNotMistakeEscapedBackslashForBoldToken()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\\\b');
        $expected = '\\b';
        $this->assertEquals($expected, $output, 'should unescape backslashes');
    }

    function testShouldUnescapeEscapedCurlyBraces()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML();
        $output = $converter->convert('\\{test\\}');
        $expected = '{test}';
        $this->assertEquals($expected, $output, 'should unescape escaped curly braces');
    }

}