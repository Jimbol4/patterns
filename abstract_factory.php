<?php

// this is the abstract factory, comprised of 3 abstract factory methods
abstract class CommsManager {
    abstract function getHeaderText();
    abstract function getApptEncoder();
    abstract function getTtdEncoder();
    abstract function getContactEncoder();
    abstract function getFooterText();

    /*
     * optionally have a make() method that takes care of object creation based on criteria in a config file
     * or something else
     */


}

class BloggsCommsManager extends CommsManager {

    function getHeaderText() {
        return "BloggsCal header\n";
    }

    function getApptEncoder() {
        return new BloggsApptEncoder();
    }

    function getTtdEncoder() {
        return new BloggsTtdEncoder();
    }

    function getContactEncoder() {
        return new BloggsContactEncoder();
    }

    function getFooterText() {
        return "BloggsCal footer\n";
    }

}

class MegaCommsManager extends CommsManager {

    function getHeaderText() {
        return "Mega header\n";
    }

    function getApptEncoder() {
        return new MegaApptEncoder();
    }

    function getTtdEncoder() {
        return new MegaTtdEncoder();
    }

    function getContactEncoder() {
        return new MegaContactEncoder();
    }

    function getFooterText() {
        return "Mega footer\n";
    }

}