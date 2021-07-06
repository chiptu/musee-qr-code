<div class="mt-4">
    <span>{{ $media->name }}</span>
    <audio class="mt-2" controls src="{{ Storage::url($media->url) }}"></audio>
</div>
