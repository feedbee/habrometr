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
 * @version 1.0.0
 */
class Habrometr_Model
{
	const VERSION_FULL = '1.0.0';
	const VERSION = '1.0';

	/**
	 * Reference
	 *
	 * @var Habrometr
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
	 * @param int $count
	 * @return array
	 */
	private function _getUserLog($userId = 1, $count = 1)
	{
		$sth = $this->_pdo->prepare("SELECT karma_value, habraforce, rate_position, log_time as log_time
								FROM `karmalog` where user_id = :uid order by log_time DESC limit :limit");
		$sth->bindValue(':limit', $count, PDO::PARAM_INT);
		$sth->bindValue(':uid', $userId, PDO::PARAM_INT);
		if (!$sth->execute())
		{
			throw new Exception('User addition — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
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
	 * Query to DB to get Habravalues groupped by days.
	 * Returns array of values array or null in case of failure.
	 *
	 * @param int $userId
	 * @param int $count
	 * @return array
	 */
	public function getHistoryGrouped($userId = 1, $count = 1)
	{
		$sth = $this->_pdo->prepare("SELECT avg(karma_value) as karma_value, avg(habraforce) as habraforce, avg(rate_position) as rate_position, DATE(log_time) as `date`
								FROM `karmalog` where user_id = :uid group by `date` order by `date` DESC LIMIT :limit");
		$sth->bindValue(':limit', $count, PDO::PARAM_INT);
		$sth->bindValue(':uid', $userId, PDO::PARAM_INT);
		if (!$sth->execute())
		{
			throw new Exception('User addition — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
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
		$sth = $this->_pdo->prepare("SELECT max(karma_value) as karma_max, min(karma_value) as karma_min,
								max(habraforce) as habraforce_max, min(habraforce) as habraforce_min,
								max(rate_position) as rate_max, min(rate_position) as rate_min
								FROM `karmalog` WHERE user_id = :uid");
		$sth->bindValue(':uid', $userId, PDO::PARAM_INT);
		if (!$sth->execute())
		{
			throw new Exception('User addition — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
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
	 * Put new Habravalues values to DB log by user id.
	 *
	 * @param array $user
	 * @param array $values
	 */
	public function putValues($user)
	{
		$userCode = $user['user_code'];
		$userId = $user['user_id'];
		
		$values = $this->parsePage($userCode);

		return $this->putValuesFromArray($userId, $values);
	}
	
	public function putValuesFromArray($userId, $values)
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
			$sth->bindValue(':uid', $values['user_id']);
			$sth->bindValue(':k', $values['karma_value']);
			$sth->bindValue(':hf', $values['habraforce']);
			$sth->bindValue(':r', $values['rate_position']);
			if (!$sth->execute())
			{
				throw new Exception('User addition — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
			}
		}
		catch (Exception $e)
		{
			throw new Exception('Saving data — DB query failed: ' . $e->getMessage(), 202);
		}

		return true;
	}

	/**
	 * Get user data from habrahabr, parse Habravalues and return it in array.
	 *
	 * @param int $userCode
	 */
	public function parsePage($userCode = 1)
	{
		$ch = curl_init("http://habrahabr.ru/api/profile/{$userCode}/");
		$options = array(
			CURLOPT_RETURNTRANSFER => true,     // return web page
			CURLOPT_HEADER         => false,    // don't return headers
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_ENCODING       => "",       // handle all encodings
			CURLOPT_USERAGENT      => sprintf("PHP/%s (Habrometr/%s; feedbee@gmail.com; http://habrometr.ru/)", PHP_VERSION, self::VERSION),
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			CURLOPT_TIMEOUT        => 120,      // timeout on response
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
		);
		curl_setopt_array($ch, $options);
		$cont = curl_exec($ch);
		
		// UTF-8!!!
		if (!$cont)
		{
			throw new Exception("Downloading http://habrahabr.ru/api/profile/{$userCode}/ failed: " . curl_errno($ch) . ' ' . curl_error($ch), 203);
		}
			
		curl_close($ch);

		try
		{
			$xml = new SimpleXMLElement($cont);
			
			if (false != ($e = $xml->xpath('/habrauser/error')))
			{
				throw new Exception('Error value received: ' . $e, 205);
			}
			
			$data = array();
			$data['karma']['value'] = $xml->karma;
			$data['habraforce']['value'] = $xml->rating;
			$data['rate']['value'] = $xml->ratingPosition;
		}
		catch (Exception $e)
		{
			throw new Exception("Parsing Habravalues failure: " . $e->getMessage(), $e->getCode() > 0 ? $e->getCode() : 204);
		}

		return $data;
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
		$sth->bindValue(':uid', $userId, PDO::PARAM_INT);
		if (!$sth->execute())
		{
			throw new Exception('User addition — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
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
	 * Get registed user list from DB.
	 * 
	 * Default values mean all users without any order.
	 * 
	 * Returns two demensional array of user information.
	 *
	 * @param string $orderField user_id, user_code or user_email
	 * @param string $orderType asc/desc
	 * @param int $from LIMIT __
	 * @param int $count LIMIT xx, __
	 * @return array
	 */
	public function getUserList($orderField = null, $orderType = null, $from = null, $count = null)
	{
		$sql = 'SELECT user_id, user_code, user_email FROM `users`';
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
				throw new Exception('Habrometr_Model::getUserList: from field must be an integer geather than or equal 0', 210);
			}
			$sql .= ' LIMIT ' . $from;
		}
		if (!is_null($from) && !is_null($count))
		{
			if (!ctype_digit((string)$from) || $count <= 0)
			{
				throw new Exception('Habrometr_Model::getUserList: count field must be an integer geather than 0', 210);
			}
			$sql .= ', ' . $count;
		}
		
		$users = array();
		foreach($this->_pdo->query($sql, PDO::FETCH_ASSOC) as $user)
		{
			$users[] = $user;
		}
		
		if ($users)
		{
			return $users;
		}
		else
		{
			return null;
		}
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
			is_null($values['user_email']) && $values['user_email'] = 'NULL'; // http://us2.php.net/manual/en/pdostatement.bindvalue.php#90625
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

		return $this->_pdo->lastInsertId();
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
			$sth->bindValue(':uid', $userId, PDO::PARAM_INT);
			$res = $sth->execute();
		}
		catch (Exception $e)
		{
			throw new Exception('User deletation — DB query failed: ' . $e->getMessage(), 206);
		}
		
		if (!$res)
		{
			throw new Exception('User addition — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
		}

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
			throw new Exception('User addition — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
		}

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
			$sth->bindValue(':uid', $userId, PDO::PARAM_INT);
			if (!$sth->execute())
			{
				throw new Exception('User addition — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
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
			$sth->bindValue(':ucode', $code, PDO::PARAM_STR);
			if (!$sth->execute())
			{
				throw new Exception('User addition — DB query failed: ' . $this->_pdo->errorInfo(), $this->_pdo->errorCode());
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
