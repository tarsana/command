<?php namespace Tarsana\Command\Interfaces\Helpers;


interface DecoderInterface {

    public function decode(string $text) : array;

}
