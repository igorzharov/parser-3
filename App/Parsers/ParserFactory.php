<?php

declare(strict_types=1);

namespace App\Parsers;

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