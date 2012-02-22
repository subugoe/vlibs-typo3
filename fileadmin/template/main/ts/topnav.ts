# TOPNAV:
temp.top_navigation = COA

temp.top_navigation.10 = HMENU
temp.top_navigation.10 {
	special = list
	special.value = {$temp.top_navigation.10.special.value}
	includeNotInMenu = 1
	1 = TMENU
	1.noBlur = 1
	stdWrap.wrap = <ul id="menuService"> | </ul>

	1.NO {
		linkWrap = <li> | </li> 
		#ATagTitle.field = subtitle
	}
	1.CUR = 1
	1.CUR {
		linkWrap = <li><strong> | </strong></li>
		#ATagTitle.field = subtitle
		##doNotLinkIt = 1
	}
temp.top_navigation.20 = HMENU
temp.top_navigation.20.stdWrap.wrap = <li class="sprachAnzeige"> | </li>

temp.top_navigation.20 {
		special = language
		special.value = 0,1
		wrap =
		1 = GMENU
		1 {
			noBlur = 1
			disableAltText = 1
##			accessKey = 1
##			NO.allWrap = <li class="sprachAnzeige"> | </li>
			No.linkWrap = <li> | </li>
			NO.imgParams = alt="" title="Deutsch" || alt="" title="English"
			NO.noLink = 1
			NO.allStdWrap.typolink {
			uniqueLinkVars = 1
			parameter.data = page:uid
			additionalParams = &L=0 || &L=1
			addQueryString = 1
			addQueryString.exclude = id
			addQueryString.method = GET
		}
		NO.10 = IMAGE
		NO.10.file = fileadmin/template/main/images/lang_german.gif|| fileadmin/template/main/images/lang_english.gif
		NO.10.offset = 1,1
		#ACT < .NO
		#ACT = 1
		#ACT.allStdWrap >
		#ACT.backColor = transparent
		#USERDEF1 < .NO
		#USERDEF1 = 1
		#USERDEF1.allStdWrap >
		#USERDEF1.imgParams = {$langMenuDimmedAltTitleTags}
		#USERDEF1.10.file = {$langMenuDimmedFileNames}
		
	}

}
