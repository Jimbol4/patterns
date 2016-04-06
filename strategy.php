<?php
/*
example 1
*/

// question classes
abstract class Question {
	protected $prompt;
	protected $marker;
	
	function __construct( $prompt, Marker $marker ) {
		$this->marker=$marker;
		$this->prompt = $prompt;
	}
	
	function mark( $response ) {
		return $this->marker->mark( $response );
	}

}


class TextQuestion extends Question {
// do text question specific things
}
class AVQuestion extends Question {
// do audiovisual question specific things
}


// marker classes
abstract class Marker {
	protected $test;

	function __construct( $test ) {
	$this->test = $test;
	}
	
	// all the client code needs to know is that it will call
	// a mark() method on an object of Marker type. It doesn't know
	// what exactly will be called here, nor the exact logic.

	// this allows us flexibility in terms of lots of different 
	// types of questions/marking procedures but the same interface.
	// we can switch out what classes are actually calling mark() at run
	// time and not need to change much code.
	abstract function mark( $response );
	
}

class MarkLogicMarker extends Marker {
	private $engine;
	function __construct( $test ) {
		parent::__construct( $test );
		// $this->engine = new MarkParse( $test );
	}
	function mark( $response ) {
		// return $this->engine->evaluate( $response );
		// dummy return value
		return true;
	}
}

class MatchMarker extends Marker {
	function mark( $response ) {
		return ( $this->test == $response );
	}
}


class RegexpMarker extends Marker {
	function mark( $response ) {
		return ( preg_match( $this->test, $response ) );
	}
}

/*

example 2

*/

// output interface and some implementations
interface OutputInterface
{
    public function load();
}

class SerializedArrayOutput implements OutputInterface
{
    public function load()
    {
        return serialize($arrayOfData);
    }
}

class JsonStringOutput implements OutputInterface
{
    public function load()
    {
        return json_encode($arrayOfData);
    }
}

class ArrayOutput implements OutputInterface
{
    public function load()
    {
        return $arrayOfData;
    }
}



class SomeClient
{
    private $output;

    public function setOutput(OutputInterface $outputType)
    {
        $this->output = $outputType;
    }

    public function loadOutput()
    {
        return $this->output->load();
    }
}



// client code
$client = new SomeClient();

// Want an array?
$client->setOutput(new ArrayOutput());
$data = $client->loadOutput();

// Want some JSON?
$client->setOutput(new JsonStringOutput());
$data = $client->loadOutput();