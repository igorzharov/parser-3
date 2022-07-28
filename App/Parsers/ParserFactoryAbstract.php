<?php

//declare(strict_types=1);

namespace App\Parsers;

abstract class ParserFactoryAbstract {

    public function create(string $parserName): Parser {

        return new ('App\Parsers\\' . $parserName);
    }

}