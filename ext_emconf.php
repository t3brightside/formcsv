<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Form CSV Attachment Finisher',
    'description' => 'Generates CSV files from mapped form fields and attaches them to form emails in TYPO3 v14.',
    'category' => 'fe',
    'author' => 'Brightside',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '14.3.0-14.99.99',
            'form' => '14.3.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
