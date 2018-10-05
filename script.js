/**
 * DokuWiki Plugin multiselect (JavaScript Component) 
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  lisps
 */
function multiselectdone(data) {
	var ret = ajaxedit_parse(data);
	ajaxedit_checkResponse(ret);
}

    
function multiselectclick(id) {
    if(!(JSINFO && JSINFO['acl_write'] === '1')) return;
    var id = multiselect_escapeStr(id);

	var $myself = jQuery('#multiselect_'+id);        
	if ($myself) {
		$myself.toggle();
		$myself.offset({top:jQuery('#multismiley_'+id).offset().top-7,left:jQuery('#multismiley_'+id).offset().left-7}); 
		 
	}
}    

function multiclickclick(pageid,id,count) {
	//hide selector window
    multiselectclick(id);

    var id = multiselect_escapeStr(id);

	//change smiley
	var mysmiley=jQuery('#multismiley_'+id)[0];
	var myselect=jQuery('#multiclick_'+id+"_"+count)[0];
	
	if ( (mysmiley)&&(myselect) ) {
		//send only changes
		if (mysmiley.innerHTML != myselect.innerHTML) {
			mysmiley.innerHTML=myselect.innerHTML;
			var token;
			if (myselect.firstChild.alt) {
				token=myselect.firstChild.alt;
			} else {
				token=myselect.innerHTML;
			}
			
			var idx = null;
			if(jQuery("#multiselect_"+id).parents('div.sortable').length != 0) { //sortable fix
				idx = jQuery("#multiselect_"+id).data("plugin-multiselect-idx");
			} else {
				//because multiselect can be moved it is necessary to idx it on the fly;
				idx= ajaxedit_getIdxByIdClass('multismiley_'+id,"multismiley_"+multiselect_escapeStr(pageid));
			}
			ajaxedit_send2(
				'multiselect',
				idx,
				multiselectdone,
				{	
                    pageid:pageid,
					token:token,
					count:ajaxedit_getIdxByIdClassNodeid('multiclick_'+id+'_'+count,'multiclicker','multiselect_'+id),
				}		
			);

		}
		//change order so we are up to date
		jQuery(jQuery('#multiselect_'+id).children(':first')).insertBefore('#multiclick_'+id+"_"+count);
		jQuery('#multiselect_'+id).prepend(jQuery('#multiclick_'+id+"_"+count));
	}
	
}
	

//close multiselector onclick expect multismiley itselfs
jQuery(document).ready(function(){
	jQuery(document).on('click',function(e) {
		jQuery('.multiselector').hide();
	});
	jQuery(document).on('click','.multismiley',function(e) {
		e.stopPropagation();
		return false;
	});
});


function multiselect_escapeStr(str) 
{
    if (str)
        return str.replace(/([ #;?%&,.+*~\':"!^$[\]()=>|\/@])/g,'\\$1');      

    return str;
}
