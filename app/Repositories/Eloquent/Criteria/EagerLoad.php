<?php

namespace App\Repositories\Eloquent\Criteria;

class EagerLoad
{
    protected $relationships;

    public function __construct($relationships) {
        $this->relationships = $relationships;
    }

    public function apply($model) {
        return $model->with($this->relationships);
    }
}
