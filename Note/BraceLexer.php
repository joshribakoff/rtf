<?php
/**
 * Lexes nested brances, given a string like "{foo{bar}}" returns an array like: array('foo'=>array('bar'))
 */
class JoshRibakoff_Note_BraceLexer
{
    protected $text;
    protected $rtf;
    protected $pointer;
    protected $nestingLevel;
    protected $lastNestingLevel;
    protected $matchedToken;
    protected $segment = null;

    function tokenize()
    {
        $this->parseData = array(0 => null);
        $this->pointer = 0;
        $this->nestingLevel = 0;
        $this->lastNestingLevel = null;
        do {
            $this->matchedToken = $this->matchToken();
            //$this->debug();

            if ('T_OPENBRACE' == $this->matchedToken['token'] && $this->nestingLevel >= 1) {
                $this->handleData();
            }

            if ('T_CLOSEBRACE' == $this->matchedToken['token'] && $this->nestingLevel >= 2) {
                $this->handleData();
            }

            if ('T_CLOSEBRACE' == $this->matchedToken['token']) {
                if ($this->nestingLevel <= 1) {
                    $this->handleCloseBrace();
                }
                $this->nestingLevel--;
            }

            if ('T_ESCAPE' == $this->matchedToken['token']) {
                $this->handleEscape();
            }
            if ('T_DATA' == $this->matchedToken['token']) {
                $this->handleData();
            }

            $this->lastNestingLevel = $this->nestingLevel;
            if ('T_OPENBRACE' == $this->matchedToken['token']) {
                if ($this->nestingLevel <= 1) {
                    $this->handleOpenBrace();
                }
                $this->nestingLevel++;
            }
            $this->pointer += strlen($this->matchedToken['match']);
        }
        while ($this->pointer < strlen($this->rtf));

        if (0 != $this->nestingLevel) {
            throw new Exception('Nesting level error, a brace was opened but not closed. Possibly asking me to parse an invalid (truncated) RTF file here!');
        }

        return array_values($this->parseData);
    }

    function handleOpenBrace()
    {
        if ($this->lastNestingLevel !== $this->nestingLevel) {
            $this->nextSegment();
        }
    }

    function handleCloseBrace()
    {
        $this->segment++;
    }

    function handleData()
    {
        if ($this->lastNestingLevel !== $this->nestingLevel && $this->nestingLevel <= 1) {
            $this->nextSegment();
        }

        if ($this->nestingLevel == 0) {
            if (!isset($this->parseData[$this->segment])) {
                $this->parseData[$this->segment] = null;
            }
            $this->parseData[$this->segment] .= $this->matchedToken['match'];
        }
        if ($this->nestingLevel >= 1) {
            if (!isset($this->parseData[$this->segment][0])) {
                $this->parseData[$this->segment] = array(0 => null);
            }
            $this->parseData[$this->segment][0] .= $this->matchedToken['match'];
        }
    }

    function handleEscape()
    {
        if ($this->lastNestingLevel !== $this->nestingLevel && $this->nestingLevel <= 1) {
            $this->nextSegment();
        }

        if ($this->nestingLevel == 0) {
            if (!isset($this->parseData[$this->segment])) {
                $this->parseData[$this->segment] = null;
            }
            $this->parseData[$this->segment] .= str_replace('\\', '', $this->matchedToken['match']);
        }
        if ($this->nestingLevel >= 1) {
            if (!isset($this->parseData[$this->segment][0])) {
                $this->parseData[$this->segment] = array(0 => null);
            }
            $this->parseData[$this->segment][0] .= $this->matchedToken['match'];
        }
    }

    function nextSegment()
    {
        if (is_null($this->segment)) {
            $this->segment = 0;
        } else {
            $this->segment++;
        }
    }

    function matchToken()
    {
        foreach ($this->tokens() as $name => $pattern) {
            $string = substr($this->rtf, $this->pointer);
            if (preg_match('#^' . $pattern . '#', $string, $matches)) {
                return array(
                    'match' => $matches[0],
                    'token' => $name,
                    'position' => $this->pointer
                );
            }
        }
        throw new Exception('Unmatched token at ' . $this->pointer);
    }

    function tokens()
    {
        return array(
            'T_ESCAPE' => '(\\\{|\\\})',
            'T_OPENBRACE' => '(\{)',
            'T_CLOSEBRACE' => '(\})',
            'T_DATA' => '([^\{\}])',

        );
    }

    function setRTF($rtf)
    {
        $this->rtf = $rtf;
    }

    function debug()
    {
        print_r($this->matchedToken + array(
            'lastnest' => $this->lastNestingLevel,
            'nest' => $this->nestingLevel,
            'segment' => $this->segment
        ));
    }

}