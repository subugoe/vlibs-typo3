
<h1>Elektronische Zeitschriftenbibliothek</h1>

<ul class="ezb-list-overview">

{foreach name=list from=$list item=item}
	<li>
		<a href="{$item.link}">{$item.title}</a> ({$item.journalcount})
	</li>
{/foreach}
</ul>

