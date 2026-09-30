<?php

namespace App\Mcp\Support;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Contracts\Taxonomies\Term as TermContract;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\Facades\Taxonomy;
use Statamic\Facades\Term;
use Statamic\Support\Str;

/**
 * Reads and writes portfolio projects through Statamic's API for the MCP tools.
 */
class Projects
{
    public const COLLECTION = 'projects';

    public const BLUEPRINT = 'project';

    public const TAXONOMY = 'clients';

    public const ASSET_CONTAINER = 'assets';

    public const ASSET_FOLDER = 'projects';

    public const MIN_YEAR = 1995;

    /**
     * Validation rules shared by create_project and update_project.
     */
    public static function rules(bool $creating): array
    {
        // On update, required fields may be omitted but not emptied.
        $required = $creating ? 'required' : 'filled';

        return [
            'url' => [$required, 'string', 'url:http,https', 'max:255'],
            'title' => ['sometimes', 'nullable', 'string', 'min:2', 'max:120'],
            'description' => [$required, 'string', 'min:10', 'max:1000'],
            'year' => [$required, 'string', static::yearRule()],
            'client' => [$required, 'string', 'min:2', 'max:120'],
            'agency' => ['sometimes', 'nullable', 'string', 'min:2', 'max:120'],
            'image' => ['sometimes', 'nullable', 'string', static::imageRule()],
        ];
    }

    public static function messages(): array
    {
        return [
            'url.url' => 'The url must be a full http(s) URL of the website, e.g. https://example.ch.',
            'description.min' => 'The description is too short. Write at least one full sentence about the project.',
        ];
    }

    /**
     * Accepts "2024" or a range like "2016 – 2020" (hyphen or en dash).
     */
    public static function yearRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) {
            $max = (int) date('Y') + 1;

            if (! is_string($value) || ! preg_match('/^(\d{4})(?:\s*[-–]\s*(\d{4}))?$/u', trim($value), $m)) {
                $fail('The year must be a four-digit year like "2024" or a range like "2016 – 2020".');

                return;
            }

            $years = array_map('intval', array_filter([$m[1], $m[2] ?? null]));

            foreach ($years as $year) {
                if ($year < static::MIN_YEAR || $year > $max) {
                    $fail('The year must be between '.static::MIN_YEAR." and {$max}.");

                    return;
                }
            }

            if (count($years) === 2 && $years[0] >= $years[1]) {
                $fail('A year range must go from the earlier to the later year, e.g. "2016 – 2020".');
            }
        };
    }

    /**
     * The image must be an existing asset in the projects folder (as returned by upload_screenshot).
     */
    public static function imageRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) {
            if ($value === null || $value === '') {
                return;
            }

            if (! str_starts_with($value, static::ASSET_FOLDER.'/')
                || ! AssetContainer::find(static::ASSET_CONTAINER)->asset($value)) {
                $fail('The image must be an asset path returned by upload_screenshot, e.g. "projects/example.ch.png".');
            }
        };
    }

    public static function normalizeYear(string $year): string
    {
        preg_match('/^(\d{4})(?:\s*[-–]\s*(\d{4}))?$/u', trim($year), $m);

        return isset($m[2]) ? "{$m[1]} – {$m[2]}" : $m[1];
    }

    /**
     * Derive the default title from a URL, the same way existing projects are named (e.g. "strut.ch").
     */
    public static function titleFromUrl(string $url): string
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return preg_replace('/^www\./', '', $host);
    }

    /**
     * Always query through the collection: a cold Stache queried with where('collection', ...)
     * before the collection is loaded only indexes one entry (Statamic core bug).
     */
    public static function query()
    {
        return Collection::findOrFail(static::COLLECTION)->queryEntries();
    }

    /**
     * Find a single project by id or slug. Throws a validation error the model can act on.
     */
    public static function find(?string $slug, ?string $id = null): EntryContract
    {
        if ($id) {
            $entry = Entry::find($id);

            if (! $entry || $entry->collectionHandle() !== static::COLLECTION) {
                throw ValidationException::withMessages(['id' => "No project with id \"{$id}\" exists."]);
            }

            return $entry;
        }

        if (! $slug) {
            throw ValidationException::withMessages(['slug' => 'Provide the project slug (see list_projects).']);
        }

        $matches = static::query()->get()->filter(fn ($entry) => static::slugOf($entry) === $slug)->values();

        if ($matches->isEmpty()) {
            throw ValidationException::withMessages(['slug' => "No project with slug \"{$slug}\" exists. Use list_projects to see all slugs."]);
        }

        if ($matches->count() > 1) {
            $options = $matches->map(fn ($e) => "{$e->id()} ({$e->get('title')}, {$e->get('year')})")->implode('; ');

            throw ValidationException::withMessages(['slug' => "Several projects share the slug \"{$slug}\". Call again with the id instead: {$options}."]);
        }

        return $matches->first();
    }

    /**
     * The slug shown to the model. Entries created by the old import script have UUID filenames,
     * so Statamic uses the id as their slug; their intended slug is kept in the "slug" field.
     */
    public static function slugOf(EntryContract $entry): string
    {
        return $entry->slug() === $entry->id() && is_string($entry->get('slug'))
            ? $entry->get('slug')
            : $entry->slug();
    }

    public static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title, '-', Site::default()->lang());
        $slug = $base;
        $i = 2;

        $taken = static::query()->get()->map(fn ($entry) => static::slugOf($entry));

        while ($taken->contains($slug)) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * Find a client/agency term by title (case-insensitive) or slug, creating it when it does not exist.
     *
     * @return array{0: TermContract, 1: bool} The term and whether it was created.
     */
    public static function resolveTerm(string $name): array
    {
        $name = trim($name);
        $slug = Str::slug($name, '-', Site::default()->lang());

        $existing = Taxonomy::findOrFail(static::TAXONOMY)->queryTerms()->get()->first(function ($term) use ($name, $slug) {
            return mb_strtolower(trim((string) $term->title())) === mb_strtolower($name) || $term->slug() === $slug;
        });

        if ($existing) {
            return [$existing, false];
        }

        $term = Term::make()->taxonomy(static::TAXONOMY)->slug($slug)->data(['title' => $name]);
        $term->save();

        return [$term, true];
    }

    /**
     * Apply validated input to an entry. Returns the titles of any client/agency terms that were newly created.
     *
     * @return array<int, string>
     */
    public static function fill(EntryContract $entry, array $input): array
    {
        $created = [];

        if (array_key_exists('url', $input)) {
            $entry->set('link', $input['url']);
        }

        foreach (['title', 'description'] as $field) {
            if (array_key_exists($field, $input) && $input[$field] !== null) {
                $entry->set($field, trim($input[$field]));
            }
        }

        if (array_key_exists('year', $input)) {
            $entry->set('year', static::normalizeYear($input['year']));
        }

        if (array_key_exists('image', $input)) {
            $input['image'] ? $entry->set('image', $input['image']) : $entry->remove('image');
        }

        foreach (['client', 'agency'] as $field) {
            if (! array_key_exists($field, $input)) {
                continue;
            }

            if (! $input[$field]) {
                $entry->remove($field);

                continue;
            }

            [$term, $wasCreated] = static::resolveTerm($input[$field]);
            $entry->set($field, $term->slug());

            if ($wasCreated) {
                $created[] = $term->title();
            }
        }

        return $created;
    }

    public static function make(): EntryContract
    {
        return Entry::make()
            ->collection(static::COLLECTION)
            ->blueprint(static::BLUEPRINT)
            ->locale(Site::default()->handle());
    }

    /**
     * Save a new entry and append it to the collection tree, as the Control Panel does.
     */
    public static function create(EntryContract $entry): void
    {
        $tree = Collection::find(static::COLLECTION)->structure()?->in(Site::default()->handle());

        if ($tree) {
            $entry->afterSave(fn ($entry) => $tree->appendTo(null, $entry)->save());
        }

        $entry->save();
    }

    /**
     * Fields a project needs before it can be published.
     *
     * @return array<int, string>
     */
    public static function missingForPublishing(EntryContract $entry): array
    {
        return collect(['link' => 'url', 'description' => 'description', 'year' => 'year', 'client' => 'client', 'image' => 'image'])
            ->filter(fn ($label, $field) => blank($entry->get($field)))
            ->values()
            ->all();
    }

    public static function summary(EntryContract $entry): array
    {
        return [
            'id' => $entry->id(),
            'slug' => static::slugOf($entry),
            'title' => $entry->get('title'),
            'client' => static::termTitle($entry->get('client')),
            'year' => filled($entry->get('year')) ? (string) $entry->get('year') : null,
            'url' => $entry->get('link'),
            'published' => $entry->published(),
        ];
    }

    public static function details(EntryContract $entry): array
    {
        return [
            ...static::summary($entry),
            'agency' => static::termTitle($entry->get('agency')),
            'description' => $entry->get('description'),
            'image' => $entry->get('image'),
            'image_url' => static::imageUrl($entry->get('image')),
            'edit_url' => url($entry->editUrl()),
        ];
    }

    public static function imageUrl(?string $path): ?string
    {
        $container = AssetContainer::find(static::ASSET_CONTAINER);

        return $path && $container->disk()->exists($path)
            ? $container->asset($path)?->absoluteUrl()
            : null;
    }

    public static function termTitle(?string $slug): ?string
    {
        if (! $slug) {
            return null;
        }

        return Term::find(static::TAXONOMY.'::'.$slug)?->title() ?? $slug;
    }

    /**
     * Record every write operation made through MCP.
     */
    public static function logWrite(string $tool, ?string $slug, array $context = []): void
    {
        Log::channel('mcp')->info("{$tool}".($slug ? " {$slug}" : ''), [
            'tool' => $tool,
            'slug' => $slug,
            'user' => auth()->user()?->email(),
            'timestamp' => now()->toIso8601String(),
            ...$context,
        ]);
    }
}
