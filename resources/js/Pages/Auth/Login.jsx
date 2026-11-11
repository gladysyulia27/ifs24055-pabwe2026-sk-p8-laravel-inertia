import React from 'react';
import { Link, router, useForm } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';
import { BookOpen, ArrowRight, LockKeyhole, AtSign } from 'lucide-react';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        username: '',
        password: '',
        remember: false,
    });

    const submit = e => {
        e.preventDefault();
        const fd = new FormData(e.currentTarget);
        const usernameVal = data.username || fd.get('username') || '';
        const passwordVal = data.password || fd.get('password') || '';

        if (usernameVal !== data.username || passwordVal !== data.password) {
            router.post('/login', {
                username: usernameVal,
                password: passwordVal,
                remember: data.remember,
            });
            return;
        }

        post('/login');
    };

    return (
        <PublicLayout>
            <div className="auth-wrap">
                <div className="auth-art">
                    <div className="auth-art-content">
                        <span className="eyebrow">WELCOME BACK</span>
                        <h2>Ide hebat dimulai dari satu halaman.</h2>
                        <p>Lanjutkan membaca, menulis, dan berbagi hal-hal yang bermakna.</p>
                        <div className="auth-quote">
                            “The more that you read, the more things you will know.”
                            <small>— Dr. Seuss</small>
                        </div>
                    </div>
                </div>
                <div className="auth-panel">
                    <div className="auth-form-wrap">
                        <div className="auth-icon"><BookOpen /></div>
                        <span className="eyebrow">YOUR CREATIVE SPACE</span>
                        <h1>Selamat datang<br />kembali.</h1>
                        <p>Masuk untuk melanjutkan perjalananmu.</p>
                        <form onSubmit={submit}>
                            <label htmlFor="login-username">Username</label>
                            <div className="field-icon">
                                <AtSign size={17} />
                                <input
                                    id="login-username"
                                    name="username"
                                    type="text"
                                    value={data.username}
                                    onChange={e => setData('username', e.target.value)}
                                    placeholder="Masukkan username"
                                    autoComplete="username"
                                />
                            </div>
                            {errors.username && <small className="field-error">{errors.username}</small>}

                            <label htmlFor="login-password">Password</label>
                            <div className="field-icon">
                                <LockKeyhole size={17} />
                                <input
                                    id="login-password"
                                    name="password"
                                    type="password"
                                    value={data.password}
                                    onChange={e => setData('password', e.target.value)}
                                    placeholder="Masukkan password"
                                    autoComplete="current-password"
                                />
                            </div>
                            {errors.password && <small className="field-error">{errors.password}</small>}

                            <label className="remember">
                                <input
                                    name="remember"
                                    type="checkbox"
                                    checked={data.remember}
                                    onChange={e => setData('remember', e.target.checked)}
                                /> Ingat saya
                            </label>
                            <button id="login-submit" type="submit" className="btn btn-primary btn-block" disabled={processing}>
                                {processing ? 'Memproses...' : 'Masuk ke akun'} <ArrowRight size={17} />
                            </button>
                        </form>
                        <div className="auth-switch">
                            Belum punya akun? <Link href="/register">Daftar sekarang</Link>
                        </div>
                        <div className="demo-credentials">
                            <b>Akun demo</b>
                            <span>Admin: admin / password123</span>
                            <span>Penulis: writer / password123</span>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
