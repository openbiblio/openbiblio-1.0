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

		$('#capture').on('click',null,function() {
			console.log('click!');
			wc.ctxIn.drawImage(wc.video,0,0, 150,100);
			wc.rotateImage(-90);
			wc.ctxOut.drawImage(wc.canvasIn,0,0, 100,150, 0,0, 100,150);
		});

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
		$('#fotoDiv').show();
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

	share: function (){
		/* derived from the following work:
		 * 'Uploading directly from HTML5 canvas to imgur
		 * written by Jonas Wagner, on 9/11/11 4:28 PM.
		 */
		// trigger me onclick
    try {
        var img = canvas.toDataURL('image/jpeg', 0.9).split(',')[1];
    } catch(e) {
        var img = canvas.toDataURL().split(',')[1];
    }
    // open the popup in the click handler so it will not be blocked
    var w = window.open();
    w.document.write('Uploading...');
    // upload to imgur using jquery/CORS
    // https://developer.mozilla.org/En/HTTP_access_control
    $.ajax({
        url: 'http://api.imgur.com/2/upload.json',
        type: 'POST',
        data: {
            type: 'base64',
            // get your key here, quick and fast http://imgur.com/register/api_anon
            key: 'YOUR-API-KEY',
            name: 'neon.jpg',
            title: 'test title',
            caption: 'test caption',
            image: img
        },
        dataType: 'json'
    }).success(function(data) {
        w.location.href = data['upload']['links']['imgur_page'];
    }).error(function() {
        alert('Could not reach api.imgur.com. Sorry :(');
        w.close();
    });
	},
}
$(document).ready(wc.init);
</script>
