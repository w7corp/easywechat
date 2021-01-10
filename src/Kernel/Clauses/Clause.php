<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Clauses;

class Clause
{
    /**
     * @var array
     */
    protected array $clauses = [
        'where' => [],
    ];

    /**
     * @param mixed ...$args
     *
     * @return $this
     */
    public function where(...$args)
    {
        array_push($this->clauses['where'], $args);

        return $this;
    }

    /**
     * @param  mixed  $payload
     *
     * @return bool
     */
    public function intercepted(mixed $payload): bool
    {
        return (bool) $this->interceptWhereClause($payload);
    }

    /**
     * @param  mixed  $payload
     *
     * @return bool
     */
    protected function interceptWhereClause(mixed $payload): bool
    {
        foreach ($this->clauses['where'] as $item) {
            [$key, $value] = $item;
            if (isset($payload[$key]) && $payload[$key] !== $value) {
                return true;
            }
        }

        return false;
    }
}
