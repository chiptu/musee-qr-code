@extends('index')
<style type="text/css">
    html {
        background-color: {{$artwork->color}};
    }
</style>
<section class="text-center">
    <div class="p-5 flex justify-center">
        <a href="{{ route('museum.index') }}"><img width="50px" class="rounded" src="/{{ \App\Models\Museum::first()->logo }}" alt="{{ \App\Models\Museum::first()->name }}"></a>
    </div>
    <h1 class="mt-6 px-8 text-5xl capitalize text-left font-bold">{{ $artwork->name }}</h1>
    <div class="p-8 flex flex-col items-center">
        @foreach($medias as $media)
            @switch($media->type)
                @case('App\Model\Text')
                    @include('media.text', $media)
                    @break
                @case('App\Model\Image')
                    @include('media.image', $media)
                    @break
                @case('App\Model\Audio')
                    @include('media.audio', $media)
                    @break
                @case('App\Model\Video')
                    @include('media.video', $media)
                    @break
            @endswitch
        @endforeach
    </div>
</section>

