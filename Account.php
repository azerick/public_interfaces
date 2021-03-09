<?php

class NotFoundException extends Exception {}

class Account{

	// Constants
	private const ACCOUNTS_DEFAULT_FILE = "reset_accounts.txt";
	private const ACCOUNTS_DATA_FILE = "accounts.txt";

	// Properties
    private $id;		// Account number
    private $balance;	// Current balance

	// Constructor
	function __construct($account_id) {
		$accounts = Account::fetchAccount($account_id);
		if (array_key_exists($account_id, $accounts)) {
			$this->id = $account_id;
			$this->balance = intval($accounts[$account_id]);
		} else {
			throw new NotFoundException();
		}
	}

	// Functions
	public static function createAccount($account_id, $balance) {
		$data = $account_id . " " . $balance . PHP_EOL;
		file_put_contents(Account::ACCOUNTS_DATA_FILE, $data , FILE_APPEND | LOCK_EX);
		return new Account($account_id);
	}

	public function getBalance() {
		return $this->balance;
	}

	public function deposit ($amount) {
		$this->balance += $amount;
		$this->updateAccountList($this->id, $this->balance);
	}
	
	public function withdraw ($amount) {
		$this->balance -= $amount;
		$this->updateAccountList($this->id, $this->balance);
	}
	
	private function updateAccountList($account_id, $balance) {
		$accountsList = array();
		foreach (file(Account::ACCOUNTS_DATA_FILE) as $line)
		{
			list($key, $value) = explode(' ', $line, 2);
			$accountsList[$key] = trim($value, "\n\r");
		}
		$accountsList[$key] = $balance;
		$fileHandle = fopen(Account::ACCOUNTS_DATA_FILE, "w+");
 		foreach ($accountsList as $key => $value) {
			fwrite($fileHandle, $key . " " . $value . PHP_EOL);
		}
		fclose($fileHandle);
	}

	// Static Functions
	private static function loadAccountList() {
		$accountsList = array();
		foreach (file(Account::ACCOUNTS_DATA_FILE) as $line)
		{
			list($key, $value) = explode(' ', $line, 2) + array(NULL, NULL);
			$accountsList[$key] = trim($value, "\n\r");
		}
		return $accountsList;
	}
	
	private static function fetchAccount($account_id) {
		$account = array();
		foreach (file(Account::ACCOUNTS_DATA_FILE) as $line)
		{
			list($key, $value) = explode(' ', $line, 2);
			if ($key == $account_id) {
				$account[$key] = trim($value, "\n\r");
			}
		}
		return $account;
	}

	public static function resetAccountList() {
		copy(Account::ACCOUNTS_DEFAULT_FILE, Account::ACCOUNTS_DATA_FILE);
	}
}
?>