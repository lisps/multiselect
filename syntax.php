<?php
/**
 * DokuWiki Plugin multiselect (Syntax Component) 
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  lisps    
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/*
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_multiselect extends DokuWiki_Syntax_Plugin {

    private $_itemPos = array();
    function incItemPos() {
        global $ID;
        if(array_key_exists($ID,$this->_itemPos)) {
            return $this->_itemPos[$ID]++;
        } else {
            $this->_itemPos[$ID] = 1;
            return 0;
        }
    }
    function getItemPos(){
        global $ID;
        if(array_key_exists($ID,$this->_itemPos)) {
            $this->_itemPos[$ID];
        } else {
            return 0;
        }
    }
	/*
	* What kind of syntax are we?
	*/
	function getType() {
		return 'substition';
	}

	/*
	* Where to sort in?
	*/
	function getSort() {
		return 155;
	}

	/*
	* Paragraph Type
	*/
	function getPType() {
		return 'normal';
	}

	/*
	* Connect pattern to lexer
	*/
	function connectTo($mode) {
		$this->Lexer->addSpecialPattern("<multiselect[^>]*>",$mode,'plugin_multiselect');
	}

	/*
	* Handle the matches
	*/
	function handle($match, $state, $pos, Doku_Handler $handler) {
        global $ID;

		//extract payload
		$match=trim($match);
		$match=trim(substr($match,13,strlen($match)-13-1));
		//$opts["smileys"]=explode(" ",$match);
		$ret = preg_match_all('/[\w\[\]\(\)\{\}\|\?\+\-\*\^\$\\\.:!\/;,+#~&%]+|"[\w\[\]\(\)\{\}\|\?\+\-\*\^\$\\\.:!\/;,+#~&%\s]+"/u',$match,$matches);
		
        $smileys=str_replace('"','',$matches[0]);
		$itemPos=$this->incItemPos();
		$page=$ID;
        
		return array(
            $smileys,
            $itemPos,
            $page
        );
	}

	function iswriter() {
		global $conf;
		global $INFO;

		return($conf['useacl'] && $INFO['perm'] > AUTH_READ);
	}
	/*
	* Create output
	*/
	function render($mode, Doku_Renderer $renderer, $opt) {
		global $INFO;
        
        list($smileys,
            $itemPos,
            $page) = $opt;
        //dbg($opt);
        //dbg(1);
		if($mode == 'metadata') {
            $renderer->smiley(reset($smileys));
        } else if ($mode == 'xhtml') {
			//$renderer->nocache();
			$Hajax = $this->loadHelper('ajaxedit');
            
            if(!reset($smileys))  {
                $renderer->doc .= 'empty';
                return true;
            }
			//insert selector if writable
			if ($Hajax && $page == $INFO['id']) {
                $htmlid = hsc($page).'_'.$itemPos;

                $renderer->cdata("\n");
				$renderer->doc .= '<span id="multiselect_'.$htmlid.'" class="multiselector" style="display:none" data-plugin-multiselect-idx="'.$itemPos.'">';

				//insert all other smileys clickable
				$count=0;        
				foreach($smileys as $smiley) {
					$renderer->cdata("\n");
					$renderer->doc .= '<span id="multiclick_'.$htmlid."_".$count.'" class="multiclicker" onclick="multiclickclick(\''.hsc($page).'\',\''.$htmlid.'\','.$count.')">';
					$renderer->smiley($smiley);
					$renderer->doc .= '</span>';            
					$count++;
				}
                $renderer->cdata("\n");
				$renderer->doc .= "</span>";
                $renderer->doc .= '<span id="multismiley_'.$htmlid.'" title="multiselect:['.implode(', ',array_map('hsc',$smileys)).']" class="multismiley multismiley_'.hsc($page).'" onclick="multiselectclick(\''.$htmlid.'\')">';
                    $renderer->smiley(reset($smileys));
                $renderer->doc .= '</span>';
            } else {
                $renderer->doc .= '<span title="multiselect:['.implode(', ',array_map('hsc',$smileys)).']" class="multismiley">';
                    $renderer->smiley(reset($smileys));
                $renderer->doc .= '</span>';
            }

			//show first smiley
			$renderer->cdata("\n");
			
		} else if ($mode == 'odt'){
			$renderer->smiley(reset($smileys));
		}
		return true;
	}

}

//Setup VIM: ex: et ts=4 enc=utf-8 :
