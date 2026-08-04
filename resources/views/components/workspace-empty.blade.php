@props(['icon' => 'OHC', 'title', 'copy'])
<div {{ $attributes->merge(['class' => 'workspace-empty']) }}>
    <span aria-hidden="true">{{ $icon }}</span>
    <h2>{{ $title }}</h2>
    <p>{{ $copy }}</p>
    @isset($action)<div>{{ $action }}</div>@endisset
</div>
