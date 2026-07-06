<?php

namespace Foorintodev\Seo\Http\Controllers;

use Foorintodev\Seo\Redirects;
use Illuminate\Http\Request;
use Statamic\Http\Controllers\CP\CpController;

class RedirectsController extends CpController
{
    public function __construct(Request $request, protected Redirects $redirects)
    {
        parent::__construct($request);
    }

    public function index()
    {
        $redirects = collect($this->redirects->all())
            ->map(fn ($to, $from) => ['from' => $from, 'to' => $to])
            ->sortBy('from')
            ->values();

        return view('statamic-seo::cp.redirects', [
            'title' => __('Redirections'),
            'redirects' => $redirects,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from' => 'required|string',
            'to' => 'required|string',
        ]);

        $this->redirects->add($data['from'], $data['to']);

        return redirect()->back()->with('success', __('Redirection enregistrée.'));
    }

    public function destroy(Request $request)
    {
        $this->redirects->forget((string) $request->input('from'));

        return redirect()->back()->with('success', __('Redirection supprimée.'));
    }
}
