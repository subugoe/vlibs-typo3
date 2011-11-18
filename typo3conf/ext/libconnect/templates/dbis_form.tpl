<div id="extSearchForm-wrapper" class="clearfix">

<form name="extSearchForm" action="{$listUrl}" method="get" class="e-search">
<input type="hidden" name="id" value="{$listPid}" />
  <fieldset>
    <legend>Erweiterte Suche nach Datenbanken</legend> 
	{section name=jq_type loop=4}
		
		<label for="jq_type{$smarty.section.jq_type.iteration}" class="hiddendebis">Feldangabe</label>
    	<select name="libconnect[search][jq_type{$smarty.section.jq_type.iteration}]" id="jq_type{$smarty.section.jq_type.iteration}">
    	
    		{foreach name=jq_type_foreach from=$form.jq_type key=key item=value}
    			<option value="{$key}" {if (intval($smarty.section.jq_type.iteration) == intval($smarty.foreach.jq_type_foreach.iteration))}selected="selected"{/if}>{$value}</option>
    		{/foreach}
    	
		</select>
		
		<label for="jq_term{$smarty.section.jq_type.iteration}" class="hiddendebis">Suchbegriff</label> 
		<input type="text" size="30" class="dbis-input" name="libconnect[search][jq_term{$smarty.section.jq_type.iteration}]" id="jq_term{$smarty.section.jq_type.iteration}" value="" />
		
		<label for="jq_bool{$smarty.section.jq_type.iteration}" class="hiddendebis">Verkn&uuml;pfung</label> 
	    <select name="libconnect[search][jq_bool{$smarty.section.jq_type.iteration}]" id="jq_bool{$smarty.section.jq_type.iteration}">
	      	{foreach name=jq_bool_foreach from=$form.jq_bool key=key item=value}
	      		<option value="{$key}">{$value}</option>
			{/foreach}
	    </select>
		
		<label for="jq_not{$smarty.section.jq_type.iteration}" class="hiddendebis">Negator</label> 
	    <select name="libconnect[search][jq_not{$smarty.section.jq_type.iteration}]" id="jq_not{$smarty.section.jq_type.iteration}">
	      	{foreach name=jq_not_foreach from=$form.jq_not key=key item=value}
	      		<option value="{$key}">{$value}</option>
			{/foreach}
	    </select>
	
	    <br />		 
	{/section}

	<br/>
	<br/>
	
    <legend>Suche einschränken auf:</legend> 
	
	<div id="dbis-extended-wrapper">
    <label for="fachgebiete">Fachgebiete:</label> 
    <select multiple="multiple" size="5" name="libconnect[search][gebiete][]" id="fachgebiete" style="height:6.6em;">
   		{foreach from=$form.gebiete key=key item=value}
   			<option value="{$key}">{$value}</option>
   		{/foreach}
    </select>

    <br/>

    <label for="typen">Datenbank-Typen:</label> 
    <select multiple="multiple" size="5" name="libconnect[search][db_type][]" id="typen" style="height:6.6em;">
    	{foreach from=$form.db_types key=key item=value}
   			<option value="{$key}">{$value}</option>
   		{/foreach}
    </select>
    <br/>

    <label for="zugaenge">Zugangsart:</label> 
    <select name="libconnect[search][zugaenge]" id="zugaenge">
    	{foreach from=$form.zugaenge key=key item=value}
   			<option value="{$key}">{$value}</option>
   		{/foreach}
    </select>

    <br/>

    <label for="laender">Regionen:</label> 
    <select multiple="multiple" size="5" name="libconnect[search][lcode][]" id="laender" style="height:6.6em;">
    	{foreach from=$form.lcode key=key item=value}
   			<option value="{$key}">{$value}</option>
   		{/foreach}
    </select>
    <br/>

    <label for="formal_type">Formaler Typ:</label> 
    <select name="libconnect[search][formal_type]" id="formal_type" >
    	{foreach from=$form.formal_type key=key item=value}
   			<option value="{$key}">{$value}</option>
   		{/foreach}
    </select>
    
    <br />
    
    <input type="submit" class="button submit" value="Suchen" />
    </div>
  </fieldset>

</form>
</div>