<?php

return [

    'scan' => [
        'enabled' => true,
        'paths' => [
            // It's important to enable scan and search for a lowercase
            // modules directory, otherwise it searches in Modules/ which
            // will return nothing on case-sensitive systems.
            base_path('modules/*'),
        ],
    ],

];
