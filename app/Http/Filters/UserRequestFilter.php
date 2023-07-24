<?php

namespace App\Http\Filters;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class UserRequestFilter extends AbstractFilter
{
    public function __construct(array|string $queryParams)
    {
        parent::__construct($queryParams);
    }

    protected function getCallbacks(): array
    {
        return [
            'user-name' => [$this, 'userName'],
            'user-email' => [$this, 'userEmail'],
            'status' => [$this, 'status'],
            'created-at' => [$this, 'createdAt'],
            'updated-at' => [$this, 'updatedAt'],
        ];
    }

    protected function createdAt(Builder $builder, array $value)
    {
        if (empty($value['from'])) {
            $builder->where('created_at', '<=', Carbon::parse($value['to'])->endOfDay()->toDateTimeString());
        } elseif (empty($value['to'])) {
            $builder->where('created_at', '>=', Carbon::parse($value['from'])->endOfDay()->toDateTimeString());
        } else {
            $builder->whereBetween(
                'created_at',
                [
                    Carbon::parse($value['from'])->startOfDay()->toDateTimeString(),
                    Carbon::parse($value['to'])->endOfDay()->toDateTimeString()
                ]
            );
        }
    }

    protected function updatedAt(Builder $builder, array $value)
    {
        if (empty($value['from'])) {
            $builder->where('updated_at', '<=', Carbon::parse($value['to'])->endOfDay()->toDateTimeString());
        } elseif (empty($value['to'])) {
            $builder->where('updated_at', '>=', Carbon::parse($value['from'])->endOfDay()->toDateTimeString());
        } else {
            $builder->whereBetween(
                'updated_at',
                [
                    Carbon::parse($value['from'])->startOfDay()->toDateTimeString(),
                    Carbon::parse($value['to'])->endOfDay()->toDateTimeString()
                ]
            );
        }
    }

    protected function status(Builder $builder, string $value)
    {
        $builder->where('status', $value);
    }

    protected function userName(Builder $builder, string $value)
    {
        $builder->whereHas('user', function ($query) use ($value) {
            $query->where('name', $value);
        });
    }

    protected function userEmail(Builder $builder, string $value)
    {
        $builder->whereHas('user', function ($query) use ($value) {
            $query->where('email', $value);
        });
    }
}
