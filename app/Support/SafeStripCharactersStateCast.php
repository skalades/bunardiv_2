<?php

namespace App\Support;

use Filament\Schemas\Components\StateCasts\StripCharactersStateCast;

class SafeStripCharactersStateCast extends StripCharactersStateCast
{
    public function set(mixed $state): mixed
    {
        if (empty($this->characters)) {
            return $state;
        }

        if (is_array($state)) {
            return array_map(fn (mixed $value): mixed => $this->setSingle($value), $state);
        }

        return $this->setSingle($state);
    }

    protected function setSingle(mixed $state): mixed
    {
        if (! is_string($state)) {
            return $state;
        }

        // If the state is a database decimal string (e.g. '500000.00' from decimal:2 cast)
        // and '.' is in characters to strip (as in Indonesian thousand separator),
        // stripping '.' would wrongly turn 500000.00 into 50000000 (100x multiplication!).
        // SQL decimal:2 values have digits followed by a single dot and 1-2 decimal digits.
        // Indonesian thousand groups always have 3 digits per group (e.g. '.000').
        if (in_array('.', $this->characters, true) && preg_match('/^\d+\.\d{1,2}$/', $state)) {
            $float = (float) $state;

            return (floor($float) == $float) ? (int) $float : $float;
        }

        return str_replace($this->characters, '', $state);
    }
}
