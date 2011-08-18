
<h1>Datenbank-Infosystem</h1>

<ul class="dbis-list">

{foreach name=list from=$list item=item}
	<li>
		<a href="{$item.link}">{$item.title}</a> ({$item.count})
	</li>
{/foreach}
</ul>

