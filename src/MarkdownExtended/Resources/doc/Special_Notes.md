Block: Miscellaneous
Title: Special Notes

You can construct a **glossary** or a **bibliography** by using special footnotes.

**Glossary notes** are constructed like a footnote, except that the first line of the note
will contain `glossary:` followed by the term defined. The associated definition has to be
placed on a second line.

    A paragraph with a referenced [glossary term][^myterm] ...

    [^myterm]: glossary: the term defined (an optional sort key)
    The term definition ... which may be multi-line.

**Bibliography notes** are constructed like a glossary, except that their IDs begins by a
sharp `#` and the in-text call may contain two parts.

    This is a statement that should be attributed to its source [p. 23][#Doe:2006].

    [#Doe:2006]: John Doe. *Some Big Fancy Book*.  Vanity Press, 2006.
