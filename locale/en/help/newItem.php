<h3>New Item Pages:</h3>
<p>
In order to enter a new item, the user must enter all the particulars of that item.
<br />
There are three methods available to add to the catalog:
	<ol>
	  <li>Manual Entry where the user types all the pertinant data into a form</li>
	  <li>Import of a single item's data from an on-line repository</li>
	  <li>Bulk import an existing file of data formated for MARC</li>
	</ol>
Only the first two will be described here.
</p>
<ol>
	<li>
	Manual Entry:<br />
	  Click the button marked 'Manual Entry' at the top of the New Item Screen.<br />
	  Fill in the information as best you can.<br />
	</li>
	<li>
	On-Line Entry: (see additional info blow)<br />
		Enter enough information to find the item of interest;<br />
		  Usually this would be the ISBN, but frequently the Title & Author will serve.<br />
		After you enter (i.e. 0-123-456-789) for an ISBN,
		you must 'tab' to another location in order to activate the 'search' button.<br />
		If there is a single hit, you will see a form partially filled in.<br />
	  Fill in any additional information as best you can.<br />
	  If there are multiple hits, choose the one most suitable.
	</li>
</ol>
Some items are marked as required - you MUST supply those.<br />
Call Numbers are optional.<br />
Additional information can be added later as it becomes available.<br />
<br />
<p>
NOTES:<br />
On-line search depends on the information entered in the
Online Options and Hosts sections of the Admin Module.
Be sure to fill those out thoughtfully and carefully.
<br />
Currently On-line searches defaults to using the International SRU protocol.
You may choose to use the Z3950 protocol via the YAZ toolbox,
but installation of YAZ of YAZ is problematic and
varies with the Computer OS you use.
<br />
Further, this implementation of the SRU client supports only
the Dublin Core (dc) record schema,
and requires the host to respond with the marcxml record format.
If the repository (host) you prefer does not support both of these
you will need to select another host repository which does.
</p>