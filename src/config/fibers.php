<?php
return [
    /**
     * Traits used in model creation
     */
    "traits" => [
        'uuid' => 'Fibers\Rocket\Traits\HasUuid',
        'softdeletes' => 'Illuminate\Database\Eloquent\SoftDeletes',
    ],

    /**
     * Default location for where routes are added
     */
    "routes" => "web",

    /**
     * View templates
     * Set paths to your templates that will be used in layout creation
     */
    "templates" => [
        'index' => null,
        'create' => null,
        'show' => null,
        'edit' => null,
        '_item' => null,
        '_form' => null
    ],

    /**
     * View folders
     * Set paths to your views that will be used in layout creation
     */
    "views" => [
        'layouts' => null,
        'partials' => null
    ],

    /**
     * Do you want to use bootstrap based views?
     */
    "bootstrap" => false,

    /**
     * Should controller and index view be paginated?
     */
    "paginated" => false,
];
