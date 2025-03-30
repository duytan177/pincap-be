<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait OrderableTrait
{
    public const TYPE_ORDERS = ['asc', 'desc'];

    public function getAttributeOrder($orderKey, $orderType)
    {
        if (empty($orderKey) || empty($orderType)) {
            return null;
        }

        $orderType = in_array(strtolower(string: $orderType), self::TYPE_ORDERS) ? $orderType : $orderType;

        return [$orderKey => $orderType];

    }
    public static function scopeApplyOrder(Builder $query, ?array $order = null): Builder
    {
        if ($order) {
            [$field, $direction] = [key(array: (array) $order), strtolower(current((array) $order))];

            if (in_array($direction, self::TYPE_ORDERS)) {
                return $query->orderBy($field, $direction);
            }
        }

        return $query;
    }
}
