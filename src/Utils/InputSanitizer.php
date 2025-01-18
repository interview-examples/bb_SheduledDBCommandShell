<?php

namespace App\Utils;

use PDO;
use RuntimeException;

class InputSanitizer
{

    /**
     * Clean a string input to prevent SQL injection.
     *
     * This function escapes special characters in the input string to prevent
     * SQL injection attacks. It also optionally quotes the input string using
     * the PDO::quote() method when a PDO instance is provided.
     *
     * @param string $description the string to clean
     * @param PDO|null $pdo optional PDO instance to use for quoting the string
     * @return string the cleaned string
     */
    public static function cleanString(string $description, PDO $pdo = null): string
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
