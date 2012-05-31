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
 * Controller
 *
 * @package Fed
 * @subpackage MVC/Controller
 */
abstract class Tx_Fed_MVC_Controller_AbstractController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @var Tx_Fed_Utility_JSON
	 */
	protected $jsonService;

	/**
	 * @var Tx_Fed_Utility_ExtJS
	 */
	protected $extJSService;

	/**
	 * @var Tx_Fed_Utility_FlexForm
	 */
	protected $flexform;

	/**
	 * @var Tx_Fed_Utility_DocumentHead
	 */
	protected $documentHead;

	/**
	 * @var Tx_Fed_Utility_Debug
	 */
	protected $debugService;

	/**
	 * @var Tx_Fed_Service_File
	 */
	protected $fileService;

	/**
	 * @var Tx_Fed_Service_Email
	 */
	protected $emailService;

	/**
	 * @param Tx_Fed_Utility_DomainObjectInfo $infoService
	 */
	public function injectInfoService(Tx_Fed_Utility_DomainObjectInfo $infoService) {
		$this->infoService = $infoService;
	}

	/**
	 * @param Tx_Fed_Utility_JSON $jsonService
	 */
	public function injectJSONService(Tx_Fed_Utility_JSON $jsonService) {
		$this->jsonService = $jsonService;
	}

	/**
	 * @param Tx_Fed_Utility_ExtJS $extJSService
	 */
	public function injectExtJSService(Tx_Fed_Utility_ExtJS $extJSService) {
		$this->extJSService = $extJSService;
	}

	/**
	 * @param Tx_Fed_Utility_FlexForm $flexform
	 */
	public function injectFlexFormService(Tx_Fed_Utility_FlexForm $flexform) {
		$this->flexform = $flexform;
	}

	/**
	 * @param Tx_Fed_Utility_DocumentHead $documentHead
	 */
	public function injectDocumentHead(Tx_Fed_Utility_DocumentHead $documentHead) {
		$this->documentHead = $documentHead;
	}

	/**
	 * @param Tx_Fed_Utility_Debug $debugService
	 */
	public function injectDebugService(Tx_Fed_Utility_Debug $debugService) {
		$this->debugService = $debugService;
	}

	/**
	 * @param Tx_Fed_Service_File $fileService
	 */
	public function injectFileService(Tx_Fed_Service_File $fileService) {
		$this->fileService = $fileService;
	}

	/**
	 * @param Tx_Fed_Service_Email $emailService
	 */
	public function injectEmailService(Tx_Fed_Service_Email $emailService) {
		$this->emailService = $emailService;
	}

	/**
	 * Clear the page cache for specified pages or current page
	 *
	 * @param mixed $pids
	 */
	protected function clearPageCache($pids=NULL) {
		if ($pids === NULL) {
			$pids = $GLOBALS['TSFE']->id;
		}
		if ($this->cacheService instanceof Tx_Extbase_Service_CacheService) {
			$this->cacheService->clearPageCache($pids);
		} else if (class_exists('Tx_Extbase_Utility_Cache')) {
			Tx_Extbase_Utility_Cache::clearPageCache($pids);
		}
	}

	/**
	 * Get the flexform definition from the current cObj instance
	 *
	 * @param boolean $fallback Set this to TRUE if you get unexpected FlexForm output - cObj ONLY stores the first detected FlexForm based on Controller name
	 * @return array
	 * @api
	 */
	public function getFlexForm($fallback=FALSE) {
		if (!$fallback) {
			$cObj = $this->configurationManager->getContentObject()->data;
			$this->flexform->setContentObjectData($cObj);
			return $this->flexform->getAll();
		}
		$data = $this->configurationManager->getContentObject()->data;
		$flexform = $data['pi_flexform'];
		$array = array();
		$dom = new DOMDocument();
		$dom->loadXML($flexform);
		foreach ($dom->getElementsByTagName('field') as $field) {
			$name = $field->getAttribute('index');
			$value = $field->getElementsByTagName('value')->item(0)->nodeValue;
			$value = trim($value);
			$array[$name] = $value;
		}
		return $array;
	}

	/**
	 * Constructs an instance of $className and validates after applying values
	 * from $data. Does not generate validation messages - is purely intended
	 * to validate a form's contents through AJAX before submission is allowed.
	 * If $className is not specified a DomainObject of the type related to this
	 * controller is assumed.
	 *
	 * Circumvents request processing to output a JSON response directly.
	 *
	 * @param array $data Associative array of data to be validated
	 * @param string $action
	 * @return string
	 */
	public function validateAction($data=array(), $action=NULL) {
		$errorArray = array();
		$parameters = $this->reflectionService->getMethodParameters(get_class($this), $data['action'] . 'Action');
		unset($data['action']);
		$hasErrors = FALSE;
		foreach ($parameters as $argumentName=>$objectData) {
			$className = $objectData['class'];
			if (!$className || !is_array($data[$argumentName])) {
				continue;
			}
			$propertyNames = $this->reflectionService->getClassPropertyNames($className);

			$instance = $this->objectManager->get($className);
			$validatorResolver = $this->objectManager->get('Tx_Extbase_Validation_ValidatorResolver');
			$validator = $validatorResolver->getBaseValidatorConjunction($className);

			$propertyMapper = $this->objectManager->get('Tx_Extbase_Property_Mapper');
			$propertyMapper->map($propertyNames, $data[$argumentName], $instance);

			 if (method_exists($validator, 'validate')) {
				$isValid = $validator->validate($instance);
				$errors = $isValid->getFlattenedErrors();
			} else {
				$isValid = $validator->isValid($instance);
				$errors = $validator->getErrors();
			}

			$errorMessages = $this->getErrorMessages($errors);
			if (count($errorMessages) > 0) {
				$hasErrors = TRUE;
			}
			$errorArray[$argumentName] = $errorMessages;
		}
		if ($hasErrors === FALSE) {
			echo '1';
		} else {
			$this->flashMessageContainer->getAllMessagesAndFlush();
			$json = $this->jsonService->encode($errorArray);
			echo $json;
		}
		exit();
	}

	/**
	 * @param mixed $errors
	 * @return array
	 */
	private function getErrorMessages($errors) {
		$errorArray = array();
		foreach ($errors as $name=>$error) {
			if (is_array($error)) {
				$propertyErrors = $error;
			} else {
				$propertyErrors = array($error);
			}
			$errorArray[$name] = array();
			foreach ($propertyErrors as $propertyError) {
				array_push($errorArray[$name], array(
					'name' => $name,
					'message' => $propertyError->getMessage(),
					'code' => $propertyError->getCode()
				));
			}
		}
		return $errorArray;
	}

	/**
	 * Handles uploads from plupload component. Immediately outputs response -
	 * cannot persist objects!
	 *
	 * @param string $objectType
	 * @param string $propertyName
	 * @return string
	 * @api
	 */
	public function uploadAction($objectType, $propertyName) {
		try {
			if (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
				$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
			} else if (isset($_SERVER["CONTENT_TYPE"])) {
				$contentType = $_SERVER["CONTENT_TYPE"];
			}
			$targetDir = PATH_site . $this->infoService->getUploadFolder($objectType, $propertyName);
			$sourceFilename = $_FILES['file']['tmp_name'];
			if (is_file($sourceFilename) === FALSE) {
				exit();
			}
			$chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
			$chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
			$filename = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
			$filename = preg_replace('/[^\w\._]+/', '', $filename);
			if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $filename)) {
				$ext = strrpos($filename, '.');
				$filenameA = substr($filename, 0, $ext);
				$filenameB = substr($filename, $ext);
				$count = 1;
				while (file_exists($targetDir . DIRECTORY_SEPARATOR . $filenameA . '_' . $count . $filenameB)) {
					$count++;
				}
				$filename = $filenameA . '_' . $count . $filenameB;
			}
			if (strpos($contentType, "multipart") !== FALSE) {
				$newFilename = $this->fileService->move($sourceFilename, $targetDir . DIRECTORY_SEPARATOR . $filename);
			} else {
				$newFilename = $this->fileService->copyChunk($sourceFilename, $targetDir, $filename, $chunk);
			}
			$response = array(
				'name' => basename($newFilename)
			);
			echo $this->jsonService->getRpcResponse($response);
		} catch (Exception $e) {
			echo $this->jsonService->getRpcError($e);
		}
		exit();
	}

	/**
	 * Handles special REST CRUD requests from ExtJS4 Model Proxies type "rest"
	 *
	 * @param string $crudAction String name of CRUD action (create, read, update or destroy)
	 * @return string
	 * @api
	 */
	public function restAction($crudAction='read') {
		switch ($crudAction) {
			case 'update': return $this->performRestUpdate();
			case 'destroy': return $this->performRestDestroy();
			case 'create': return $this->performRestCreate();
			case 'read':
			default: return $this->performRestRead();
		}
	}

	/**
	 * PURELY INTERNAL - CAN BE OVERRIDDEN
	 * @return stdClas
	 */
	protected function performRestCreate() {
		$data = $this->fetchRestBodyData();
		$object = $this->fetchRestObject();
		$repository = $this->infoService->getRepositoryInstance($object);
		$extensionName = $this->infoService->getExtensionName($object);
		$storagePid = $this->getConfiguredStoragePid($extensionName);
		unset($data['uid']); // do NOT allow creation of UID=0
		$object = $this->extJSService->mapDataFromExtJS($object, $data);
		$object->setPid($storagePid);
		$repository->add($object);
		$persistenceManager = $this->objectManager->get('Tx_Extbase_Persistence_Manager');
		$persistenceManager->persistAll();
		return $this->formatRestResponseData($object);
	}

	/**
	 * PURELY INTERNAL - CAN BE OVERRIDDEN
	 * @return mixed
	 */
	protected function performRestRead() {
		$object = $this->fetchRestObject();
		$repository = $this->infoService->getRepositoryInstance($object);
		$all = $repository->findAll()->toArray();
		$export = $this->extJSService->exportDataToExtJS($all);
		return $this->jsonService->encode($export);
	}

	/**
	 * PURELY INTERNAL - CAN BE OVERRIDDEN
	 * @return stdClas
	 */
	protected function performRestUpdate() {
		$data = $this->fetchRestBodyData();
		$object = $this->fetchRestObject();
		$repository = $this->infoService->getRepositoryInstance($object);
		$target = $repository->findOneByUid($data['uid']);
		$object = $this->extJSService->mapDataFromExtJS($target, $data);
		$repository->update($object);
		return $this->formatRestResponseData($object);
	}

	/**
	 * PURELY INTERNAL - CAN BE OVERRIDDEN
	 * @return void
	 */
	protected function performRestDestroy() {
		$data = $this->fetchRestBodyData();
		$object = $this->fetchRestObject();
		$repository = $this->infoService->getRepositoryInstance($object);
		$target = $repository->findOneByUid($data['uid']);
		$repository->remove($target);
		return $this->formatRestResponseData();
	}

	/**
	 * Fetch an instance of an aggregate root object as specified by the request parameters
	 * @return Tx_Extbase_DomainObject_AbstractEntity
	 * @api
	 */
	public function fetchRestObject() {
		$thisClass = get_class($this);
		$controllerName = $this->request->getArgument('controller');
		$objectClassname = str_replace("Controller_{$controllerName}Controller", 'Domain_Model_', $thisClass) . $controllerName;
		$object = $this->objectManager->get($objectClassname);
		return $object;
	}

	/**
	 * Returns associative array (with subarrays if necessary) of REST body
	 *
	 * @param string $body The request body to parse, empty for auto-fetch
	 * @return array
	 */
	public function fetchRestBodyData($body=NULL) {
		if ($body === NULL) {
			$body = file_get_contents("php://input");
		}
		$arr = array();
		$data = $this->jsonService->decode($body);
		foreach ($data as $k=>$v) {
			$arr[$k] = $v;
		}
		return $arr;
	}

	/**
	 * Fetch an associative array of fields posted as REST request body
	 *
	 * @param string $body The request body to parse, empty for auto-fetch
	 * @return array
	 * @api
	 */
	public function fetchRestBodyFields($body=NULL) {
		return array_keys($this->fetchRestBodyData($body));
	}

	/**
	 * Formats $data into a format agreable with ExtJS4 REST
	 *
	 * @param type $data Empty for NULL response
	 * @return mixed
	 */
	public function formatRestResponseData($data=NULL) {
		if ($data === NULL) {
			return "{}";
		}
		$responseData = $this->extJSService->exportDataToExtJS($data);
		$response = $this->jsonService->encode($responseData);
		return $response;
	}

	/**
	 * Get the current configured storage PID for $extensionName
	 * @param string $extensionName Optional extension name, empty for current extension name
	 * @return int
	 */
	public function getConfiguredStoragePid() {
		$object = $this->fetchRestObject();
		if ($object) {
			$extensionName = $this->infoService->getExtensionName($object);
		} else {
			$extensionName = $this->request->getExtensionName();
		}
		$config = $this->infoService->getExtensionTyposcriptConfiguration($extensionName);
		if (is_array($config)) {
			return $config['persistence']['storagePid'];
		} else {
			return $GLOBALS['TSFE']->id;
		}
	}


}

?>