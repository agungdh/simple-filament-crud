<?php

namespace App\Policies;

use App\Policies\Concerns\ManagesOwnedPegawaiRecord;

class LcPolicy
{
    use ManagesOwnedPegawaiRecord;
}
