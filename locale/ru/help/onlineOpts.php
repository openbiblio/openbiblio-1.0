
<h3>New Item Pages:</h3>
<ol>
<p>
Protocol: basically the 'language' used to talk with the host. SRU uses the normal WWW stuff, so nothing special is needed. YAZ is very different and you will have to add the YAZ module to your servers PHP support.
</li>
<li>
Maximum No of hits: how many books do you want listed before the process stops? Ask for a lot and you PC may run out of memory, or you may fall asleep waiting.
</li>
<li>
Keep Dashes: Some hosts want ISBN with dashes, some do not.
</li>
<li>
Call Number Type: which call number style does your library use.
</li>
<li>
Use Default Dewey: Assign the following dewey code (assuming you picked Dew above) for you call number, if noe is provided by the host.
</li>
<li>
Default Dewet Code:  see above
</li>
<li>
Generate Cutter if none: Generate a Cutter code if the host doesnt provide one.
</li>
<li>
Cutter Type to create: This would normally match the call number type selected above
</li>
<li>
Dewey Cutter Word no.: Normally Cutter codes use the first letter of the first word in a title; if you library has a lot of books whose titles begin with 'Energy' and you selected word #1 they would all get the same code. Not much help.
</li>
<li>
Below this point is experimental; You can safely ignore or not as you like.
</li>
<li>
Use Auto Collection: If you want Lookup to recognize  Fiction and assign a proper call number when none is supplied by the host.
</li>
<li>
Fiction collection Name: Some libraries use 'Fic', some 'F', others ....  What do you want to see?
</li>
<li>
Index no. of Fiction codes to use: When Lookup encounters one of the folowing, which one is your preference.
</li>
</ol>
