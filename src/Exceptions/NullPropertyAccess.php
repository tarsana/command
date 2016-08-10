<?php namespace Tarsana\Command\Exceptions;

/**
 * This exception is thrown when trying to access
 * a null property of some object. Generally trying
 * to acess a property before initializing it.
 */
class NullPropertyAccess extends \Exception {}
