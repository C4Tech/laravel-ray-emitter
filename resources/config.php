<?php

return [
    /**
     * Prefix to Event handlers on the aggregate. If the event is named ItHappened,
     * the expected handler should be named {apply_prefix}SomeEvent (e.g.
     * applyItHappened).
     */
    'apply_prefix' => 'apply',

    /**
     * Prefix to Command handlers on the aggregate. If the command is named
     * MakeItRain, the expected handler should be named {handle_prefix}MakeItRain
     *  (e.g. handleMakeItRain).
     */
    'handle_prefix' => 'handle'
];
