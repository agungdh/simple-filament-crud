<?php

namespace App\Policies;

use App\Policies\Concerns\ManagesOwnedPegawaiRecord;

class WebinarPolicy
{
    use ManagesOwnedPegawaiRecord;
}
