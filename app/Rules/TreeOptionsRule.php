<?php

namespace App\Rules;

use App\Entity;
use Illuminate\Contracts\Validation\Rule;

class TreeOptionsRule implements Rule
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
        preg_match_all('!\d+!', $value, $matches);
        if (count($items = $matches[0]) < 2) return false;
        
        $exists = true;
        foreach ($items as $item) {
            $exists = Entity::where('id', $item)
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
