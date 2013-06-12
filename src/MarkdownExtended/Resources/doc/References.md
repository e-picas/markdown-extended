Block: Miscellaneous
Title: References

You can use references for links or images. This allows you to write some more easy-to-read
content by writing the attributes of the element after this content.

    A paragraph with a referenced [hypertext link][myid] and some more text embedding an
    image: ![image for the test][myimage].

    [myid]: http://example.com/ "Optional link title"
    [myimage]: http://example.com/test.com "Optional image title" width=40px height=40px

References are basically constructed by writting the ID of the definition in content and
this definition anywhere in the document, on a single line, with no space to begin and
writting first the ID between brackets followed by a colon and the classic definition of
the object.

This way you can write all your references at the end of your document, for example, and
make multi-calls of each reference.

The references allows you to add some attributes for the generated tag. Just write them
at the end of the reference line.
