<?php

namespace Modules\Feedbacks\Services\Facebook\Webhooks;

interface VerbInterface
{
    const VERB_ADD = 'add';
    const VERB_EDITED = 'edited';
    const VERB_REMOVE = 'remove';
}
