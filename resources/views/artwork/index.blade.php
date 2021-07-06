@extends('index')

<section class="text-center">
    <div class="p-5 bg-gray-300">
        <h4> {{ $artwork->name }} </h4>
    </div>

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

