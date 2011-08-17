<div id="ezb-detail">

{if !isset($error)}

<h1>{$journal.title}</h1>

<table class="ezb-result-details">
<tbody>

<tr>
	<td width="130">
	<strong>Verfügbarkeit:</strong>
	</td>

	<td>
	<img src="typo3conf/ext/libconnect/templates/img/ezb-list_{$journal.color_code}.png" alt="colorcode" />
	{if $journal.color_code == "1"}Frei zugänglich
		{elseif $journal.color_code == "2"}Im Campus-Netz sowie für Angehörige der Universität auch extern zugänglich
		{elseif $journal.color_code == "6"}Nur für einen Teil der erschienenen Jahrgänge zugänglich
		{elseif $journal.color_code == "4"}Für Ihren Standort nicht freigeschaltet. Zum Teil bekommen Sie Zugriff auf Abstracts oder Inhaltsverzeichnisse
	{/if}
	</td>
</tr>

{if count($journal.periods)}

	{foreach name=periods from=$journal.periods item=period}
	<tr>
		{if strlen($period.label)}
		<td>
			<strong>Lizenzierter Zeitraum:</strong>
		</td>
			{if strlen(trim($period.label))}
			<td>
				<img src="typo3conf/ext/libconnect/templates/img/ezb-list_{$period.color_code}.png" alt="colorcode" />
				<a class="ezb-license" href="{$period.link|urldecode}" target="_blank">{$period.label}</a>
			</td>
			{else}
			<td>
				<img src="typo3conf/ext/libconnect/templates/img/ezb-list_{$period.color_code}.png" alt="colorcode" />
				<a class="ezb-license" href="{$period.link|urldecode}" target="_blank">gesamter Zeitraum</a>
			</td>
			{/if}
		{else}
		<td>
			<strong>Restliche Zeiträume:</strong>
		</td>
		<td>
			<img src="typo3conf/ext/libconnect/templates/img/ezb-list_{$period.color_code}.png" alt="colorcode" />
			<a class="ezb-license" href="{$period.link|urldecode}" target="_blank">Homepage der Zeitschrift</a>
		</td>
		{/if}
	</tr>

	{/foreach}

{/if}


{if strlen($journal.fulltext)}
<tr>
	<td>
	<strong>Volltext:</strong>
	</td>

	<td>
		<a class="dbis-research" href="{$journal.fulltext_link|urldecode}" target="_blank">{$journal.fulltext|truncate:70}</a>
	</td>
</tr>
{/if}

{if count($journal.homepages)}
<tr>
	<td>
	<strong>Homepage(s):</strong>
	</td>

	<td>
		{foreach name=homepage from=$journal.homepages item=homepage}
			{if $smarty.foreach.homepage.iteration > 1}<br/>{/if}

			<a class="dbis-research" href="http://ezb.uni-regensburg.de/warpto.phtml?bibid={$bibid}&colors=7&lang=de&jour_id={$journal.id}&url={$homepage|urlencode}" target="_blank">{$homepage|truncate:70}</a>
		{/foreach}
	</td>
</tr>
{/if}

{if $journal.first_fulltext.date > 0}
<tr>
	<td>
	<strong>Volltext online seit:</strong>
	</td>

	<td>
	{if $journal.first_fulltext.volume}Jg. {$journal.first_fulltext.volume}{/if}{if $journal.first_fulltext.issue}, H. {$journal.first_fulltext.issue} {/if}
	({$journal.first_fulltext.date})
	</td>
</tr>
{/if}

{if $journal.last_fulltext}
<tr>
	<td>
	<strong>Volltext online bis:</strong>
	</td>

	<td>
	{if $journal.last_fulltext.volume}Jg. {$journal.last_fulltext.volume}{/if}{if $journal.last_fulltext.issue}, H. {$journal.last_fulltext.issue} {/if}
	({$journal.last_fulltext.date})
	</td>
</tr>
{/if}

{if isset($journal.publisher)}
<tr>
	<td>
	<strong>Verlag:</strong>
	</td>

	<td>
	{$journal.publisher}
	</td>
</tr>
{/if}

{if isset($journal.ZDB_number)}
<tr>
	<td>
	<strong>ZDB Nummer:</strong>
	</td>

	<td>
	{if $journal.ZDB_number_link}<a href="{$journal.ZDB_number_link}" target="_blank">{/if}
	{$journal.ZDB_number}
	{if $journal.ZDB_number_link}</a>{/if}
	</td>
</tr>
{/if}

{if strlen($journal.subjects_join)}
<tr>
	<td>
	<strong>Fachgruppe(n):</strong>
	</td>

	<td>
	{$journal.subjects_join}
	</td>
</tr>
{/if}

{if isset($journal.keywords_join)}
<tr>
	<td>
	<strong>Schlagwort(e):</strong>
	</td>

	<td>
	{$journal.keywords_join}
	</td>
<tr>
{/if}

{if strlen($journal.eissns_join)}
<tr>
	<td>
	<strong>E-ISSN(s):</strong>
	</td>

	<td>
	{$journal.eissns_join}
	</td>
</tr>
{/if}

{if strlen($journal.pissns_join)}
<tr>
	<td>
	<strong>P-ISSN(s):</strong>
	</td>

	<td>
	{$journal.pissns_join}
	</td>
</tr>
{/if}

{if strlen($journal.appearence)}
<tr>
	<td>
	<strong>Form:</strong>
	</td>

	<td>
	{$journal.appearence}
	</td>
</tr>
{/if}

{if strlen($journal.costs)}
<tr>
	<td>
	<strong>Kosten:</strong>
	</td>

	<td>
	{$journal.costs}
	</td>
</tr>
{/if}

{if strlen($journal.remarks)}
<tr>
	<td>
	<strong>Bemerkung:</strong>
	</td>

	<td>
	{$journal.remarks}
	</td>
</tr>
{/if}

</tbody>
</table>

{else}

<h1>Fehler</h1>
<p>
	Es ist ein Fehler bei der Anfrage der elektronische Zeitschrift aufgetreten.
	<br/><br/>
	Möglicherweise ist die angefragte Zeitschrift nicht vorhanden. Bitte versuchen Sie es erneut
	oder wenden Sie sich an das Web-Team der Bibliothek, das Ihnen beim Auffinden des Datensatzes
	gerne behilflich ist.
</p>
{/if}

</div>