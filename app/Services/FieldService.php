<?php
/**
 * Created by PhpStorm.
 * User: Arman
 * Date: 23.11.2018
 * Time: 18:10
 */

namespace App\Services;



class FieldService
{
    /**
     * @param array $payload
     * @param string $key
     * @return array
     */
    public function parsePayload(array $payload, string $key = 'text') : array
    {
        if (!isset($payload[$key])) return $payload;
        
        $fields = [];
        foreach ($payload[$key] as $locale => $text) {
            $text_fields = $this->parseText($text);
            
            if (count($text_fields) > 0) $fields = array_merge($fields, $text_fields);
        }
    
        if (count($fields) > 0) $payload['fields'] = $fields;
        
        return $payload;
    }
    
    /**
     * @param string $string
     * @return array
     */
    public function parseText(string $string) : array
    {
        preg_match_all('/(?<=data\-field\=[\"\']{1})[\d\,]+/', $string, $matches);
        
        return array_unique($matches[0]);
    }
    
    /**
     * @param array $payloadArray
     * @return array
     */
    public function getFieldsIdsFromPayload(array $payloadArray) : array
    {
        $fieldIds = [];
        foreach ($payloadArray as $payload) {
            if (!isset($payload['payload']->fields)) continue;
            $fieldIds = array_merge($payload['payload']->field, $fieldIds);
        }
        
        return $fieldIds;
    }
    
    /**
     * @param array $fieldsData
     * @param string $html
     * @return null|string|string[]
     */
    public function fillFields(array $fieldsData, string $html)
    {
        return preg_replace_callback(
            "/\<span data\-field\=.+?span\>/",
            function ($matches) use ($fieldsData) {
            
                $key = $this->parseText($matches[0])[0];
                foreach ($fieldsData as $field) {
                    if ($field['id'] == $key) {
                        $value = $field['value'];
                        break;
                    }
                }
            
                return $value ?? '';
            },
            $html
        );
    }
    
    /**
     * @param array $fieldsData
     * @param array $matrixSet
     * @return array
     */
    public function fillMatrixSetFields(array $fieldsData, array $matrixSet) : array
    {
        foreach ($matrixSet as $key => $matrix) {
            foreach ($matrix['payload']->text as $text) {
                $matrixSet[$key]['payload']->text = (new FieldService())->fillFields($fieldsData, $text);
            }
        }
        
        return $matrixSet;
    }
}