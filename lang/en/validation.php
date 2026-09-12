<?php

// Only the rules actually used in this project (contact form,
// app/Http/Controllers/ContactController.php) — not the full default
// Laravel validation set, to avoid maintaining translations for rules we
// never use.
return [

    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute field must be a valid email address.',
    'string' => 'The :attribute field must be text.',
    'max' => [
        'string' => 'The :attribute field may not be longer than :max characters.',
        'numeric' => 'The :attribute field may not be greater than :max.',
    ],
    'prohibited' => 'The :attribute field is not allowed.',

    'attributes' => [
        'name' => 'name',
        'email' => 'email',
        'phone' => 'phone',
        'type' => 'project type',
        'message' => 'message',
        'website' => 'website',
    ],

];
