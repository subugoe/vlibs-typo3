<form id="e-search" name="extSearchForm" action="{$listUrl}" method="get" class="e-search">

<input type="hidden" name="id" value="{$listPid}" />
  <fieldset>
    <legend>Erweiterte Suche nach Zeitschriften</legend> 

	{section name=jq_type loop=4}
		<label for="jq_type{$smarty.section.jq_type.iteration}" class="hiddenezb">Feldangabe</label>
    	<select name="libconnect[search][jq_type{$smarty.section.jq_type.iteration}]" id="jq_type{$smarty.section.jq_type.iteration}">
    	
    		{foreach name=jq_type_foreach from=$form.jq_type key=key item=value}
    			<option value="{$key}" {if (intval($smarty.section.jq_type.iteration) == intval($smarty.foreach.jq_type_foreach.iteration))}selected="selected"{/if}>{$value}</option>
    		{/foreach}
    	
		</select>
		
		<label for="jq_term{$smarty.section.jq_type.iteration}" class="hiddenezb">Suchbegriff</label> 
		<input type="text" size="30" class="dbis-input" name="libconnect[search][jq_term{$smarty.section.jq_type.iteration}]" id="jq_term{$smarty.section.jq_type.iteration}" value="" />
		
		<label for="jq_bool{$smarty.section.jq_type.iteration}" class="hiddenezb">Verkn&uuml;pfung</label> 
	    <select name="libconnect[search][jq_bool{$smarty.section.jq_type.iteration}]" id="jq_bool{$smarty.section.jq_type.iteration}">
	      	{foreach name=jq_bool_foreach from=$form.jq_bool key=key item=value}
	      		<option value="{$key}">{$value}</option>
			{/foreach}
	    </select>
	
		<label for="jq_not{$smarty.section.jq_type.iteration}" class="hiddenezb">Negator</label> 
	    <select name="libconnect[search][jq_not{$smarty.section.jq_type.iteration}]" id="jq_not{$smarty.section.jq_type.iteration}">
	      	{foreach name=jq_not_foreach from=$form.jq_not key=key item=value}
	      		<option value="{$key}">{$value}</option>
			{/foreach}
	    </select>
	
	    <br />		 
	{/section}

	<br/>
	
    <label for="hits_per_page">Treffer pro Seite:</label>
    <select size="1" name="libconnect[search][hits_per_page]">
		<option value="10">10</option>
		<option value="25">25</option>
		<option selected="" value="50">50</option>
		<option value="100">100</option>
		<option value="250">250</option>
		<option value="500">500</option>
		<option value="100000">alle</option>
    </select>

	<br/><br/>
	
    <label for="fachgebiete">Fachgebiete:</label> 
    <select multiple="multiple" size="5" name="libconnect[search][Notations][]" id="fachgebiete" style="width:34em;margin-left:6.5em;">
   		{foreach from=$form.Notations key=key item=value}
   			<option value="{$key}">{$value}</option>
   		{/foreach}
    </select>

    <br/><br/>

    <label for="zugaenge">Volltextartikel sind:</label> 
   	<p>
   	{foreach from=$form.selected_colors key=key item=value}
  		<input type="checkbox" name="libconnect[search][selected_colors][]" value="{$key}" checked="checked" /> {$value} <br/>
  	{/foreach}
	</p>

    <br />
    <input type="submit" class="button submit" value="Suchen" />
  </fieldset>

</form>
