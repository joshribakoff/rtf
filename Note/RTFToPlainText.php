<?php
class JoshRibakoff_Note_RTFToPlainText
{
    /** This token indicates black text/no explicit color */
    const RTF_COLOR_DEFAULT = '\\\\cf0';
    /** This token indicates a specific color from the color table */
    const RTF_COLOR_REGEX = '\\\\cf([0-9]+)';

    /** The paragraph token indicates a line break */
    const RTF_NEWLINE_REGEX = '\\\\par';

    /** These tokens indicate the start & end of bold text */
    const RTF_BOLD_START_REGEX = '\\\\b';
    const RTF_BOLD_END_REGEX = '\\\\b0';

    /** These tokens indicate the start & end of italics text */
    const RTF_ITALICS_START_REGEX = '\\\\i';
    const RTF_ITALICS_END_REGEX = '\\\\i0';

    /** These tokens indicate the start & end of underlined text */
    const RTF_UNDERLINE_START_REGEX = '\\\\ul';
    const RTF_UNDERLINE_END_REGEX = '\\\\ulnone';

    /**
     * These are tokens which I want to ignore. I have to specify them to that "\pard" does not match
     * "\par" token and then "d" plain text, instead it matches this special noop token.
     */
    const RTF_TOKEN_NOOP = '\\\\uc1|\\\\uc2|\\\\pard|\\\\f[0-9]+|\\\\fs[0-9]+|\\\\viewkind[0-4]+';

    protected $newline = "\n";

    function convert($rtf)
    {
        $plaintext = str_replace("\n", '', $rtf);

        foreach ($this->tokens() as $pattern) {
            $plaintext = preg_replace('#' . $pattern . '#s', '', $plaintext);
        }

        $plaintext = str_replace("\\par", $this->newline, $plaintext);

        return join($this->newline, array_map("trim", explode($this->newline, $plaintext)));
    }

    /** @return array of possible tokens & the regex used to match them */
    function tokens()
    {
        return array(
            'T_NOOP' => self::RTF_TOKEN_NOOP,

            'T_UNDERLINE_END' => self::RTF_UNDERLINE_END_REGEX,
            'T_UNDERLINE_START' => self::RTF_UNDERLINE_START_REGEX,

            'T_BOLD_END' => self::RTF_BOLD_END_REGEX,
            'T_BOLD_START' => self::RTF_BOLD_START_REGEX,

            'T_ITALICS_END' => self::RTF_ITALICS_END_REGEX,
            'T_ITALICS_START' => self::RTF_ITALICS_START_REGEX,

            'T_COLOR_DEFAULT' => self::RTF_COLOR_DEFAULT,
            'T_COLOR' => self::RTF_COLOR_REGEX,

        );
    }

    function setNewline($char)
    {
        $this->newline = $char;
    }

}