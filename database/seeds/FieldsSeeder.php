<?php

use App\Field;
use App\Group;
use Illuminate\Database\Seeder;

class FieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fieldIds = [];
        foreach ($this->fields() as $field) {
            $oldField = Field::where('title->ru', '=', $field['title']['ru'])->first();
            if ($oldField) {
                $fieldIds[] = $oldField->id;
                continue;
            }
            
            $newField   = Field::create($field);
            $fieldIds[] = $newField->id;
        }
        
        foreach ($this->groups() as $group) {
            $oldGroup = Group::where('title->ru', '=', $group['title']['ru'])->first();
            $newGroup = $oldGroup ?: Group::create($group);
            
            $newGroup->fields()->sync($fieldIds, false);
        }
    }
    
    
    private function fields()
    {
        return [
            [
                'title'       => [
                    'ru' => 'Наименование',
                ],
                'placeholder' => [
                    'ru' => 'Наименование',
                ],
                'description' => [
                    'ru' => 'Укажите наименование',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Наименование документа',
                ],
                'placeholder' => [
                    'ru' => 'Наименование документа',
                ],
                'description' => [
                    'ru' => 'Укажите наименование документа',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Номер документа',
                ],
                'placeholder' => [
                    'ru' => 'Номер документа',
                ],
                'description' => [
                    'ru' => 'Укажите номер документа',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Дата выдачи документа',
                ],
                'placeholder' => [
                    'ru' => 'Дата выдачи документа',
                ],
                'description' => [
                    'ru' => 'Укажите дату выдачи документа',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Орган, выдавший документ',
                ],
                'placeholder' => [
                    'ru' => 'Орган, выдавший документ',
                ],
                'description' => [
                    'ru' => 'Укажите орган, выдавший документ',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Индивидуальный номер налогоплательщика (ИНН)',
                ],
                'placeholder' => [
                    'ru' => 'Индивидуальный номер налогоплательщика (ИНН)',
                ],
                'description' => [
                    'ru' => 'Укажите индивидуальный номер налогоплательщика',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Адрес',
                ],
                'placeholder' => [
                    'ru' => 'Адрес',
                ],
                'description' => [
                    'ru' => 'Укажите адрес',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Контактная информация',
                ],
                'placeholder' => [
                    'ru' => 'Контактная информация',
                ],
                'description' => [
                    'ru' => 'Укажите контактную информацию',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Телефон',
                ],
                'placeholder' => [
                    'ru' => 'Телефон',
                ],
                'description' => [
                    'ru' => 'Укажите телефон',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Факс',
                ],
                'placeholder' => [
                    'ru' => 'Факс',
                ],
                'description' => [
                    'ru' => 'Укажите факс',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Расчетный счет (Р/сч)',
                ],
                'placeholder' => [
                    'ru' => 'Расчетный счет (Р/сч)',
                ],
                'description' => [
                    'ru' => 'Укажите расчетный счет',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Банк',
                ],
                'placeholder' => [
                    'ru' => 'Банк',
                ],
                'description' => [
                    'ru' => 'Укажите банк',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Банковский идентификационный код (БИК)',
                ],
                'placeholder' => [
                    'ru' => 'Банковский идентификационный код (БИК)',
                ],
                'description' => [
                    'ru' => 'Укажите банковский идентификационный код',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Основной государственный регистрационный номер (ОГРН)',
                ],
                'placeholder' => [
                    'ru' => 'Основной государственный регистрационный номер (ОГРН)',
                ],
                'description' => [
                    'ru' => 'Укажите основной государственный регистрационный номер',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Код причины постановки на налоговый учет (КПП)',
                ],
                'placeholder' => [
                    'ru' => 'Код причины постановки на налоговый учет (КПП)',
                ],
                'description' => [
                    'ru' => 'Укажите код причины постановки на налоговый учет',
                ],
                'type'        => 'input',
            ],
            [
                'title'       => [
                    'ru' => 'Корреспондентский счет (кор./сч)',
                ],
                'placeholder' => [
                    'ru' => 'Корреспондентский счет (кор./сч)',
                ],
                'description' => [
                    'ru' => 'Укажите корреспондентский счет',
                ],
                'type'        => 'input',
            ],
        ];
    }
    
    private function groups()
    {
        return [
            [
                'title' => [
                    'ru' => 'Сторона 1'
                ],
                'placeholder' => [
                    'ru' => 'Сторона 1'
                ],
                'type' => 'requisite',
            ],
            [
                'title' => [
                    'ru' => 'Сторона 2'
                ],
                'placeholder' => [
                    'ru' => 'Сторона 2'
                ],
                'type' => 'requisite',
            ],
        ];
    }
}
