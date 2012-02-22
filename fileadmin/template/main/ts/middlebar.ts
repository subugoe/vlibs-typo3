# MIDDLEBAR: BREADCRAMB + SPECIALMENU

temp.history = TEXT
temp.history.stdWrap.wrap = <li> | </li>
[globalVar = GP:L = 1]
temp.history.value = Search history
[else]
temp.history.value = Suchgeschichte
[global]
temp.history.typolink.parameter = 16
temp.history.typolink.additionalParams = &i&request=history&frame=VifaXML



temp.bookshelf = TEXT
#temp.bookshelf.stdWrap.wrap = <li> | </li>

[globalVar = GP:L = 1]
temp.bookshelf.value = Bookshelf
[else]
temp.bookshelf.value = Sammelmappe
[global]
temp.bookshelf.typolink.parameter = 16
temp.bookshelf.typolink.additionalParams = &i&request=saved_records&frame=VifaXML

temp.middlebar = COA
temp.middlebar.stdWrap.wrap = <div id="mittelLeiste"> | </div>
temp.middlebar.5 = TEXT
temp.middlebar.5.stdWrap.wrap = <span class="SieSindHier"> | </span>
[globalVar = GP:L = 1]
temp.middlebar.5.value = You are here:
[else]
temp.middlebar.5.value = Sie sind hier:
[global]

###Breadcrumb
temp.middlebar.10 = HMENU
temp.middlebar.10 {
	wrap= <ul id="breadcrumb">|</ul>
	special = rootline
	spacial.range = 0/-1
	1 = TMENU
	1 {

	NO.linkWrap = |*| <li> |&nbsp;&gt; </li> |*| |
	ACT = 1

	ACT.linkWrap = |*| <li> |&nbsp;&gt; </li> |*|  <li><span id="breadSel"> | </span></li>
	}
}


##SPECIALMENU: SEARCH-HISTORY & BOOKSHELF
temp.middlebar.20 = COA
#temp.middlebar.20.stdWrap.wrap = <ul id="features"> | </ul>
#temp.middlebar.20.linkwrap = <li> | </li>

temp.middlebar.20 {

	10 < temp.history
	10.linkWrap = <li> | </li>
	20 < temp.bookshelf
	20.linkWrap = <li> | </li>
}
