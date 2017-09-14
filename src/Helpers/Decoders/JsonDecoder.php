<?php namespace Tarsana\Command\Helpers\Decoders;

use Tarsana\Command\Interfaces\Helpers\DecoderInterface;


class JsonDecoder implements DecoderInterface {

    public function decode(string $text) : array
    {
        return json_decode($text, true);
    }

}
