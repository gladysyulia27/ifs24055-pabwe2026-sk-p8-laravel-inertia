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
        $flash = $props['flash'] ?? [];
        $isDashboardComponent = str_starts_with($component, 'Post/') || str_starts_with($component, 'Profile/') || str_starts_with($component, 'Admin/');
        $isAdminComponent = str_starts_with($component, 'Admin/');
        $dashTitle = match ($component) {
            'Post/Index' => 'Artikel Saya',
            'Post/Show' => 'Detail Artikel',
            'Profile/Edit' => 'Profil',
            'Admin/UserList' => 'Pengguna',
            'Admin/PostList' => 'Semua Artikel',
            'Admin/UserPostList' => 'Artikel Penulis',
            default => 'Dashboard',
        };
    @endphp
    <style>{!! $inlineCss !!}</style>
    @viteReactRefresh
    @vite(['resources/js/app.jsx'])
    @inertiaHead
</head>
<body>
    <div id="app" data-page="{{ json_encode($page) }}">
        @if($isDashboardComponent)
            <div class="dash-shell">
                <aside class="sidebar" aria-label="Menu Samping">
                    <a href="/" class="brand side-brand">
                        <span class="brand-mark" aria-hidden="true">B</span>
                        <span>BlogVerse <span class="brand-light">Pro</span></span>
                    </a>
                    <div class="side-label">WORKSPACE</div>
                    <nav class="side-links" aria-label="Menu Workspace">
                        @if($isAdminComponent || ($authUser['role'] ?? '') === 'admin')
                            <a href="/admin/users" class="side-link {{ str_contains($component, 'User') ? 'active' : '' }}">Pengguna</a>
                            <a href="/admin/posts" class="side-link {{ $component === 'Admin/PostList' ? 'active' : '' }}">Semua Artikel</a>
                        @else
                            <a href="/dashboard/posts" class="side-link {{ str_starts_with($component, 'Post/') ? 'active' : '' }}">Artikel Saya</a>
                            <a href="/profile" class="side-link {{ str_starts_with($component, 'Profile/') ? 'active' : '' }}">Profil</a>
                        @endif
                    </nav>
                    <div class="side-bottom">
                        <a href="/articles" class="side-link">Lihat Blog</a>
                        <div class="side-user">
                            <div class="avatar-mini" aria-hidden="true">{{ substr($authUser['name'] ?? 'U', 0, 1) }}</div>
                            <div class="side-user-text">
                                <b>{{ $authUser['name'] ?? 'Penulis' }}</b>
                                <small>{{ $isAdminComponent ? 'Administrator' : 'Penulis' }}</small>
                            </div>
                            <form action="/logout" method="post" style="display:inline">
                                @csrf
                                <button type="submit" title="Keluar" aria-label="Keluar dari akun">↪</button>
                            </form>
                        </div>
                    </div>
                </aside>
                <main class="dash-main" id="main-content">
                    <header class="dash-top">
                        <button type="button" class="mobile-toggle dark" aria-label="Buka menu samping" aria-expanded="false">☰</button>
                        <div>
                            <span class="crumb">WORKSPACE / </span>
                            <b>{{ $dashTitle }}</b>
                        </div>
                        <div class="dash-top-right">
                            <span class="online-dot" aria-hidden="true"></span> Sistem Aktif
                            <div class="avatar-mini" aria-hidden="true">{{ substr($authUser['name'] ?? 'U', 0, 1) }}</div>
                        </div>
                    </header>
                    <div class="dash-content">
                        @if(!empty($flash['success']) || !empty($flash['error']))
                            <div role="alert" class="alert {{ !empty($flash['error']) ? 'alert-error' : '' }}">
                                {{ $flash['success'] ?? $flash['error'] }}
                            </div>
                        @endif

                        @if($component === 'Post/Index')
                            @php
                                $stats = $props['stats'] ?? ['total' => 0, 'published' => 0, 'draft' => 0];
                                $postsData = $props['posts']['data'] ?? [];
                                $postsLinks = $props['posts']['links'] ?? [];
                            @endphp
                            <div class="page-heading">
                                <div>
                                    <span class="eyebrow">CONTENT STUDIO</span>
                                    <h1>Artikel saya<span class="heading-dot">.</span></h1>
                                    <p>Kelola ide dan publikasi tulisanmu di satu tempat.</p>
                                </div>
                                <button type="button" class="btn btn-primary">Tulis artikel</button>
                            </div>
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <span>Total Artikel</span>
                                    <b>{{ $stats['total'] ?? 0 }}</b>
                                    <small>Semua tulisanmu</small>
                                </div>
                                <div class="stat-card stat-green">
                                    <span>Total Selesai</span>
                                    <b>{{ $stats['published'] ?? 0 }}</b>
                                    <small>Sudah dipublikasikan</small>
                                </div>
                                <div class="stat-card stat-amber">
                                    <span>Belum Selesai</span>
                                    <b>{{ $stats['draft'] ?? 0 }}</b>
                                    <small>Masih berupa draft</small>
                                </div>
                            </div>
                            <div class="toolbar">
                                <div>
                                    <h2>Daftar tulisan</h2>
                                    <p>Daftar artikel terbaru yang kamu buat.</p>
                                </div>
                                <div class="toolbar-actions">
                                    <button type="button" class="btn btn-outline btn-sm">AI Generator</button>
                                    <form action="/dashboard/posts/import" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <label class="btn btn-outline btn-sm upload-label">
                                            Impor Excel
                                            <input type="file" name="file" aria-label="Impor file Excel" accept=".xlsx,.xls,.csv">
                                        </label>
                                    </form>
                                    <a class="btn btn-outline btn-sm" href="/dashboard/posts/export">Ekspor</a>
                                </div>
                            </div>
                            <div class="list-table-wrap">
                                <table class="list-table">
                                    <thead>
                                        <tr>
                                            <th>JUDUL ARTIKEL</th>
                                            <th>KATEGORI</th>
                                            <th>STATUS</th>
                                            <th>DIPERBARUI</th>
                                            <th><span style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0)">AKSI</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($postsData as $p)
                                            <tr>
                                                <td>
                                                    <div class="post-title-cell">
                                                        <span class="post-file-icon" aria-hidden="true">📄</span>
                                                        <div>
                                                            <b>{{ $p['title'] ?? '' }}</b>
                                                            <small>{{ ($p['excerpt'] ?? '') !== '' ? $p['excerpt'] : 'Tidak ada ringkasan' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="category-soft">{{ $p['category'] ?? '' }}</span></td>
                                                <td>
                                                    <span class="status {{ !empty($p['is_published']) ? 'published' : 'draft' }}">
                                                        <i aria-hidden="true"></i>
                                                        {{ !empty($p['is_published']) ? 'Published' : 'Draft' }}
                                                    </span>
                                                </td>
                                                <td class="date-cell">{{ !empty($p['updated_at']) ? substr($p['updated_at'], 0, 10) : '—' }}</td>
                                                <td class="row-actions">
                                                    <button type="button" class="icon-button" aria-label="Menu aksi untuk {{ $p['title'] ?? '' }}" aria-expanded="false">⋮</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5">
                                                    <div class="empty-state compact">
                                                        <b>Belum ada artikel</b>
                                                        <span>Mulai tulis artikel pertamamu.</span>
                                                        <button type="button" class="btn btn-primary btn-sm">Tulis artikel</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if(!empty($postsLinks))
                                <nav class="pagination" aria-label="Navigasi Halaman">
                                    @foreach($postsLinks as $l)
                                        <a href="{{ $l['url'] ?: '#' }}" class="{{ !empty($l['active']) ? 'active' : '' }} {{ empty($l['url']) ? 'disabled' : '' }}">{!! $l['label'] ?? '' !!}</a>
                                    @endforeach
                                </nav>
                            @endif
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
                            @php
                                $usersData = $props['users']['data'] ?? [];
                                $usersTotal = $props['users']['total'] ?? count($usersData);
                                $usersLinks = $props['users']['links'] ?? [];
                            @endphp
                            <div class="page-heading">
                                <div>
                                    <span class="eyebrow">ADMINISTRATION</span>
                                    <h1>Manajemen pengguna<span class="heading-dot">.</span></h1>
                                    <p>Pantau seluruh akun dan aktivitas penulis BlogVerse.</p>
                                </div>
                                <div class="admin-count">
                                    <b>{{ $usersTotal }}</b>
                                    <span>Total pengguna</span>
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
                                                <td>
                                                    <div class="post-title-cell">
                                                        <span class="avatar-mini" aria-hidden="true">{{ substr($u['name'] ?? 'U', 0, 1) }}</span>
                                                        <div>
                                                            <b>{{ $u['name'] ?? '' }}</b>
                                                            <small>{{ '@' . ($u['username'] ?? '') }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $u['email'] ?? '' }}</td>
                                                <td><span class="status {{ ($u['role'] ?? '') === 'admin' ? 'published' : 'draft' }}">{{ ($u['role'] ?? '') === 'admin' ? 'Admin' : 'Member' }}</span></td>
                                                <td><span class="count-pill">{{ $u['posts_count'] ?? 0 }}</span></td>
                                                <td><a class="table-link" href="/admin/users/{{ $u['id'] }}/posts">Lihat artikel</a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if(!empty($usersLinks))
                                <nav class="pagination" aria-label="Navigasi Halaman">
                                    @foreach($usersLinks as $l)
                                        <a href="{{ $l['url'] ?: '#' }}" class="{{ !empty($l['active']) ? 'active' : '' }} {{ empty($l['url']) ? 'disabled' : '' }}">{!! $l['label'] ?? '' !!}</a>
                                    @endforeach
                                </nav>
                            @endif
                        @elseif($component === 'Admin/PostList' || $component === 'Admin/UserPostList')
                            @php
                                $postsData = $props['posts']['data'] ?? [];
                                $postsLinks = $props['posts']['links'] ?? [];
                                $author = $props['author'] ?? null;
                            @endphp
                            <div class="page-heading">
                                <div>
                                    @if($component === 'Admin/UserPostList' && $author)
                                        <a href="/admin/users" class="back-link">← Kembali ke pengguna</a>
                                        <span class="eyebrow">AUTHOR MONITORING</span>
                                        <h1>{{ $author['name'] ?? 'Artikel Penulis' }}<span class="heading-dot">.</span></h1>
                                        <p>{{ '@' . ($author['username'] ?? '') }} · {{ $author['email'] ?? '' }}</p>
                                    @else
                                        <span class="eyebrow">CONTENT MONITORING</span>
                                        <h1>Semua artikel<span class="heading-dot">.</span></h1>
                                        <p>Monitoring seluruh artikel dalam sistem. Data bersifat read-only.</p>
                                    @endif
                                </div>
                                @if($component === 'Admin/UserPostList' && $author)
                                    <a href="/admin/users/{{ $author['id'] }}/posts/export" class="btn btn-primary">Ekspor artikel</a>
                                @else
                                    <a href="/admin/posts/export" class="btn btn-primary">Ekspor Excel</a>
                                @endif
                            </div>
                            <div class="list-table-wrap">
                                <table class="list-table">
                                    <thead>
                                        <tr>
                                            <th>{{ $component === 'Admin/UserPostList' ? 'JUDUL' : 'ARTIKEL' }}</th>
                                            @if($component !== 'Admin/UserPostList')
                                                <th>PENULIS</th>
                                            @endif
                                            <th>KATEGORI</th>
                                            <th>STATUS</th>
                                            <th>{{ $component === 'Admin/UserPostList' ? 'DIPERBARUI' : 'DITERBITKAN' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($postsData as $p)
                                            <tr>
                                                <td>
                                                    <div class="post-title-cell">
                                                        <span class="post-file-icon" aria-hidden="true">📄</span>
                                                        <div>
                                                            <b>{{ $p['title'] ?? '' }}</b>
                                                            <small>{{ $component === 'Admin/UserPostList' ? ($p['excerpt'] ?? '') : ($p['slug'] ?? '') }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                @if($component !== 'Admin/UserPostList')
                                                    <td>{{ $p['user']['name'] ?? '' }}</td>
                                                @endif
                                                <td><span class="category-soft">{{ $p['category'] ?? '' }}</span></td>
                                                <td><span class="status {{ !empty($p['is_published']) ? 'published' : 'draft' }}">{{ !empty($p['is_published']) ? 'Published' : 'Draft' }}</span></td>
                                                <td class="date-cell">{{ !empty($p['published_at']) ? substr($p['published_at'], 0, 10) : (!empty($p['updated_at']) ? substr($p['updated_at'], 0, 10) : '—') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if(!empty($postsLinks))
                                <nav class="pagination" aria-label="Navigasi Halaman">
                                    @foreach($postsLinks as $l)
                                        <a href="{{ $l['url'] ?: '#' }}" class="{{ !empty($l['active']) ? 'active' : '' }} {{ empty($l['url']) ? 'disabled' : '' }}">{!! $l['label'] ?? '' !!}</a>
                                    @endforeach
                                </nav>
                            @endif
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
                    </div>
                </main>
            </div>
        @else
            <div class="public-shell">
                <header class="topbar">
                    <div class="topbar-inner">
                        <a href="/" class="brand">
                            <span class="brand-mark" aria-hidden="true">B</span>
                            <span>BlogVerse <span class="brand-light">Pro</span></span>
                        </a>
                        <nav class="top-links" aria-label="Navigasi Utama">
                            <a href="/">Beranda</a>
                            <a href="/articles">Jelajahi Artikel</a>
                        </nav>
                        <div class="top-actions">
                            @if($authUser)
                                <div class="user-menu-wrap">
                                    <a href="{{ ($authUser['role'] ?? '') === 'admin' ? '/admin/users' : '/dashboard/posts' }}" class="user-chip" aria-label="Menu akun {{ $authUser['name'] ?? 'Pengguna' }}">
                                        <span class="avatar-mini">{{ substr($authUser['name'] ?? 'U', 0, 1) }}</span>
                                        <span>{{ $authUser['name'] ?? 'Pengguna' }}</span>
                                    </a>
                                </div>
                            @else
                                <a href="/login" class="nav-login">Masuk</a>
                                <a href="/register" class="btn btn-primary btn-sm">Mulai Menulis <span aria-hidden="true">↗</span></a>
                            @endif
                            <button type="button" class="mobile-toggle" aria-label="Buka menu navigasi" aria-expanded="false">☰</button>
                        </div>
                    </div>
                </header>
                <main class="public-main" id="main-content">
                    @if(!empty($flash['success']) || !empty($flash['error']))
                        <div role="alert" class="toast {{ !empty($flash['error']) ? 'toast-error' : '' }}">
                            {{ $flash['success'] ?? $flash['error'] }}
                        </div>
                    @endif

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
                                <p>{{ $post['content'] ?? $post['excerpt'] ?? '' }}</p>
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
                        <div class="auth-wrap">
                            <div class="auth-art">
                                <div class="auth-art-content">
                                    <span class="eyebrow">WELCOME BACK</span>
                                    <div class="auth-art-heading">Ide hebat dimulai dari satu halaman.</div>
                                    <p>Lanjutkan membaca, menulis, dan berbagi hal-hal yang bermakna.</p>
                                    <div class="auth-quote">
                                        “The more that you read, the more things you will know.”
                                        <small>— Dr. Seuss</small>
                                    </div>
                                </div>
                            </div>
                            <div class="auth-panel">
                                <div class="auth-form-wrap">
                                    <div class="auth-icon" aria-hidden="true">📖</div>
                                    <span class="eyebrow">YOUR CREATIVE SPACE</span>
                                    <h1>Selamat datang<br>kembali.</h1>
                                    <p>Masuk untuk melanjutkan perjalananmu.</p>
                                    <form action="/login" method="post">
                                        @csrf
                                        <label for="login-username">Username</label>
                                        <div class="field-icon">
                                            <input id="login-username" name="username" type="text" placeholder="Masukkan username" autocomplete="username">
                                        </div>

                                        <label for="login-password">Password</label>
                                        <div class="field-icon">
                                            <input id="login-password" name="password" type="password" placeholder="Masukkan password" autocomplete="current-password">
                                        </div>

                                        <label class="remember">
                                            <input name="remember" type="checkbox"> Ingat saya
                                        </label>

                                        <button id="login-submit" type="submit" class="btn btn-primary btn-block">Masuk ke akun</button>
                                    </form>
                                    <div class="auth-switch">
                                        Belum punya akun? <a href="/register">Daftar sekarang</a>
                                    </div>
                                    <div class="demo-credentials">
                                        <b>Akun demo</b>
                                        <span>Admin: admin / password123</span>
                                        <span>Penulis: writer / password123</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($component === 'Auth/Register')
                        <div class="auth-wrap">
                            <div class="auth-art register-art">
                                <div class="auth-art-content">
                                    <span class="eyebrow">CREATE SOMETHING MEANINGFUL</span>
                                    <div class="auth-art-heading">Suaramu layak untuk didengar.</div>
                                    <p>Mulai perjalanan menulismu. Bagikan ide, pengalaman, dan pengetahuan kepada dunia.</p>
                                    <div class="auth-quote">
                                        “There is no greater agony than bearing an untold story inside you.”
                                        <small>— Maya Angelou</small>
                                    </div>
                                </div>
                            </div>
                            <div class="auth-panel">
                                <div class="auth-form-wrap">
                                    <div class="auth-icon" aria-hidden="true">📖</div>
                                    <span class="eyebrow">JOIN THE COMMUNITY</span>
                                    <h1>Buat akun<br>penulis.</h1>
                                    <p>Hanya butuh beberapa langkah singkat.</p>
                                    <form action="/register" method="post">
                                        @csrf
                                        <label for="register-name">Nama lengkap</label>
                                        <div class="field-icon">
                                            <input id="register-name" name="name" type="text" placeholder="Nama kamu" autocomplete="name">
                                        </div>

                                        <label for="register-username">Username</label>
                                        <div class="field-icon">
                                            <input id="register-username" name="username" type="text" placeholder="username" autocomplete="username">
                                        </div>

                                        <label for="register-email">Email</label>
                                        <div class="field-icon">
                                            <input id="register-email" name="email" type="email" placeholder="nama@email.com" autocomplete="email">
                                        </div>

                                        <label for="register-password">Password</label>
                                        <div class="field-icon">
                                            <input id="register-password" name="password" type="password" placeholder="Minimal 8 karakter" autocomplete="new-password">
                                        </div>

                                        <label for="register-password-confirmation">Konfirmasi password</label>
                                        <div class="field-icon">
                                            <input id="register-password-confirmation" name="password_confirmation" type="password" placeholder="Ulangi password" autocomplete="new-password">
                                        </div>

                                        <button id="register-submit" type="submit" class="btn btn-primary btn-block">Buat akun penulis</button>
                                    </form>
                                    <div class="auth-switch">
                                        Sudah punya akun? <a href="/login">Masuk</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        @php
                            $isFeatured = !empty($props['featured']);
                            $postsList = $props['posts']['data'] ?? [];
                            $postsLinks = $props['posts']['links'] ?? [];
                        @endphp
                        @if($isFeatured)
                            <section class="hero">
                                <div class="hero-content">
                                    <div class="eyebrow-pill">IDEAS THAT MATTER</div>
                                    <h1>Setiap ide punya<br><em>cerita.</em> Bagikan di sini.</h1>
                                    <p>Temukan perspektif baru, pelajari hal menarik, dan bagikan pemikiranmu bersama komunitas penulis yang terus berkembang.</p>
                                    <div class="hero-actions">
                                        <a href="/articles" class="btn btn-primary btn-lg">Jelajahi Artikel</a>
                                        <a href="/register" class="btn btn-outline btn-lg">Mulai Menulis</a>
                                    </div>
                                    <div class="hero-proof">
                                        <div class="proof-avatars" aria-hidden="true">
                                            <i>R</i><i>A</i><i>M</i><i>+</i>
                                        </div>
                                        <span>Ruang terbuka untuk semua cerita.</span>
                                    </div>
                                </div>
                            </section>
                        @endif
                        <section class="section-wrap">
                            <div class="section-heading">
                                <div>
                                    <span class="eyebrow">THE JOURNAL</span>
                                    @if($isFeatured)
                                        <h2>Cerita pilihan</h2>
                                    @else
                                        <h1>Jelajahi artikel</h1>
                                    @endif
                                    <p>Wawasan dan inspirasi dari komunitas BlogVerse.</p>
                                </div>
                                @if($isFeatured)
                                    <a href="/articles" class="text-link">Lihat semua artikel</a>
                                @endif
                            </div>

                            @if(!$isFeatured)
                                <form class="filter-bar" action="/articles" method="get" role="search">
                                    <div class="search-box">
                                        <input type="search" name="search" aria-label="Cari judul atau isi artikel" placeholder="Cari judul atau isi artikel..." value="{{ $props['filters']['search'] ?? '' }}">
                                    </div>
                                    <select name="category" aria-label="Filter kategori artikel">
                                        <option value="">Semua kategori</option>
                                        @foreach(['Teknologi', 'Pendidikan', 'Gaya Hidup', 'Bisnis', 'Opini', 'Umum'] as $cat)
                                            <option value="{{ $cat }}" {{ ($props['filters']['category'] ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary">Cari</button>
                                </form>
                            @endif

                            <div class="article-grid">
                                @forelse($postsList as $post)
                                    <article class="article-card">
                                        <a href="/articles/{{ $post['slug'] ?? '' }}" class="article-cover" aria-label="Baca artikel {{ $post['title'] ?? '' }}">
                                            <div class="cover-placeholder" aria-hidden="true">
                                                <span>{{ $post['category'] ?? 'Artikel' }}</span>
                                                <b>{{ substr($post['title'] ?? 'B', 0, 1) }}</b>
                                            </div>
                                            <span class="category-tag">{{ $post['category'] ?? 'Artikel' }}</span>
                                        </a>
                                        <div class="article-card-body">
                                            <div class="article-meta">
                                                {{ $post['user']['name'] ?? 'BlogVerse Writer' }} <span>·</span>
                                                {{ !empty($post['published_at']) ? substr($post['published_at'], 0, 10) : 'Draft' }}
                                            </div>
                                            @if($isFeatured)
                                                <h3><a href="/articles/{{ $post['slug'] ?? '' }}">{{ $post['title'] ?? '' }}</a></h3>
                                            @else
                                                <h2><a href="/articles/{{ $post['slug'] ?? '' }}">{{ $post['title'] ?? '' }}</a></h2>
                                            @endif
                                            <p>{{ $post['excerpt'] ?? '' }}</p>
                                            <a href="/articles/{{ $post['slug'] ?? '' }}" class="read-link">Baca artikel</a>
                                        </div>
                                    </article>
                                @empty
                                    <div class="empty-state">
                                        @if($isFeatured)
                                            <h3>Belum ada artikel</h3>
                                        @else
                                            <h2>Belum ada artikel</h2>
                                        @endif
                                        <p>Coba kata kunci atau kategori lainnya.</p>
                                    </div>
                                @endforelse
                            </div>

                            @if(!empty($postsLinks))
                                <nav class="pagination" aria-label="Navigasi Halaman">
                                    @foreach($postsLinks as $l)
                                        <a href="{{ $l['url'] ?: '#' }}" class="{{ !empty($l['active']) ? 'active' : '' }} {{ empty($l['url']) ? 'disabled' : '' }}">{!! $l['label'] ?? '' !!}</a>
                                    @endforeach
                                </nav>
                            @endif
                        </section>

                        @if($isFeatured)
                            <section class="join-banner">
                                <div>
                                    <span class="eyebrow">YOUR VOICE MATTERS</span>
                                    <h2>Punya cerita untuk dibagikan?</h2>
                                    <p>Jadikan ide dan pengalamanmu sesuatu yang berarti bagi orang lain.</p>
                                </div>
                                <a href="/register" class="btn btn-white">Bergabung sekarang</a>
                            </section>
                        @endif
                    @endif
                </main>
                <footer class="footer">
                    <div class="footer-inner">
                        <a href="/" class="brand">
                            <span class="brand-mark" aria-hidden="true">B</span>
                            <span>BlogVerse <span class="brand-light">Pro</span></span>
                        </a>
                        <p>Ruang untuk ide, cerita, dan pengetahuan.</p>
                        <nav aria-label="Navigasi Footer" style="display:flex;gap:20px">
                            <a href="/">Beranda</a>
                            <a href="/articles">Artikel</a>
                            <a href="/login">Login</a>
                            <a href="/register">Register</a>
                        </nav>
                        <small>© {{ date('Y') }} BlogVerse Pro. Dibuat untuk berbagi inspirasi.</small>
                    </div>
                </footer>
            </div>
        @endif
    </div>
</body>
</html>
