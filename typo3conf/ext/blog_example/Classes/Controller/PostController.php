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
 * The posts controller for the Blog package
 *
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Tx_BlogExample_Controller_PostController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_BlogExample_Domain_Model_PostRepository
	 */
	protected $postRepository;

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	public function initializeAction() {
		$this->postRepository = t3lib_div::makeInstance('Tx_BlogExample_Domain_Repository_PostRepository');
		$this->personRepository = t3lib_div::makeInstance('Tx_BlogExample_Domain_Repository_PersonRepository');
	}

	/**
	 * List action for this controller. Displays latest posts
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $blog The blog to show the posts of
	 * @param string $tag The name of the tag to show the posts for
	 * @return string
	 */
	public function indexAction(Tx_BlogExample_Domain_Model_Blog $blog, $tag = NULL) {
		if (empty($tag)) {
			$posts = $this->postRepository->findRecentByBlog($blog, $this->settings['maxPosts']);
		} else {
			$tag = urldecode($tag);
			$posts = $this->postRepository->findByTagAndBlog($tag, $blog, $this->settings['maxPosts']);
			$this->view->assign('tag', $tag);
		}
		$this->view->assign('blog', $blog);
		$this->view->assign('posts', $posts);
	}

	/**
	 * Action that displays one single post
	 *
	 * @param Tx_BlogExample_Domain_Model_Post $post The post to display
	 * @param Tx_BlogExample_Domain_Model_Comment $newComment A new comment
	 * @dontvalidate $newComment
	 * @return string The rendered view
	 */
	public function showAction(Tx_BlogExample_Domain_Model_Post $post, Tx_BlogExample_Domain_Model_Comment $newComment = NULL) {
		$this->view->assign('post', $post);
		$this->view->assign('newComment', $newComment);
	}

	/**
	 * Displays a form for creating a new post
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $blog The blog the post belogs to
	 * @param Tx_BlogExample_Domain_Model_Post $newPost A fresh post object taken as a basis for the rendering
	 * @return string An HTML form for creating a new post
	 * @dontvalidate $newPost
	 */
	public function newAction(Tx_BlogExample_Domain_Model_Blog $blog, Tx_BlogExample_Domain_Model_Post $newPost = NULL) {
		$this->view->assign('authors', $this->personRepository->findAll());
		$this->view->assign('blog', $blog);
		$this->view->assign('newPost', $newPost);
	}

	/**
	 * Creates a new post
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $blog The blog the post belogns to
	 * @param Tx_BlogExample_Domain_Model_Post $newBlog A fresh Blog object which has not yet been added to the repository
	 * @return void
	 */
	public function createAction(Tx_BlogExample_Domain_Model_Blog $blog, Tx_BlogExample_Domain_Model_Post $newPost) {
		$blog->addPost($newPost);
		$newPost->setBlog($blog);
		$this->flashMessages->add('Your new post was created.');
		$this->redirect('index', NULL, NULL, array('blog' => $blog));
	}

	/**
	 * Displays a form to edit an existing post
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $blog The blog the post belogs to
	 * @param Tx_BlogExample_Domain_Model_Post $post The original post
	 * @return string Form for editing the existing blog
	 * @dontvalidate $post
	 */
	public function editAction(Tx_BlogExample_Domain_Model_Blog $blog, Tx_BlogExample_Domain_Model_Post $post) {
		$this->view->assign('authors', $this->personRepository->findAll());
		$this->view->assign('blog', $blog);
		$this->view->assign('post', $post);
	}

	/**
	 * Updates an existing post
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $blog The blog the post belongs to
	 * @param Tx_BlogExample_Domain_Model_Post $post The existing, unmodified post
	 * @param Tx_BlogExample_Domain_Model_Post $updatedPost A clone of the original post with the updated values already applied
	 * @return void
	 */
	public function updateAction(Tx_BlogExample_Domain_Model_Blog $blog, Tx_BlogExample_Domain_Model_Post $post) {
		$this->postRepository->update($post);
		$this->flashMessages->add('Your post was updated.');
		$this->redirect('index', NULL, NULL, array('blog' => $blog));
	}

	/**
	 * Deletes an existing post
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $blog The blog the post belongs to
	 * @param Tx_BlogExample_Domain_Model_Post $post The post to be deleted
	 * @return void
	 */
	public function deleteAction(Tx_BlogExample_Domain_Model_Blog $blog, Tx_BlogExample_Domain_Model_Post $post) {
		$this->postRepository->remove($post);
		$this->flashMessages->add('Your post was removed.');
		$this->redirect('index', NULL, NULL, array('blog' => $blog));
	}

}

?>