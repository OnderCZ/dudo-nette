<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

class SignPresenter extends \Nette\Application\UI\Presenter {

  public function actionOut() {
	$this->user->logout();
	$this->flashMessage('Odhlášení bylo úspěšné.');
	$this->redirect('Homepage:');
  }

  public function createComponentSignInForm() {
	$form = new Form();

	$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Prosím vyplňte uživatelské jméno.');
	$form->addPassword('password', 'Heslo')
			->setRequired('Prosím vyplňte své heslo.');
	$form->addSubmit('send', 'Přihlásit');

	$form->onSuccess[] = [$this, 'SignInFormSucceeded'];

	return $form;
  }

  public function signInFormSucceeded($form, $values) {
	try {
	  $this->getUser()->login($values->username, $values->password);
	  $this->redirect('Homepage:');

	} catch (Nette\Security\AuthenticationException $e) {
	  $form->addError('Nesprávné přihlašovací jméno nebo heslo.');
	}
  }
}
