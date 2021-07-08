@extends('index')
<style type="text/css">
    html {
        background-color: {{$museum->color}};

        @if($museum->color != 'white')
            color: beige;
    @endif
}
</style>
<section class="text-center">
    <div class="p-5 flex justify-center">
       <img width="50px" class="rounded" src="/{{ $museum->logo }}" alt="{{ $museum->name }}">
    </div>

    <h1 class="mt-6 px-8 text-5xl capitalize text-left font-bold">{{ $museum->name }}</h1>

    <div class="p-8 flex flex-col items-center">
        <div class="text-left mt-4">
            {{$museum->description}}
        </div>

    </div>

</section>
