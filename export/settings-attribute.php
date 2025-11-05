<?php

return [
    "name" => "attribute",
    "title" => "Attributes",
    "description" => "Attributes module",
    "module_name" => [
        "single" => "Eigenschap",
        "multiple" => "Eigenschappen"
    ],
    "tabtitle" => "title",
    "type" => "module",
    "active" => true,
    "locale" => "nl",
    "data" => [],
    "sort" => 999,
    "route" => null,
    "url" => null,
    "icon" => null,
    "rights" => null,
    "ereg" => [],
    "settings" => [],
    "fields" => [
        "locale" => [
            "active" => false,
            "type" => "text",
            "title" => "Taal",
            "read" => true,
            "required" => false,
            "edit" => false,
        ],
        "uploads" => [
            "active" => false,
            "type" => "",
            "title" => "Uploads",
            "read" => false,
            "required" => false,
        ],
        "name" => [
            "active" => true,
            "type" => "text",
            "title" => "Naam",
            "read" => true,
            "required" => true,
            "edit" => true,
        ],
        "code" => [
            "active" => true,
            "type" => "text",
            "title" => "Code",
            "read" => true,
            "required" => true,
            "edit" => true,
        ],
        "type" => [
            "active" => true,
            "type" => "select",
            "title" => "Type",
            "read" => true,
            "required" => true,
            "edit" => true,
            "options" => [
                "text" => "Tekst",
                "number" => "Getal",
                "select" => "Selectie",
                "multiselect" => "Meervoudige selectie",
                "boolean" => "Ja/Nee",
                "date" => "Datum",
                "textarea" => "Tekstvak",
                "color" => "Kleur",
                "url" => "URL",
                "email" => "E-mail"
            ]
        ],
        "sort" => [
            "active" => true,
            "type" => "number",
            "title" => "Sortering",
            "read" => true,
            "required" => false,
            "edit" => true,
        ],
        "config" => [
            "active" => true,
            "type" => "json",
            "title" => "Configuratie",
            "read" => true,
            "required" => false,
            "edit" => true,
        ],
    ]
];
