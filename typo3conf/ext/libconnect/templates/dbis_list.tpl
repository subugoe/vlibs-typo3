
<h3>DBIS Fachgebiet</h3>

<h1>{$subject}</h1>

<h2>Zugriffe:</h2>

<div class="dbis-list-legend">
<ul>
	{foreach name=dbs from=$list.access_infos item=info}
		{if $info.id == "access_0"}<li class="dbis-list-legend-one">{$info.title} ({$info.description})</li>{/if}
		{if $info.id == "access_1"}<li class="dbis-list-legend-two">{$info.title} ({$info.description})</li>{/if}
		{if $info.id == "access_2"}<li class="dbis-list-legend-three">{$info.title} ({$info.description})</li>{/if}
		{if $info.id == "access_7"}<li class="dbis-list-legend-six">{$info.title} ({$info.description})</li>{/if}
		{if $info.id == "access_5"}<li class="dbis-list-legend-seven">{$info.title} ({$info.description})</li>{/if}
		{if $info.id == "access_4"}<li class="dbis-list-legend-eight">{$info.title} ({$info.description})</li>{/if}
		{if $info.id == "access_6"}<li class="dbis-list-legend-nine">{$info.title} ({$info.description})</li>{/if}
		{if $info.id == "access_500"}<li class="dbis-list-legend-four">{$info.title} ({$info.description})</li>{/if}
		{if $info.id == "access_300"}<li class="dbis-list-legend-five">{$info.title} ({$info.description})</li>{/if}
	{/foreach}
</ul>
</div>







{if count($list.top)}
<div class="top-database-wrapper">
	<h2>Top Datenbanken</h2>
	<ul class="dbis-top-list">
	{foreach name=list from=$list.top item=db}
		<li><a href="{$db.detail_link}">{$db.title}</a> ({$db.access})</li>
	{/foreach}
	</ul>
</div>
{/if}



<div style="margin-top: 3em;">
{if count($list.groups)}
	{foreach name=groups from=$list.groups item=group}

		{if count($group.dbs)}
			<div class="accordion-fce-wrapper">
				<div class="accordion-fce">
					<h2>{if !(isset($group.title))}
						Gesamtangebot {else}{$group.title}
					{/if}  ({$group.dbs|@count})</h2>

					<div class="accordion-content clearfix">
					<ul class="dbis-dblist">
						{foreach name=dbs from=$group.dbs item=db}
							{if $db.access_ref == "access_0"}<li><img src="typo3conf/ext/libconnect/templates/img/dbis-list_1.png" alt="dbis-list_1" /><a href="{$db.detail_link}">{$db.title}</a> </li>
							{elseif $db.access_ref == "access_1"}<li><img src="typo3conf/ext/libconnect/templates/img/dbis-list_2.png" alt="dbis-list_2" /><a href="{$db.detail_link}">{$db.title}</a> </li>
							{elseif $db.access_ref == "access_2"}<li><img src="typo3conf/ext/libconnect/templates/img/dbis-list_3.png" alt="dbis-list_3" /><a href="{$db.detail_link}">{$db.title}</a> </li>
							{elseif $db.access_ref == "access_7"}<li><img src="typo3conf/ext/libconnect/templates/img/dbis-list_4.png" alt="dbis-list_4" /><a href="{$db.detail_link}">{$db.title}</a> </li>
							{elseif $db.access_ref == "access_5"}<li><img src="typo3conf/ext/libconnect/templates/img/dbis-list_5.png" alt="dbis-list_5" /><a href="{$db.detail_link}">{$db.title}</a> </li>
							{elseif $db.access_ref == "access_4"}<li><img src="typo3conf/ext/libconnect/templates/img/dbis-list_6.png" alt="dbis-list_6" /><a href="{$db.detail_link}">{$db.title}</a> </li>
							{elseif $db.access_ref == "access_6"}<li><img src="typo3conf/ext/libconnect/templates/img/dbis-list_7.png" alt="dbis-list_7" /><a href="{$db.detail_link}">{$db.title}</a> </li>
							{elseif $db.access_ref == "access_500"}<li><img src="typo3conf/ext/libconnect/templates/img/dbis-list_germany.png" alt="dbis-list_germany"/><a href="{$db.detail_link}">{$db.title}</a> </li>
							{elseif $db.access_ref == "access_300"}<li><img src="typo3conf/ext/libconnect/templates/img/ezb-list_euro.png" alt="dbis-list_euro"/><a href="{$db.detail_link}">{$db.title}</a> </li>
							{else}<li><a href="{$db.detail_link}">{$db.title}</a> </li>
							{/if}
						{/foreach}
					</ul>
					</div>
				</div>
			</div>
		{/if}

	{/foreach}
{/if}

</div>




