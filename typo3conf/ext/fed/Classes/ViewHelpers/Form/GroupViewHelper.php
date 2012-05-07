<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * ************************************************************* */

/**
 * Form field group ViewHelper.
 *
 * Can repeat a group of fields a requested number of times. With an added
 * fed:form.group.countSelector the number of groups can be modified through
 * DHTML (jQuery required).
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers/Form
 */
class Tx_Fed_ViewHelpers_Form_GroupViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

	/**
	 * Template variable backups
	 * @var array
	 */
	protected $backups = array();

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var Tx_Fed_Utility_DocumentHead
	 */
	protected $documentHead;

	/**
	 * @var Tx_Fed_Utility_JSON
	 */
	protected $jsonService;

	/**
	 * @var Tx_Fluid_Core_ViewHelper_TagBuilder
	 */
	protected $tag = NULL;

	/**
	 * @var string
	 */
	protected $tagName = 'fieldset';

	/**
	 * @param Tx_Fluid_Core_ViewHelper_TagBuilder $tagBuilder Tag builder
	 */
	public function injectTagBuilder(Tx_Fluid_Core_ViewHelper_TagBuilder $tagBuilder) {
		$this->tag = $tagBuilder;
	}

	/**
	 * @param Tx_Fed_Utility_DomainObjectInfo $infoService
	 */
	public function injectInfoService(Tx_Fed_Utility_DomainObjectInfo $infoService) {
		$this->infoService = $infoService;
	}

	/**
	 * @param Tx_Extbase_Object_ObjectManager $objectManager
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param Tx_Fed_Utility_DocumentHead $documentHead
	 */
	public function injectDocumentHead(Tx_Fed_Utility_DocumentHead $documentHead) {
		$this->documentHead = $documentHead;
	}

	/**
	 * @param Tx_Fed_Utility_JSON $jsonService
	 */
	public function injectJsonService(Tx_Fed_Utility_JSON $jsonService) {
		$this->jsonService = $jsonService;
	}

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerUniversalTagAttributes();
		$this->registerArgument('amount', 'integer', 'Number of field groups to render', TRUE);
		$this->registerArgument('maximum', 'integer', 'Maximum number of field groups to render', FALSE, 10);
		$this->registerArgument('minimum', 'integer', 'Minimum number of field groups to render', FALSE, 1);
		$this->registerArgument('iteration', 'string', 'Variable name for iteration, if any');
		$this->registerArgument('zeroLabel', 'string', 'Optional label for zero items', FALSE, '0');
	}

	/**
	 * Render
	 *
	 * @return string
	 */
	public function render() {
		$property = $this->arguments['property'];
		$amount = $this->arguments['amount'];
		$maximum = $this->arguments['maximum'];
		$minimum = $this->arguments['minimum'];
		$fieldNamePrefix = $this->getName();
		$hasFormObject = $this->viewHelperVariableContainer->exists('Tx_Fluid_ViewHelpers_FormViewHelper', 'formObject');
		if (!$this->isObjectAccessorMode() || !$hasFormObject) {
			throw new Exception('Form/GroupViewHelper requires associated form object', 1323351482);
		}
		$formObject = $this->viewHelperVariableContainer->get('Tx_Fluid_ViewHelpers_FormViewHelper', 'formObject');
		$complexObjectType = $this->infoService->getPropertyType($formObject, $property);
		$objectType = $this->infoService->parseObjectStorageAnnotation($complexObjectType);
		$objectInstance = $this->objectManager->get($objectType);
		$properties = Tx_Extbase_Reflection_ObjectAccess::getGettablePropertyNames($objectInstance);

		$content = array();
		$buffer = array();

		$this->viewHelperVariableContainer->add('Tx_Fed_ViewHelpers_Form_GroupViewHelper', 'amount', $amount);
		$this->viewHelperVariableContainer->add('Tx_Fed_ViewHelpers_Form_GroupViewHelper', 'maximum', $maximum);
		$this->viewHelperVariableContainer->add('Tx_Fed_ViewHelpers_Form_GroupViewHelper', 'minimum', $minimum);
		$this->backupTemplateVariables();
		$cycle = 1;
		for ($i = $minimum; $i <= $maximum; $i++) {
			$names = $this->createFormFieldNames($properties, $i);
			$this->templateVariableContainer->add('property', $names);
			$iteration = array(
				'isFirst' => ($i == $minimum),
				'isLast' => (($i+1) == $maximum),
				'isOdd' => ($i%2 == 1),
				'isEven' => ($i%2 == 0),
				'cycle' => $cycle,
				'index' => ($cycle - 1)
			);
			if ($this->arguments['iteration']) {
				$this->templateVariableContainer->add($this->arguments['iteration'], $iteration);
			}
			$this->viewHelperVariableContainer->addOrUpdate('Tx_Fed_ViewHelpers_Form_GroupViewHelper', 'iteration', $iteration);
			if ($i < $amount) {
				array_push($content, $this->renderChildren());
			} else {
				array_push($buffer, $this->renderChildren());
			}
			$this->templateVariableContainer->remove('property');
			if ($this->arguments['iteration']) {
				$this->templateVariableContainer->remove($this->arguments['iteration']);
			}
			$cycle++;
		}
		$this->viewHelperVariableContainer->remove('Tx_Fed_ViewHelpers_Form_GroupViewHelper', 'amount');
		$this->viewHelperVariableContainer->remove('Tx_Fed_ViewHelpers_Form_GroupViewHelper', 'maximum');
		$this->viewHelperVariableContainer->remove('Tx_Fed_ViewHelpers_Form_GroupViewHelper', 'minimum');
		$this->restoreTemplateVariables();
		if ($this->arguments['id']) {
			$domElementId = $this->arguments['id'];
		} else {
			$domElementId = uniqid('fieldGroup');
			$this->tag->addAttribute('id', $domElementId);
		}
		$content = implode(LF, $content);
		$this->tag->setContent($content);
		$scriptFile = t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/FormFieldGroup.js';
		$this->documentHead->includeFile($scriptFile);
		$options = array(
			'amount' => $amount,
			'maximum' => $maximum,
			'buffer' => $buffer
		);
		$optionsJsonString = $this->jsonService->encode($options);
		$script = <<< JS
jQuery(document).ready(function() {
	jQuery('#$domElementId').formFieldGroup($optionsJsonString);
});
JS;
		$this->documentHead->includeHeader($script, 'js');
		return $this->tag->render();
	}

	/**
	 * Generates and registers for token generation the necessary fields for
	 * $properties at iteration $iteration.
	 *
	 * @param array $properties
	 * @param integer $iteration
	 * @return array
	 */
	protected function createFormFieldNames($properties, $iteration) {
		$propertyName = $this->arguments['property'];
		$fieldNames = array();
		$formObject = $this->viewHelperVariableContainer->get('Tx_Fluid_ViewHelpers_FormViewHelper', 'formObject');
		if ($formObject) {
			$relationValue = Tx_Extbase_Reflection_ObjectAccess::getProperty($formObject, $propertyName);
			if ($relationValue) {
				$relationValueArray = $relationValue->toArray();
				$currentRelation = $relationValueArray[$iteration];
			}
		}
		foreach($properties as $property) {
			$fieldName = implode('.', array($propertyName, $iteration, $property));
			if (!$currentRelation) {
				$value = NULL;
			} else {
				$value = Tx_Extbase_Reflection_ObjectAccess::getProperty($currentRelation, $property);
			}
			$fieldNames[$property] = array(
				'name' => $fieldName,
				'value' => $value
			);
			$this->registerFieldNameForFormTokenGeneration($fieldName);
		}
		return $fieldNames;
	}

	/**
	 * Backups up possible collision template variables
	 */
	protected function backupTemplateVariables() {
		$reserved = array(
			'property',
			$this->arguments['iteration']
		);
		foreach ($reserved as $name) {
			if ($this->templateVariableContainer->exists($name)) {
				$this->backups[$name] = $this->templateVariableContainer->get($name);
				$this->templateVariableContainer->remove($name);
			}
		}
	}

	/**
	 * Restores backed up template variables
	 */
	protected function restoreTemplateVariables() {
		foreach ($this->backups as $name=>$value) {
			$this->templateVariableContainer->add($name, $value);
		}
	}



}

?>