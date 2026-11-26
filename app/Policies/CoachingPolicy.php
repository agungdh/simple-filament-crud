<?php

namespace App\Policies;

use App\Policies\Concerns\ManagesOwnedPegawaiRecord;

class CoachingPolicy
{
    use ManagesOwnedPegawaiRecord;
}
