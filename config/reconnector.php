<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Reconnector Cluster Check Flag
    |--------------------------------------------------------------------------
    |
    | This flag is used by Reconnector package to enable check for cluster node
    | readiness when picking working connection from the servers array. This is
    | required to prevent connecting and using a node that is a part from
    | nonoperational component. This check executes one extra query under the
    | hood to retrieve MySQL variable information from the current connection.
    |
    */

    'clustered' => env('RECONNECTOR_CLUSTERED', false),

];