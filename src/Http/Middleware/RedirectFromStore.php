<?php

namespace Foorintodev\Seo\Http\Middleware;

use Closure;
use Foorintodev\Seo\Redirects;

/**
 * Applique les redirections 301 enregistrées avant que Statamic ne traite la requête.
 * Les « sources » ne correspondent qu'à d'anciens slugs disparus : aucun risque de
 * masquer une page existante.
 */
class RedirectFromStore
{
    public function __construct(protected Redirects $redirects)
    {
    }

    public function handle($request, Closure $next)
    {
        $from = $request->getPathInfo();
        $to = $this->redirects->lookup($from);

        if ($to && $to !== $this->redirects->normalize($from)) {
            return redirect($to, 301);
        }

        return $next($request);
    }
}
