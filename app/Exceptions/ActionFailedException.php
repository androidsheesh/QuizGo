<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A business rule stopped the action (e.g. "already enrolled"). Rendered in bootstrap/app.php
 * as a redirect back with the message attached to $field, like a validation error.
 */
class ActionFailedException extends RuntimeException
{
    public function __construct(string $message, public readonly string $field = 'error')
    {
        parent::__construct($message);
    }
}
