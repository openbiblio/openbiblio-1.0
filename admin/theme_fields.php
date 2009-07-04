<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

?>

<table class="primary">
	<!--tr>
		<th colspan="5" valign="top" nowrap="yes" align="left">
			<?php //echo $headerWording;?> <?php //echo T("Theme"); ?>
		</td>
	</tr-->
	<tbody id="part1">
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Theme Name"); ?>
		</td>
		<td colspan="4" valign="top" class="primary">
			<?php	echo inputfield('text',"themeName",'',array('size'=>'40','maxlength'=>'40')); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Table Border Color:"); ?>
		</td>
		<td colspan="4" valign="top" class="primary">
			<?php echo inputfield('text',"borderColor",'',array('size'=>'10','maxlength'=>'20')); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Error Color:"); ?>
		</td>
		<td colspan="4" valign="top" class="primary">
			<?php echo inputfield('text',"primaryErrorColor",'',array('size'=>'10','maxlength'=>'20')); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Table Border Width:"); ?>
		</td>
		<td colspan="4" valign="top" class="primary">
			<?php echo inputfield('text',"borderWidth",'',array('size'=>'2','maxlength'=>'2')); ?>
			<?php echo T("px"); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Table Cell Padding:"); ?>
		</td>
		<td colspan="4" valign="top" class="primary">
			<?php echo inputfield('text',"tablePadding",'',array('size'=>'2','maxlength'=>'2')); ?>
			<?php echo T("px"); ?>
		</td>
	</tr>
	</tbody>
	
	<txhead>
	<tr>
		<th valign="top">
			&nbsp;
		</td>
		<th valign="top">
			<?php echo T("Title"); ?>
		</td>
		<th valign="top">
			<?php echo T("Main Body"); ?>
		</td>
		<th valign="top">
			<?php echo T("Navigation"); ?>
		</td>
		<th valign="top">
			<?php echo T("Tabs"); ?>
		</td>
	</tr>
	</xthead>
	
	<tbody id="part2">
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Background Color:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"titleBg",'',array('size'=>'10','maxlength'=>'20')); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"primaryBg",'',array('size'=>'10','maxlength'=>'20')); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"alt1Bg",'',array('size'=>'10','maxlength'=>'20')); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"alt2Bg",'',array('size'=>'10','maxlength'=>'20')); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Font Face:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"titleFontFace",'',array('size'=>'10','maxlength'=>'128')); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"primaryFontFace",'',array('size'=>'10','maxlength'=>'128')); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"alt1FontFace",'',array('size'=>'10','maxlength'=>'128')); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"alt2FontFace",'',array('size'=>'10','maxlength'=>'128')); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Font Size:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"titleFontFace",'',array('size'=>'2','maxlength'=>'2')); ?>
			<?php echo T("px"); ?>
			<?php echo inputfield('checkbox',"titleFontBold",'CHECKED',null,$postVars["titleFontBold"]); ?>
			<?php echo T("bold");?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"primaryFontFace",'',array('size'=>'2','maxlength'=>'2')); ?>
			<?php echo T("px"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"alt1FontFace",'',array('size'=>'2','maxlength'=>'2')); ?>
			<?php echo T("px"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"alt2FontFace",'',array('size'=>'2','maxlength'=>'2')); ?>
			<?php echo T("px"); ?>
			<?php echo inputfield('checkbox',"alt2FontBold",'CHECKED',null,$postVars["alt2FontBold"]); ?>
			<?php echo T("bold");?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Font Color:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"titleFontColor",'',array('size'=>'10','maxlength'=>'20')); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"primaryFontColor",'',array('size'=>'10','maxlength'=>'20')); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"alt1FontColor",'',array('size'=>'10','maxlength'=>'20')); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"alt2FontColor",'',array('size'=>'10','maxlength'=>'20')); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Link Color:"); ?>
		</td>
		<td valign="top" class="primary">
			&nbsp;
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"primaryFontColor",'',array('size'=>'10','maxlength'=>'20')); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"alt1FontColor",'',array('size'=>'10','maxlength'=>'20')); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text',"alt2FontColor",'',array('size'=>'10','maxlength'=>'20')); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Align:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php
			  $data = array('left'=>'left','center'=>'center','right'=>'right');
				echo inputfield('select','titleAlign',$postVars['titleAlign'],NULL,$data);
			?>
		</td>
		<td colspan="3" valign="top" class="primary">
			&nbsp;
		</td>
	</tr>
	</tbody>
	<tfoot>
	<tr>
		<td align="center" colspan="5" class="primary">
			<input type="button" onclick="editTheme()" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onclick="parent.location='../admin/theme_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>
	</tfoot>
</table>
