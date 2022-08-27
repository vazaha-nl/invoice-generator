<?php

namespace App\ToggleTrack;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class EntryImport implements ToCollection
{
    public function collection(Collection $collection)
    {
        return $collection->mapInto(EntryCollection::class);
    }
}
