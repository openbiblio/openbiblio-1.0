<style>
	#nav {padding: 0; margin: 0; }
	#nav a { padding:0; margin: 0; }
	#nav.dyn li ul { display: none; }
	#nav.dyn li a.strong { font-weight: bold; }
	#nav.dyn li ul.show { display: block; }
	#nav.dyn li { padding-left: 1px;	}
/*	#nav.dyn li.parent { background:url(plus.gif) 0 2px no-repeat #fff; }*/
	#nav.dyn ul.open { background:url(minus.gif) 0 2px no-repeat #fff; }
</style>

<script type="text/javascript">
// JavaScript Document
sn = {
	init: function () {
		$('#nav').addClass('dyn');
		
		var crntPage = window.location.href.split('?')[1];
		$('#nav li a').each(function () {
		  var $link = $(this).attr('href').split('?')[1];
			if ($link == crntPage) {
				$(this).addClass('strong');
				return false; // === break
			}
		});

		$('#nav ul').parent().bind('click',null,sn.changeSection)
								.addClass('parent');
								
		$('ul#nav li:has(a.strong) ul').addClass('show open');
	},
	changeSection: function (e) {
		var t = e.target;
		var $firstList = $('#nav ul');
		if ($firstList.hasClass('show')) {
		  $firstList.removeClass('show');
		  $firstList.parent().removeClass('open').addClass('parent');
		} else {
		  $firstList.addClass('show');
		  $firstList.parent().removeClass('parent').addClass('open');
		}
		e.stopPropagation();
	}
}
$(document).ready(sn.init);
</script>