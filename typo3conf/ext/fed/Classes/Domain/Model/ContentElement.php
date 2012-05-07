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
*  the Free Software Foundation; either version 3 of the License, or
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
 * Domain Model for Content Elements
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage Domain\Model
 */
class Tx_Fed_Domain_Model_ContentElement extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * @var string
	 */
	protected $txFedFcefile;

	/**
	 * @var string
	 */
	protected $ctype;

	/**
	 * @var integer
	 */
	protected $colpos;

	/**
	 * @var integer
	 */
	protected $sysLanguageUid;

	/**
	 * @var string
	 */
	protected $header;

	/**
	 * @var string
	 */
	protected $headerLayout;

	/**
	 * @var string
	 */
	protected $headerPosition;

	/**
	 * @var string
	 */
	protected $headerLink;

	/**
	 * @var integer
	 */
	protected $date;

	/**
	 * @var string
	 */
	protected $bodytext;

	/**
	 * @var boolean
	 */
	protected $rteEnabled;

	/**
	 * @var integer
	 */
	protected $layout;

	/**
	 * @var integer
	 */
	protected $spacebefore;

	/**
	 * @var integer
	 */
	protected $spaceafter;

	/**
	 * @var string
	 */
	protected $sectionFrame;

	/**
	 * @var integer
	 */
	protected $sectionindex;

	/**
	 * @var boolean
	 */
	protected $hidden;

	/**
	 * @var boolean
	 */
	protected $linktotop;

	/**
	 * @var integer
	 */
	protected $starttime;

	/**
	 * @var integer
	 */
	protected $endtime;

	/**
	 * @var string
	 */
	protected $feGroup;

	/**
	 * @var string
	 */
	protected $textAlign;

	/**
	 * @var string
	 */
	protected $textFace;

	/**
	 * @var string
	 */
	protected $textSize;

	/**
	 * @var string
	 */
	protected $textColor;

	/**
	 * @var integer
	 */
	protected $imagewidth;

	/**
	 * @var integer
	 */
	protected $imageheight;

	/**
	 * @var string
	 */
	protected $imageorient;

	/**
	 * @var integer
	 */
	protected $imagecols;

	/**
	 * @var string
	 */
	protected $imagecaptionPosition;

	/**
	 * @var integer
	 */
	protected $cols;

	/**
	 * @var boolean
	 */
	protected $recursive;

	/**
	 * @var string
	 */
	protected $menuType;

	/**
	 * @var string
	 */
	protected $listType;

	/**
	 * @var string
	 */
	protected $tableBgcolor;

	/**
	 * @var integer
	 */
	protected $tableBorder;

	/**
	 * @var integer
	 */
	protected $tableCellspacing;

	/**
	 * @var integer
	 */
	protected $tableCellpadding;

	/**
	 * @var string
	 */
	protected $splashLayout;

	/**
	 * @var float
	 */
	protected $sorting;

	/**
	 * @var string
	 */
	protected $l18nDiffsource;

	/**
	 * @var integer
	 */
	protected $crdate;

	/**
	 * @var integer
	 */
	protected $cruserId;

	/**
	 * @var integer
	 */
	protected $tstamp;

	/**
	 * @var integer
	 */
	protected $t3verStage;

	/**
	 * @var array
	 */
	protected $piFlexform;

	/**
	 *
	 */
	public function __construct() {

	}

	/**
	 * @param string $file
	 */
	public function setTxFedFcefile($file) {
		$this->txFedFcefile = $file;
	}

	/**
	 * @return string
	 */
	public function getTxFedFcefile() {
		return $this->txFedFcefile;
	}

	/**
	 * @param string $ctype
	 */
	public function setCtype($ctype) {
		$this->ctype = $ctype;
	}

	/**
	 * @return string
	 */
	public function getCtype() {
		return $this->ctype;
	}

	/**
	 * @param integer $colpos
	 */
	public function setColpos($colpos) {
		$this->colpos = $colpos;
	}

	/**
	 * @return integer
	 */
	public function getColpos() {
		return $this->colpos;
	}

	/**
	 * @param integer $uid
	 */
	public function setSysLanguageUid($uid) {
		$this->sysLanguageUid = $uid;
	}

	/**
	 * @return integer
	 */
	public function getSysLanguageUid() {
		return $this->sysLanguageUid;
	}

	/**
	 * @param string $header
	 */
	public function setHeader($header) {
		$this->header = $header;
	}

	/**
	 * @return string
	 */
	public function getHeader() {
		return $this->header;
	}

	/**
	 * @param string $headerLayout
	 */
	public function setHeaderLayout($headerLayout) {
		$this->headerLayout = $headerLayout;
	}

	/**
	 * @return string
	 */
	public function getHeaderLayout() {
		return $this->headerLayout;
	}

	/**
	 * @param string $headerPosition
	 */
	public function setHeaderPosition($headerPosition) {
		$this->headerPosition = $headerPosition;
	}

	/**
	 * @return string
	 */
	public function getHeaderPosition() {
		return $this->headerPosition;
	}

	/**
	 * @param string $headerLink
	 */
	public function setHeaderLink($headerLink) {
		$this->headerLink = $headerLink;
	}

	/**
	 * @return string
	 */
	public function getHeaderLink() {
		return $this->headerLink;
	}

	/**
	 * @param integer $date
	 */
	public function setDate($date) {
		$this->date = $date;
	}

	/**
	 * @return integer
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * @param boolean $boolean
	 */
	public function setTransformBodytext($boolean) {
		$this->transformBodytext = $boolean;
	}

	/**
	 * @return boolean
	 */
	public function getTransformBodytext() {
		return $this->transformBodytext;
	}

	/**
	 * @param string $bodytext
	 */
	public function setBodytext($bodytext) {
		$this->bodytext = $bodytext;
	}

	/**
	 * @return string
	 */
	public function getBodytext() {
		return $this->bodytext;
	}

	/**
	 * @param boolean $boolean
	 */
	public function setRteEnabled($boolean) {
		$this->rteEnabled = $boolean;
	}

	/**
	 * @return boolean
	 */
	public function getRteEnabled() {
		return $this->rteEnabled;
	}

	/**
	 * @param integer $layout
	 */
	public function setLayout($layout) {
		$this->layout = $layout;
	}

	/**
	 * @return integer
	 */
	public function getLayout() {
		return $this->layout;
	}

	/**
	 * @param integer $spacebefore
	 */
	public function setSpacebefore($spacebefore) {
		$this->spacebefore = $spacebefore;
	}

	/**
	 * @return integer
	 */
	public function getSpacebefore() {
		return $this->spacebefore;
	}

	/**
	 * @param integer $spaceafter
	 */
	public function setSpaceafter($spaceafter) {
		$this->spaceafter = $spaceafter;
	}

	/**
	 * @return integer
	 */
	public function getSpaceafter() {
		return $this->spaceafter;
	}

	/**
	 * @param string $sectionFrame
	 */
	public function setSectionFrame($sectionFrame) {
		$this->sectionFrame = $sectionFrame;
	}

	/**
	 * @return string
	 */
	public function getSectionFrame() {
		return $this->sectionFrame;
	}

	/**
	 * @param integer $sectionindex
	 */
	public function setSectionIndex($sectionindex) {
		$this->sectionindex = $sectionindex;
	}

	/**
	 * @return integer
	 */
	public function getSectionindex() {
		return $this->sectionindex;
	}

	/**
	 * @param boolean $hidden
	 */
	public function setHidden($hidden) {
		$this->hidden = $hidden;
	}

	/**
	 * @return boolean
	 */
	public function getHidden() {
		return $this->hidden;
	}

	/**
	 * @param boolean $linktotop
	 */
	public function setLinktotop($linktotop) {
		$this->linktotop = $linktotop;
	}

	/**
	 * @return boolean
	 */
	public function getLinktotop() {
		return $this->linktotop;
	}

	/**
	 * @param integer $starttime
	 */
	public function setStarttime($starttime) {
		$this->starttime = $starttime;
	}

	/**
	 * @return integer
	 */
	public function getStarttime() {
		return $this->starttime;
	}

	/**
	 * @param integer $endtime
	 */
	public function setEndtime($endtime) {
		$this->endtime = $endtime;
	}

	/**
	 * @return integer
	 */
	public function getEndtime() {
		return $this->endtime;
	}

	/**
	 * @param string $feGroup
	 */
	public function setFeGroup($feGroup) {
		$this->feGroup = $feGroup;
	}

	/**
	 * @return string
	 */
	public function getFeGroup() {
		return $this->feGroup;
	}

	/**
	 * @param string $textAlign
	 */
	public function setTextAlign($textAlign) {
		$this->textAlign = $textAlign;
	}

	/**
	 * @return string
	 */
	public function getTextAlign() {
		return $this->textAlign;
	}

	/**
	 * @param string $textFace
	 */
	public function setTextFace($textFace) {
		$this->textFace = $textFace;
	}

	/**
	 * @return string
	 */
	public function getTextFace() {
		return $this->textFace;
	}

	/**
	 * @param string $textSize
	 */
	public function setTextSize($textSize) {
		$this->textFace = $textFace;
	}

	/**
	 * @return string
	 */
	public function getTextSize() {
		return $this->textSize;
	}

	/**
	 * @param string $textColor
	 */
	public function setTextColor($textColor) {
		$this->textColor = $textColor;
	}

	/**
	 * @return string
	 */
	public function getTextColor() {
		return $this->textColor;
	}

	/**
	 * @param integer $imagewidth
	 */
	public function setImagewidth($imagewidth) {
		$this->imagewidth = $imagewidth;
	}

	/**
	 * @return integer
	 */
	public function getImagewidth() {
		return $this->imagewidth;
	}

	/**
	 * @param integer $imageheight
	 */
	public function setImageheight($imageheight) {
		$this->imageheight = $imageheight;
	}

	/**
	 * @return integer
	 */
	public function getImageheight() {
		return $this->imageheight;
	}

	/**
	 * @param string $imageorient
	 */
	public function setImageorient($imageorient) {
		$this->imageorient = $imageorient;
	}

	/**
	 * @return string
	 */
	public function getImageorient() {
		return $this->imageorient;
	}

	/**
	 * @param integer $imagecols
	 */
	public function setImagecols($imagecols) {
		$this->imagecols = $imagecols;
	}

	/**
	 * @return integer
	 */
	public function getImagecols() {
		return $this->imagecols;
	}

	/**
	 * @param string $position
	 */
	public function setImagecaptionPosition($position) {
		$this->imagecaptionPosition = $position;
	}

	/**
	 * @return string
	 */
	public function getImagecaptionPosition() {
		return $this->imagecaptionPosition;
	}

	/**
	 * @param integer $cols
	 */
	public function setCols($cols) {
		$this->cols = $cols;
	}

	/**
	 * @return integer
	 */
	public function getCols() {
		return $this->cols;
	}

	/**
	 * @param boolean $recursive
	 */
	public function setRecursive($recursive) {
		$this->recursive = $recursive;
	}

	/**
	 * @return boolean
	 */
	public function getRecursive() {
		return $this->recursive;
	}

	/**
	 * @param string $menuType
	 */
	public function setMenuType($menuType) {
		$this->menuType = $menuType;
	}

	/**
	 * @return string
	 */
	public function getMenuType() {
		return $this->menuType;
	}

	/**
	 * @param string $listType
	 */
	public function setListType($listType) {
		$this->listType = $listType;
	}

	/**
	 * @return string
	 */
	public function getListType() {
		return $this->listType;
	}

	/**
	 * @param string $tableBgcolor
	 */
	public function setTableBgcolor($tableBgcolor) {
		$this->tableBgcolor = $tableBgcolor;
	}

	/**
	 * @return string
	 */
	public function getTableBgcolor() {
		return $this->tableBgcolor;
	}

	/**
	 * @param integer $tableBorder
	 */
	public function setTableBorder($tableBorder) {
		$this->tableBorder = $tableBorder;
	}

	/**
	 * @return integer
	 */
	public function getTableBorder() {
		return $this->tableBorder;
	}

	/**
	 * @param integer $tableCellspacing
	 */
	public function setTableCellspacing($tableCellspacing) {
		$this->tableCellspacing = $tableCellspacing;
	}

	/**
	 * @return integer
	 */
	public function getTableCellspacing() {
		return $this->tableCellspacing;
	}

	/**
	 * @param integer $tableCellpadding
	 */
	public function setTableCellpadding($tableCellpadding) {
		$this->tableCellpadding = $tableCellpadding;
	}

	/**
	 * @return integer
	 */
	public function getTableCellpadding() {
		return $this->tableCellpadding;
	}

	/**
	 * @param string $splashLayout
	 */
	public function setSplashLayout($splashLayout) {
		$this->splashLayout = $splashLayout;
	}

	/**
	 * @return string
	 */
	public function getSplashLayout() {
		return $this->splashLayout;
	}

	/**
	 * @param float $sorting
	 */
	public function setSorting($sorting) {
		$this->sorting = $sorting;
	}

	/**
	 * @return float
	 */
	public function getSorting() {
		return	$this->sorting;
	}

	/**
	 * @param string $l18nDiffsource
	 */
	public function setL18nDiffsource($l18nDiffsource) {
		$this->l18nDiffsource = $l18nDiffsource;
	}

	/**
	 * @return string
	 */
	public function getL18nDiffsource() {
		return $this->l18nDiffsource;
	}

	/**
	 * @param integer $crdate
	 */
	public function setCrdate($crdate) {
		$this->crdate = $crdate;
	}

	/**
	 * @return integer
	 */
	public function getCrdate() {
		return $this->crdate;
	}

	/**
	 * @param integer $cruserId
	 */
	public function setCruserId($cruserId) {
		$this->cruserId = $cruserId;
	}

	/**
	 * @return integer
	 */
	public function getCruserId() {
		return $this->cruserId;
	}

	/**
	 * @param integer $tstamp
	 */
	public function setTstamp($tstamp) {
		$this->tstamp = $tstamp;
	}

	/**
	 * @return integer
	 */
	public function getTstamp() {
		return $this->tstamp;
	}

	/**
	 * @param integer $t3verStage
	 */
	public function setT3verStage($t3verStage) {
		$this->t3verStage = $t3verStage;
	}

	/**
	 * @return integer
	 */
	public function getT3verStage() {
		return $this->t3verStage;
	}

	/**
	 * @param string $piFlexform
	 */
	public function setPiFlexform($piFlexform) {
		$this->piFlexform = $piFlexform;
	}

	/**
	 * @return string
	 */
	public function getPiFlexform() {
		return $this->piFlexform;
	}

}
?>