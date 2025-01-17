<?php

namespace App\Utils;

class InputSanitizer
{

    public static function cleanString(string $description, \PDO $pdo = null): string
    {
        $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
        if ($pdo) {
            $description = $pdo->quote($description);
        } else {
            $description = addcslashes($description, "\0..\37\177..\377\%_");
        }

        return $description;
    }
}