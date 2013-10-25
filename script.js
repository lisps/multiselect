/**
 * DokuWiki Plugin multiselect (JavaScript Component) 
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  lisps
 */
function multiselectdone(data) {
	ret = ajaxedit_parse(data);
	ajaxedit_checkResponse(ret);
}

    
function multiselectclick(id) {
	$myself = jQuery('#multiselect'+id);        

	if ($myself) {
		$myself.toggle();
		$myself.offset({top:jQuery('#multismiley'+id).offset().top-7,left:jQuery('#multismiley'+id).offset().left-7}); 
		 
	}
}    

function multiclickclick(pageid,id,count) {
	//hide selector window
	multiselectclick(id);

	//change smiley
	var mysmiley=document.getElementById('multismiley'+id);
	var myselect=document.getElementById('multiclick'+id+"_"+count);
	
	if ( (mysmiley)&&(myselect) ) {
		//send only changes
		if (mysmiley.innerHTML != myselect.innerHTML) {
			mysmiley.innerHTML=myselect.innerHTML;
			if (myselect.firstChild.alt) {
				token=myselect.firstChild.alt;
			} else {
				token=myselect.innerHTML;
			}
			ajaxedit_send(
				'multiselect',
				ajaxedit_getIdxByIdClass('multismiley'+id,"multismiley"),//because multiselect can be moved it is necessary to idx it on the fly 
				multiselectdone,
				{	
					token:token,
					count:ajaxedit_getIdxByIdClassNodeid('multiclick'+id+"_"+count,'multiclicker','multiselect'+id),
				}		
			);

		}
		//change order so we are up to date
		jQuery(jQuery('#multiselect'+id).children(':first')).insertBefore('#multiclick'+id+"_"+count);
		jQuery('#multiselect'+id).prepend(jQuery('#multiclick'+id+"_"+count));
	}
	
}
	

//close multiselector onclick expect multismiley itselfs
jQuery(document).ready(function(){
	jQuery(document).click(function(e) {
		jQuery('.multiselector').hide();
	});
	jQuery('.multismiley').click(function(e) {
		e.stopPropagation();
		return false;
	});
});
