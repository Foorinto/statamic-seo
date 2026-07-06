<?php

namespace Foorintodev\Seo\Listeners;

use Foorintodev\Seo\Redirects;
use Statamic\Events\EntrySaved;
use Statamic\Facades\Entry;

/**
 * À chaque enregistrement d'entrée, si le slug a changé et que la case
 * « Créer une redirection » est cochée, on ajoute une redirection 301
 * de l'ancienne URL vers la nouvelle.
 */
class CreateRedirectOnSlugChange
{
    public function __construct(protected Redirects $redirects)
    {
    }

    public function handle(EntrySaved $event): void
    {
        $entry = $event->entry;

        if (! $entry->value('create_redirect')) {
            return;
        }

        $oldSlug = $entry->getOriginal('slug');
        $newSlug = $entry->slug();

        if (! $oldSlug || $oldSlug === $newSlug) {
            return;
        }

        $newPath = $this->pathOf($entry->url());
        $oldPath = $this->swapLastSegment($newPath, $newSlug, $oldSlug);

        if ($oldPath === null) {
            return;
        }

        // La page elle-même.
        $this->redirects->add($oldPath, $newPath);

        // Ses descendants : leur URL change car le segment parent change. On remplace
        // le préfixe parent (nouveau) par l'ancien pour reconstituer leur ancienne URL.
        if ($entry->collection()->hasStructure()) {
            $this->redirectDescendants($entry, $oldPath, $newPath);
        }
    }

    protected function redirectDescendants($entry, string $parentOldPath, string $parentNewPath): void
    {
        $prefix = $parentNewPath.'/';

        Entry::query()
            ->where('collection', $entry->collectionHandle())
            ->where('site', $entry->locale())
            ->get()
            ->each(function ($descendant) use ($entry, $parentOldPath, $parentNewPath, $prefix) {
                if ($descendant->id() === $entry->id()) {
                    return;
                }

                $descendantNew = $this->pathOf($descendant->url());

                if (! str_starts_with($descendantNew, $prefix)) {
                    return;
                }

                $descendantOld = $parentOldPath.substr($descendantNew, strlen($parentNewPath));

                $this->redirects->add($descendantOld, $descendantNew);
            });
    }

    protected function pathOf(?string $url): string
    {
        return '/'.ltrim(parse_url((string) $url, PHP_URL_PATH) ?: '/', '/');
    }

    /** Remplace le dernier segment du chemin (le slug) par l'ancien slug. */
    protected function swapLastSegment(string $path, string $newSlug, string $oldSlug): ?string
    {
        $trimmed = trim($path, '/');

        if ($trimmed === '') {
            return null;
        }

        $segments = explode('/', $trimmed);

        if (end($segments) !== $newSlug) {
            return null;
        }

        $segments[count($segments) - 1] = $oldSlug;

        return '/'.implode('/', $segments);
    }
}
