@extends('layouts.app')

@push('styles')
<style>
    body {
        @if (!empty($uploadedFile))
            background: url("{{ asset('storage/' . $uploadedFile) }}") no-repeat center center fixed;
            background-size: cover;
        @else
            background-color: {{ e($color ?? 'white') }};
        @endif
    }
</style>
@endpush

@section('content')

<div id="clock" style="color: white; font-size: 10rem; text-align:center"></div>

<x-dropdown :trigger="'Menu'">
    <h1>Choose A Theme</h1>

    {{-- File Upload Form --}}
    <h2>Choose an Image Theme</h2>
    <form action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" class="block w-full border p-2 mb-4">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Upload</button>
    </form>

    {{-- File Upload Messages --}}
    @if($errors->has('file'))
        <p class="text-red-500">{{ $errors->first('file') }}</p>
    @endif
    @if(session('error'))
        <p class="text-red-500">{{ session('error') }}</p>
    @endif
    @if(session('success'))
        <p class="text-green-500">{{ session('success') }}</p>
    @endif

    {{-- Remove Image Button --}}
    @if (!empty($uploadedFile))
        <form action="{{ route('remove.image') }}" method="POST">
            @csrf
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded mt-2">Remove Image</button>
        </form>
    @endif

    {{-- Color Picker Form --}}
    <h2>Or Pick a Color Theme</h2>
    <form action="{{ route('save.color') }}" method="POST">
        @csrf
        <div id="colorPicker"></div>
        <input type="hidden" name="color" id="colorInput" value="{{ e($color ?? 'white') }}">
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded mt-2">Save Color</button>
    </form>

    {{-- Color Picker Messages --}}
    @if($errors->has('color'))
        <p class="text-red-500">{{ $errors->first('color') }}</p>
    @endif
</x-dropdown>

{{-- Include Pickr.js for Color Selection --}}
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css">

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let clockColor = @json($color ?? 'white');

        const pickr = Pickr.create({
            el: '#colorPicker',
            theme: 'classic',
            default: clockColor,
            swatches: ['#ff0000', '#00ff00', '#0000ff', '#ffffff', '#000000'],
            components: {
                preview: true,
                opacity: true,
                hue: true,
                interaction: {
                    hex: true,
                    rgba: true,
                    input: true,
                    clear: true,
                    save: true
                }
            }
        });

        pickr.on('save', (color) => {
            document.getElementById('colorInput').value = color.toHEXA().toString();
            pickr.hide(); // Close color picker after selection
        });

        function formatTime(date) {
            let hours = String(date.getHours() % 12 || 12).padStart(2, '0');
            let minutes = String(date.getMinutes()).padStart(2, '0');
            let seconds = String(date.getSeconds()).padStart(2, '0');
            let ampm = date.getHours() >= 12 ? 'PM' : 'AM';
            return `${hours}:${minutes}:${seconds} ${ampm}`;
        }

        function updateClock() {
            document.getElementById('clock').textContent = formatTime(new Date());
        }

        updateClock();
        setInterval(updateClock, 1000);
    });
</script>

@endsection
