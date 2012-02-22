# mainmenue content object:
temp.mainmenu = HMENU
temp.mainmenu.stdWrap.wrap = <ul id="menuMain"> | </ul>

#first level menu
temp.mainmenu.1 = TMENU
temp.mainmenu.1 {
	noBlur = 1
	entryLevel = 2
        NO.allWrap = <li> | </li> 	
	NO.stdWrap.wrap = <span class="menuPraefix">&rsaquo;</span>&nbsp;| 
	NO.ATagBeforeWrap = 1
	ACT = 1
	ACT.allWrap = <li> | </li>
	ACT.stdWrap.wrap = <span class="menuPraefix">&rsaquo;</span>&nbsp;| 
	ACT.ATagBeforeWrap = 1
}

