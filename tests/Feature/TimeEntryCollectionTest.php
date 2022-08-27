<?php

namespace Tests\Feature;

use App\Collections\TimeEntryCollection;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TimeEntryCollectionTest extends TestCase
{
    use RefreshDatabase;

    protected TimeEntryCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();
        TimeEntry::query()->create([
            'project' => 'Project 1',
            'client' => 'Client 1',
            'description' => 'description',
            // duration 77 mins
            'start' => '2022-01-01 08:00:00',
            'end' => '2022-01-01 09:17:00',
        ]);
        TimeEntry::create([
            'project' => 'Project 1',
            'client' => 'Client 1',
            'description' => 'description',
            // duration 2h 42 = 162 mins
            'start' => '2022-01-01 13:02:00',
            'end' => '2022-01-01 15:44:00',
        ]);
        TimeEntry::create([
            'project' => 'Project 1',
            'client' => 'Client 1',
            'description' => 'description',
            // duration 2h 42 = 162 mins
            'start' => '2022-01-02 13:02:00',
            'end' => '2022-01-02 15:44:00',
        ]);
        TimeEntry::create([
            'project' => 'Project 1',
            'client' => 'Client 1',
            'description' => 'description',
            // duration 77 mins
            'start' => '2022-01-05 08:00:00',
            'end' => '2022-01-05 09:17:00',
        ]);

        $this->collection = TimeEntryCollection::make(TimeEntry::all());

        // dump($this->collection);
    }

    protected function tearDown(): void
    {
        // not really needed?
        $this->collection->each(fn (TimeEntry $timeEntry) => $timeEntry->delete());
        parent::tearDown();
    }

    public function test_single_entry_duration_is_correct()
    {
        $this->assertEquals($this->collection->first()->getDurationInSeconds(), (77*60));
    }

    public function test_total_duration_is_correct()
    {
        $this->assertEquals($this->collection->getDurationInSeconds(), ((2 * 77) + (2 * 162)) * 60);
    }

    public function test_rounded_duration_in_hours_is_correct()
    {
        $this->assertEquals($this->collection->getRoundedDurationInHours(), 8.0);
    }

    public function test_correct_dates()
    {
        $dates = $this->collection->getUniqueDates()->map(fn (Carbon $date) => (string)$date);

        $this->assertEquals($dates->count(), 3);
        $this->assertContains((string)Carbon::parse('2022-01-01'), $dates);
        $this->assertContains((string)Carbon::parse('2022-01-02'), $dates);
        $this->assertContains((string)Carbon::parse('2022-01-05'), $dates);
    }

    public function test_correct_date_string()
    {
        $this->assertEquals('01-02/01, 05/01', $this->collection->getDateString());
    }

    public function test_correct_description()
    {
        $this->assertEquals('Project 1 (01-02/01, 05/01)', $this->collection->getDescription());
    }
}
