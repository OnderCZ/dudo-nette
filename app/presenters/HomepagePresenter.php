<?php

namespace App\Presenters;

use Nette;
use App\Model\ArticleManager;

class HomepagePresenter extends \Nette\Application\UI\Presenter
{
  /** @var ArticleManager */
  private $articleManager;

  /** @persistent */
  public $page;

  public function __construct(ArticleManager $articleManager) {
    $this->articleManager = $articleManager;
  }

  public function renderDefault(int $page = 1) {
	$articles = $this->articleManager->getPublicArticles();

    $lastPage = 0;
	$this->template->posts = $articles->page($page, 10, $lastPage);

	$this->page = $page;

	$this->template->page = $page;
	$this->template->lastPage = $lastPage;

  }
}
