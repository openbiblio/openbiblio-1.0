<script language="JavaScript" >
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>
// JavaScript Document
"use strict";

var wc = {
	init: function () {
		wc.url = '../catalog/catalogServer.php';
		wc.initWidgets();
		$('.help').hide();

		if ('<?php echo $_SESSION['show_item_photos'];?>' == 'Y') {
			if (Modernizr.video) {
				console.log('video is supported here');
		  	var html = '<video id="camera" width="<?php echo Settings::get('thumbnail_height');?>"'
								 + ' height="<?php echo Settings::get('thumbnail_width');?>"'
								 + ' preload="none" ></video>';
				$('#canvasIn').before(html);
				$('#fotoDiv').show();
			} else {
				console.log('video is not supported here');
			}
		}

		wc.video = document.querySelector('video');
		wc.videoOpts = { video:true, audio:false, };
    var errBack = function (error) {
 			alert("<?php echo T("allowWebcamAccess4Camera"); ?>");
			console.log("Video capture error: ", error.code);
		};
		var handleVideo = function (stream) {
	    wc.video.src = window.URL.createObjectURL(stream);
		};
		navigator.getUserMedia = navigator.getUserMedia
													 || navigator.webkitGetUserMedia || navigator.mozGetUserMedia
													 || navigator.msGetUserMedia || navigator.oGetUserMedia ;
		if (navigator.getUserMedia) {
		    navigator.getUserMedia(wc.videoOpts, handleVideo, errBack);
		}

		wc.fotoWidth = <?php echo Settings::get('thumbnail_width');?> || 176;
		wc.fotoHeight = <?php echo Settings::get('thumbnail_height');?> || 233;
		wc.fotoRotate = <?php echo Settings::get('thumbnail_rotation');?> || 0;

		wc.canvasOut = document.getElementById('canvasOut'),
		wc.ctxOut = canvasOut.getContext('2d');
		wc.canvasIn = document.getElementById('canvasIn'),
		wc.ctxIn = canvasIn.getContext('2d');

		$('input.fotoSrceBtns').on('click',null,wc.changeImgSource);
		$('#capture').on('click',null,wc.takeFoto);
		$('#browse').on('change',null,wc.getFotoFile);
		$('#addFotoBtn').on('click',null,wc.sendFoto);
		$('#updtFotoBtn').on('click',null,wc.doUpdatePhoto);
		$('#deltFotoBtn').on('click',null,wc.doDeletePhoto);

		/* support drag and drop during 'Browse mode' */
		wc.canvasOut.ondragover = function (e){
			// keep browser from replacing entire page with dropped image //
      e.preventDefault();
			return false;
		};
		wc.canvasOut.ondrop = function (e) { wc.getFotoDrop(e); };

		wc.resetForm();
	},
	//------------------------------
	initWidgets: function () {
	},
	//----//
	resetForm: function () {
		$('.help').hide();
		//$('#errSpace').hide();
		$('#camera').hide();
		$('#canvasIn').hide();
		$('#fotoAreaDiv').show();
		$('#addFotoBtn').show();
		wc.changeImgSource();
	},

	//----//
	changeImgSource: function (e) {
		var chkd = $('input[name=imgSrce]:checked', '#fotoForm').val();
		if (chkd == 'cam') {
			$('#camera').attr('autoplay',true);
			//$('#fotoName').val('filename.jpg');
			$('#capture').show();
			$('#browse').hide();
		} else {
			$('#camera').removeAttr('autoplay');
			//$('#fotoName').val('');
			$('#capture').hide();
			$('#browse').show();
		}
	},
	//----//
	rotateImage: function (angle) {
    var tw = wc.canvasIn.width/2,
				th = wc.canvasIn.height/2,
				angle = angle*(Math.PI/180.0);
		wc.ctxIn.save();
		wc.ctxIn.translate(tw, th);
		wc.ctxIn.rotate(angle);
		wc.ctxIn.translate(-th, -tw);
		wc.ctxIn.drawImage(canvasIn, 0,0);
		wc.ctxIn.restore();
	},
	showImage: function (fn) {
		var img = new Image;
		img.onload = function() { wc.ctxOut.drawImage(img, 0,0, wc.fotoWidth,wc.fotoHeight); };
		img.src = fn;
	},
	eraseImage: function () {
		wc.ctxOut.clearRect(0,0, wc.canvasOut.width,wc.canvasOut.height)
		wc.ctxIn.clearRect(0,0, wc.canvasIn.width,wc.canvasIn.height)
	},
	readFile: function (file) {
		var reader = new FileReader();  // output is to reader.result
		reader.onerror = function () {
			console.log('FileReader error: '+reader.error);
			return;
		}
		reader.onloadend = function(e) {
    	var tempImg = new Image();
    	tempImg.src = reader.result;
    	tempImg.onload = function() {
        wc.ctxOut.drawImage(tempImg, 0, 0, wc.fotoWidth,wc.fotoHeight);
			}
		};
    reader.readAsDataURL(file);
	},

	//------------------------------
	getFotoDrop: function (e) {
		e.preventDefault();
		e = e || window.event;
		var files = e.dataTransfer.files;
		if (files) wc.readFile(files[0]);
	},
	getFotoFile: function (e) {
		// Get the FileList object from the file select event
		var files = e.target.files;
		if(files.length === 0) return;
		var file = files[0];
		if(file.type !== '' && !file.type.match('image.*')) return;
		//$('#fotoName').val($('#browse').val());
		wc.readFile(file)
	},
	takeFoto: function () {
  	$('#errSpace').hide();
		wc.ctxIn.drawImage(wc.video,0,0, wc.fotoHeight,wc.fotoWidth);
		wc.rotateImage(wc.fotoRotate);
		wc.ctxOut.drawImage(wc.canvasIn,0,0, wc.fotoWidth,wc.fotoHeight, 0,0, wc.fotoWidth,wc.fotoHeight);
	},
	sendFoto: function (e) {
		e.stopPropagation();
    $('#errSpace').hide();
		var imgMode = '',
				url = $('#fotoName').val();
		imgMode = (url.substr(-3) == 'png')? 'image/png' : 'image/jpeg';
		$.post(wc.url,{'mode':'addNewPhoto',
									 'type':'base64',
									 'img':wc.canvasOut.toDataURL(imgMode, 0.8),
                   'bibid':$('#fotoBibid').val(),
									 'url': url,
                   'position':0,
									},
									function (response) {
										var data = JSON.parse(response);
										//console.log('image posting OK');
										var crntFotoUrl = '../photos/' + data[0]['url'];
										if (typeof(bs) !== 'undefined') bs.crntFotoUrl = crntFotoUrl;
										$('#fotoMsg').html('cover photo posted').show();
										$('#bibBlkB').html('<img src="'+crntFotoUrl+'" id="biblioFoto" class="hover" '
      									+ 'height="'+wc.fotoHeight+'" width="'+wc.fotoWidth+'" >');
										$('#photoAddBtn').hide();
										$('#photoEditBtn').show();
									}
		);
		//e.preventDefault();
		return false;
	},
	doUpdatePhoto: function () {
	  /// left as an exercise for the motivated - FL (I'm burned out on this project)
	},
	doDeletePhoto: function (e) {
		if (confirm("<?php echo T("Are you sure you want to delete this cover photo"); ?>")) {
	  	$.post(bs.url,{'mode':'deletePhoto',
										 'bibid':$('#fotoBibid').val(),
										 'url':$('#fotoName').val(),
										},
										function(response){
											wc.eraseImage();
											$('#bibBlkB').html('<img src="../images/shim.gif" id="biblioFoto" class="noHover" '
      													+ 'height="'+wc.fotoHeight+'" width="'+wc.fotoWidth+'" >');
                      idis.crntFoto = null;
						          $('#fotoName').val('');
											$('#photoAddBtn').show();
											$('#photoEditBtn').hide();
											$('#fotoMsg').html('cover photo deleted').show();
										}
			);
		}
		e.stopPropagation();
		return false;
	},

}
/*  this code should be explicity initialized when needed unless
		the appearance of the video allow/deny prompt is acceptable
 */
//$(document).ready(wc.init);
</script>
