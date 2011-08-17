

<h1>Ergebnisse Ihrer Suche</h1>


{if $journals.page_vars.search_count != -1}

<div class="ezb-list-legend">
	<ul>
		<li class="ezb-list-legend-one">Frei zugänglich</li>
		<li class="ezb-list-legend-two">Im Campus-Netz sowie für Angehörige der Universität auch extern zugänglich</li>
		<li class="ezb-list-legend-six">Nur für einen Teil der erschienenen Jahrgänge zugänglich</li>
		<li class="ezb-list-legend-four">Für Ihren Standort nicht freigeschaltet. Zum Teil bekommen Sie Zugriff auf Abstracts oder Inhaltsverzeichnisse</li>
	</ul>
</div>

<br/><br/>

{$journals.page_vars.search_count} Treffer

<br/><br/>

{if is_array($journals.navlist.pages)}
	<div class="ezb-navigation">
	{foreach name=navlist from=$journals.navlist.pages item=item}
		{if is_array($item)}

			<a href="{$item.link}">{$item.title}</a>
		{else}

			<em>{$item}</em>
		{/if}
	{/foreach}
	</div>

	<br/><br/>
{/if}

<div class="ezb-list-output">
{foreach name=list from=$journals.alphabetical_order.first_fifty item=section}
	<h3><a href="{$section.link}">{$section.first_fifty_titles}...</a></h3>
{/foreach}

{if isset($journals.alphabetical_order.current_title)}
<h2>{$journals.alphabetical_order.current_title}...</h2>
{/if}


<ul class="ezb-list" {if ! ($journals.alphabetical_order.current_title)}style="padding-left: 0px !important;"{/if}>
{foreach name=list from=$journals.alphabetical_order.journals item=journal}
	<li><img src="typo3conf/ext/libconnect/templates/img/ezb-list_{$journal.color_code}.png" alt="colorcode" /> <a href="{$journal.detail_link}">{$journal.title}</a></li>
{/foreach}
</ul>


{foreach name=list from=$journals.alphabetical_order.next_fifty item=section}
	<h3><a href="{$section.link}">{$section.next_fifty_titles}...</a></h3>
{/foreach}
</div>

{if is_array($journals.navlist.pages)}

	<br/><br/>

	<div class="ezb-navigation">
	{foreach name=navlist from=$journals.navlist.pages item=item}
		{if is_array($item)}

			<a href="{$item.link}">{$item.title}</a>
		{else}

			<em>{$item}</em>
		{/if}
	{/foreach}
	</div>


{/if}

{else}

  <b>Bei der Suche ist ein Fehler aufgetreten:</b><br/><br/>
  Sie haben keine Suchbegriffe eingegeben und kein Fachgebiet ausgewählt.

{/if}


