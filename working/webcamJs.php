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

		wc.canvasOut = document.getElementById('canvasOut'),
		wc.ctxOut = canvasOut.getContext('2d');
		wc.canvasIn = document.getElementById('canvasIn'),
		wc.ctxIn = canvasIn.getContext('2d');

		wc.video = document.querySelector('video');
		wc.videoOpts = { video:true, audio:false, };

    var errBack = function (error) {
 			alert("You must allow webcam access for this page to work.");
			console.log("Video capture error: ", error.code);
		};

		if (navigator.getUserMedia) { // Standard
			console.log("standard 'getUserMedia()'");
			navigator.getUserMedia(wc.videoOpts,
				function(stream) {
					wc.video.src = stream;
				},
				errBack);
		} else if(navigator.webkitGetUserMedia) { // WebKit-prefixed
			console.log("webkit 'getUserMedia()'");
			navigator.webkitGetUserMedia(wc.videoOpts,
				function(stream){
					wc.video.src = window.webkitURL.createObjectURL(stream);
				},
				errBack);
		} else if(navigator.mozGetUserMedia) { // MOZ-prefixed
			console.log("firefox 'getUserMedia()'");
		  navigator.mozGetUserMedia(wc.videoOpts,
				function(stream) {
					wc.video.src = window.URL.createObjectURL(stream);
				},
				errBack);
		}

		$('input.fotoSrceBtns').on('click',null,wc.changeImgSource);

		$('#capture').on('click',null,function() {
			//console.log('click!');
    	$('#errSpace').hide();
			wc.ctxIn.drawImage(wc.video,0,0, 150,100);
			wc.rotateImage(-90);
			wc.ctxOut.drawImage(wc.canvasIn,0,0, 100,150, 0,0, 100,150);
		});

		$('#browse').on('change',null,function (e) {
			// Get the FileList object from the file select event
			var files = e.target.files;
			if(files.length === 0) return;
			var file = files[0];
			if(file.type !== '' && !file.type.match('image.*')) return;
			$('#fotoName').val($('#browse').val());
			wc.readFile(file)
		});

		$('#addFotoBtn').on('click',null,wc.sendFoto);
		//$('#fotoForm').on('submit',null,wc.sendFoto);

		wc.resetForm();
	},
	//------------------------------
	initWidgets: function () {
	},
	//----//
	resetForm: function () {
		//console.log('resetting Search Form');
		$('.help').hide();
		$('#errSpace').hide();
		$('#fotoAreaDiv').show();
		wc.changeImgSource();
	},
	//----//
	changeImgSource: function () {
		if ($("input:checked").val() == 'cam') {
			$('#capture').show();
			$('#browse').hide();
		} else {
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
        wc.ctxOut.drawImage(tempImg, 0, 0, 100,150);
			}
		};
    reader.readAsDataURL(file);
	},

	//------------------------------
	sendFoto: function (e) {
		e.stopPropagation();
    $('#errSpace').hide();
		var imgMode = '',
				url = $('#fotoName').val();
		imgMode = (url.substr(-3) == 'png')? 'image/png' : 'image/jpeg';
		$.post(wc.url,{'mode':'addNewFoto',
									 'type':'base64',
									 'img':wc.canvasOut.toDataURL(imgMode, 0.8),
                   'bibid':'99999',
									 'url': url,
                   'position':0,
									},
									function (response) {
									if (response.indexOf('posted') >= 0)
										$('#errSpace').html(response).show();
									});
		e.preventDefault();
		return false;
	},

}
$(document).ready(wc.init);
</script>
