@php
    $currentRoute = Request::route()->getName() ?? '';
    $isCiente     = str_starts_with($currentRoute, 'cliente') || str_starts_with($currentRoute, 'clientes');
    $role         = $isCiente ? 'cliente' : 'admin';
    $breadcrumbs  = getRouteBreadcrumbs($currentRoute, $role);
    $pageTitle    = end($breadcrumbs)['label'] ?? '';
@endphp

<div class="page-header">
    <h1 class="page-header-title">{{ $pageTitle }}</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $crumb)
                @if ($loop->last)
                    <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
                @else
                    <li class="breadcrumb-item">
                        @if (!empty($crumb['route']))
                            <a href="{{ route($crumb['route']) }}">{{ $crumb['label'] }}</a>
                        @else
                            <span>{{ $crumb['label'] }}</span>
                        @endif
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
</div>
