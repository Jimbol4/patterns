<?php

class Preferences {

	private $props = [];
	private static $instance;

	private function __construct() {}

	// ensures only a single instance exists in a script.
	// you can only get access to $instance by calling 
	// getInstance(), which checks if one already exists before
	// creating/returning it.
	public static function getInstance() {
		if (empty(self::$instance)) {
			self::$instance = new Preferences();
		}
		return self::$instance;
	}

	public function setProperty($key, $val) {
		$this->props[$key] = $val;
	}

	public function getProperty($key) {
		return $this->props[$key];
	}

}

$pref = Preferences::getInstance();
$pref->setProperty("name", "jim");
unset($pref);

$pref2 = Preferences::getInstance();

echo $pref2->getProperty("name") . "\n"; // value is not lost