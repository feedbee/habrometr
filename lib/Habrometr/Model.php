<?php
/**
 *  Habrahabr.ru Habrometr.
 *  Copyright (C) 2009 Leontyev Valera
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Habrahabr.ru Habrometr.
 * http://habrometr.ru/
 *
 * @author Valera Leontyev (feedbee)
 * @link http://habrometr.ru/
 * @copyright 2009, feedbee@gmail.com.
 * @license GNU General Public License (GPL).
 * @version 2.0.0
 */
class Habrometr_Model
{
	const VERSION_FULL = '2.0.0';
	const VERSION = '2.0';

	/**
	 * Reference
	 *
	 * @var Habrometr_Model
	 */
	private static $_instance = null;
	/**
	 * PDO connection
	 *
	 * @var PDO
	 */
	private $_pdo = null;

	private static $_conversions;

	/**
	 * Constructor: Establish DB connection
	 *
	 */
	private function __construct()
	{
		$this->_connect(Config::DB_NAME, Config::DB_USER, Config::DB_PASS);
	}

	/**
	 * Singleton. Returns instance of Habrometr class
	 *
	 * @return Habrometr_Model
	 */
	public static function getInstance()
	{
		if (!self::$_instance)
		{
			self::$_instance = new Habrometr_Model();
		}

		return self::$_instance;
	}

	/**
	 * Establish DB connection
	 *
	 */
	private function _connect($dbName, $userName, $userPass)
	{
		try
		{
			$this->_pdo = new PDO('mysql:host=localhost;dbname=' . $dbName, $userName, $userPass);
			
		}
		catch (PDOException $e)
		{
			throw new Exception('DB connection failed: ' . $e->getMessage(), 201);
		}
	}

	/**
	 * Get last Habravalues from DB by user_id.
	 * Returns null on no data was found.
	 *
	 * @param int $userId
	 * @return array|null
	 */
	public function getValues($userId = 1)
	{
		if (false != ($r = $this->_getUserLog($userId, 1)))
		{
			return $r[0];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get Habravalues history ($count records) from DB by user_id.
	 * Returns null on no data was found.
	 *
	 * @param int $userId
	 * @param int $count
	 * @return array|null
	 */
	public function getHistory($userId = 1, $count = 1)
	{
		return $this->_getUserLog($userId, $count);
	}

	/**
	 * Query to DB to get Habravalues.
	 * Returns array of values array or null in case of failure.
	 *
	 * @param int $userId
	 * @param int $recordsLimit
	 * @return array
	 */
	private function _getUserLog($userId = 1, $recordsLimit = 1)
	{
		Log::debug(sprintf("Habrometr_Model: user log access for User ID `%d`", $userId));
		$sth = $this->_pdo->prepare("SELECT karma_value, habraforce, rate_position, log_time as log_time
								FROM `karmalog` where user_id = :uid order by log_time DESC limit :limit");
		/* @var PDOStatement $sth */
		$sth->bindValue(':limit', $recordsLimit, PDO::PARAM_INT);
		$sth->bindValue(':uid', $userId, PDO::PARAM_INT);
		if (!$sth->execute())
		{
			throw new Exception('getUserLog — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
		}
		$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		if ($rows)
		{
			return $rows;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Query to DB to get Habravalues grouped by days.
	 * Returns array of values array or null in case of failure.
	 *
	 * @param int $userId
	 * @param int $count
	 * @return array
	 */
	public function getHistoryGrouped($userId = 1, $count = 1)
	{
		Log::debug(sprintf("Habrometr_Model: grouped user log access for User ID `%d`", $userId));
		$sth = $this->_pdo->prepare("SELECT avg(karma_value) as karma_value, avg(habraforce) as habraforce, avg(rate_position) as rate_position, DATE(log_time) as `date`
								FROM `karmalog` where user_id = :uid group by `date` order by `date` DESC LIMIT :limit");
		/* @var PDOStatement $sth */
		$sth->bindValue(':limit', $count, PDO::PARAM_INT);
		$sth->bindValue(':uid', $userId, PDO::PARAM_INT);
		if (!$sth->execute())
		{
			throw new Exception('getHistoryGrouped — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
		}
		$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		if ($rows)
		{
			return $rows;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get minimum and maximum values of karma and habraforce from DB log by user id.
	 *
	 * @param int $userId
	 * @return array
	 */
	public function getExtremums($userId = 1)
	{
		Log::debug(sprintf("Habrometr_Model: extremums calculation for User ID `%d`", $userId));
		$sth = $this->_pdo->prepare("SELECT max(karma_value) as karma_max, min(karma_value) as karma_min,
								max(habraforce) as habraforce_max, min(habraforce) as habraforce_min,
								max(rate_position) as rate_max, min(rate_position) as rate_min
								FROM `karmalog` WHERE user_id = :uid");
		/* @var PDOStatement $sth */
		$sth->bindValue(':uid', $userId, PDO::PARAM_INT);
		if (!$sth->execute())
		{
			throw new Exception('getExtremums — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
		}
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		
		if ($row)
		{
			return $row;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Update Habravalues values in database from Habrahabr web-site.
	 *
	 * @param array $user User data array
	 */
	public function pullValues($user)
	{
		$userId = $user['user_id'];
		$userCode = $user['user_code'];

		$values = $this->getRemoteValues($userCode);
		$this->pushValues($userId, $values);
	}

	/**
	 * Save new user Habravalues to database
	 *
	 * @param $userId
	 * @param $values
	 * @throws Exception
	 */
	public function pushValues($userId, $values)
	{
		$values = array(
			'user_id' => $userId,
			'karma_value' => (float)$values['karma']['value'],
			'habraforce' => (float)$values['habraforce']['value'],
			'rate_position' => (int)$values['rate']['value']
		);
		try
		{
			$sth = $this->_pdo->prepare("INSERT `karmalog` (user_id, karma_value, habraforce, rate_position) VALUES (:uid, :k, :hf, :r)");
			/* @var PDOStatement $sth */
			$sth->bindValue(':uid', $values['user_id']);
			$sth->bindValue(':k', $values['karma_value']);
			$sth->bindValue(':hf', $values['habraforce']);
			$sth->bindValue(':r', $values['rate_position']);
			if (!$sth->execute())
			{
				throw new Exception('Update values — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
			}
		}
		catch (Exception $e)
		{
			$message = "Saving data — DB query failed: {$e->getMessage()}";
			Log::err($message);
			throw new Exception($message, 202);
		}
		Log::debug(sprintf("Habrometr_Model: values updated in database for User ID `%d`", $userId));
	}

	/**
	 * Get user data from Habrahabr web-site in array.
	 *
	 * @param string $userCode Habrahabr user code
	 * @return array
	 */
	public function getRemoteValues($userCode)
	{
		$xmlString = $this->getRemoteUserDataXml($userCode);

		try
		{
			$xmlRoot = new SimpleXMLElement($xmlString);
			
			if (false != ($e = $xmlRoot->xpath('/habrauser/error')))
			{
				throw new Exception('Error value received: ' . $e, 205);
			}
			
			$data = array();
			$data['karma']['value'] = $xmlRoot->karma;
			$data['habraforce']['value'] = $xmlRoot->rating;
			$data['rate']['value'] = $xmlRoot->ratingPosition;
		}
		catch (Exception $e)
		{
			Log::warn(sprintf("Habrometr_Model: parsing Habrahabr user XML for `%s` failed", $userCode));
			Log::warn(sprintf("Habrometr_Model: failed XML string is `%s`", $xmlString));
			throw new Exception("Parsing Habravalues failure: " . $e->getMessage(), $e->getCode() > 0 ? $e->getCode() : 204);
		}

		return $data;
	}

	private function getRemoteUserDataXml($userCode)
	{
		$agent = sprintf("PHP/%s (Habrometr/%s; feedbee@gmail.com; http://habrometr.ru/)", PHP_VERSION, self::VERSION);
		$ch = curl_init("http://habrahabr.ru/api/profile/{$userCode}/");
		$options = array(
			CURLOPT_RETURNTRANSFER => true,     // return web page
			CURLOPT_HEADER         => false,    // don't return headers
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_ENCODING       => "",       // handle all encodings
			CURLOPT_USERAGENT      => $agent,   // set user-agent header
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 30,       // timeout on connect
			CURLOPT_TIMEOUT        => 20,       // timeout on response
			CURLOPT_MAXREDIRS      => 5,       // stop after 10 redirects
		);
		curl_setopt_array($ch, $options);

		$time = microtime(true);
		$page = curl_exec($ch);

		$requestTime = (microtime(true) - $time);

		if (false === $page)
		{
			Log::warn(sprintf("Habrometr_Model: Habrahabr page loading for user `%s` failed, request time %f seconds",
				$userCode, $requestTime));
			$errorMessage = "Downloading http://habrahabr.ru/api/profile/{$userCode}/ failed: " . curl_errno($ch) . ' ' . curl_error($ch);
			curl_close($ch);
			Log::warn("Habrometr_Model: $errorMessage");
			throw new Exception($errorMessage, 203);
		}
		else
		{
			curl_close($ch);
		}

		Log::debug(sprintf("Habrometr_Model: Habrahabr page loaded for user `%s`, request time %f seconds",
			$userCode, $requestTime));

		return $page;
	}
	
	/**
	 * Get user information from DB
	 *
	 * @param int $userId
	 * @return array
	 */
	public function getUser($userId)
	{
		$sth = $this->_pdo->prepare("SELECT user_id, user_code, user_email FROM `users` WHERE user_id = :uid");
		/* @var PDOStatement $sth */
		$sth->bindValue(':uid', $userId, PDO::PARAM_INT);
		if (!$sth->execute())
		{
			throw new Exception('getUser — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
		}
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		
		if ($row)
		{
			return $row;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Get registered user list from DB.
	 * 
	 * Default values mean all users without any order.
	 * 
	 * Returns two dimensional array of user information.
	 *
	 * @param array $filter array of where conditions
	 * @param string $orderField user_id, user_code or user_email
	 * @param string $orderType asc/desc
	 * @param int $from LIMIT __
	 * @param int $count LIMIT xx, __
	 * @return array
	 */
	public function getUserList(array $filter = array(), $orderField = null,
		$orderType = null, $from = null, $count = null)
	{
		$sql = 'SELECT SQL_CALC_FOUND_ROWS user_id, user_code, user_email FROM `users`';

		$conditions = $parameters = array();
		if (count($filter) > 0)
		{
			foreach (array_values($filter) as $index => $element)
			{
				if (!is_array($element) || count($element) < 2)
				{
					throw new Exception('Habrometr_Model::getUserList: filter array elements must be array with at least 2 elements', 210);
				}
				if (!in_array($element[0], array('user_id', 'user_code', 'user_email')))
				{
					throw new Exception('Habrometr_Model::getUserList: filter array elements [0] element must be one of: user_id, user_code, user_email', 210);
				}
				$field = $element[0];
				$operator = in_array($field, array('user_code', 'user_email')) ? 'LIKE' : '=';
				$parameter = ":{$field}_$index";
				$conditions[] = "$field $operator $parameter";
				$value = str_replace(array('\\', '%', '_'), // escape value for MySQL LIKE syntax
									 array('\\\\', '\\%', '\\_'),
									 $element[1]);
				// translate * -> %, ? -> _, according to MySQL LIKE syntax
				$value = str_replace(array('*', '?'), array('%', '_'), $value);
				$parameters[$parameter] = $value;
			}

			if (count($conditions) > 0)
			{
				$sql .= "\r\nWHERE " . implode("\r\n AND ", $conditions) . "\r\n";
			}
		}
		if (!is_null($orderField))
		{
			if (!in_array($orderField, array('user_id', 'user_code', 'user_email')))
			{
				throw new Exception('Habrometr_Model::getUserList: orderField must be one of: user_id, user_code, user_email', 210);
			}
			$sql .= ' ORDER BY ' . $orderField;
		}
		if (!is_null($orderField) && !is_null($orderType) )
		{
			if (!in_array(strtoupper($orderType), array('DESC', 'ASC')))
			{
				throw new Exception('Habrometr_Model::getUserList: orderType must be DESC or ASC', 210);
			}
			$sql .= ' ' . strtoupper($orderType);
		}
		if (!is_null($from))
		{
			if (!ctype_digit((string)$from) || $from < 0)
			{
				throw new Exception('Habrometr_Model::getUserList: from field must be an integer greater than or equal 0', 210);
			}
			$sql .= ' LIMIT ' . $from;
		}
		if (!is_null($from) && !is_null($count))
		{
			if (!ctype_digit((string)$from) || $count <= 0)
			{
				throw new Exception('Habrometr_Model::getUserList: count field must be an integer greater than 0', 210);
			}
			$sql .= ', ' . $count;
		}

		$sth = $this->_pdo->prepare($sql);
		/* @var PDOStatement $sth */
		foreach ($parameters as $parameter => $value)
		{
			$sth->bindParam($parameter, $value);
		}

		if (!$sth->execute())
		{
			throw new Exception('Habrometr_Model::getUserList — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
		}
		
		$users = array();
		while(false !== ($user = $sth->fetch(PDO::FETCH_ASSOC)))
		{
			$users[] = $user;
		}

		$stmt = $this->_pdo->query('SELECT FOUND_ROWS()', PDO::FETCH_COLUMN, 0);
		/* @var PDOStatement $stmt */
		$overalCount = $stmt->fetch();

		return array('list' => $users, 'overal_count' => $overalCount);
	}
	
	/**
	 * Register new user.
	 * 
	 * Returns new user ID.
	 *
	 * @param array $userData
	 * @return int
	 */
	public function addUser(array $userData)
	{
		$values = array(
			'user_code' => $userData['user_code'],
			'user_email' => $userData['user_email']
		);
		$res = false;
		try
		{
			$sth = $this->_pdo->prepare("INSERT `users` (user_code, user_email) VALUES (:user_code, :user_email)");
			/* @var PDOStatement $sth */
			$res = $sth->execute($values);
		}
		catch (Exception $e)
		{
			throw new Exception('User addition — DB query failed: ' . $e->getMessage(), 206);
		}
		
		if (!$res)
		{
			throw new Exception('User addition — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
		}
		$newUserId = $this->_pdo->lastInsertId();

		$creatorIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '[undefined]';
		Log::debug(sprintf("Habrometr_Model: new user created with ID `%d` (creator IP is %s)", $newUserId, $creatorIp));

		return $newUserId;
	}
	
	/**
	 * Delete existed user from registry.
	 *
	 * Returns true on success.
	 * 
	 * @param int $userId
	 * @return bool
	 */
	public function deleteUser($userId)
	{
		$res = false;
		try
		{
			$sth = $this->_pdo->prepare("DELETE from `users` WHERE user_id = :uid");
			/* @var PDOStatement $sth */
			$sth->bindValue(':uid', $userId, PDO::PARAM_INT);
			$res = $sth->execute();
		}
		catch (Exception $e)
		{
			throw new Exception('User deletion — DB query failed: ' . $e->getMessage(), 206);
		}
		
		if (!$res)
		{
			throw new Exception('User deletion — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
		}

		Log::debug(sprintf("Habrometr_Model: user ID `%d` deleted", $userId));

		return true;
	}
	
	/**
	 * Update existed user information.
	 * 
	 * Returns true on success.
	 *
	 * @param array $userData array('user_id'=>, 'user_code'=>, 'user_email'=>)
	 * @return bool
	 */
	public function updateUser(array $userData)
	{
		$res = false;
		try
		{
			$sth = $this->_pdo->prepare("UPDATE `users` SET user_code = :code, user_email = :email WHERE user_id = :uid");
			/* @var PDOStatement $sth */
			$sth->bindValue(':uid', $userData['user_id'], PDO::PARAM_INT);
			$sth->bindValue(':code', $userData['user_code'], PDO::PARAM_INT);
			$sth->bindValue(':email', $userData['user_email'], PDO::PARAM_INT);
			$res = $sth->execute();
		}
		catch (Exception $e)
		{
			throw new Exception('User update — DB query failed: ' . $e->getMessage(), 207);
		}
		
		if (!$res)
		{
			throw new Exception('User update — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
		}

		Log::debug(sprintf("Habrometr_Model: user ID `%d` updated", $userData['user_id']));

		return true;
	}
	
	/**
	 * Returns user id by his code (habrahabr nickname).
	 *
	 * @param int $userId
	 * @return string
	 */
	public function userId2Code($userId)
	{
		if (is_array(self::$_conversions)
			&& isset(self::$_conversions['id2code'])
			&& isset(self::$_conversions['id2code'][$userId]))
		{
			return self::$_conversions['id2code'][$userId];
		}
		try
		{
			$sth = $this->_pdo->prepare("SELECT user_code FROM `users` where user_id = :uid");
			/* @var PDOStatement $sth */
			$sth->bindValue(':uid', $userId, PDO::PARAM_INT);
			if (!$sth->execute())
			{
				throw new Exception('userId2Code — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
			}
			$row = $sth->fetch(PDO::FETCH_ASSOC);
		}
		catch (Exception $e)
		{
			throw new Exception("Getting user code by id failure: " . $e->getMessage(), 208);
		}

		if ($row)
		{
			self::$_conversions['id2code'][$userId] = $row['user_code'];
			return $row['user_code'];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Returns user code (habrahabr nickname) by his id.
	 *
	 * @param string $code
	 * @return int
	 */
	public function code2UserId($code)
	{
		if (is_array(self::$_conversions)
			&& isset(self::$_conversions['code2id'])
			&& isset(self::$_conversions['code2id'][$code]))
		{
			return self::$_conversions['code2id'][$code];
		}

		try
		{
			$sth = $this->_pdo->prepare("SELECT user_id FROM `users` where user_code = :ucode");
			/* @var PDOStatement $sth */
			$sth->bindValue(':ucode', $code, PDO::PARAM_STR);
			if (!$sth->execute())
			{
				throw new Exception('code2UserId — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
			}
			$row = $sth->fetch(PDO::FETCH_ASSOC);
		}
		catch (Exception $e)
		{
			throw new Exception("Getting user by code failure: " . $e->getMessage(), 209);
		}

		if ($row)
		{
			self::$_conversions['code2id'][$code] = $row['user_id'];
			return $row['user_id'];
		}
		else
		{
			return null;
		}
	}
}
