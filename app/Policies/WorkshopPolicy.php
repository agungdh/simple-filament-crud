<?php

namespace App\Policies;

use App\Policies\Concerns\ManagesOwnedPegawaiRecord;

class WorkshopPolicy
{
    use ManagesOwnedPegawaiRecord;
}
