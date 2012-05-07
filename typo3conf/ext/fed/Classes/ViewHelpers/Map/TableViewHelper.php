<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Claus Due <claus@wildside.dk>, Wildside A/S
*  			
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * 
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers\Map
 */
class Tx_Fed_ViewHelpers_Map_TableViewHelper extends Tx_Fed_ViewHelpers_TableViewHelper {
	
	/**
	 * Render method
	 * 
	 * @param boolean $auto If TRUE, renders markers automatically. If FALSE you need to manually iterate all markers or data manually
	 * @return string
	 */
	public function render($auto=TRUE) {
		$this->rowClassPrefix = 'marker';
		parent::render();
		if ($objects || $data) {
			return $this->tag->render();
		} else if ($auto === FALSE) {
			$this->tag->setContent($this->renderChildren());
			return $this->tag->render();
		}
		$layers = $this->templateVariableContainer->get('layers');
		$rows = "";
		
		foreach ($layers as $layer) {
			foreach ($layer as $marker) {
				$data = $marker['data'];
				$rows .= "<tr class='{$this->rowClassPrefix}{$marker['id']}'>";
				foreach ($data as $name=>$value) {
					$rows .= "<td class='{$name}'>{$value}</td>";
				}
				$rows .= "</tr>\n";
			}
		}
		$first = array_shift(array_shift($layers));
		$keys = array_keys($first['data']);
		$head = "";
		foreach ($keys as $name=>$key) {
			$head .= "<th class='{$name}'>{$key}</th>";
		}
		
		$html = "<thead>
<tr>
	{$head}
</tr>
</thead>
<tbody>
{$rows}
</tbody>";

		$this->tag->setContent($html);
		return $this->tag->render();
	}
}
	
?>