/*
 * JTip
 * By Cody Lindley (http://www.codylindley.com)
 * Under an Attribution, Share Alike License
 * JTip is built on top of the very light weight jquery library.
 */

//on page load (as soon as its ready) call JT_init

var tab_Colors = new Array();
tab_Colors['cadre_orange'] = '#558ed5';
tab_Colors['cadre_vert'] = '#9e5ece';
tab_Colors['cadre_rose'] = '#39c0e0';
tab_Colors['cadre_bleu'] = '#00ff73';
tab_Colors['cadre_violet'] = '#de4dd0';
tab_Colors['cadre_violet2'] = '#fe0000';

var tab_ImgDir = new Array();
tab_ImgDir['cadre_orange'] = 'bleu';
tab_ImgDir['cadre_vert'] = 'violet';
tab_ImgDir['cadre_rose'] = 'turquoise';
tab_ImgDir['cadre_bleu'] = 'vert';
tab_ImgDir['cadre_violet'] = 'violet2';
tab_ImgDir['cadre_violet2'] = 'rouge';


$(document).ready(JT_init);

$(document).ready (function() {

    $("div.jTip").hover(function() {
       
        var content = $("#"+$(this).attr('id')+" .jTip_content").html();   
        JT_show('', $(this).attr('id'), content);

    }, function() {
        //$('#JT').fadeOut('slow');
        $(this).unbind('mousemove');
        $('#JT').remove()
    });

});

function JT_init(){
	       $("a.jTip")
		   .hover(function(){JT_show(this.href,this.id,this.name)},function(){$('#JT').remove()})
           .click(function(){return false});	   
}

function JT_show(url,linkId,title){
	if(title == false)title="&nbsp;";
	var de = document.documentElement;
	var w = self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
	var hasArea = w - getAbsoluteLeft(linkId);
	var clickElementy = getAbsoluteTop(linkId) - 200; //set y position
	
	var queryString = url.replace(/^[^\?]+\??/,'');
	var params = parseQuery( queryString );
	if(params['width'] === undefined){params['width'] = 300};
	if(params['link'] !== undefined){
        $('#' + linkId).bind('click',function(){window.location = params['link']});
        $('#' + linkId).css('cursor','pointer');
	}
	
    $("body").append("<div id='JT' style='display:none;width:"+params['width']*1+"px'>"
        +"<div id='JT_hg'><img src='img/img_css/popup/"+tab_ImgDir[linkId]+"/hg.png' height='20' width='20'></div>"
        +"<div id='JT_top'></div>"
        +"<div id='JT_hd'><img src='img/img_css/popup/"+tab_ImgDir[linkId]+"/hd.png' height='20' width='20'></div>"
        +"<div id='JT_content'>"+title+"</div>"
        +"<div id='JT_bg'><img src='img/img_css/popup/"+tab_ImgDir[linkId]+"/bg.png' height='20' width='20'></div>"
        +"<div id='JT_bottom'></div>"
        +"<div id='JT_bd'><img src='img/img_css/popup/"+tab_ImgDir[linkId]+"/bd.png' height='20' width='20'></div>"
        +"<div id='JT_arrow'></div></div>");//right side
    var arrowOffset = getElementWidth(linkId) - 250;
    var clickElementx = getAbsoluteLeft(linkId) + arrowOffset; //set x position
	
    $('#JT_arrow').append('<img src="img/img_css/popup/'+tab_ImgDir[linkId]+'/fleche.png" width="100" height="23">');
    $('#JT').css({left: clickElementx+"px", top: clickElementy+"px" });
    $('#JT_content, #JT_top, #JT_bottom').css({backgroundColor: tab_Colors[linkId] });
    $('#JT_top, #JT_bottom').css('height', '20px');
	$('#JT').fadeIn("slow");

    $("#"+linkId).mousemove(function(e) {
        newX = e.pageX - (300/2);
        newY = e.pageY - 300;
        $("#JT").css({ top:newY+"px", left:newX+"px"});
    });

}

function getElementWidth(objectId) {
	x = document.getElementById(objectId);
	return x.offsetWidth;
}

function getAbsoluteLeft(objectId) {
	// Get an object left position from the upper left viewport corner
	o = document.getElementById(objectId)
	oLeft = o.offsetLeft            // Get left position from the parent object
	while(o.offsetParent!=null) {   // Parse the parent hierarchy up to the document element
		oParent = o.offsetParent    // Get parent object reference
		oLeft += oParent.offsetLeft // Add parent left position
		o = oParent
	}
	return oLeft
}

function getAbsoluteTop(objectId) {
	// Get an object top position from the upper left viewport corner
	o = document.getElementById(objectId)
	oTop = o.offsetTop            // Get top position from the parent object
	while(o.offsetParent!=null) { // Parse the parent hierarchy up to the document element
		oParent = o.offsetParent  // Get parent object reference
		oTop += oParent.offsetTop // Add parent top position
		o = oParent
	}
	return oTop
}

function parseQuery ( query ) {
   var Params = new Object ();
   if ( ! query ) return Params; // return empty object
   var Pairs = query.split(/[;&]/);
   for ( var i = 0; i < Pairs.length; i++ ) {
      var KeyVal = Pairs[i].split('=');
      if ( ! KeyVal || KeyVal.length != 2 ) continue;
      var key = unescape( KeyVal[0] );
      var val = unescape( KeyVal[1] );
      val = val.replace(/\+/g, ' ');
      Params[key] = val;
   }
   return Params;
}

function blockEvents(evt) {
              if(evt.target){
              evt.preventDefault();
              }else{
              evt.returnValue = false;
              }
}
