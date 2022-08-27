<?php

namespace App\Services\ToggleTrack;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Services\ToggleTrack\EntryCollection;

class EntryImport implements ToCollection
{
    public function collection(Collection $collection)
    {
        return $collection->mapInto(EntryCollection::class);
    }
}
