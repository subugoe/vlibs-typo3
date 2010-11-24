<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2009 Jochen Rau <jochen.rau@typoplanet.de>
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
 * Comments controller for the Blog package
 *
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Tx_BlogExample_Controller_CommentController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * Action that adds a comment to a blog post and redirects to single view
	 *
	 * @param Tx_BlogExample_Domain_Model_Post $post The post the comment is related to
	 * @param Tx_BlogExample_Domain_Model_Comment $newComment The comment to create
	 * @return void
	 */
	public function createAction(Tx_BlogExample_Domain_Model_Post $post, Tx_BlogExample_Domain_Model_Comment $newComment) {
		$post->addComment($newComment);
		$this->flashMessages->add('Your new comment was created.');
		$this->redirect('show', 'Post', NULL, array('post' => $post));
	}

	/**
	 * Deletes an existing comment
	 *
	 * @param Tx_BlogExample_Domain_Model_Post $post The post the comment is related to
	 * @param Tx_BlogExample_Domain_Model_Comment $comment The comment to be deleted
	 * @return void
	 */
	public function deleteAction(Tx_BlogExample_Domain_Model_Post $post, Tx_BlogExample_Domain_Model_Comment $comment) {
		$post->removeComment($comment);
		$this->flashMessages->add('The comment was removed.');
		$this->redirect('edit', 'Post', NULL, array('post' => $post, 'blog' => $post->getBlog()));
	}

	/**
	 * Deletes all comments of the given post
	 *
	 * @param Tx_BlogExample_Domain_Model_Post $post The post the comment is related to
	 * @return void
	 */
	public function deleteAllAction(Tx_BlogExample_Domain_Model_Post $post) {
		$post->removeAllComments();
		$this->flashMessages->add('Comments have been removed.');
		$this->redirect('edit', 'Post', NULL, array('post' => $post, 'blog' => $post->getBlog()));
	}

	/**
	 * Override getErrorFlashMessage to present
	 * nice flash error messages.
	 *
	 * @return string
	 */
	protected function getErrorFlashMessage() {
		switch ($this->actionMethodName) {
			case 'createAction' :
				return 'Could not create the new comment:';
			case 'deleteAction' :
				return 'Could not delete comment:';
			case 'createAction' :
				return 'Could not delete comments:';
			default :
				return parent::getErrorFlashMessage();
		}
	}
		
}

?>