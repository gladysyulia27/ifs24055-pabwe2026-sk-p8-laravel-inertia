<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="BlogVerse Pro — Platform publikasi artikel, ide, cerita, dan pengetahuan komunitas penulis Indonesia.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>{{ config('app.name', 'BlogVerse Pro') }}</title>
    @php
        static $inlineCss = null;
        if ($inlineCss === null) {
            $rawCss = @file_get_contents(resource_path('css/app.css')) ?: '';
            $inlineCss = preg_replace('/\s+/', ' ', preg_replace('/\/\*.*?\*\//s', '', $rawCss));
        }
        $component = $page['component'] ?? '';
        $props = $page['props'] ?? [];
        $authUser = $props['auth']['user'] ?? null;
        $isDashboardComponent = str_starts_with($component, 'Post/') || str_starts_with($component, 'Profile/') || str_starts_with($component, 'Admin/');
        $isAdminComponent = str_starts_with($component, 'Admin/');
    @endphp
    <style>{!! $inlineCss !!}</style>
    @viteReactRefresh
    @vite(['resources/js/app.jsx'])
    @inertiaHead
</head>
<body>
    <div id="app" data-page="{{ json_encode($page) }}">
        @if($isDashboardComponent)
            <div class="dashboard-shell">
                <aside class="sidebar" aria-label="Menu Samping">
                    <div>
                        <a href="/" class="brand sidebar-brand">
                            <span class="brand-mark">B</span>
                            <span>BlogVerse <span class="brand-light">Pro</span></span>
                        </a>
                        <div class="user-card">
                            <div class="avatar-circle" aria-hidden="true">{{ substr($authUser['name'] ?? 'U', 0, 1) }}</div>
                            <div>
                                <b>{{ $authUser['name'] ?? 'Penulis' }}</b>
                                <small>{{ '@' . ($authUser['username'] ?? 'writer') }}</small>
                            </div>
                        </div>
                        <span class="menu-caption">{{ $isAdminComponent ? 'ADMIN PANEL' : 'WORKSPACE' }}</span>
                        <nav class="side-nav" aria-label="Menu Dashboard">
                            @if($isAdminComponent || ($authUser['role'] ?? '') === 'admin')
                                <a href="/admin/users" class="{{ str_contains($component, 'User') ? 'active' : '' }}">Pengguna</a>
                                <a href="/admin/posts" class="{{ $component === 'Admin/PostList' ? 'active' : '' }}">Semua Artikel</a>
                            @else
                                <a href="/dashboard/posts" class="{{ str_starts_with($component, 'Post/') ? 'active' : '' }}">Artikel Saya</a>
                            @endif
                            <a href="/profile" class="{{ str_starts_with($component, 'Profile/') ? 'active' : '' }}">Profil</a>
                        </nav>
                    </div>
                    <div class="sidebar-bottom">
                        <a href="/articles">← Jelajahi Blog</a>
                        <button type="button" class="logout-btn">Keluar</button>
                    </div>
                </aside>
                <div class="dashboard-body">
                    <header class="dashboard-mobile-bar">
                        <a href="/" class="brand">
                            <span class="brand-mark">B</span>
                            <span>BlogVerse</span>
                        </a>
                        <button type="button" class="mobile-toggle" aria-label="Buka menu navigasi">☰</button>
                    </header>
                    <main class="dashboard-main" id="main-content">
                        @if($component === 'Post/Index')
                            @php
                                $stats = $props['stats'] ?? ['total' => 0, 'published' => 0, 'draft' => 0];
                                $postsData = $props['posts']['data'] ?? [];
                            @endphp
                            <div class="page-heading">
                                <div>
                                    <span class="eyebrow">CONTENT STUDIO</span>
                                    <h1>Manajemen artikel<span class="heading-dot">.</span></h1>
                                    <p>Tulis, perbarui, impor, ekspor, dan buat draft artikel berbantuan AI.</p>
                                </div>
                                <div class="toolbar-actions">
                                    <button type="button" class="btn btn-outline">AI Generator</button>
                                    <a href="/dashboard/posts/export" class="btn btn-outline">Ekspor Excel</a>
                                    <button type="button" class="btn btn-primary">Artikel Baru</button>
                                </div>
                            </div>
                            <div class="stats-grid">
                                <div class="stat-box"><span>TOTAL ARTIKEL</span><b>{{ $stats['total'] ?? 0 }}</b></div>
                                <div class="stat-box"><span class="green">PUBLISHED</span><b>{{ $stats['published'] ?? 0 }}</b></div>
                                <div class="stat-box"><span class="amber">DRAFT</span><b>{{ $stats['draft'] ?? 0 }}</b></div>
                            </div>
                            <div class="dashboard-filter-bar">
                                <div class="search-input">
                                    <input id="dashboard-post-search" aria-label="Cari judul atau ringkasan artikel" placeholder="Cari judul atau ringkasan artikel..." value="">
                                </div>
                                <div class="filter-tabs" role="group" aria-label="Filter status artikel">
                                    <button type="button" class="filter-tab active">Semua ({{ $stats['total'] ?? 0 }})</button>
                                    <button type="button" class="filter-tab">Published ({{ $stats['published'] ?? 0 }})</button>
                                    <button type="button" class="filter-tab">Draft ({{ $stats['draft'] ?? 0 }})</button>
                                </div>
                            </div>
                            <div class="list-table-wrap">
                                <table class="list-table">
                                    <thead>
                                        <tr>
                                            <th>ARTIKEL</th>
                                            <th>KATEGORI</th>
                                            <th>STATUS</th>
                                            <th>DIPERBARUI</th>
                                            <th>AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($postsData as $p)
                                            <tr>
                                                <td>
                                                    <div class="post-title-cell">
                                                        <div>
                                                            <b>{{ $p['title'] ?? '' }}</b>
                                                            <small>{{ $p['excerpt'] ?? '' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="category-soft">{{ $p['category'] ?? '' }}</span></td>
                                                <td><span class="status {{ !empty($p['is_published']) ? 'published' : 'draft' }}">{{ !empty($p['is_published']) ? 'Published' : 'Draft' }}</span></td>
                                                <td class="date-cell">{{ !empty($p['updated_at']) ? substr($p['updated_at'], 0, 10) : '—' }}</td>
                                                <td>
                                                    <div class="row-actions">
                                                        <a href="/dashboard/posts/{{ $p['id'] }}" class="icon-action" aria-label="Lihat artikel {{ $p['title'] ?? '' }}">Lihat</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="empty-row">Belum ada artikel yang cocok dengan filter ini.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @elseif($component === 'Profile/Edit')
                            @php $u = $props['user'] ?? $authUser ?? []; @endphp
                            <div class="page-heading">
                                <div>
                                    <span class="eyebrow">ACCOUNT SETTINGS</span>
                                    <h1>Profil penulis<span class="heading-dot">.</span></h1>
                                    <p>Perbarui informasi akun dan identitas publikmu.</p>
                                </div>
                            </div>
                            <form class="profile-form" action="/profile" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-field">
                                    <label for="profile-name">Nama lengkap</label>
                                    <input id="profile-name" name="name" type="text" value="{{ $u['name'] ?? '' }}">
                                </div>
                                <div class="form-field">
                                    <label for="profile-username">Username</label>
                                    <input id="profile-username" name="username" type="text" value="{{ $u['username'] ?? '' }}">
                                </div>
                                <div class="form-field">
                                    <label for="profile-email">Email</label>
                                    <input id="profile-email" name="email" type="email" value="{{ $u['email'] ?? '' }}">
                                </div>
                                <div class="form-field">
                                    <label for="profile-bio">Bio</label>
                                    <textarea id="profile-bio" name="bio" rows="4" placeholder="Ceritakan sedikit tentang dirimu...">{{ $u['bio'] ?? '' }}</textarea>
                                </div>
                                <button id="profile-submit" type="submit" class="btn btn-primary">Simpan perubahan</button>
                            </form>
                        @elseif($component === 'Admin/UserList')
                            @php $usersData = $props['users']['data'] ?? []; @endphp
                            <div class="page-heading">
                                <div>
                                    <span class="eyebrow">ADMINISTRATION</span>
                                    <h1>Manajemen pengguna<span class="heading-dot">.</span></h1>
                                    <p>Pantau seluruh akun dan aktivitas penulis BlogVerse.</p>
                                </div>
                            </div>
                            <div class="list-table-wrap">
                                <table class="list-table">
                                    <thead>
                                        <tr>
                                            <th>PENGGUNA</th>
                                            <th>EMAIL</th>
                                            <th>ROLE</th>
                                            <th>ARTIKEL</th>
                                            <th>AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($usersData as $u)
                                            <tr>
                                                <td><b>{{ $u['name'] ?? '' }}</b> <small>{{ '@' . ($u['username'] ?? '') }}</small></td>
                                                <td>{{ $u['email'] ?? '' }}</td>
                                                <td><span class="status {{ ($u['role'] ?? '') === 'admin' ? 'published' : 'draft' }}">{{ ($u['role'] ?? '') === 'admin' ? 'Admin' : 'Member' }}</span></td>
                                                <td>{{ $u['posts_count'] ?? 0 }}</td>
                                                <td><a class="table-link" href="/admin/users/{{ $u['id'] }}/posts">Lihat artikel</a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif($component === 'Admin/PostList' || $component === 'Admin/UserPostList')
                            @php $postsData = $props['posts']['data'] ?? []; @endphp
                            <div class="page-heading">
                                <div>
                                    <span class="eyebrow">CONTENT MONITORING</span>
                                    <h1>{{ $component === 'Admin/UserPostList' ? ($props['author']['name'] ?? 'Artikel Penulis') : 'Semua artikel' }}<span class="heading-dot">.</span></h1>
                                    <p>Monitoring seluruh artikel dalam sistem. Data bersifat read-only.</p>
                                </div>
                                <a href="/admin/posts/export" class="btn btn-primary">Ekspor Excel</a>
                            </div>
                            <div class="list-table-wrap">
                                <table class="list-table">
                                    <thead>
                                        <tr>
                                            <th>ARTIKEL</th>
                                            <th>PENULIS</th>
                                            <th>KATEGORI</th>
                                            <th>STATUS</th>
                                            <th>DITERBITKAN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($postsData as $p)
                                            <tr>
                                                <td><b>{{ $p['title'] ?? '' }}</b></td>
                                                <td>{{ $p['user']['name'] ?? '' }}</td>
                                                <td><span class="category-soft">{{ $p['category'] ?? '' }}</span></td>
                                                <td><span class="status {{ !empty($p['is_published']) ? 'published' : 'draft' }}">{{ !empty($p['is_published']) ? 'Published' : 'Draft' }}</span></td>
                                                <td class="date-cell">{{ !empty($p['published_at']) ? substr($p['published_at'], 0, 10) : '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            @php $p = $props['post'] ?? []; @endphp
                            <div class="page-heading">
                                <div>
                                    <a href="/dashboard/posts" class="back-link">← Kembali ke daftar</a>
                                    <span class="eyebrow">ARTICLE PREVIEW</span>
                                    <h1>{{ $p['title'] ?? 'Detail Artikel' }}</h1>
                                    <p>{{ ($p['category'] ?? 'Artikel') . ' · ' . (!empty($p['is_published']) ? 'Published' : 'Draft') }}</p>
                                </div>
                                <div class="toolbar-actions">
                                    <a href="/dashboard/posts" class="btn btn-outline">Daftar artikel</a>
                                    <button type="button" class="btn btn-primary">{{ !empty($p['is_published']) ? 'Jadikan Draft' : 'Publikasikan' }}</button>
                                </div>
                            </div>
                            <div class="detail-tools">
                                <label class="btn btn-outline btn-sm">
                                    Unggah cover
                                    <input type="file" aria-label="Unggah gambar cover" accept="image/*" hidden>
                                </label>
                                <button type="button" class="btn btn-danger btn-sm">Hapus artikel</button>
                            </div>
                            <div class="detail-paper">
                                <div class="detail-excerpt">{{ $p['excerpt'] ?? '' }}</div>
                                <div class="markdown-view">
                                    <p>{{ $p['content'] ?? $p['excerpt'] ?? '' }}</p>
                                </div>
                            </div>
                        @endif
                    </main>
                </div>
            </div>
        @else
            <div class="public-shell">
                <header class="topbar">
                    <div class="topbar-inner">
                        <a href="/" class="brand">
                            <span class="brand-mark">B</span>
                            <span>BlogVerse <span class="brand-light">Pro</span></span>
                        </a>
                        <nav class="top-links" aria-label="Navigasi Utama">
                            <a href="/">Beranda</a>
                            <a href="/articles">Jelajahi Artikel</a>
                        </nav>
                        <div class="top-actions">
                            @if($authUser)
                                <a href="{{ ($authUser['role'] ?? '') === 'admin' ? '/admin/users' : '/dashboard/posts' }}" class="btn btn-outline">Dashboard</a>
                            @else
                                <a href="/login" class="text-link">Masuk</a>
                                <a href="/register" class="btn btn-primary">Mulai Menulis</a>
                            @endif
                            <button type="button" class="mobile-toggle" aria-label="Buka menu navigasi">☰</button>
                        </div>
                    </div>
                </header>
                <main class="public-main" id="main-content">
                    @if($component === 'Public/PostShow' && !empty($props['post']))
                        @php $post = $props['post']; @endphp
                        <article class="reading-layout">
                            <a href="/articles" class="back-link">← Kembali ke artikel</a>
                            <div class="reading-head">
                                <span class="category-tag static">{{ $post['category'] ?? 'Artikel' }}</span>
                                <h1>{{ $post['title'] ?? 'Artikel BlogVerse' }}</h1>
                                <p class="reading-excerpt">{{ $post['excerpt'] ?? '' }}</p>
                                <div class="reading-byline">
                                    <div class="avatar-mini" aria-hidden="true">{{ substr($post['user']['name'] ?? 'P', 0, 1) }}</div>
                                    <span>
                                        <b>{{ $post['user']['name'] ?? 'Penulis' }}</b>
                                        <small>{{ !empty($post['published_at']) ? substr($post['published_at'], 0, 10) : '' }}</small>
                                    </span>
                                    <span class="reading-time">Artikel BlogVerse</span>
                                </div>
                            </div>
                            <div class="markdown-view">
                                <p>{{ $post['excerpt'] ?? '' }}</p>
                            </div>
                            <div class="reading-community">
                                <div class="community-icon" aria-hidden="true">✳</div>
                                <div>
                                    <h2>Jadilah bagian dari cerita.</h2>
                                    <p>Bergabung dengan komunitas BlogVerse dan bagikan perspektifmu.</p>
                                </div>
                                <div class="community-actions">
                                    <a href="/login" class="text-link">Masuk</a>
                                    <a href="/register" class="btn btn-primary">Daftar</a>
                                </div>
                            </div>
                        </article>
                    @elseif($component === 'Auth/Login')
                        <section class="auth-section">
                            <div class="auth-visual">
                                <span class="eyebrow">WELCOME BACK</span>
                                <h1>Ruang kerja untuk tulisan terbaikmu<span class="heading-dot">.</span></h1>
                                <p>Kelola draft, terbitkan artikel, dan manfaatkan asisten AI untuk mempercepat proses kreatifmu.</p>
                            </div>
                            <form class="auth-card" action="/login" method="post">
                                @csrf
                                <div class="auth-card-head">
                                    <h2>Masuk ke akunmu</h2>
                                    <p>Gunakan email dan kata sandi yang terdaftar.</p>
                                </div>
                                <div class="form-field">
                                    <label for="login-email">Email</label>
                                    <input id="login-email" name="email" type="email" placeholder="nama@email.com" required>
                                </div>
                                <div class="form-field">
                                    <label for="login-password">Kata sandi</label>
                                    <input id="login-password" name="password" type="password" placeholder="••••••••" required>
                                </div>
                                <button id="login-submit" type="submit" class="btn btn-primary btn-block">Masuk</button>
                                <p class="auth-switch">Belum punya akun? <a href="/register">Daftar sekarang</a></p>
                            </form>
                        </section>
                    @elseif($component === 'Auth/Register')
                        <section class="auth-section">
                            <div class="auth-visual">
                                <span class="eyebrow">JOIN THE COMMUNITY</span>
                                <h1>Mulai perjalanan menulismu hari ini<span class="heading-dot">.</span></h1>
                                <p>Buat akun gratis dan publikasikan gagasanmu kepada pembaca di seluruh Indonesia.</p>
                            </div>
                            <form class="auth-card" action="/register" method="post">
                                @csrf
                                <div class="auth-card-head">
                                    <h2>Daftar akun baru</h2>
                                    <p>Lengkapi informasi berikut untuk mulai menulis.</p>
                                </div>
                                <div class="form-field">
                                    <label for="register-name">Nama lengkap</label>
                                    <input id="register-name" name="name" type="text" placeholder="Nama lengkap" required>
                                </div>
                                <div class="form-field">
                                    <label for="register-username">Username</label>
                                    <input id="register-username" name="username" type="text" placeholder="username" required>
                                </div>
                                <div class="form-field">
                                    <label for="register-email">Email</label>
                                    <input id="register-email" name="email" type="email" placeholder="nama@email.com" required>
                                </div>
                                <div class="form-field">
                                    <label for="register-password">Kata sandi</label>
                                    <input id="register-password" name="password" type="password" placeholder="Minimal 8 karakter" required>
                                </div>
                                <div class="form-field">
                                    <label for="register-password-confirmation">Konfirmasi kata sandi</label>
                                    <input id="register-password-confirmation" name="password_confirmation" type="password" placeholder="Ulangi kata sandi" required>
                                </div>
                                <button id="register-submit" type="submit" class="btn btn-primary btn-block">Buat Akun</button>
                                <p class="auth-switch">Sudah punya akun? <a href="/login">Masuk di sini</a></p>
                            </form>
                        </section>
                    @else
                        @php
                            $isFeatured = !empty($props['featured']);
                            $postsList = $props['posts']['data'] ?? [];
                        @endphp
                        @if($isFeatured)
                            <section class="hero">
                                <div class="hero-content">
                                    <div class="eyebrow-pill">IDEAS THAT MATTER</div>
                                    <h1>Setiap ide punya<br><em>cerita.</em> Bagikan di sini.</h1>
                                    <p>Temukan perspektif baru, pelajari hal menarik, dan bagikan pemikiranmu bersama komunitas penulis yang terus berkembang.</p>
                                    <div class="hero-actions">
                                        <a href="/articles" class="btn btn-primary">Jelajahi Artikel</a>
                                        <a href="/register" class="btn btn-outline">Mulai Menulis</a>
                                    </div>
                                </div>
                            </section>
                        @endif
                        <section class="section-wrap">
                            <div class="section-head">
                                <div>
                                    <span class="eyebrow">EXPLORE</span>
                                    @if($isFeatured)
                                        <h2>Semua Artikel<span class="heading-dot">.</span></h2>
                                    @else
                                        <h1>Semua Artikel<span class="heading-dot">.</span></h1>
                                    @endif
                                </div>
                                <form class="search-bar" action="/articles" method="get">
                                    <div class="search-input">
                                        <input name="search" aria-label="Cari artikel berdasarkan judul atau isi" placeholder="Cari artikel berdasarkan judul atau isi..." value="{{ $props['filters']['search'] ?? '' }}">
                                    </div>
                                    <select name="category" aria-label="Filter kategori artikel">
                                        <option value="">Semua Kategori</option>
                                    </select>
                                    <button type="submit" class="btn btn-dark">Filter</button>
                                </form>
                            </div>
                            <div class="article-grid">
                                @foreach(array_slice($postsList, 0, 6) as $post)
                                    <article class="article-card">
                                        <div class="card-body">
                                            <div class="card-meta">
                                                <span class="category-tag">{{ $post['category'] ?? 'Artikel' }}</span>
                                                <span class="dot">•</span>
                                                <span>{{ !empty($post['published_at']) ? substr($post['published_at'], 0, 10) : '' }}</span>
                                            </div>
                                            @if($isFeatured)
                                                <h3><a href="/articles/{{ $post['slug'] ?? '' }}">{{ $post['title'] ?? '' }}</a></h3>
                                            @else
                                                <h2><a href="/articles/{{ $post['slug'] ?? '' }}">{{ $post['title'] ?? '' }}</a></h2>
                                            @endif
                                            <p>{{ $post['excerpt'] ?? '' }}</p>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </main>
                <footer class="public-footer">
                    <div class="footer-inner">
                        <div>
                            <div class="brand">
                                <span class="brand-mark">B</span>
                                <span>BlogVerse <span class="brand-light">Pro</span></span>
                            </div>
                            <p>Ruang untuk ide, cerita, dan pengetahuan.</p>
                        </div>
                        <small>© {{ date('Y') }} BlogVerse Pro. Dibuat untuk berbagi inspirasi.</small>
                    </div>
                </footer>
            </div>
        @endif
    </div>
</body>
</html>
