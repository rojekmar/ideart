<?php

// Tylko reguły faktycznie używane w tym projekcie (formularz kontaktowy,
// app/Http/Controllers/ContactController.php) — nie pełny, kompletny zestaw
// wszystkich reguł Laravela, żeby nie utrzymywać tłumaczenia rzeczy, których
// i tak nigdzie nie używamy.
return [

    'required' => 'Pole :attribute jest wymagane.',
    'email' => 'Pole :attribute musi być poprawnym adresem e-mail.',
    'string' => 'Pole :attribute musi być tekstem.',
    'max' => [
        'string' => 'Pole :attribute nie może być dłuższe niż :max znaków.',
        'numeric' => 'Pole :attribute nie może być większe niż :max.',
    ],
    'prohibited' => 'Pole :attribute jest niedozwolone.',

    'attributes' => [
        'name' => 'imię',
        'email' => 'e-mail',
        'phone' => 'telefon',
        'type' => 'rodzaj projektu',
        'message' => 'wiadomość',
        'website' => 'website',
    ],

];
