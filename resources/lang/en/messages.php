<?php

return [
    'v1' => [
        '500' => 'Oops, Something went wrong! Please try again later.',
        'registration' => [
            'verify' => 'A verification link was sent to your email.',
            'error' => 'Registration Error!'
        ],
        'verification' => [
            'invalid' => 'Invalid verification link.',
            'success' => 'Your account was successfully verified. Referral link was sent to your email.',
            #'already' => 'Your account was already verified.'
            'already' => 'Page Already Expired.'
        ],
        'settings' => [
            'update-profile' => [
                'not_found' => 'User not found!',
                'success' => 'Profile successfully updated'
            ],
            'change-avatar' => [
                'not_found' => 'User not found!',
                'success' => 'Avatar successfully updated'
            ],
            'change-password' => [
                'incorrect' => 'Password is incorrect.',
                'success' => 'Password successfully changed.'
            ],
        ],
        'forgot-password' => [
            'invalid_email' => 'Sorry!, We cannot find your account email.',
            'valid_email' => 'Please check your email for your Reset Password Code.',
            'valid_code' => 'You can now reset your password.',
            'invalid_code' => 'Password reset code is invalid.',
            'reset' => 'Password successfully reset. You can now login with your new password. Thank you!',
        ],
    ]
];
