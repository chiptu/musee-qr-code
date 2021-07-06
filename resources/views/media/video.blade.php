<div>
    <video controls>
        <source src="{{Storage::url($media->url)}}" type="video/mp4">
        <p>Votre navigateur ne prend pas en charge les vidéos HTML5.
            Voici <a href="{{Storage::url($media->url)}}">un lien pour télécharger la vidéo</a>.</p>
    </video>
</div>
