<?php

namespace App\Tags;

use App\Modifiers\ShortName;
use Statamic\Facades\Entry;
use Statamic\Tags\Tags;

class Agencies extends Tags
{
  /**
   * The studios credited for concept/design on projects, most frequent first.
   * Names are shortened with the short_name modifier (no location, no trailing legal form).
   *
   * Usage: {{ agencies }}{{ name }}{{ /agencies }} ({{ projects }} holds the count)
   */
  public function index()
  {
    return Entry::query()
      ->where('collection', 'projects')
      ->get()
      ->map(fn ($entry) => $entry->agency?->title())
      ->filter()
      ->map(fn ($title) => ShortName::shorten($title))
      ->reject(fn ($name) => $name === 'marceli.to')
      ->countBy()
      ->sortDesc()
      ->map(fn ($projects, $name) => ['name' => $name, 'projects' => $projects])
      ->values()
      ->all();
  }
}
