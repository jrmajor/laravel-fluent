<?php

return [

    /*
     * In strict mode, exceptions will be thrown for syntax errors
     * in .ftl files, unknown variables in messages etc.
     * It's recommended to enable this setting in development
     * to make it easy to spot mistakes.
     */
    'strict' => ! app()->isProduction(),

    /*
     * Determines if it should use Unicode isolation marks (FSI, PDI)
     * for bidirectional interpolations. You may want to enable this
     * behaviour if your application uses right-to-left script.
     */
    'use_isolating' => false,

    /*
     * Determines if namespaced translations should be overridable in the
     * standard Laravel manner of creating a `lang/vendor` directory.
     */
    'allow_overrides' => true,

];
