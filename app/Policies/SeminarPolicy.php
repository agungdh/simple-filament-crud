<?php

namespace App\Policies;

use App\Policies\Concerns\ManagesOwnedPegawaiRecord;

class SeminarPolicy
{
    use ManagesOwnedPegawaiRecord;
}
