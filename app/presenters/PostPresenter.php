<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

class PostPresenter extends \Nette\Application\UI\Presenter {

  /**
   *
   * @var \Nette\Database\Context
   */
  private $database;

  public function __construct(\Nette\Database\Context $database) {
    $this->database = $database;
  }

  public function renderShow($postId) {
    $post = $this->database->table('web_texty')->get($postId);

    if(!$post) {
      $this->error('Stránka nebyla nalezena.');
    }

    $this->template->post = $post;
    $this->template->comments = $post->related('web_komentare')->order('created_at');
  }

  public function actionEdit($postId) {
	if(!$this->user->isLoggedIn()) {
	  $this->redirect('Sign:in');
	}

    $post = $this->database->table('web_texty')->get($postId);

    if(!$post) {
      $this->error('Příspěvek nebyl nalezen');
    }
    $this['postForm']->setDefaults($post->toArray());
  }

  public function actionCreate() {
	if(!$this->user->isLoggedIn()) {
	  $this->redirect('Sign:in');
	}
  }

  protected function createComponentCommentForm() {
    $form = new Form;

    $form->addText('name', 'Jméno')
            ->setRequired();

    $form->addEmail('email', 'E-mail');

    $form->addTextArea('content', 'Komentář')
            ->setRequired();

    $form->addSubmit('send', 'Publikovat komentář');

    $form->onSuccess[] = [$this, 'commentFormSucceeded'];

    return $form;
  }

  public function commentFormSucceeded($form, $values) {
    $postId = $this->getParameter('postId');

    $this->database->table('web_komentare')->insert([
        'id_textu' => $postId,
        'jmeno' => $values->name,
        'mejl' => $values->email,
        'text' => $values->content,
    ]);

    $this->flashMessage('Děkuji za komentář.', 'success');
    $this->redirect('this');
  }

  public function createComponentPostForm() {
    $form = new Form();

    $form->addText('nadpis', 'Titulek:')
            ->setRequired();
    $form->addTextArea('text', 'Obsah:')
            ->setRequired();
    $form->addSubmit('send', 'Uložit a publikovat');
    $form->onSuccess[] = [$this, 'postFormSucceeded'];

    return $form;
  }

  public function postFormSucceeded($form, $values) {

	if($this->user->isLoggedIn()) {
	  $this->error('Pro vytvoření, nebo editování příspěvku se musíte přihlásit.');
	}

    $postId = $this->getParameter('postId');

    if($postId) {
      $post = $this->database->table('web_texty')->get($postId);
      $post->update([
        'nadpis' => $values->nadpis,
        'text' => $values->text,
      ]);
    } else {
      $post = $this->database->table('web_texty')->insert([
                'nadpis' => $values->nadpis,
                'text' => $values->text,
      ]);
    }

    $this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
    $this->redirect('Post:show', $post->id);
  }
}