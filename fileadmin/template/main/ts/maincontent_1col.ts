# maincontent content object:
temp.maincontent = COA
temp.maincontent.stdWrap.wrap = <div id="contentContainer"> | </div>
temp.maincontent.10 = TEXT
temp.maincontent.10 {
	field = subtitle
	stdWrap.wrap = <h1 class="standard"> | </h1>
}

temp.maincontent.20 = COA
temp.maincontent.20.stdWrap.wrap = <div id="cLeft"> | </div>
temp.maincontent.20.10 < temp.mainmenu
temp.maincontent.20.20 < styles.content.get
temp.maincontent.20.20.stdWrap.wrap = <div id="cInhalt"><div id="cText"> | </div></div>
temp.maincontent.20.30 <
temp.maincontent.20.30.stdWrap.wrap =
page.10 {

  workOnSubpart = DOCUMENT_BODY
  #marks.TOPNAV < temp.top_navigation
  #subparts.MIDDLE < temp.middlebar
  subparts.CONTENT < temp.maincontent
  #subparts.FOOT < temp.foot

  #marks.LEFT_CONTENT  < temp.rss
  #styles.content.getLeft
  
  #marks.FOOTER < lib.logo_area 
  

 }
