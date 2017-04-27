<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;
		// Admin
		$router[] = new Route('admin/<presenter>/<action>/<id>', array(
			'module' => 'Admin',
			'presenter' => 'Admin',
			'action' => 'default',
			'id' => NULL,
			));

		// SurveyExample
		$router[] = new Route('survey/<action>/<id>', array(
			'module' => 'Frontend',
			'presenter' => 'Survey',
			'action' => 'default',
			'id' => NULL,
			));

		// Frontend
		$router[] = new Route('<action>/<id>', array(
			'module' => 'Frontend',
			'presenter' => 'Home',
			'action' => 'default',
			'id' => NULL,
			));
		return $router;
	}

}
