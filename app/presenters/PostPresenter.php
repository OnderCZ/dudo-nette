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
}