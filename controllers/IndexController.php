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
			if (!($this->_userId = (int)(Habrometr_Model::getInstance()->code2UserId($this->_userCode))))
				throw new Exception('Error: user not found.', 404);
		}
		else
		{
			$this->_userCode = 'feedbee';
			$this->_userId = 1;
		}
		
		$view = Lpf_Dispatcher::getView();
		
		$view->getHelper('menuView')->setElements(array(
			'/users' => array('url' => './users/', 'text' => 'Пользователи'),
			'/register' => array('url' => './register/', 'text' => 'Регистрация'),
			'/source' => array('url' => './source/', 'text' => 'Исходные коды'),
			'_ext' => array('url' => 'http://habrahabr.ru/post/141049/', 'text' => 'Виджет Mac OS X', 'external' => true)
		));
	}
	
	public function allUsersAction()
	{
		$itemsPerPage = 30;

		// Defaults
		$filter = null;
		$orderField = 'user_id';
		$orderDirection = 'ASC';
		$userRequestedOrder = null;
		$page = 1;

		// User Input
		if (isset($_REQUEST['page']))
		{
			if (ctype_digit($_REQUEST['page']) && $_REQUEST['page'] > 0)
			{
				$page = $_REQUEST['page'];
			}
		}
		if (isset($_REQUEST['filter']) && $_REQUEST['filter'] !== '')
		{
			$filter = $_REQUEST['filter'];
		}
		if (isset($_REQUEST['order']))
		{
			$parts = explode('.', $_REQUEST['order']);
			if (in_array(count($parts), array(1, 2)))
			{
				if (in_array(strtolower($parts[0]), array('name', 'regtime')))
				{
					$orderField = ($parts[0] == 'name' ? 'user_code' : 'user_id');
					$userRequestedOrder = $parts[0];

					if (isset($parts[1]) && in_array(strtolower($parts[1]), array('asc', 'desc')))
					{
						$orderDirection = strtoupper($parts[1]);
						$userRequestedOrder .= ".{$parts[1]}";
					}
				}
			}
		}

		$from = ($page - 1) * $itemsPerPage;
		$filterArray = is_null($filter) ? array() : array(array('user_code', $filter));
		$result = Habrometr_Model::getInstance()->getUserList($filterArray, $orderField, $orderDirection, $from, $itemsPerPage);
		$userList = $result['list'];
		$overalCount = $result['overal_count'];
		$overalPages = ceil($overalCount / $itemsPerPage);

		if ($overalPages > 0 && $overalPages < $page)
		{
			throw new Exception('Error: page not found.', 404);
		}

		$view = Lpf_Dispatcher::getView();
		$view->userList = $userList;
		$view->usersOveral = $overalCount;
		$view->page = $page;
		$view->order = array('field' => $orderField, 'direction' => $orderDirection);
		$view->requestedOrder = $userRequestedOrder;
		$view->filter = $filter;
		$view->overalPages = $overalPages;

		$view->userListPathBuilder = function ($order = null, $page = null, $filter = null)
		{
			$parts = array();
			if (!is_null($order))
			{
				$parts[] = "order-by-{$order}";
			}
			if (!is_null($page))
			{
				$parts[] = "page-{$page}";
			}

			$string = '';
			if (count($parts) > 0)
			{
				$string = implode('/', $parts) . '/';
			}

			if (!is_null($filter))
			{
				$string .= "?filter=$filter";
			}

			return $string;
		};
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
				$errors[] = 'Хабралогин пользователя должен состоять из символов латинского алфавита, цифр и символов "-", "_".';
			}
			if (isset($_GET['user_email']) && $_GET['user_email'] !== '')
			{
				$user_email = trim($_GET['user_email']);
				if (!preg_match("/[0-9A-Za-z_\.]+@[0-9A-Za-z_^\.-]+\.[a-z]{2,4}/i", $user_email))
				{
					$errors[] = 'E-mail пользователя должен соответствовать шаблону "user@host.zone".';
				}
			}
			if (!$errors)
			{
				try
				{
					$habravaluesFromXML = Habrometr_Model::getInstance()->getRemoteValues($user_code);
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
				if (!is_null(Habrometr_Model::getInstance()->code2UserId($user_code)))
				{
					$errors[] = "Вы (<a href=\"./?action=user_page&user={$user_code}\">{$user_code}</a>) уже зарегистрированы в системе. Повторная регистрация невозможна.";
				}
			}
			
			// Ошибок нет, надо регить юезра
			if (!$errors)
			{
				if (false != ($userId = Habrometr_Model::getInstance()
					->addUser(array('user_code' => $user_code, 'user_email' => $user_email))))
				{
					$ok = true;
					try
					{
						Habrometr_Model::getInstance()->pushValues($userId, $habravaluesFromXML);
					}
					catch (Exception $e)
					{}
					$m = new Lpf_Memcache('habrometr');
					$m->delete('/users/');
				}
				else
				{
					$errors[] = "Ошибка регистрации";
				}
			}
		}
		
		$view = Lpf_Dispatcher::getView();
		$user = Habrometr_Model::getInstance()->getUser($this->_userId);
		$view->userCode = $user_code;
		$view->userEmail = $user_email;
		$view->errors = $errors;
		$view->ok = $ok;
	}
	
	public function userPageAction()
	{
		$view = Lpf_Dispatcher::getView();
		$view->userData = Habrometr_Model::getInstance()->getUser($this->_userId);
		$view->current = Habrometr_Model::getInstance()->getValues($this->_userId);
		$view->history = Habrometr_Model::getInstance()->getHistoryGrouped($this->_userId, 90);
	}
	
	public function getAction()
	{
		$view = Lpf_Dispatcher::getView();
		$view->sizes = array(
			array('x' => 425, 'y' => 120),
			array('x' => 88,  'y' => 120),
			array('x' => 88,  'y' => 15),
			array('x' => 88,  'y' => 31),
			array('x' => 31,  'y' => 31),
			array('x' => 350,  'y' => 20)
		);
		$user = Habrometr_Model::getInstance()->getUser($this->_userId);
		$view->userCode = $user['user_code'];
	}
	
	public function sourceAction()
	{}
	
	public function defaultAction()
	{
		Lpf_Dispatcher::getView()->version = Habrometr_Model::VERSION;
	}
}