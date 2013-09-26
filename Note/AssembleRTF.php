<?php
/** Converts a parsed structure back to ANSII text (RTF document) */
class JoshRibakoff_Note_AssembleRTF
{
    /** Parsed structure of the RTF document to assemble back to ANSII text */
    protected $rtf_fonttable, $rtf_colortable, $rtf_text_pieces;

    /** @var string the assembled RTF */
    protected $assembled_rtf;

    function __construct($params)
    {
        $this->rtf_fonttable = $params['fonttable'];
        $this->rtf_colortable = $params['colortable'];
        $this->rtf_text_pieces = $params['rtf_text_pieces'];
    }

    function assemble()
    {
        $this->addHeader();
        $this->addFontTable();
        $this->addColorTable();
        $this->addTextPieces();
        return $this->assembled_rtf;
    }

    function addHeader()
    {
        $this->assembled_rtf = "\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049";
    }

    function addFontTable()
    {
        $this->assembled_rtf .= "{\\fonttbl";
        foreach ($this->rtf_fonttable as $fontKey => $fontValue) {
            $this->assembled_rtf .= "{\\" . $fontKey . $fontValue . ";}";
        }
        $this->assembled_rtf .= "}\n";
    }

    function addColorTable()
    {
        $this->assembled_rtf .= "{\\colortbl ";
        foreach ($this->rtf_colortable as $colorValue) {
            $this->assembled_rtf .= '\\red' . hexdec(substr($colorValue, 0, 2));
            $this->assembled_rtf .= '\\green' . hexdec(substr($colorValue, 2, 2));
            $this->assembled_rtf .= '\\blue' . hexdec(substr($colorValue, 4, 2));
            $this->assembled_rtf .= ";";

        }
        $this->assembled_rtf .= "}\n";
    }

    function addTextPieces()
    {
        $count = count($this->rtf_text_pieces);
        $i = 0;
        foreach ($this->rtf_text_pieces as $textElement) {
            $i++;
            if (isset($textElement['rtf'])) {
                $this->assembled_rtf .= $textElement['rtf'];
                $this->assembled_rtf .= "\\par";
                if ($i != $count) {
                    $this->assembled_rtf .= "\n";
                }
                continue;
            }

            $newRTFLine = array();
            if (isset($textElement['color']) || isset($textElement['font']) || isset($textElement['fontsize'])) {
                $newRTFLine[0] = null;
            }

            if (isset($textElement['color'])) {
                $newRTFLine[0] .= "\\cf" . $textElement['color'];
            }

            if (isset($textElement['font'])) {
                $newRTFLine[0] .= "\\f" . $textElement['font'];
            }

            if (isset($textElement['fontsize'])) {
                $newRTFLine[0] .= "\\fs" . $textElement['fontsize'];
            }

            if (isset($textElement['text']) && $textElement['text']) {
                $newRTFLine[1] = $textElement['text'];
            }

            $this->assembled_rtf .= implode(' ', $newRTFLine);
            $this->assembled_rtf .= "\\par";
            if ($i != $count) {
                $this->assembled_rtf .= "\n";
            }
        }
    }
}