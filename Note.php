<?php
require_once 'Note/AssembleRTF.php';
class JoshRibakoff_Note
{
    const DEFAULT_FONTSIZE = '17';
    const DEFAULT_FONT = '0';

    const DEFAULT_COLOR = '000000';

    protected $rtf_fonttable;
    protected $rtf_colortable;
    protected $rtf_text_pieces;
    protected $raw_rtf;

    /** Set the RTF text this Note object should represent. Overwrites any previously set RTF data. */
    function setRTF($rtf)
    {
        $this->raw_rtf = $rtf;
        $rtf = $this->stripOuterBraces($rtf);
        $tree = $this->convertBracesToArray($rtf);

        // find 'text' section (last element of tree array) and escape RTF tokens (brace lexer un-escapes them)
        $textSection = $tree[count($tree) - 1];
        $textSection = str_replace('{', '\{', $textSection);
        $textSection = str_replace('}', '\}', $textSection);
        $tree[count($tree) - 1] = $textSection;

        $sectionLexer = new JoshRibakoff_Note_SectionLexer;
        $sectionLexer->setInput($tree);

        $this->rtf_fonttable = $sectionLexer->fontTable();
        $this->rtf_colortable = $sectionLexer->colorTable();
        $this->rtf_text_pieces = $sectionLexer->textPieces();
        $this->rtf_text = $sectionLexer->text();
    }

    function stripOuterBraces($rtf)
    {
        if (preg_match('#^\\{(.*)\\}#s', $rtf, $matches)) {
            return $matches[1];
        }
        return $rtf;
    }

    function convertBracesToArray($rtf)
    {
        $braceLexer = new JoshRibakoff_Note_BraceLexer;
        $braceLexer->setRTF($rtf);
        return $braceLexer->tokenize();
    }

    function colorTable()
    {
        return $this->rtf_colortable;
    }

    function text()
    {
        return $this->rtf_text_pieces;
    }

    /**
     * Takes a line of HTML to prepend to the top of the RTF originally passed to setRTF(). Converts HTML in that
     * line to RTF tokens and also updates the RTF document's color table & font table.
     */
    function prependNote($noteDataToPrepend)
    {
        if (!isset($noteDataToPrepend['fontsize'])) {
            $noteDataToPrepend['fontsize'] = self::DEFAULT_FONTSIZE;
        }

        if (!isset($noteDataToPrepend['font'])) {
            $noteDataToPrepend['font'] = self::DEFAULT_FONT;
        }

        $converter = new JoshRibakoff_Note_HTMLToRTF;
        $converter->setColorTable($this->rtf_colortable);
        $noteDataToPrepend['text'] = $converter->convert($noteDataToPrepend['text']);
        $this->rtf_colortable = $converter->newColorTable();

        array_unshift($this->rtf_text_pieces, $noteDataToPrepend);
        $this->updateRTF();
    }

    /** Formats the note represented by this object as HTML */
    function formatHTML()
    {
        $converter = new JoshRibakoff_Note_RTFToHTML;
        $converter->setColorTable($this->colorTable());
        return $converter->convert(trim($this->rtf_text));
    }

    function formatPlaintext($params = array())
    {
        $converter = new JoshRibakoff_Note_RTFToPlainText();
        if (isset($params['newline'])) {
            $converter->setNewline($params['newline']);
        }
        return $converter->convert($this->rtf_text);
    }

    /**
     * Creates a new RTF document from the Note this object represents. Internally it just assembles "pieces" that were
     * lexed & parsed from setRTF(), and sometimes augmented with prependNote(). In that case this will look like the
     * original RTF document, with some new RTF data at the top, but before the header.
     */
    function formatRTF()
    {
        $assembler = new JoshRibakoff_Note_AssembleRTF(array(
            'fonttable' => $this->rtf_fonttable,
            'colortable' => $this->rtf_colortable,
            'rtf_text_pieces' => $this->rtf_text_pieces
        ));
        return $assembler->assemble();
    }

    function updateRTF()
    {
        $this->setRTF($this->formatRTF());
    }

}