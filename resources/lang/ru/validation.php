<?php
return [
    'accepted'             => 'Значение поля :attribute должно быть отмечено.',
    'active_url'           => 'Значение поля :attribute не является ссылкой.',
    'after'                => 'Значение поля :attribute должно быть датой позднее :date.',
    'alpha'                => 'Значение поля :attribute может сордержать только буквы.',
    'alpha_dash'           => 'Значение поля :attribute может содержать только буквы, цифры и дефисы.',
    'alpha_num'            => 'Значение поля :attribute может содержать только буквы и цифры.',
    'array'                => 'Значение поля :attribute должно быть массивом.',
    'before'               => 'Значение поля :attribute должно быть датой ранее :date.',
    'between'              => [
        'numeric' => 'Значение поля :attribute должно быть между :min и :max.',
        'file'    => 'Значение поля :attribute должно быть между :min и :max КБ.',
        'string'  => 'Значение поля :attribute должно быть между :min и :max символов.',
        'array'   => 'Значение поля :attribute должно быть между :min и :max штук.',
    ],
    'boolean'              => 'The :attribute field must be true or false.',
    'confirmed'            => 'Подтверждение поля :attribute не совпадает.',
    'date'                 => 'Поле :attribute не явлояется корректной датой.',
    'date_format'          => 'The :attribute does not match the format :format.',
    'different'            => 'The :attribute and :other must be different.',
    'digits'               => 'The :attribute must be :digits digits.',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'dimensions'           => 'The :attribute has invalid image dimensions.',
    'distinct'             => 'The :attribute field has a duplicate value.',
    'email'                => ':attribute должен быть настоящим.',
    'exists'               => 'The selected :attribute is invalid.',
    'file'                 => 'The :attribute must be a file.',
    'filled'               => 'The :attribute field is required.',
    'image'                => 'The :attribute must be an image.',
    'in'                   => 'The selected :attribute is invalid.',
    'in_array'             => 'The :attribute field does not exist in :other.',
    'integer'              => 'The :attribute must be an integer.',
    'ip'                   => 'The :attribute must be a valid IP address.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'max'                  => [
        'numeric' => 'Поле :attribute не должно быть больше :max.',
        'file'    => 'Поле :attribute не должно быть больше :max КБ.',
        'string'  => 'Поле :attribute не должно быть больше :max символов.',
        'array'   => 'Поле :attribute не должно быть больше :max записей.',
    ],
    'mimes'                => 'Поле :attribute должно быть файлом следующего типа: :values.',
    'min'                  => [
        'numeric' => 'Поле :attribute должно содержать минимум :min.',
        'file'    => 'Поле :attribute должно содержать минимум :min КБ.',
        'string'  => 'Поле :attribute должно содержать минимум :min символов.',
        'array'   => 'Поле :attribute должно содержать минимум :min записей.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'present'              => 'The :attribute field must be present.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => ':attribute нужно обязательно заполнить.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'string'               => 'Поле :attribute должно быть строкой.',
    'timezone'             => ':attribute должна быть корректной зоной.',
    'unique'               => 'Значение поля :attribute уже есть в базе.',
    'url'                  => 'Формат поля :attribute некорректный.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'email' => 'Email адрес',
        'password' => 'Пароль',
        'phone' => 'Номер телефона',
        'name' => 'Имя',
    ],
];
