<?php

namespace Esign\UnderscoreTranslatable\Tests\Models;

use Esign\UnderscoreTranslatable\UnderscoreTranslatable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use UnderscoreTranslatable;

    protected $guarded = [];
    public $timestamps = false;
    public $translatable = [
        'title',
        'field_with_accessor',
        'field_with_mutator',
    ];

    public function getFieldWithAccessorAttribute($value)
    {
        return strtolower($value);
    }

    public function setFieldWithMutatorAttribute($value, $locale): void
    {
        $this->attributes['field_with_mutator_' . $locale] = strtoupper($value);
    }
}
