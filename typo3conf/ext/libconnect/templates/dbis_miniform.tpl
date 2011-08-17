


<div class="datenbank-suche-wrapper">

	<h1>Suche nach Datenbanken</h1>

	<form action="{$siteUrl}" method="get">
	<input type="hidden" name="id" value="{$listPid}" />
		<input type="text" name="libconnect[search][sword]" size="20" value="{$vars.sword}" />
		<input type="submit" value="Suchen" />

		<p><a href="{$searchUrl}">Erweiterte Suche</a></p>

	</form>

</div>





{if ! isset($vars.sword)}

	<div class="datenbank-suche-wrapper">

		<h1>Datenbanken anzeigen</h1>


		<form action="{$siteUrl}" method="get">

		<p>
			<select name="libconnect[search][zugaenge]" style="width: 180px; padding: 0.06em">
			{foreach name=zugaenge from=$form.zugaenge item=value key=key}
			{if $key < 1000}
				{if $value != ''}<option  {if isset($vars.zugaenge) && ($key == $vars.zugaenge)}selected{/if} value="{$key}" >{$value}</option>{/if}
			{/if}
			{/foreach}
			</select>
		</p>

			<input type="submit" value="Zugang ausw&auml;hlen" />



		</form>

	</div>

{/if}