
<h3>EZB Fachgebiet</h3>

<h1>{$journals.subject}</h1>

<div class="ezb-list-legend">
	<ul>
		<li class="ezb-list-legend-one">Frei zugänglich</li>
		<li class="ezb-list-legend-two">Im Campus-Netz sowie für Angehörige der Universität auch extern zugänglich</li>
		<li class="ezb-list-legend-six">Nur für einen Teil der erschienenen Jahrgänge zugänglich</li>
		<li class="ezb-list-legend-four">Für Ihren Standort nicht freigeschaltet. Zum Teil bekommen Sie Zugriff auf Abstracts oder Inhaltsverzeichnisse</li>
	</ul>
</div>

<br/><br/>

<div class="ezb-navigation">
{foreach name=navlist from=$journals.navlist.pages item=item}
	{if is_array($item)}

		<a href="{$item.link}">{$item.title}</a>
	{else}

		<em>{$item.title}</em>
	{/if}
{/foreach}
</div>

<br/><br/>

<div class="ezb-list-output">
{foreach name=list from=$journals.alphabetical_order.first_fifty item=section}
	<h3><a href="{$section.link}">{$section.first_fifty_titles}...</a></h3>
{/foreach}

<h2>{$journals.navlist.current_title}...</h2>

<ul class="ezb-list">
{foreach name=list from=$journals.alphabetical_order.journals item=journal}
	<li><img src="typo3conf/ext/libconnect/templates/img/ezb-list_{$journal.color_code}.png" alt="colorcode" /> <a href="{$journal.detail_link}">{$journal.title}</a></li>
{/foreach}
</ul>


{foreach name=list from=$journals.alphabetical_order.next_fifty item=section}
	<h3><a href="{$section.link}">{$section.next_fifty_titles}...</a></h3>
{/foreach}
</div>