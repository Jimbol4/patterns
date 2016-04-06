<?php

/*
 * example 1
 */
abstract class Tile {
    abstract function getWealthFactor();
}

class Plains extends Tile {
    private $wealthfactor = 2;
    function getWealthFactor() {
        return $this->wealthfactor;
    }
}

abstract class TileDecorator extends Tile {
    protected $tile;
    function __construct( Tile $tile ) {
        $this->tile = $tile;
    }
}

class DiamondDecorator extends TileDecorator {
    function getWealthFactor() {
        return $this->tile->getWealthFactor()+2;
    }
}

class PollutionDecorator extends TileDecorator {
    function getWealthFactor() {
        return $this->tile->getWealthFactor()-4;
    }
}

$tile = new Plains();
print $tile->getWealthFactor(); // 2

$tile = new DiamondDecorator( new Plains() );
print $tile->getWealthFactor(); // 4

$tile = new PollutionDecorator(
    new DiamondDecorator( new Plains() ));
print $tile->getWealthFactor(); // 0


/*
 * example 2
 */
class RequestHelper{}

abstract class ProcessRequest {
    abstract function process( RequestHelper $req );
}

class MainProcess extends ProcessRequest {
    function process( RequestHelper $req ) {
        print __CLASS__.": doing something useful with request\n";
    }
}

abstract class DecorateProcess extends ProcessRequest {
    protected $processrequest;
    function __construct( ProcessRequest $pr ) {
        $this->processrequest = $pr;
    }
}

class LogRequest extends DecorateProcess {
    function process( RequestHelper $req ) {
        print __CLASS__.": logging request\n";
        $this->processrequest->process( $req );
    }
}

class AuthenticateRequest extends DecorateProcess {
    function process( RequestHelper $req ) {
        print __CLASS__.": authenticating request\n";
        $this->processrequest->process( $req );
    }
}

class StructureRequest extends DecorateProcess {
    function process( RequestHelper $req ) {
        print __CLASS__.": structuring request data\n";
        $this->processrequest->process( $req );
    }
}

$process = new AuthenticateRequest( new StructureRequest(
    new LogRequest (
        new MainProcess()
    )));
$process->process( new RequestHelper() );

/*
example 3
*/

// classes/interfaces

// interface ensures that everything that implements it will have a loadBody() method.
interface eMailBody {
    public function loadBody();
}
 
// main eMail class. This is the class that will be 'decorated' or added to without changing it
class eMail implements eMailBody {
    public function loadBody() {
        echo "This is Main Email body.<br />";
    } 
}
 

// abstract decorator class. 
abstract class emailBodyDecorator implements eMailBody {
    
    // accepts a class that implements eMailBody in the constructor.
    // this guarantees that it will have access to loadBody().
    // you can then execute any additional logic, and call loadBody() to pass
    // it down the chain.    
    protected $emailBody;
     
    public function __construct(eMailBody $emailBody) {
        $this->emailBody = $emailBody;
    }
     
    abstract public function loadBody();
     
} 
 
class christmasEmailBody extends emailBodyDecorator {
     
    public function loadBody() {
         
        echo 'This is Extra Content for Christmas<br />';
        $this->emailBody->loadBody();
         
    }
     
}
 
class newYearEmailBody extends emailBodyDecorator {
 
    public function loadBody() {
         
        echo 'This is Extra Content for New Year.<br />';
        $this->emailBody->loadBody();
         
    }
 
}

// client code

/*
 *  Normal Email
 */
 
$email = new eMail();
$email->loadBody();
 
// Output
//This is Main Email body.
 
 
/*
 *  Email with Xmas Greetings
 */
 
$email = new eMail();
$email = new christmasEmailBody($email);
$email->loadBody();
 
// Output
//This is Extra Content for Christmas
//This is Main Email body.
 
/*
 *  Email with New Year Greetings
 */
 
$email = new eMail();
$email = new newYearEmailBody($email);
$email->loadBody();
 
 
// Output
//This is Extra Content for New Year.
//This is Main Email body.
 
/*
 *  Email with Xmas and New Year Greetings
 */
 
$email = new eMail();
$email = new christmasEmailBody($email);
$email = new newYearEmailBody($email);
$email->loadBody();
 
// Output
//This is Extra Content for New Year.
//This is Extra Content for Christmas
//This is Main Email body.