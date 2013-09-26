<?php
class JoshRibakoff_Note_SectionLexer
{
    protected $input;

    function setInput($input)
    {
        $this->input = $input;
    }

    function text()
    {
        $text = $this->input[count($this->input) - 1];
        return $text;
    }

    function textPieces()
    {
        $text = $this->text();

        $text = preg_split('#\\\\(par\\b)#', $text);

        $return = array();
        foreach ($text as $key => $lineOfText) {
            if (!trim($lineOfText)) {
                continue;
            }
            $lineOfText = $this->lineOfText($lineOfText);

            if (false === $lineOfText) {
                throw new Exception('Unable to parse line: ' . $text[$key]);
            }
            $return[] = $lineOfText;
        }

        return $return;
    }

    function lineOfText($lineOfText)
    {
        $lineOfText = $this->removeNewLines($lineOfText);
        $returnLine = array(
            'rtf' => $lineOfText,
            'text' => null
        );
        if (substr($lineOfText, 0, 1) !== '\\') {
            return array('text' => $lineOfText);
        }

        preg_match('#^([\S]+)\s?#', $lineOfText, $matches);

        $lineHeader = $matches[0];

        $returnLine['text'] = str_replace($lineHeader, '', $lineOfText);

        $pattern = '#\\\\([^\\\\\\s])+#';
        if (!preg_match_all($pattern, $lineHeader, $matches)) {
            return false;
        }

        $rtfTokens = array_reverse($matches[0]);

        foreach ($rtfTokens as $rtfToken) {
            if (preg_match('#\\\\f([0-9]+)#', $rtfToken, $matches)) {
                $returnLine['font'] = $matches[1];
            }

            if (preg_match('#\\\\cf([0-9]+)#', $rtfToken, $matches)) {
                $returnLine['color'] = $matches[1];
            }

            if (preg_match('#\\\\fs([0-9]+)#', $rtfToken, $matches)) {
                $returnLine['fontsize'] = $matches[1];
            }
        }

        if (!isset($returnLine['color'])) {
            $returnLine['color'] = 0;
        }


        return $returnLine;
    }

    function colorTable()
    {
        $colorTable = $this->section('colortbl');
        $colorTable = $this->removeNewLines($colorTable);
        $colorTable = str_replace('\\colortbl', '', $colorTable);
        $colorTable = explode(';', $colorTable);
        array_pop($colorTable);

        $return = array();
        foreach ($colorTable as $colorItem) {
            if ('' == trim($colorItem)) {
                $return[] = '000000';
                continue;
            }
            $pattern = "#\\\\red([0-9]{0,3})\\\\green([0-9]{0,3})\\\\blue([0-9]{0,3})#";
            preg_match($pattern, $colorItem, $matches);
            $return[] = strtoupper(
                sprintf('%02s', dechex($matches[1])) .
                    sprintf('%02s', dechex($matches[2])) .
                    sprintf('%02s', dechex($matches[3]))
            );
        }
        return $return;
    }

    function fontTable()
    {
        $fontTable = $this->section('fonttbl');
        $lexer = new JoshRibakoff_Note_BraceLexer;
        $lexer->setRTF($fontTable);
        $tree = $lexer->tokenize();
        array_shift($tree);

        $return = array();
        foreach ($tree as $treeItem) {
            preg_match('#^\\\(f[0-9]+)(\\\.*?);#s', $treeItem[0], $match);
            $return[$match[1]] = $this->removeNewLines($match[2]);
        }
        return $return;
    }

    /**
     * RTF Files have headers (font table, color table). These define a color as hex, and a numeric index
     * for which the color can be referred to later in the RTF document. This function locates the color/font
     * table part of the document header.
     */
    function section($sectionName)
    {
        foreach ($this->input as $inputSection) {
            switch ($sectionName) {
                case 'fonttbl':
                    if (preg_match('#^\\\fonttbl#', $inputSection[0], $matches)) {
                        return $inputSection[0];
                    }
                    break;
                case 'colortbl':
                    if (preg_match('#^\\\colortbl#', $inputSection[0], $matches)) {
                        return $inputSection[0];
                    }
                    break;
            }
        }
    }

    function removeNewLines($text)
    {
        return str_replace($this->newLines(), '', $text);
    }

    function newLines()
    {
        return array("\r\n", "\n", "\r");
    }


}