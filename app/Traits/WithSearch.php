<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait WithSearch
{
    public function scopeSearch($query, $search, array $searchable = null)
    {
        $search = trim(strtolower($search)); // Clean up white space
        $fields = $searchable ?: $this->searchable ?: [];

        if (!$search) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search, $fields) {
            foreach ($fields as $key => $field) {
                if (Str::contains($field, '.')) {
                    [$relationship, $relationshipField] = explode('.', $field);
                    $query->orWhereHas($relationship, function ($query) use ($relationshipField, $search) {
                        $query->where($relationshipField, 'like', "%$search%");
                    });
                } else {
                    $query->orWhere($field, 'like', "%$search%");
                }
            }
        });
    }
}
