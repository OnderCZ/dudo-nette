<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

class TestmailPresenter extends \Nette\Application\UI\Presenter {
  
  /** @persistent */
  public $lang;
  
  protected function createComponentRegistrationForm() {
    $form = new Form();
    
    $form->addText('name', 'Jméno:')
            ->setRequired('Zadejte prosím jméno');
    $form->addPassword('password', 'Heslo:')
            ->setRequired('Zvolte si heslo')
            ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaky', 3);
    $form->addPassword('passwordVerify', 'Heslo pro kontrolu:')
            ->setRequired('Zadejte heslo prosím ještě jednou pro kontrolu')
            ->addRule(Form::EQUAL, 'Hesla se neshodují', $form['password']);
    $form->addText('age', 'Věk')
            ->setHtmlType('number')
            ->setRequired(false)
            ->addRule(Form::INTEGER, 'Věk musí být číslo')
            ->addRule(Form::RANGE, 'Věk musí být v rozmezí od %d do %d let', [18,120]);
    $form->addSelect('language', 'Jazyk', [
        'cs' => 'Čeština',
        'en' => 'Angličtina',
    ]);
    $form['language']->setDefaultValue('en');
    
    $form->addSubmit('login', 'Registrovat');
    $form->onSuccess[] = [$this, 'registrationFormSucceeded'];
    
    return $form;
  }
  
  public function registrationFormSucceeded($form, $values) {
    $this->lang = $values->language;
    $this->flashMessage('Byl jste úspěšně zaregistrován');
    $this->redirect('Testmail:');
  }
  
  public function renderJson() {
    $this->sendJson($this->httpRequest->getHeaders());    
  }
}
