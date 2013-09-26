<?php
class JoshRibakoff_Note_HTMLToRTF
{
    protected $colortable;

    function setColorTable($colorTable)
    {
        $this->colortable = $colorTable;
    }

    function newColorTable()
    {
        return $this->colortable;
    }

    function convert($text)
    {
        $text = $this->escapeRTFTokens($text);

        $text = $this->convertSpanWithColorStyleToRTF($text);
        $text = $this->convertFontColorToRTF($text);

        $text = str_replace('</p><p>', '\\par', $text);

        $text = preg_replace('#<b[^>]*>#', '\\b ', $text);
        $text = str_replace('</b>', ' \\b0', $text);

        $text = preg_replace('#<strong[^>]*>#', '\\b ', $text);
        $text = str_replace('</strong>', ' \\b0', $text);

        $text = preg_replace('#<i[^>]*>#', '\\i ', $text);
        $text = str_replace('</i>', ' \\i0', $text);

        $text = preg_replace('#<em[^>]*>#', '\\i ', $text);
        $text = str_replace('</em>', ' \\i0', $text);

        $text = preg_replace('#<u[^>]*>#', '\\ul ', $text);
        $text = str_replace('</u>', ' \\ulnone', $text);

        $text = str_replace('&nbsp;', ' ', $text);
        $text = html_entity_decode($text, ENT_QUOTES, "UTF-8");

        $text = strip_tags($text);

        return $text;
    }

    function escapeRTFTokens($text)
    {
        return str_replace(
            array('\\', '}', '{'),
            array('\\\\', '\\}', '\\{'),
            $text
        );
    }

    function convertSpanWithColorStyleToRTF($text)
    {
        if (!preg_match_all('#<span.*?style=".*?color:\#(.+?)">#', $text, $matches)) {
            return $text;
        }
        foreach ($matches[1] as $matchedColor) {
            $cfnumber = $this->updateColorTable($matchedColor);
            $text = preg_replace('#<span.*?style=".*?color:.+?">#', '\\cf' . $cfnumber . ' ', $text, 1);
            $text = '\\cf0 ' . $text;
            $text = str_replace('</span>', ' \\cf0', $text);
        }
        return $text;
    }

    function convertFontColorToRTF($text)
    {
        if (!preg_match_all('#<font color="\#(.+?)">#', $text, $matches)) {
            return $text;
        }
        foreach ($matches[1] as $matchedColor) {
            $cfnumber = $this->updateColorTable($matchedColor);
            $text = preg_replace('#<font color=".+?">#', '\\cf' . $cfnumber . ' ', $text, 1);
            $text = '\\cf0 ' . $text;
            $text = str_replace('</font>', ' \\cf0', $text);
        }

        return $text;
    }

    function updateColorTable($newColor)
    {
        if (!count($this->colortable)) {
            $this->colortable = array(
                '000000',
                $newColor
            );
        } else {
            $this->colortable[] = $newColor;
        }
        return count($this->colortable) - 1;
    }
}