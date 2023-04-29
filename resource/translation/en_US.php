<?php
return [
    'validation' => [
        'required' => 'This is a required value',
        'collection' => [
            'tooFew' => 'Minimum %min% required, %counted% given',
            'tooMany' => 'Maximum %max% allowed, %counted% given',
        ],
        'composite' => [
            'keysNotAllowed' => 'More values given than expected',
        ],
        'expected' => [
            'boolean' => 'A boolean expected',
            'collection' => 'A list was expected',
            'composite' => 'A composite was expected',
            'integer' => 'An integer expected',
            'number' => 'A number expected',
            'string' => 'A string expected',
        ],
        'number' => [
            'precision' => 'Maximum %max% decimal places',
            'between' => 'Value between %minimal% and %maximal% expected',
        ],
        'string' => [
            'format' => [
                'emailAddress' => 'Valid email address format expected',
                'https' => 'Expected an https link',
                'searchTerm' => 'Expected alphanumeric text',
                'date' => 'Expected date in "year-month-day" format, for example "2014-05-21"',
                'ip' => 'Valid IP address expected',
            ],
            'tooShort' => 'Minimum %minLength% characters required, %givenLength% counted',
            'tooLong' => 'Maximum %maxLength% characters allowed, %givenLength% counted',
            'notAllowed' => 'This value is not allowed',
            'required' => 'Text is required'
        ],
    ],
];
