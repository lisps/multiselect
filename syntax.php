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

	var $idcount = 0;

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
	function handle($match, $state, $pos, &$handler) {
	global $INFO;

		//extract payload
		$match=trim($match);
		$match=trim(substr($match,13,strlen($match)-13-1));
		//$opts["smileys"]=explode(" ",$match);
		$ret = preg_match_all('/[\w\[\]\(\)\{\}\|\?\+\-\*\^\$\\\.:!\/;,+#~&%]+|"[\w\[\]\(\)\{\}\|\?\+\-\*\^\$\\\.:!\/;,+#~&%\s]+"/u',$match,$matches);
		$opts["smileys"]=str_replace('"','',$matches[0]);
		$opts["id"]=$this->idcount++;
		$opts["page"]=$INFO["id"];
		$opts["valid"]=count($opts["smileys"])>0;

		return ($opts);
	}

	function iswriter() {
		global $conf;
		global $INFO;

		return($conf['useacl'] && $INFO['perm'] > AUTH_READ);
	}
	/*
	* Create output
	*/
	function render($mode, &$renderer, $opt) {
		global $INFO;

		if($mode == 'metadata') return false;
		if($mode == 'xhtml') {
			$renderer->nocache();
			$Hajax = plugin_load('helper', 'ajaxedit');
			if(!$Hajax){
				msg('Plugin ajaxedit is missing');
			}
			//insert selector if writable
			if ($this->iswriter()==TRUE && $Hajax && !is_a($renderer,'renderer_plugin_dw2pdf')) {
				$renderer->cdata("\n");
				$renderer->doc .= '<span id="multiselect'.$opt["id"].'" class="multiselector" style="display:none">';

				//insert all other smileys clickable
				$count=0;        
				foreach($opt["smileys"] as $smiley) {
					$imgfile=DOKU_BASE.'lib/images/smileys/'.$renderer->smileys[$smiley];

					$renderer->cdata("\n       ");
					$renderer->doc .= '<span id="multiclick'.$opt["id"]."_".$count.'" class="multiclicker" onclick="multiclickclick(\''.base64_encode($INFO["id"]).'\','.$opt["id"].','.$count.')">';
					$renderer->smiley($smiley);
					$renderer->doc .= '</span>';            
					$count++;
				}
				$renderer->doc .= "</span>";
			}

			//show first smiley
			$renderer->cdata("\n");
			$renderer->doc .= '<span id="multismiley'.$opt["id"].'" title="multiselector'.$opt["id"].'" class="multismiley" onclick="multiselectclick('.$opt["id"].')">';
			if(!reset($opt["smileys"]))
			$renderer->doc .= 'empty';
			else
			$renderer->smiley(reset($opt["smileys"]));
			$renderer->doc .= '</span>';
		}
		else if ($mode == 'odt'){
			$renderer->smiley(reset($opt["smileys"]));
		}
		return true;
	}

}

//Setup VIM: ex: et ts=4 enc=utf-8 :
