<?php

namespace App\Utils;

use RuntimeException;

class InputSanitizer
{

    public static function cleanString(string $description, \PDO $pdo = null): string
    {
        $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
        if ($pdo) {
            $quotedDescription = $pdo->quote($description);
            if ($quotedDescription === false) {
                throw new RuntimeException('Failed to quote the description.');
            }
            $description = $quotedDescription;
        } else {
            $description = addcslashes($description, "\0..\37\177..\377\%_");
        }

        return $description;
    }
}
