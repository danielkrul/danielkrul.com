<?php

namespace AdminModule;
use \Nette\Application\UI;

class AdminPresenter extends BasePresenter {

	/** @var \PostsRepository */
	private $postsRepository;

	/** @var \CommentsRepository */
	private $commentsRepository;

	public function __construct(\PostsRepository $postsRepository, \CommentsRepository $commentsRepository) {
		$this->postsRepository = $postsRepository;
		$this->commentsRepository = $commentsRepository;
	}

	public function renderDefault() {
		$this->template->posts = $this->postsRepository->fetchAll();
	}
}