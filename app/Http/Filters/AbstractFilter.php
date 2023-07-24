<?php


namespace App\Http\Filters;


use App\Models\API\Entities\Property;
use Illuminate\Database\Eloquent\Builder;

abstract class AbstractFilter implements FilterInterface
{
    /** @var array */
    private $queryParams = [];

    /**
     * AbstractFilter constructor.
     *
     * @param array $queryParams
     */
    public function __construct(array $queryParams)
    {

        $this->queryParams = $queryParams;
    }

    abstract protected function getCallbacks(): array;

    public function getCallback($name)
    {
        if (isset($this->getCallbacks()[$name]))
            return $this->getCallbacks()[$name];

        return false;
    }

    public function apply(Builder $builder)
    {
        $this->before($builder);

        foreach ($this->queryParams as $field => $value) {
            $callback = $this->getCallback($field);
            if ($callback)
                call_user_func($callback, $builder, $value);
            else
                $this->dynamicFilter($builder, $field);
        }
    }


    /**
     * Dynamic Filter
     * @param $builder
     * @param $field
     * @return bool|null
     */
    protected function dynamicFilter($builder, $field)
    {
        $value = $this->getQueryParam($field);
        $_field = str_replace('-', '_', $field);
        $tableColumns = $this->getModelFields($builder);

        // Filter by inner model fields
        if (false !== array_search($_field, $tableColumns)) {
            $this->applyDynamicFilter($builder, $_field, $value);
            return true;
        }

        return null;
    }


    // Filter by Model Fillables
    protected function applyDynamicFilter(Builder $builder, $field, $value)
    {
        if (is_array($value)) {

            // for range
            if (!empty($value['from']))
                $builder->where($field, '>=', (int)$value['from']);
            if (!empty($value['to']))
                $builder->where($field, '<=', (int)$value['to']);

            // for stack
            if (empty($value['from']) && empty($value['to'])) {
                $builder->whereIn($field, $value);
            }
        } elseif (!empty($value))
            $builder->where($field, $value);
        else
            $builder->whereNotNull($field);
    }


    protected function getModelFields(Builder $builder)
    {
        return $builder->getModel()->getFillable();
    }


    /**
     * @param Builder $builder
     */
    protected function before(Builder $builder)
    {
    }

    /**
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    protected function getQueryParam(string $key, $default = null)
    {
        return $this->queryParams[$key] ?? $default;
    }

    /**
     * @param string[] $keys
     *
     * @return AbstractFilter
     */
    protected function removeQueryParam(string ...$keys)
    {
        foreach ($keys as $key) {
            unset($this->queryParams[$key]);
        }

        return $this;
    }
}
