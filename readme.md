Main Usage
======
```php
$note = new JoshRibakoff_Note;
$note->setRTF($yourRTFDocumentAsText);
$note->formatHTML(); // returns your RTF converted to HTML as best as possible.
```

Convert between RTF documents & HTML or plaintext.
=========================

I was tasked with the wild job of displaying data from a closed sourced microsoft product in our PHP web app, allowing it to be manipulated in a WYSIWYG editor, and written back. It turned out this product used an MSSQL database which PHP can work with. The problem then was the manipulation and conversion of RTF to HTML and back. So I created this monstrosity using TDD. It was really interesting to compare how elegant HTML is compared to RTF, given that they both do essentially the same thing.

I don't recommend using RTF for anything. Its a bad Microsoft standard. If you do, you can use this library which translates RTF to HTML & back (and of course plaintext) it supports font colors, sizes, bold, italics & underline.

Other Uses & Design Forces
============
Part of RTF is nested "{" and "}", nested data doesn't work well with regex which is why I wrote a full lexer. There is a class that can match any "{" to its matching "}", called recursively it can create a multi dimensional array. You could re-use this class to create a language parsed by PHP (if you were so inclined)

Short Comings
============
Given the absurdity of working with a Microsoft product, I did the minimal coding to complete the job. My task only required to support bold, underline, italics, and color. Some other basic stuff is supported but it will have undefined results with more advanced RTF like images & tables. Also my task was to simply take HTML entered in a WYSIWYG editor and prepend it to the top of the RTF document. If you want to actually convert an HTML document to RTF from scratch, you can't with this. Nor should you. The main goal is mostly to go from RTF to HTML to display an RTF document in your web app, while supporting basic manipulation of the RTF.


Tag Order
=========
In RTF, tags can be closed in a different order than they are opened.  It is a
 lexer which means it looks for tokens at the pointer position in the RTF text, when it finds a token it does some work
 and then advances the pointer, until there is no more RTF data to lex. If the current token closes a tag that wasn't the last tag opened, close as few tags as necessary to allow
      us to close the current token's tag. As we close each tag, we remove it from the stack of open tags, but push
      it onto a second stack so we can reopen them again after closing the tag for the current token. THen it Puts an HTML closing tag for the current token and removes that token from the stack of open tags

In order words:

```
\b bold \i bold+italics \b0 italics \i0
```

This would translate directly to:

```
<b>bold<i>bold+italics</b>italics</i>
```

Which is invalid. So this library "guesses" what the HTML version would look like & would do this:

```
<b>bold<i>bold+italics</i></b><i>italics</i>
```


Also RTF has a "font table" where colors are indexed to an ID in the header. This library manipulates that font table header.


Further Examples
======

**Convert simple RTF fragment to HTML**
```php
$converter = new JoshRibakoff_Note_RTFToHTML();
$converter->convert('\\ulfoo\\ulnone');
```
Creates this:
```
<u>foo</u>
```

**Modifying an existing RTF document to add some text (converted from HTML to RTF)**
```php
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
```

Creates this:
```
\\rtf1\\ansi\\ansicpg1251\\deff0\\deflang1049{\\fonttbl{\\f0\\fnil\\fcharset0 Microsoft Sans Serif;}{\\f1\\fnil\\fcharset204 Microsoft Sans Serif;}}
{\\colortbl }
\\f0\\fs17 \b bold note \b0\\par
\\uc1\\pard\\f0\\fs17 4/4/2013 2:36:12 PM - jribakoff: simple note\\par
\\f1\\par
```

`
