<div class="datenbank-suche-wrapper">

<h1>Suche nach Zeitschriften</h1>

<form action="{$siteUrl}" method="get">
<input type="hidden" name="id" value="{$listPid}" />
	<label for="libconnect_ezb_minisuche" class="hiddenezb">Name der Zeitschrift</label>
	<input id="libconnect_ezb_minisuche" type="text" name="libconnect[search][sword]" size="20" value="{$vars.sword|urldecode}" />
	<input type="submit" value="Suchen" /> <br/>
	
	<p><a href="{$searchUrl}">Erweiterte Suche</a></p>
	
</form>
</div>




{*

{if ! isset($vars.sword)}
	
	<div class="csc-frame">
	
		<h1>Zeitschriften anzeigen</h1>
		
		
		<form action="{$siteUrl}" method="get">
		
		<p>
	
			{foreach name=selected_colors from=$form.selected_colors item=value key=key}
			{if $key < 1000}
				<input type="checkbox" name="libconnect[search][selected_colors][]" {if isset($vars.selected_colors) && ($key == $vars.selected_colors)}checked{/if} value="{$key}" /> {$value} <br/>
			{/if}
			{/foreach}
			
			<br/>
			<input type="submit" value="Zugang ausw&auml;hlen" />
		
		</p>
		
		</form>
	
	</div>

{/if}

*}