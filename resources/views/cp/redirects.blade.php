@extends('statamic::layout')

@section('title', __('Redirections'))

@section('content')
    <div class="max-w-4xl mx-auto p-4">

        <header class="mb-6">
            <h1 class="text-xl font-bold">{{ __('Redirections') }}</h1>
            <p class="text-sm text-gray-600 mt-1">
                {{ __('Redirections 301. Créées automatiquement au changement de slug, ou ajoutées à la main ci-dessous.') }}
            </p>
        </header>

        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-100 border border-green-200 text-green-800 px-4 py-2 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-100 border border-red-200 text-red-800 px-4 py-2 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form method="POST" action="{{ cp_route('foorintodev-seo.redirects.store') }}"
                  class="flex flex-wrap gap-3 items-end">
                @csrf
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Ancien chemin') }}</label>
                    <input name="from" value="{{ old('from') }}" placeholder="/ancienne-page" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm font-mono">
                </div>
                <div class="text-gray-400 pb-2">→</div>
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Nouveau chemin') }}</label>
                    <input name="to" value="{{ old('to') }}" placeholder="/nouvelle-page" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm font-mono">
                </div>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md px-4 py-2 text-sm">
                    {{ __('Ajouter') }}
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if ($redirects->isEmpty())
                <p class="p-8 text-center text-gray-500 text-sm">
                    {{ __('Aucune redirection pour le moment.') }}
                </p>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 font-medium">{{ __('Ancien chemin') }}</th>
                            <th class="px-4 py-3"></th>
                            <th class="px-4 py-3 font-medium">{{ __('Nouveau chemin') }}</th>
                            <th class="px-4 py-3 text-right font-medium w-px">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($redirects as $redirect)
                            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-gray-800">{{ $redirect['from'] }}</td>
                                <td class="px-4 py-3 text-gray-400">→</td>
                                <td class="px-4 py-3 font-mono text-gray-800">{{ $redirect['to'] }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <form method="POST" action="{{ cp_route('foorintodev-seo.redirects.destroy') }}"
                                          onsubmit="return confirm('{{ __('Supprimer cette redirection ?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="from" value="{{ $redirect['from'] }}">
                                        <button type="submit" class="text-red-600 hover:text-red-800 hover:underline">
                                            {{ __('Supprimer') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
@endsection
