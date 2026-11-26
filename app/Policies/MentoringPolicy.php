<?php

namespace App\Policies;

use App\Policies\Concerns\ManagesOwnedPegawaiRecord;

class MentoringPolicy
{
    use ManagesOwnedPegawaiRecord;
}
