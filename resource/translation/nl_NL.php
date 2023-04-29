<?php
return [
    'validation' => [
        'required' => 'Dit is een verplichte waarde',
        'collection' => [
            'tooFew' => 'Minimaal %min% vereist, %counted% opgegeven',
            'tooMany' => 'Minimaal %max% toegestaan, %counted% opgegeven',
        ],
        'composite' => [
            'keysNotAllowed' => 'Meer waardes opgegeven dan verwacht',
        ],
        'expected' => [
            'boolean' => 'Een boolean verwacht',
            'collection' => 'Er was een lijst verwacht',
            'composite' => 'Er is een samenstelling verwacht',
            'integer' => 'Een geheel getal verwacht',
            'number' => 'Een getal verwacht',
            'string' => 'Een stuk tekst verwacht',
        ],
        'number' => [
            'precision' => 'Maximaal %max% decimalen',
            'between' => 'Waarde tussen %minimal% en %maximal% verwacht',
        ],
        'string' => [
            'format' => [
                'emailAddress' => 'Geldig e-mail adres formaat verwacht',
                'https' => 'Verwacht een https link',
                'searchTerm' => 'Verwacht alfanumerieke tekst',
                'date' => 'Datum "jaar-maand-dag" verwacht, bijvoorbeeld "2014-05-21"',
                'ip' => 'Geldig IP adres verwacht',
            ],
            'tooShort' => 'Minimaal %minLength% karakters vereist, %givenLength% geteld',
            'tooLong' => 'Maximaal %maxLength% karakters toegestaan, %givenLength% geteld',
            'notAllowed' => 'Deze waarde is niet toegestaan',
            'required' => 'Tekst is verplicht'
        ],
    ],
];
