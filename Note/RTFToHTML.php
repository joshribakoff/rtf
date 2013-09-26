<?php
/**
 * This class converts RTF to HTML. FYI, RTF tags can be closed in a different order than they were opened. It is a
 * lexer which means it looks for tokens at the pointer position in the RTF text, when it finds a token it does some work
 * and then advances the pointer, until there is no more RTF data to lex.
 */
class JoshRibakoff_Note_RTFToHTML
{
    /** This token indicates black text/no explicit color */
    const RTF_COLOR_DEFAULT = '^\\\\cf0';
    /** This token indicates a specific color from the color table */
    const RTF_COLOR_REGEX = '^\\\\cf([0-9]+)';

    /** The paragraph token indicates a line break */
    const RTF_NEWLINE_REGEX = '^\\\\par';

    /** These tokens indicate the start & end of bold text */
    const RTF_BOLD_START_REGEX = '^\\\\b';
    const RTF_BOLD_END_REGEX = '^\\\\b0';

    /** These tokens indicate the start & end of italics text */
    const RTF_ITALICS_START_REGEX = '^\\\\i';
    const RTF_ITALICS_END_REGEX = '^\\\\i0';

    /** These tokens indicate the start & end of underlined text */
    const RTF_UNDERLINE_START_REGEX = '^\\\\ul';
    const RTF_UNDERLINE_END_REGEX = '^\\\\ulnone';

    /**
     * These are tokens which I want to ignore. I have to specify them to that "\pard" does not match
     * "\par" token and then "d" plain text, instead it matches this special noop token.
     */
    const RTF_TOKEN_NOOP = '^\\\\uc1|^\\\\uc2|^\\\\pard|^\\\\f[0-9]+|^\\\\fs[0-9]+|^\\\\viewkind[0-4]+|^\\\\lang[0-9]+';

    /** This is the line separator which we'll replace \par with */
    const LINE_SEPARATOR = '<br />';

    /**
     * This is an array of information about the current token, the name of the token, the text that matches the token,
     * and the length of the text that matched the token.
     */
    protected $matchedToken;

    /** This is the RTF color table, simply an array with numerical indexes and hex color values */
    protected $color_table;

    /** This is the string where we append HTML as the RTF is lexed. */
    protected $converted_html = '';

    /** The current position within the RTF where we've lexed up to at this point */
    protected $pointer = 0;

    /** These are tokens for which we've put closing HTML tags that need to be reopened. */
    protected $deferred_stack = array();

    /** These are tokens for which we have HTML tags open that we need to ensure get closed at some point. */
    protected $stack = array();

    /** This is a flag that is set if we see RTF tokens closed out of order */
    protected $detected_out_of_order_tags = false;

    /** Allows a color table to be injected, which is used to convert RTF tokens to HEX colors for HTML */
    function setColorTable($colorTable)
    {
        $this->color_table = $colorTable;
    }

    /** This is the "main" method which defines the high level strategy of converting RTF to HTML */
    function convert($rtf)
    {
        $this->rtf = trim($rtf);

        do {
            $this->matchedToken = $this->matchToken();
            $this->handleToken();
            $this->advancePointer();
        }
        while ($this->pointer < strlen($this->rtf));

        $this->closeHangingTags();

        return $this->converted_html;
    }

    /** If there are any HTML tags that have not yet been closed, close them */
    function closeHangingTags()
    {
        if (!count($this->stack)) {
            return;
        }
        $this->stack = array_reverse($this->stack);
        $this->debug('detected hanging tags, will close these:' . implode($this->stack, ', '));
        foreach ($this->stack as $tag) {
            $this->addClosingTag($tag);
        }
    }

    /** Handles the current token by converting it to HTML, and pushing/popping from the stack of open tags */
    function handleToken()
    {
        $this->debug('handling token ' . $this->matchedToken['token'] . ' [' . $this->matchedToken['match'] . ']');
        switch ($this->token()) {
            case 'T_COLOR_DEFAULT':
            case 'T_COLOR':
                $this->stackPop();
                $this->stackPush();
                break;

            case 'T_UNDERLINE_START':
            case 'T_ITALICS_START':
            case 'T_BOLD_START':
                $this->stackPush();
                break;

            case 'T_UNDERLINE_END':
            case 'T_ITALICS_END':
            case 'T_BOLD_END':
                $this->stackPop();
                break;

            case 'T_NEWLINE':
                $this->converted_html .= '<br />';
                break;

            case 'T_TEXT':
                $this->converted_html .= $this->matchedToken['match'];
                break;

            case 'T_ESCAPE_BACKSLASH':
                $this->converted_html .= '\\';
                break;

            CASE 'T_ESCAPE_OPENBRACE':
                $this->converted_html .= '{';
                break;

            CASE 'T_ESCAPE_CLOSEBRACE':
                $this->converted_html .= '}';
                break;
        }
        $this->debug("after handling token we have: \n" . $this->converted_html);
    }

    /** Get the name of the current token we're at */
    function token()
    {
        return $this->matchedToken['token'];
    }

    /** Did we see RTF tokens closed out of order? */
    function detectedOutOfOrderRTFCloseTags()
    {
        return $this->detected_out_of_order_tags;
    }

    /** Add a closing HTML tag, and pops the RTF token off of the stack of open tags */
    function stackPop()
    {
        if (!count($this->stack)) {
            return;
        }

        if ('T_COLOR_DEFAULT' == $this->token() && !$this->thereIsAColorTagOpen()) {
            return;
        }

        if (!$this->closingLastOpenedTag()) {
            $this->detected_out_of_order_tags = true;
            $this->debug('found a closing tag ' . $this->token() . ' that doesnt match the last opened tag ' . $this->lastTagOpened());
            $this->debug('heres the tags that are currently open ' . implode($this->stack, ', '));
            $this->closeLastOpenedTagsUntilWeGetToCurrentTagToClose();
            $this->closeCurrentTag();
            $this->reopenTags();
            return;
        }

        $this->addClosingTag($this->token());

        $this->debug('popping stack head ' . $this->lastTagOpened());
        array_pop($this->stack);
    }

    function addClosingTag($token)
    {
        switch ($token) {
            case 'T_UNDERLINE_END':
            case 'T_UNDERLINE_START':
                $this->converted_html .= '</u>';
                break;
            case 'T_ITALICS_START':
            case 'T_ITALICS_END':
                $this->converted_html .= '</i>';
                break;
            case 'T_BOLD_START':
            case 'T_BOLD_END':
                $this->converted_html .= '</b>';
                break;
            case 'T_COLOR_DEFAULT':
            case 'T_COLOR':
                $this->converted_html .= '</span>';
                break;
        }
    }

    /** Is there a color tag open? */
    function thereIsAColorTagOpen()
    {
        foreach ($this->stack as $tag) {
            if ($this->isColorTag($tag)) {
                return true;
            }
        }
        return false;
    }

    /** Is some token a color tag? */
    function isColorTag($token)
    {
        return 'T_COLOR' == $token || 'T_COLOR_DEFAULT' == $token;
    }

    /**
     * If the current token closes a tag that wasn't the last tag opened, close as few tags as necessary to allow
     * us to close the current token's tag. As we close each tag, we remove it from the stack of open tags, but push
     * it onto a second stack so we can reopen them again after closing the tag for the current token.
     */
    function closeLastOpenedTagsUntilWeGetToCurrentTagToClose()
    {
        $tagsToClose = array();
        foreach (array_reverse($this->stack) as $tag) {
            if ($this->tagsMatch($this->token(), $tag)) {
                break;
            }
            array_push($tagsToClose, $tag);
        }

        $this->debug('i am going to close these tags ' . implode($tagsToClose, ', '));
        foreach ($tagsToClose as $elementToBeClosed) {
            switch ($elementToBeClosed) {
                case 'T_UNDERLINE_START':
                    $this->debug('closing the UNDERLINE tag due to invalid nesting');
                    array_pop($this->stack);
                    array_push($this->deferred_stack, 'T_UNDERLINE_START');
                    $this->converted_html .= '</u>';
                    break;
                case 'T_ITALICS_START':
                    $this->debug('closing the ITALICS tag due to invalid nesting');
                    array_pop($this->stack);
                    array_push($this->deferred_stack, 'T_ITALICS_START');
                    $this->converted_html .= '</i>';
                    break;
                case 'T_BOLD_START':
                    $this->debug('closing the BOLD tag due to invalid nesting');
                    array_pop($this->stack);
                    array_push($this->deferred_stack, 'T_BOLD_START');
                    $this->converted_html .= '</b>';
                    break;
                case 'T_COLOR':
                    $this->debug('closing the COLOR tag due to invalid nesting');
                    array_pop($this->stack);
                    array_push($this->deferred_stack, 'T_COLOR_START');
                    $this->converted_html .= '</span>';
                    break;

            }
        }
    }

    /** Puts an HTML closing tag for the current token and removes that token from the stack of open tags */
    function closeCurrentTag()
    {
        switch ($this->token()) {
            case 'T_UNDERLINE_END':
                $this->debug('closing the UNDERLINE tag');
                $this->converted_html .= '</u>';
                array_pop($this->stack);
                break;
            case 'T_ITALICS_END':
                $this->debug('closing the ITALICS tag');
                $this->converted_html .= '</i>';
                array_pop($this->stack);
                break;
            case 'T_BOLD_END':
                $this->debug('closing the BOLD tag');
                $this->converted_html .= '</b>';
                array_pop($this->stack);
                break;

            case 'T_COLOR_DEFAULT':
                $this->debug('closing the COLOR[default] tag');
                $this->converted_html .= '</span>';
                array_pop($this->stack);
                break;

        }
    }

    /** Reopen tags that were closed in order to prevent */
    function reopenTags()
    {
        foreach ($this->deferred_stack as $i => $elementToBeReopened) {
            switch ($elementToBeReopened) {
                case 'T_UNDERLINE_START':
                    $this->debug('Re-Opening the UNDERLINE tag');
                    $this->converted_html .= '<u>';
                    array_push($this->stack, 'T_UNDERLINE_START');
                    break;
                case 'T_BOLD_START':
                    $this->debug('Re-Opening the BOLD tag');
                    $this->converted_html .= '<b>';
                    array_push($this->stack, 'T_BOLD_START');
                    break;
                case 'T_COLOR':
                    $this->debug('Re-Opening the COLOR tag');
                    $this->converted_html .= '<color>';
                    break;
            }
            unset($this->deferred_stack[$i]);

        }
    }

    /** Add an opening HTML tag for the current token, and push that token to the stack of opened tags. */
    function stackPush()
    {
        if ('T_COLOR_DEFAULT' == $this->token() && !$this->thereIsAColorTagOpen()) {
            return;
        }

        $this->debug('pushing to stack ' . $this->token());
        switch ($this->token()) {
            case 'T_ITALICS_START':
                $this->converted_html .= '<i>';
                break;
            case 'T_UNDERLINE_START':
                $this->converted_html .= '<u>';
                break;
            case 'T_BOLD_START':
                $this->converted_html .= '<b>';
                break;
            case 'T_COLOR':
                $color = $this->colorHexForToken($this->matchedToken['match']);
                $this->converted_html .= "<span style=\"color:#$color\">";
                break;
        }
        array_push($this->stack, $this->token());
        $this->debug('Currently the stack is: ' . implode($this->stack, ', '));
    }

    /** @return bool If the current token closes the last opened tag */
    function closingLastOpenedTag()
    {
        // Starting a new color implicitly closes the last color, counts as closing the last opened tag
        if ($this->tagsMatch($this->token(), $this->lastTagOpened())) {
            return true;
        }

        return $this->token() == $this->lastTagOpened();
    }

    /** Check if one token is the closing tag for the first token's opening tag */
    function tagsMatch($openToken, $closeToken)
    {
        // Starting a new color implicitly closes the last color, counts as closing the last opened tag
        if ($openToken == 'T_COLOR_DEFAULT' && 'T_COLOR' == $closeToken) {
            return true;
        }
        if ($openToken == 'T_BOLD_END' && 'T_BOLD_START' == $closeToken) {
            return true;
        }
        if ($openToken == 'T_ITALICS_END' && 'T_ITALICS_START' == $closeToken) {
            return true;
        }
        if ($openToken == 'T_UNDERLINE_END' && 'T_UNDERLINE_START' == $closeToken) {
            return true;
        }
        return false;
    }

    /** @return string the last token opened */
    function lastTagOpened()
    {
        return end($this->stack);
    }

    /** Advance the internal pointer to the next token */
    function advancePointer()
    {
        $advanceBy = strlen($this->matchedToken['match']);
        $this->debug('Advancing pointer from ' . $this->pointer . ', ' . $advanceBy . ' characters to position ' . ($this->pointer + $advanceBy));
        $this->pointer += $advanceBy;
    }

    /** Figure out what kind of token the pointer is currently at */
    function matchToken()
    {
        $string = substr($this->rtf, $this->pointer);

        foreach ($this->tokens() as $name => $pattern) {
            if (preg_match('#' . $pattern . '#s', $string, $matches)) {
                return array(
                    'match' => $matches[0],
                    'token' => $name,
                    'position' => $this->pointer
                );
            }
        }
        throw new Exception("Unmatched token at position $this->pointer near: $string");
    }

    /** @return array of possible tokens & the regex used to match them */
    function tokens()
    {
        return array(
            'T_ESCAPE_BACKSLASH' => '^\\\\\\\\',
            'T_ESCAPE_OPENBRACE' => '^\\\\{',
            'T_ESCAPE_CLOSEBRACE' => '^\\\\}',

            'T_NOOP' => self::RTF_TOKEN_NOOP,

            'T_NEWLINE' => self::RTF_NEWLINE_REGEX,

            'T_UNDERLINE_END' => self::RTF_UNDERLINE_END_REGEX,
            'T_UNDERLINE_START' => self::RTF_UNDERLINE_START_REGEX,

            'T_BOLD_END' => self::RTF_BOLD_END_REGEX,
            'T_BOLD_START' => self::RTF_BOLD_START_REGEX,

            'T_ITALICS_END' => self::RTF_ITALICS_END_REGEX,
            'T_ITALICS_START' => self::RTF_ITALICS_START_REGEX,

            'T_COLOR_DEFAULT' => self::RTF_COLOR_DEFAULT,
            'T_COLOR' => self::RTF_COLOR_REGEX,

            'T_TEXT' => '.'
        );
    }

    /** Removes unrecognized tokens, if we don't recognize an RTF token we'll just ignore it. */
    function removeRTFTokens($text)
    {
        return preg_replace(self::RTF_TOKEN_REGEX, '', $text);
    }

    /** Convert a color token like \cf1 to a hex color like #FF0000, uses the color lookup table */
    function colorHexForToken($token)
    {
        $index = $this->colorTokenToIndex($token);
        return $this->colorHex($index);
    }

    /** Looks up the color table index for a token, converts something like "\cf1" to "1" */
    function colorTokenToIndex($token)
    {
        preg_match('#' . self::RTF_COLOR_REGEX . '#', $token, $matches);
        $index = $matches[1];
        return $index;
    }

    /** Gets the hex value for color at specified index in the color lookup table */
    function colorHex($index)
    {
        if (isset($this->color_table[$index])) {
            return $this->color_table[$index];
        } else {
            throw new Exception("No color table entry at index [$index]");
        }
    }

    /** Replace only the first occurrence of #find with $replace in text $text */
    function replaceOnce($find, $replace, $text)
    {
        return implode($replace, explode($find, $text, 2));
    }

    function debug($msg)
    {
        $this->doDebug($msg);
        $this->doDebug("\n=======================================================\n");
    }

    function doDebug($msg)
    {
        //echo $msg;
    }

}