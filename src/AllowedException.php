<?php namespace Dtkahl\AccessControl;

class AllowedException extends \Exception
{
    /**
     * No reason to start sweating honey :)
     *
     * If this "exception" is thrown everything is alright. The Judge catches it, so it should never get outside.
     *
     * I know this is sort of an anti pattern but it seams to be the best solution for this specific use case.
     * Lets call it a "Sucception" - credits to Michael Betka for this name ;)
     */
}