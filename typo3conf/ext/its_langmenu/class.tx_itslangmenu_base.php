<?php
class tx_itslangmenu_base {

	function tx_itslangmenu_base($cObj,$type=0, $conf=array(),&$parent)   {
			$this->cObj = &$cObj;
			$this->conf = $conf;
			$this->parent = $parent;
	}

	function getAcceptedLanguages () {
		$languagesArr = array ();
		$rawAcceptedLanguagesArr = t3lib_div::trimExplode (',',t3lib_div::getIndpEnv('HTTP_ACCEPT_LANGUAGE'),1);



		foreach ($rawAcceptedLanguagesArr as $languageAndQualityStr) {
			list ($languageCode, $quality) = t3lib_div::trimExplode (';',$languageAndQualityStr);
			$acceptedLanguagesArr[$languageCode] = $quality ? (float)substr ($quality,2) : (float)1;
		}

			// Now sort the accepted languages by their quality and create an array containing only the language codes in the correct order.
		if (is_array($acceptedLanguagesArr)) {
			arsort($acceptedLanguagesArr);
			$languageCodesArr = array_keys($acceptedLanguagesArr);
			if (is_array($languageCodesArr)) {
				foreach ($languageCodesArr as $languageCode) {
					$languagesArr[$languageCode] = $languageCode;
				}
			}
		}
		return $languagesArr;
    }

    function getBrowserMatchingtag() {
		$this->deflangid = $this->conf[defaultLangID];
		if ( ! isset($this->deflangid) )
			$this->deflangid = 0;
		$tag = $this->deflangid;
		$query = "SELECT * ,sys_language.uid as langid FROM sys_language,static_languages WHERE (static_lang_isocode = static_languages.uid) AND (tx_itslangmenu_disable_in_menu = 0) ORDER BY `title` ASC ";
		$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);		
		$langArr=array();
		$uid = $this->cObj->parentRecord['data']['uid'] ;
		if  ($this->conf['defaultLanguagelg_iso_2'] ) {
			$langArr[0] = $array_offered_langs[0] = $this->conf['defaultLanguageLabel'];
			$array_offered_langs[0] = $this->conf['defaultLanguagelg_iso_2'];
		}
		while($row=mysql_fetch_assoc($res))	{

			$langArr[strtolower($row["langid"])]=$row["title"];
			$array_offered_langs[$row["langid"]]=strtolower($row["lg_iso_2"]);
		}

		if (isset ($GLOBALS['_GET']['L']))
		{
			$l1 = ($GLOBALS['_GET']['L']);
			if (strlen($langArr[$l1]) == 0)
				unset ($GLOBALS['_GET']['L']);
		} else  {
			//$GLOBALS['_GET']['L']=0;
		}

		$HTTP_ACCEPT_LANGUAGE= t3lib_div::getIndpEnv('HTTP_ACCEPT_LANGUAGE');
		$array_accepted_langs = explode(",",$HTTP_ACCEPT_LANGUAGE);
		/*debug($array_offered_langs );*/
		//$array_offered_langs = array("de","en");
		foreach ($array_accepted_langs as $str_testlang) {
			// Compares the browser-sent "accepted languages" step by step with the offered languages.
			foreach ($array_offered_langs as $key =>$tmp_off_lang) {
				 if ( stristr($str_testlang,$tmp_off_lang) ) {
						 $str_selected_lang = $tmp_off_lang;
						 $tag = $key;
						 break;
				 }
			}
			if (isset($str_selected_lang)) break;
		}
		$this->langArr = $langArr;
		return $tag;
    }

    function GetSelectStyle () {
		$htmlform = '<script language="javascript">
<!--
function ITSJUMP() {
	var welcherLink = document.Springen.URLs.selectedIndex;
	//document.Springen.URLs.selectedIndex = "0";
	if(welcherLink > "-1"){

	   top.location.href = document.Springen.URLs.options[welcherLink].value;
	}
}
 //-->
</script>';

		$htmlform.='<form name="Springen" action="index.php"><div class="langselect"><label for="sel_lang">Language/Sprache </label>
		<select id="sel_lang" name="URLs" onchange="ITSJUMP();" class="selectlang1"  size="1" >';
		$lang = $GLOBALS["TSFE"]->tmpl->setup['config.']['sys_language_uid'] ;
		$getvars = t3lib_div::_GET ();
		unset ($getvars['L']);
		#unset ($getvars['cHash']);
    	unset ($getvars['id']);
		
		

		foreach($this->langArr as $tag1=>$element)
		{
			$pp =  array("L" => "$tag1");
			$pp2 = array_merge($getvars,$pp);
			if ($tag1 == $lang) {
				$link=$this->parent->pi_getPageLink($GLOBALS['TSFE']->id,"", array("L" => "$tag1"));
				if (!substr(strtolower ($link),0,4)== 'http')
					$link = '/' .$link;
				$htmlform.='
					<option value="'.$link.'" selected="selected">'.$element.'</option>';

			} else	{
				
				$link=$this->parent->pi_getPageLink($GLOBALS['TSFE']->id,"", $pp2);

				if (!substr(strtolower ($link),0,4)== 'http')
					$link = '/' .$link;
				$htmlform.='
					<option value="'.$link.'">'.$element.'</option>';
			}
		}
		$htmlform .='
		</select></div></form>';
		$htmlform.='<noscript>';

		$htmlform.= $this->GetLinkStyle();

		$htmlform.='</noscript>';
		return $htmlform;


    }

    function GetLinkStyle() {
    
    	$getvars = t3lib_div::_GET ();
    	unset ($getvars['L']);
    	#unset ($getvars['cHash']);
    	unset ($getvars['id']);
    	$lang = $GLOBALS["TSFE"]->tmpl->setup['config.']['sys_language_uid'] ;		
		if (is_array($this->langArr)) {
			$htmlform .= '<ul>';
			$count = 0;
			$langcount = count($this->langArr);
			foreach($this->langArr as $tag1=>$element)
			{
				$count++;
				$pp =  array("L" => "$tag1");
				$pp2 = array_merge($getvars,$pp);
				if ($tag1 == $lang) {
					$htmlform .= '<li class="activelang"><span class="activelangspan">';
					$htmlform .= $element;
					$htmlform .= '</span>';
				} 	else { 
					$htmlform .= '<li class="nonactivelang"><span class="nonactivelangspan">';
					$htmlform .= '<a href="'.$this->parent->pi_getPageLink($GLOBALS['TSFE']->id,"",$pp2).'">'.$element.'</a>';
					$htmlform .= '</span>';
				}
					
				if ($count < $langcount)
					$htmlform .= $this->conf['listdelimiter'];
				$htmlform .= '</li>';
			}
			$htmlform .= '</ul>';

		}

		$htmlform.='<div style="display:none">';
		$htmlform.='<br/><a href="http://www.its-hofmann.de"> TYPO3 Plugin its_langmenu 0.3.0 by ITS Hofmann</a>';
		$htmlform.='</div>';


		return $htmlform;

    }

    function jumptolang ($tag) {


		$l1 = ($GLOBALS['_GET']['L']);
		if (isset ($GLOBALS['_GET']['L']))
			$l1 = ($GLOBALS['_GET']['L']);

		if (!isset ($GLOBALS['_GET']['L'])  || $GLOBALS['TSFE']->language_uid_modified==1)
		{



		

			$referer = t3lib_div::getIndpEnv('HTTP_REFERER');


			if (strlen($referer) && stristr($referer, t3lib_div::getIndpEnv('TYPO3_SITE_URL'))) return 0;


			if (!isset($tag)) {$tag = 1;}
			$page = $GLOBALS['TSFE']->page;

			$linkData = $GLOBALS['TSFE']->tmpl->linkData($page,'',0,'',array(),'&L='.$tag);
			$locationURL = $this->conf['dontAddSchemeToURL'] ? $linkData['totalURL'] : t3lib_div::locationHeaderUrl($linkData['totalURL']);


			$jumpto ='/'.$this->parent->pi_getPageLink($GLOBALS['TSFE']->id,"", array("L" => "$tag"));


			$GLOBALS['TSFE']->language_uid_modified=0;
			header('webdeveloper: ITS-Hofmann');
			header('Location: '.$locationURL);

			/* Stellen Sie sicher, dass der nachfolgende Code nicht ausgefuehrt wird, wenn
			   eine Umleitung stattfindet. */


			return 1;
		}
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/its_langmenu/class.tx_itslangmenu_base.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/its_langmenu/class.tx_itslangmenu_base.php']);
}

?>