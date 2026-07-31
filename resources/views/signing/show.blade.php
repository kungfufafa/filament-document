<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign {{ $document->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-3xl px-4 py-10">
        <div class="mb-6">
            <p class="text-sm font-medium text-blue-600">Document signing</p>
            <h1 class="mt-1 text-2xl font-semibold">{{ $document->title }}</h1>
            <p class="mt-2 text-sm text-slate-600">Signed invitation for {{ $recipient->name }} ({{ $recipient->email }})</p>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-slate-500">Status</dt>
                    <dd class="font-medium">{{ $recipient->status->getLabel() }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Document status</dt>
                    <dd class="font-medium">{{ $document->status->getLabel() }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="text-slate-500">File</dt>
                    <dd class="font-medium">{{ $document->original_filename }}</dd>
                </div>
            </dl>

            @if ($recipient->status === \App\Enums\RecipientStatus::Signed)
                <p class="mt-6 text-sm text-green-700">You signed this document{{ $recipient->signed_at ? ' on '.$recipient->signed_at->timezone(config('app.timezone'))->format('d M Y, H:i') : '' }}.</p>
            @else
                <form method="POST" action="{{ route('signing.sign', $recipient->access_token) }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label for="signature" class="mb-1 block text-sm font-medium">Type your signature</label>
                        <input
                            id="signature"
                            name="signature"
                            type="text"
                            required
                            value="{{ old('signature', $recipient->name) }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                        >
                        @error('signature')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @foreach ($fields->where('type', \App\Enums\FieldType::Text) as $field)
                        <div>
                            <label class="mb-1 block text-sm font-medium">Text field (page {{ $field->page }})</label>
                            <input
                                type="text"
                                name="fields[{{ $loop->index }}][value]"
                                required
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            >
                            <input type="hidden" name="fields[{{ $loop->index }}][field_id]" value="{{ $field->id }}">
                        </div>
                    @endforeach

                    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Sign document
                    </button>
                </form>
            @endif
        </div>
    </div>
</body>
</html>
