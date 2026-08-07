@props(['eyebrow' => null, 'title', 'description' => null])
<header class="workspace-heading">
    <div>
        @if($eyebrow)<p>{{ $eyebrow }}</p>@endif
        <h1>{{ $title }}</h1>
        @if($description)<span>{{ $description }}</span>@endif
    </div>
    @isset($actions)<div class="workspace-heading__actions">{{ $actions }}</div>@endisset
</header>
