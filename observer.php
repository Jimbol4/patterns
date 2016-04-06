<?php
// in the observer pattern you attach/detach observers to an Observable object.
// the observable object then calls notify() whenever something of interest happens,
// and the code then loops through each attached observer and calls update() on each one.


interface Observable {
	function attach( Observer $observer );
	function detach( Observer $observer );
	function notify();
}

class Login implements Observable {
	private $observers=array();
	private $storage;
	const LOGIN_USER_UNKNOWN = 1;
	const LOGIN_WRONG_PASS = 2;
	const LOGIN_ACCESS = 3;

	function attach( Observer $observer ) {
		$this->observers[] = $observer;
	}

	function detach( Observer $observer ) {
		$this->observers = array_filter( $this->observers,
		function( $a ) use ( $observer ) { return (! ($a === $observer )); });
	}

	function notify() {
		foreach ( $this->observers as $obs ) {
		$obs->update( $this );
	}
}

	function handleLogin( $user, $pass, $ip ) {
		switch ( rand(1,3) ) {
		case 1:
			$this->setStatus( self::LOGIN_ACCESS, $user, $ip );
			$isvalid = true; 
			break;
		case 2:
			$this->setStatus( self::LOGIN_WRONG_PASS, $user, $ip );
			$isvalid = false; 
			break;
		case 3:
			$this->setStatus( self::LOGIN_USER_UNKNOWN, $user, $ip );
			$isvalid = false; 
			break;
		}

		$this->notify();
		return $isvalid;

	}

}

interface Observer {
	function update( Observable $observable );
}

class SecurityMonitor implements Observer {
	
	function update( Observable $observable ) {
		$status = $observable->getStatus();
		if ( $status[0] == Login::LOGIN_WRONG_PASS ) {
			// send mail to sysadmin
			print __CLASS__.":\tsending mail to sysadmin\n";
		}
	}

}

// client code
$login = new Login();
$login->attach( new SecurityMonitor() );


/* example 2 using SPL */

class Login implements SplSubject {
    private $storage;
    const LOGIN_USER_UNKNOWN = 1;
    const LOGIN_WRONG_PASS   = 2;
    const LOGIN_ACCESS       = 3;

    function __construct() {
        $this->storage = new SplObjectStorage();
    }
    function attach( SplObserver $observer ) {
        $this->storage->attach( $observer );
    }

    function detach( SplObserver $observer ) {
        $this->storage->detach( $observer );
    }

    function notify() {
        foreach ( $this->storage as $obs ) {
            $obs->update( $this );
        }
    }

    function handleLogin( $user, $pass, $ip ) {
        switch ( rand(1,3) ) {
            case 1: 
                $this->setStatus( self::LOGIN_ACCESS, $user, $ip );
                $isvalid = true; break;
            case 2:
                $this->setStatus( self::LOGIN_WRONG_PASS, $user, $ip );
                $isvalid = false; break;
            case 3:
                $this->setStatus( self::LOGIN_USER_UNKNOWN, $user, $ip );
                $isvalid = false; break;
        }
        $this->notify();
        return $isvalid;
    }

    private function setStatus( $status, $user, $ip ) {
        $this->status = array( $status, $user, $ip ); 
    }

    function getStatus() {
        return $this->status;
    }

}

abstract class LoginObserver implements SplObserver {
    private $login;
    function __construct( Login $login ) {
        $this->login = $login; 
        $login->attach( $this );
    }

    function update( SplSubject $subject ) {
        if ( $subject === $this->login ) {
            $this->doUpdate( $subject );
        }
    }

    abstract function doUpdate( Login $login );
} 

class SecurityMonitor extends LoginObserver {
    function doUpdate( Login $login ) {
        $status = $login->getStatus(); 
        if ( $status[0] == Login::LOGIN_WRONG_PASS ) {
            // send mail to sysadmin 
            print __CLASS__.":\tsending mail to sysadmin\n"; 
        }
    }
}

class GeneralLogger  extends LoginObserver {
    function doUpdate( Login $login ) {
        $status = $login->getStatus(); 
        // add login data to log
        print __CLASS__.":\tadd login data to log\n"; 
    }
}

class PartnershipTool extends LoginObserver {
    function doUpdate( Login $login ) {
        $status = $login->getStatus(); 
        // check $ip address 
        // set cookie if it matches a list
        print __CLASS__.":\tset cookie if it matches a list\n"; 
    }
}

$login = new Login();
new SecurityMonitor( $login );
new GeneralLogger( $login );
$pt = new PartnershipTool( $login );
$login->detach( $pt );
for ( $x=0; $x<10; $x++ ) {
    $login->handleLogin( "bob","mypass", '158.152.55.35' );
    print "\n";
}


/* example 3 */

abstract class AbstractObserver {
    abstract function update(AbstractSubject $subject_in);
}

abstract class AbstractSubject {
    abstract function attach(AbstractObserver $observer_in);
    abstract function detach(AbstractObserver $observer_in);
    abstract function notify();
}

function writeln($line_in) {
    echo $line_in."<br/>";
}

class PatternObserver extends AbstractObserver {
    public function __construct() {
    }
    public function update(AbstractSubject $subject) {
      writeln('*IN PATTERN OBSERVER - NEW PATTERN GOSSIP ALERT*');
      writeln(' new favorite patterns: '.$subject->getFavorites());
      writeln('*IN PATTERN OBSERVER - PATTERN GOSSIP ALERT OVER*');      
    }
}

class PatternSubject extends AbstractSubject {
    private $favoritePatterns = NULL;
    private $observers = array();
    function __construct() {
    }
    function attach(AbstractObserver $observer_in) {
      //could also use array_push($this->observers, $observer_in);
      $this->observers[] = $observer_in;
    }
    function detach(AbstractObserver $observer_in) {
      //$key = array_search($observer_in, $this->observers);
      foreach($this->observers as $okey => $oval) {
        if ($oval == $observer_in) { 
          unset($this->observers[$okey]);
        }
      }
    }
    function notify() {
      foreach($this->observers as $obs) {
        $obs->update($this);
      }
    }
    function updateFavorites($newFavorites) {
      $this->favorites = $newFavorites;
      $this->notify();
    }
    function getFavorites() {
      return $this->favorites;
    }
}

  writeln('BEGIN TESTING OBSERVER PATTERN');
  writeln('');

  $patternGossiper = new PatternSubject();
  $patternGossipFan = new PatternObserver();
  $patternGossiper->attach($patternGossipFan);
  $patternGossiper->updateFavorites('abstract factory, decorator, visitor');
  $patternGossiper->updateFavorites('abstract factory, observer, decorator');
  $patternGossiper->detach($patternGossipFan);
  $patternGossiper->updateFavorites('abstract factory, observer, paisley');

  writeln('END TESTING OBSERVER PATTERN');


?>



