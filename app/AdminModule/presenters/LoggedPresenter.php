<?php

namespace AdminModule;
use \Nette\Application\UI;

class LoggedPresenter extends BasePresenter {
	public function renderDefault() {
		if (!$this->user->isLoggedIn()) {
			$this->flashMessage('Session vypršela!', 'alert-danger');
			$this->redirect('Admin:');
		}
	}

	public function handleLogout() {
        $this->user->logout();
        $this->flashMessage('Byl jste odhlášen.', 'alert-info');
        $this->redirect('Admin:');
        exit;
    }
}