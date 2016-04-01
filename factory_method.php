<?php

abstract class ApptEncoder {
	abstract function encode();
}

class BloggsApptEncoder extends ApptEncoder {

	function encode() {
		return "Appointment data encode in BloggsCal format\n";
	}

}

abstract class CommsManager {
	abstract function getHeaderText();
	// this is the factory method. The code does not know what concrete class
	// it will get until run time.
	abstract function getApptEncoder();
	abstract function getFooterText();
}

class BloggsCommsManager extends CommsManager {

	function getHeaderText() {
		return "BloggsCal header\n";
	}

	// this is the factory method implementation.
	function getApptEncoder() {
		return new BloggsApptEncoder();
	}

	function getFooterText() {
		return "BloggsCal footer\n";
	}

}

$mgr = new BloggsCommsManager();

echo $mgr->getHeaderText();
echo $mgr->getApptEncoder()->encode();
echo $mgr->getFooterText();

// to extend you just need to write a new implementation of CommsManager.