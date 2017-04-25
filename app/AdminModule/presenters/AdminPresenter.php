<?php

namespace AdminModule;
use \Nette\Application\UI;
use Instante;

class AdminPresenter extends BasePresenter {

	protected function createComponentSignInForm()
	{
		$form = new UI\Form;
		$form->addText('email', 'E-mail:')
			->setRequired('Please enter your username.');
		$form->addPassword('password', 'Heslo:')
			->setRequired('Please enter your password.');
		$form->addCheckbox('remember', 'Zapamatovat přihlášení');
		$form->addSubmit('send', 'Přihlásit');
		// call method signInFormSubmitted() on success
		$form->onSuccess[] = [$this, 'signInFormSubmitted'];
		$form->setRenderer(new Instante\Bootstrap3Renderer\BootstrapRenderer);
		return $form;
	}

	public function signInFormSubmitted($form){
		$values = $form->getValues();
		if ($values->remember) {
			$this->getUser()->setExpiration('+ 14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('+ 20 minutes', TRUE);
		}
		try {
			$this->getUser()->login($values->email, $values->password);
			$this->redirect('Logged:');
		} catch (\Nette\Security\AuthenticationException $e) {
			$this->flashMessage($e->getMessage(), 'alert-danger');
		}
	}

	public function renderDefault() {
		if ($this->user->isLoggedIn()) {
			$this->redirect('Logged:');
		}
	}
}