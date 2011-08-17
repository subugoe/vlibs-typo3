<div id="dbis-detail">

{if !isset($error)}

<h3>Datenbank</h3>
<h1>{$db.title}</h1>


{if $db.access_id != "access_4"}
{* BG> Hide start research link for internal access only items *}
<p>
<a class="dbis-research" href="http://rzblx10.uni-regensburg.de/dbinfo/{$db.access.href}" target="_blank">Recherche starten</a>
</p>
{/if}

<table class="dbis-result-details">
<tbody>

{if count($db.else_titles)}
<tr>
	<th>

	<strong>Weitere Titel:</strong>
	</th>

	<td>
	{foreach name=elsetitles from=$db.else_titles item=title}
	{$title}
	{/foreach}

	</td>
</tr>
{/if}

{if count($db.access_lic)}
<tr>
	<th>

	<strong>Weitere lizenzierte Zugänge:</strong>
	</th>

	<td>
	{foreach name=access_lic from=$db.access_lic item=a_lic}
		{if $smarty.foreach.access_lic.iteration > 1}<br/>{/if}
		<a href="http://rzblx10.uni-regensburg.de/dbinfo/{$a_lic.href}" target="_blank">{$a_lic.name}</a>
	{/foreach}

	</td>
</tr>
{/if}

{if isset($db.db_access_short_text)}
<tr>
	<th>
	<strong>Verfügbarkeit:</strong>
	</th>

	<td>
	{$db.db_access_short_text}
	{if $db.access_id == "access_0"}
		<img src="typo3conf/ext/libconnect/templates/img/dbis-list_1.png" alt="dbis-list_1" />
	{elseif  $db.access_id == "access_1"}
		<img src="typo3conf/ext/libconnect/templates/img/dbis-list_2.png" alt="dbis-list_2" />
	{elseif  $db.access_id == "access_2"}
		<img src="typo3conf/ext/libconnect/templates/img/dbis-list_3.png" alt="dbis-list_3" />
	{elseif  $db.access_id == "access_7"}
		<img src="typo3conf/ext/libconnect/templates/img/dbis-list_4.png" alt="dbis-list_4" />
	{elseif  $db.access_id == "access_5"}
		<img src="typo3conf/ext/libconnect/templates/img/dbis-list_5.png" alt="dbis-list_5" />
	{elseif  $db.access_id == "access_4"}
		<img src="typo3conf/ext/libconnect/templates/img/dbis-list_6.png" alt="dbis-list_6" />
	{elseif  $db.access_id == "access_6"}
		<img src="typo3conf/ext/libconnect/templates/img/dbis-list_7.png" alt="dbis-list_7" />
	{elseif  $db.access_id == "access_500"}
		<img src="typo3conf/ext/libconnect/templates/img/dbis-list_germany.png" alt="dbis-list_germany" />
	{elseif  $db.access_id == "access_300"}
		<img src="typo3conf/ext/libconnect/templates/img/ezb-list_euro.png" alt="dbis-list_euro" />
	{/if}
	</td>
</tr>
{/if}






{if isset($db.hints)}
<tr>
	<th>
	<strong>Hinweise:</strong>
	</th>

	<td>
	{$db.hints}
	</td>
</tr>
{/if}


{if isset($db.content)}
<tr>
	<th>
	<strong>Inhalte:</strong>
	</th>

	<td>
	{$db.content}
	</td>
</tr>
{/if}


{if isset($db.subjects)}
<tr>
	<th>
	<strong>Fachgebiete:</strong>
	</th>

	<td>
	{foreach name=subjects from=$db.subjects item=subject}
		{$subject} <br/>
	{/foreach}
	</td>
</tr>
{/if}

{if count($db.keywords_join)}
<tr>
	<th>
	<strong>Schlagwörter:</strong>
	</th>

	<td>
		{$db.keywords_join}
	</td>
</tr>
{/if}

{if isset($db.appearence)}
<tr>
	<th>
	<strong>Erscheinungsform:</strong>
	</th>

	<td>
	{$db.appearence}
	</td>
</tr>
{/if}

{if count($db.db_type_infos_join)}
<tr>
	<th>
	<strong>Datenbank-Typ:</strong>
	</th>

	<td>
		{$db.db_type_infos_join}
	</td>
</tr>
{/if}

{if isset($db.publisher)}
<tr>
	<th>
	<strong>Verlag:</strong>
	</th>

	<td>
	{$db.publisher}
	</td>
</tr>
{/if}


{if isset($db.report_periods)}
<tr>
	<th>
	<strong>Berichtszeitraum:</strong>
	</th>

	<td>
	{$db.report_periods}
	</td>
</tr>
{/if}

{if isset($db.update)}
<tr>
	<th>
	<strong>Erscheinungsweise:</strong>
	</th>

	<td>
	{$db.update}
	</td>
</tr>
{/if}

{if isset($db.license)}
<tr>
	<th>
	<strong>Lizenz:</strong>
	</th>

	<td>
	{$db.license}
	</td>
</tr>
{/if}



{if isset($db.remarks)}
<tr>
	<th>
	<strong>Weitere Bemerkungen:</strong>
	</th>

	<td>
	{$db.remarks}
	</td>
</tr>
{/if}

</tbody>

</table>


{else}

<h1>Fehler</h1>
<p>
	Es ist ein Fehler bei der Anfrage der Datenbank aufgetreten.
	<br/><br/>
	Möglicherweise ist die angefragte Datenbank nicht vorhanden. Bitte versuchen Sie es erneut
	oder wenden Sie sich an das Web-Team der Bibliothek, das Ihnen beim Auffinden des Datensatzes
	gerne behilflich ist.
</p>
{/if}


</div>
