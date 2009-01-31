<?php

class IndexController
{
	private $_userId = null;
	private $_userCode = null;
	
	public function __construct()
	{
		if (isset($_GET['user']))
		{
			$this->_userCode = $_GET['user'];
			if (!($this->_userId = (int)(Habrometr::getInstance()->code2UserId($this->_userCode))))
				throw new Exception('Error: user not found.');
		}
		else
		{
			$this->_userCode = 'feedbee';
			$this->_userId = 1;
		}
	}
	
	public function allUsersAction()
	{
		$view = Dispatcher::getView();
		$view->userList = Habrometr::getInstance()->getUserList();
	}
	
	public function registerAction()
	{
		$errors = array();
		$ok = false;
		$habravaluesFromXML = null;
		$user_code = null;
		$user_email = null;
		if (isset($_GET['user_code']))
		{
			$user_code = trim($_GET['user_code']);
			if (!preg_match('#[a-zA-Z0-9\-_]{1,100}#', $user_code))
			{
				$errors[] = 'Логин пользователя должен состоять из символов латинского алфавита, цифр и символов "-", "_".';
			}
			if (isset($_GET['user_email']) && $_GET['user_email'] !== '')
			{
				$user_email = trim($_GET['user_email']);
				if (!preg_match("/[0-9a-z_]+@[0-9a-z_^\.-]+\.[a-z]{2,4}/i", $user_email))
				{
					$errors[] = 'E-mail пользователя должен соответствовать шаблону "user@host.zone".';
				}
			}
			if (!$errors)
			{
				try
				{
					$habravaluesFromXML = Habrometr::getInstance()->parsePage($user_code);
				}
				catch(Exception $e)
				{
					if ($e->getCode() == 404)
					{
						$errors[] = "Хабрахабр сообщает, что запрошенный пользователь не существует. Регистрация невозможна.";
					}
					else
					{
						$errors[] = "При попытке загрузки страницы пользователя (http://habrahabr.ru/api/profile/{$user_code}/) возникла ошибка. Сервис не может зарегистрировать пользователя, не убедившись, что он зарегистрирован на Хабрахабре.";
					}
				}
			}
			if (!$errors)
			{
				if (!is_null(Habrometr::getInstance()->code2UserId($user_code)))
				{
					$errors[] = "Вы (<a href=\"./?action=user_page&user={$user_code}\">{$user_code}</a>) уже зарегистрированы в системе. Повторная регистрация невозможна.";
				}
			}
			
			// Ошибок нет, надо регить юезра
			if (!$errors)
			{
				if ($userId = Habrometr::getInstance()->addUser(array('user_code' => $user_code, 'user_email' => $user_email)))
				{
					$ok = true;
					try
					{
						Habrometr::getInstance()->putValuesFromArray($userId, $habravaluesFromXML);
					}
					catch (Exception $e)
					{}
					$m = new XMemcache('habrometr');
					$m->delete('/users/');
				}
			}
		}
		
		$view = Dispatcher::getView();
		$user = Habrometr::getInstance()->getUser($this->_userId);
		$view->userCode = $user_code;
		$view->userEmail = $user_email;
		$view->errors = $errors;
		$view->ok = $ok;
	}
	
	public function userPageAction()
	{
		$view = Dispatcher::getView();
		$view->userData = Habrometr::getInstance()->getUser($this->_userId);
		$view->current = Habrometr::getInstance()->getValues($this->_userId);
		$view->history = Habrometr::getInstance()->getHistory($this->_userId, 500);
	}
	
	public function getAction()
	{
		$view = Dispatcher::getView();
		$view->sizes = array(
			array('x' => 425, 'y' => 120),
			array('x' => 88,  'y' => 120),
			array('x' => 88,  'y' => 15)
		);
		$user = Habrometr::getInstance()->getUser($this->_userId);
		$view->userCode = $user['user_code'];
	}
	
	public function defaultAction()
	{
		Dispatcher::getView()->version = Habrometr::VERSION;
	}
}