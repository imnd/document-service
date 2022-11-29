<?php

namespace App\Rules;

use App\Entity;
use Illuminate\Contracts\Validation\Rule;

class DependencyExistsRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $valueItems = explode(':', $value);
        if (count($valueItems) == 0) return false;
    
        $exists = true;
        foreach ($valueItems as $valueItem) {
            $exists = Entity::where('id', $valueItem)
                ->where('type', config('entities.types.tree'))
                ->exists();
            
            if (!$exists) break;
        }
        
        return $exists;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The option :attribute is invalid.';
    }
}
