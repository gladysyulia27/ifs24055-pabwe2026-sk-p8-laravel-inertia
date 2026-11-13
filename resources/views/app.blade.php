<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="BlogVerse Pro — Platform publikasi artikel, ide, cerita, dan pengetahuan komunitas penulis Indonesia.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>{{ config('app.name', 'BlogVerse Pro') }}</title>
    <style>{!! file_get_contents(resource_path('css/app.css')) !!}</style>
    @viteReactRefresh
    @vite(['resources/js/app.jsx'])
    @inertiaHead
</head>
<body>
    @php
        $component = $page['component'] ?? '';
        $props = $page['props'] ?? [];
    @endphp
    <div id="app" data-page="{{ json_encode($page) }}">
        <div class="public-shell">
            <header class="topbar">
                <div class="topbar-inner">
                    <a href="/" class="brand"><span class="brand-mark">B</span><span>BlogVerse <span class="brand-light">Pro</span></span></a>
                    <nav class="top-links" aria-label="Navigasi Utama"><a href="/">Beranda</a><a href="/articles">Jelajahi Artikel</a></nav>
                </div>
            </header>
            <main class="public-main">
                @if($component === 'Public/PostShow' && !empty($props['post']))
                    <article class="reading-layout">
                        <a href="/articles" class="back-link">← Kembali ke artikel</a>
                        <div class="reading-head">
                            <span class="category-tag static">{{ $props['post']['category'] ?? 'Artikel' }}</span>
                            <h1>{{ $props['post']['title'] ?? 'Artikel BlogVerse' }}</h1>
                            <p class="reading-excerpt">{{ $props['post']['excerpt'] ?? '' }}</p>
                        </div>
                    </article>
                @elseif($component === 'Public/PostIndex' && !empty($props['featured']))
                    <section class="hero">
                        <div class="hero-content">
                            <div class="eyebrow-pill">IDEAS THAT MATTER</div>
                            <h1>Setiap ide punya<br><em>cerita.</em> Bagikan di sini.</h1>
                            <p>Temukan perspektif baru, pelajari hal menarik, dan bagikan pemikiranmu bersama komunitas penulis yang terus berkembang.</p>
                        </div>
                    </section>
                @else
                    <section class="section-wrap">
                        <h1>{{ config('app.name', 'BlogVerse Pro') }}</h1>
                        <p>Ruang untuk ide, cerita, dan pengetahuan.</p>
                    </section>
                @endif
            </main>
        </div>
    </div>
</body>
</html>
