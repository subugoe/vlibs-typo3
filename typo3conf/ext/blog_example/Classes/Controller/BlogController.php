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
 * The blog controller for the Blog package
 *
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Tx_BlogExample_Controller_BlogController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_BlogExample_Domain_Model_BlogRepository
	 */
	protected $blogRepository;

	/**
	 * @var Tx_BlogExample_Domain_Model_AdministratorRepository
	 */
	protected $administratorRepository;

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	public function initializeAction() {
		$this->blogRepository = t3lib_div::makeInstance('Tx_BlogExample_Domain_Repository_BlogRepository');
		$this->administratorRepository = t3lib_div::makeInstance('Tx_BlogExample_Domain_Repository_AdministratorRepository');
	}

	/**
	 * Index action for this controller. Displays a list of blogs.
	 *
	 * @return string The rendered view
	 */
	public function indexAction() {
		$this->view->assign('blogs', $this->blogRepository->findAll());
	}

	/**
	 * Displays a form for creating a new blog
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $newBlog A fresh blog object taken as a basis for the rendering
	 * @return string An HTML form for creating a new blog
	 * @dontvalidate $newBlog
	 */
	public function newAction(Tx_BlogExample_Domain_Model_Blog $newBlog = NULL) {
		$this->view->assign('newBlog', $newBlog);
		$this->view->assign('administrators', $this->administratorRepository->findAll());
	}

	/**
	 * Creates a new blog
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $newBlog A fresh Blog object which has not yet been added to the repository
	 * @return void
	 */
	public function createAction(Tx_BlogExample_Domain_Model_Blog $newBlog) {
		$this->blogRepository->add($newBlog);
		$this->flashMessages->add('Your new blog was created.');
		$this->redirect('index');
	}
	
	/**
	 * Edits an existing blog
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $blog The blog to be edited. This might also be a clone of the original blog already containing modifications if the edit form has been submitted, contained errors and therefore ended up in this action again.
	 * @return string Form for editing the existing blog
	 * @dontvalidate $blog
	 */
	public function editAction(Tx_BlogExample_Domain_Model_Blog $blog) {
		$this->view->assign('blog', $blog);
		$this->view->assign('administrators', $this->administratorRepository->findAll());
	}

	/**
	 * Updates an existing blog
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $blog A not yet persisted clone of the original blog containing the modifications
	 * @return void
	 */
	public function updateAction(Tx_BlogExample_Domain_Model_Blog $blog) {
		$this->blogRepository->update($blog);
		$this->flashMessages->add('Your blog has been updated.');
		$this->redirect('index');
	}

	/**
	 * Deletes an existing blog
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $blog The blog to delete
	 * @return void
	 */
	public function deleteAction(Tx_BlogExample_Domain_Model_Blog $blog) {
		$this->blogRepository->remove($blog);
		$this->flashMessages->add('Your blog has been removed.');
		$this->redirect('index');
	}

	/**
	 * Deletes an existing blog
	 *
	 * @return void
	 */
	public function deleteAllAction() {
		$this->blogRepository->removeAll();
		$this->redirect('index');
	}

	/**
	 * Creates a several new blogs
	 *
	 * @return void
	 */
	public function populateAction() {
		$author = t3lib_div::makeInstance('Tx_BlogExample_Domain_Model_Person', 'Stephen', 'Smith', 'foo.bar@example.com');
		for ($blogNumber = 1; $blogNumber < 4; $blogNumber++) {
			$blog = $this->getBlog($blogNumber, $author);
			$this->blogRepository->add($blog);
		}
		$this->redirect('index');
	}

	/**
	 * Returns a sample blog populated with generic data. It is also an example how to handle objects and repositories in general.
	 *
	 * @param int $blogNumber The number of the blog
	 * @param Tx_BlogExample_Domain_Model_Person $author The author of posts
	 * @return Tx_BlogExample_Domain_Model_Blog The blog object
	 */
	private function getBlog($blogNumber, $author) {
		$blog = new Tx_BlogExample_Domain_Model_Blog;
		$blog->setTitle('Blog #' . $blogNumber);
		$blog->setDescription('A blog about TYPO3 extension development.');

		$administrator = new Tx_BlogExample_Domain_Model_Administrator();
		$administrator->setName('John Doe');
		$administrator->setEmail('john.doe@example.com');
		$blog->setAdministrator($administrator);

		for ($postNumber = 1; $postNumber < 6; $postNumber++) {
			$post = new Tx_BlogExample_Domain_Model_Post;
			$post->setTitle('The Post #' . $postNumber);
			$post->setAuthor($author);
			$post->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.');
			$blog->addPost($post);

			$comment = new Tx_BlogExample_Domain_Model_Comment;
			$comment->setDate(new DateTime);
			$comment->setAuthor('Peter Pan');
			$comment->setEmail('peter.pan@example.com');
			$comment->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.');
			$post->addComment($comment);

			$comment = new Tx_BlogExample_Domain_Model_Comment;
			$comment->setDate(new DateTime('2009-03-19 23:44'));
			$comment->setAuthor('John Smith');
			$comment->setEmail('john@matrix.org');
			$comment->setContent('Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.');
			$post->addComment($comment);

			$tag = new Tx_BlogExample_Domain_Model_Tag('MVC');
			$post->addTag($tag);

			$tag = new Tx_BlogExample_Domain_Model_Tag('Domain Driven Design');
			$post->addTag($tag);

			$post->setBlog($blog);
		}

		return $blog;
	}
	
	/**
	 * Override getErrorFlashMessage to present
	 * nice flash error messages.
	 *
	 * @return string
	 */
	protected function getErrorFlashMessage() {
		switch ($this->actionMethodName) {
			case 'updateAction' :
				return 'Could not update the blog:';
			case 'createAction' :
				return 'Could not create the new blog:';
			default :
				return parent::getErrorFlashMessage();
		}
	}

}

?>