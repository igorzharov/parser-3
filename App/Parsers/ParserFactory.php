<?php

namespace App\Parsers;

use App\DB\DBParser;

enum ParserFactory: string
{
    case SANTEHORBITA = 'ParserSantehOrbita';

    public function create(): Parser
    {
        return match ($this) {
            self::SANTEHORBITA => new ParserSantehOrbita(),
        };
    }
}