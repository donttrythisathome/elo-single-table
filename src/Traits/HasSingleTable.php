<?php

namespace SingleTable\Traits;

trait HasSingleTable
{
    /**
     * Create a new model instance that is existing.
     *
     * @param  array  $attributes
     * @param  string|null  $connection
     * @return static
     */
    public function newFromBuilder($attributes = array(), $connection = null)
    {
        $model = $this->getInheriеtedModelInstance($type)??$this;

        return tap ($model->setRawAttributes((array) $attributes, true),function($instance){
            $instance->exists = true;
        });
    }

    /**
     * Inherited model factory.
     *
     * @param  string $type
     * @return Illuminate\Database\Eloquent\Model
     */
    public static function getInheriеtedModelInstance($type)
    {
        $mapped = collect(config('elo_single_table'))->search($type);
        if (!$mapped){
            return new $type;
        }

        return new $mapped;
    }

    /**
     * Scope query to current type.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('type',function(Builder $builder){
            $builder->when(static::resolveTypeId(), function($builder,$type){
                $builder->where('type', $type);
            });
        });
    }

    /**
     * Resolve record type id.
     *
     * @return integer
     */
    public static function resolveType()
    {
        if(!$class = config('elo_single_table.'.static::class)){
            return static::class;
        };

        return $class;
    }
}