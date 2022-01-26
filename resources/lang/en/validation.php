<?php

return [
    "required" => ":Attribute is required",
    "confirmed" => ":Attribute must be confirmed",
    "exists" => ":Attribute is not existing",
    "is_base64" => "Image invalid",
    "date_format" => ":Attribute format invalid",
    "after" => "Cannot set past date",
    "integer" => ":Attribute must be an integer",
    "digits" => ":Attribute must be a number",
    "min" => [
        "numeric" => ":Attribute must not be lower than :min",
        'string' => ":Attribute must at least :min characters length",
    ],
    "max" => [
        "string" => ":Attribute must not be greater than :max"
    ],
    "filled" => ":Attribute is required",
    "in" => ":Attribute not valid",
    "boolean" => ":Attribute should be boolean",
    "numeric" => ":Attribute should be a number",
    "sku_on_create" => ":Attribute already exist",
    "sku_on_update" => ":Attribute already exist",

    'custom' => [
        'password' => [
            'same' => 'Password doesn\'t match'
        ],
        'confirm_password' => [
            'required' => 'Please confirm password',
            'same' => 'Password doesn\'t match'
        ],
        'email' => [
            'unique' => 'Email already exist',
            'email' => 'Must be a valid email'
        ],
        'mobile_no' => [
            'mobile' => 'Mobile number is invalid'
        ],
        'new_password' => [
            'same' => 'New Password doesn\'t match'
        ],
        'confirm_new_password' => [
            'required' => 'Please new confirm password.',
            'same' => 'Confirm New Password doesn\'t match'
        ],
        'uom' => [
            'uom_unique' => 'Unit of measure already exist.',
        ],
    ]
];
