<h1>Understanding barcodes:</h1>
<br><br>

Help Sub Sections:
<ul>
  <li><a href="#limi">Limitations for OpenBiblio barcode numbers</a></li>
  <li><a href="#memb">Member barcodes</a></li>
  <li><a href="#copy">Bibliography copy barcodes</a></li>
  <li><a href="#scan">Using a barcode scanner</a></li>
  <li><a href="#labe">Printing barcodes and labeling copies</a></li>
  <li><a href="#link">Some links with helpful sources on barcodes</a></li>
</ul>
<br><br>


<a name="limi">Limitations for OpenBiblio barcode numbers</a>:
<ul>
  <li>Barcode number must be all alphabetic and/or numeric characters: a-zA-Z0-9<br>
Though many barcode symbologies (type of barcodes) can encode special characters, OpenBiblio does not allow
non alphanumeric characters like - $ % SPACE . / + 
  </li>
  <li>After submitting, alphabetical characters are converted to lowercase.</li>
  <li>Maximum length of a barcode is 20 characters.</li>
</ul>
When choosing a numbering structure you have to be aware of these limitations. Also make sure the barcode 
symbology you will use for printing does not conflict with the limitations. For example: 
Code 39 (3 of 9, not the extended version) encodes only uppercase letters.
<br><br><br>
  
<a name="memb">Member barcodes</a>:
<br>
To have efficient checkout workflow, especially when using a barcode scanner, use a numbering structure with 
leading zeros for member barcodes. If every member barcode has a fixed number of digits, OpenBiblio is forced 
to show the Member Info page after scanning or typing a member barcode correctly. This allows subsequent 
scanning of copy barcodes to check out items.
<br>
If a scanned or typed member barcode does not have the required fixed number of digits, Search results for 
members could appear, even if the member barcode would be unique. The reason for this is Search Member by Card 
Number does not search for an exact match, just like Search Member by Last Name it takes the Card Number as a 
Search Phrase with right truncation.
<br><br><br>

<a name="copy">Bibliography copy barcodes</a>:
<br>
If your library already has labeled barcodes to copies, make sure the numbering structure does not conflict with 
<a href="#limi">Limitations for OpenBiblio barcodes</a>.
<br>
If your library never assigned unique numbers to copies you have to decide about a suitable numbering structure or use 
<a href="../shared/help.php?page=biblioCopyEdit#auto">Barcode Number - Autogenerate</a>.
This has a useful property when switching from a simple card file: 
<a href="../shared/help.php?page=biblioCopyEdit#seri">Copy Serial Numbers integrated in Barcode Numbers</a> 
facilitate entering copy information from a simple card file when unique numbers were not assigned, only serial numbers for multiple copies of a title. 
<br><br><br>

<a name="scan">Using a barcode scanner:</a>
<br>
Any barcode scanner that emulates the keyboard is supported by OpenBiblio. This includes USB scanners 
and scanners that connect between the computer and its keyboard (keyboard wedge).<br>
Most barcode scanners can be programmed to automatically append a carriage return (same as hitting 
ENTER on the keyboard) to every scan. In this way, OpenBiblio will be able to process your request 
as soon as you scan the item.
<br><br><br>

<a name="labe">
Labeling copies with Barcodes</a> makes circulation efficient, especially when using actual barcodes and a barcode scanning device.
<ul>
  <li>Labels containing text and numbers can be printed using a PDF report; Reports tab, Print Labels.</li>
  <li>Actual barcodes can be produced outside OpenBiblio, for example by using a word processor and a barcode font.</li>
</ul>
<br>

<a name="link">Some links with helpful sources on barcodes</a>
<br>
<a href="javascript:popSecondaryLarge('http://en.wikipedia.org/wiki/Barcode')">Wikipedia: barcode</a>
<br>
<a href="javascript:popSecondaryLarge('http://www.barcodesymbols.com/')">http://www.barcodesymbols.com/</a>
<br>
<a href="javascript:popSecondaryLarge('http://www.barcodefaq.com/')">http://www.barcodefaq.com/</a>
