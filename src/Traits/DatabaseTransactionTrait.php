<?php

namespace TwenyCode\LaravelBlueprint\Traits;

use Illuminate\Support\Facades\DB;
use Closure;

/**
 * Trait for standardized error handling across application components
 */
trait DatabaseTransactionTrait
{
    /**
     * Execute a Closure within a database transaction.
     *
     * @param \Closure $callback The callback to execute within the transaction
     * @param int $attempts Number of attempts
     * @return mixed The result of the callback
     */
    protected function transaction(Closure $callback, int $attempts = 1)
    {
        return DB::transaction($callback, $attempts);
    }
}