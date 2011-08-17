

<h1>Ergebnisse Ihrer Suche </h1>

Sie suchten nach folgenden Kriterien:<br/>
<ul>
	{foreach from=$list.searchDescription item=description}
		<li>{$description}</li>
	{/foreach}
</ul>

<br/><br/>

{if ! isset($list.error)}

<h3>Zugriffe:</h3>

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

<br/><br/>

{$list.alphasort|@count} Treffer


<div style="margin-top: 3em;">
{if count($list.alphasort)}
	<div class="accordion-content clearfix">
		<ul class="dbis-dblist">

			{foreach from=$list.alphasort item=db key=titleid }
				<li>
					{if $list.values.$titleid.access_ref == "access_0"}<img src="typo3conf/ext/libconnect/templates/img/dbis-list_1.png" alt="dbis-list_1" />{/if}
					{if $list.values.$titleid.access_ref == "access_1"}<img src="typo3conf/ext/libconnect/templates/img/dbis-list_2.png" alt="dbis-list_2" />{/if}
					{if $list.values.$titleid.access_ref == "access_2"}<img src="typo3conf/ext/libconnect/templates/img/dbis-list_3.png" alt="dbis-list_3" />{/if}
					{if $list.values.$titleid.access_ref == "access_7"}<img src="typo3conf/ext/libconnect/templates/img/dbis-list_4.png" alt="dbis-list_4" />{/if}
					{if $list.values.$titleid.access_ref == "access_5"}<img src="typo3conf/ext/libconnect/templates/img/dbis-list_5.png" alt="dbis-list_5" />{/if}
					{if $list.values.$titleid.access_ref == "access_4"}<img src="typo3conf/ext/libconnect/templates/img/dbis-list_6.png" alt="dbis-list_6" />{/if}
					{if $list.values.$titleid.access_ref == "access_6"}<img src="typo3conf/ext/libconnect/templates/img/dbis-list_7.png" alt="dbis-list_7" />{/if}
					{if $list.values.$titleid.access_ref == "access_500"}<img src="typo3conf/ext/libconnect/templates/img/dbis-list_germany.png" alt="dbis-list_germany" />{/if}
					{if $list.values.$titleid.access_ref == "access_300"}<img src="typo3conf/ext/libconnect/templates/img/ezb-list_euro.png" alt="ezb-list_euro" />{/if}
					<a href="{$list.values.$titleid.detail_link}">{$list.values.$titleid.title}</a>
				</li>
			{/foreach}
		</ul>
	</div>
{/if}
</div>


{else}

  {$list.error}

{/if}


