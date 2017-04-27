<?php

namespace FrontendModule;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter
{
	public function beforeRender()
    {
        parent::beforeRender();
    }
}