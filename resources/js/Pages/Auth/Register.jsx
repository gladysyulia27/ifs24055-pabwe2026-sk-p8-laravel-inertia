import React from 'react';
import { Link, router, useForm } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';
import { BookOpen, ArrowRight, User, AtSign, Mail, LockKeyhole } from 'lucide-react';

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        username: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit = e => {
        e.preventDefault();
        const fd = new FormData(e.currentTarget);
        const payload = {
            name: data.name || fd.get('name') || '',
            username: data.username || fd.get('username') || '',
            email: data.email || fd.get('email') || '',
            password: data.password || fd.get('password') || '',
            password_confirmation: data.password_confirmation || fd.get('password_confirmation') || data.password || fd.get('password') || '',
        };

        if (payload.email !== data.email || payload.password !== data.password) {
            router.post('/register', payload);
            return;
        }

        post('/register');
    };

    return (
        <PublicLayout>
            <div className="auth-wrap">
                <div className="auth-art register-art">
                    <div className="auth-art-content">
                        <span className="eyebrow">CREATE SOMETHING MEANINGFUL</span>
                        <div className="auth-art-heading">Suaramu layak untuk didengar.</div>
                        <p>Mulai perjalanan menulismu. Bagikan ide, pengalaman, dan pengetahuan kepada dunia.</p>
                        <div className="auth-quote">
                            “There is no greater agony than bearing an untold story inside you.”
                            <small>— Maya Angelou</small>
                        </div>
                    </div>
                </div>
                <div className="auth-panel">
                    <div className="auth-form-wrap">
                        <div className="auth-icon"><BookOpen /></div>
                        <span className="eyebrow">JOIN THE COMMUNITY</span>
                        <h1>Buat akun<br />penulis.</h1>
                        <p>Hanya butuh beberapa langkah singkat.</p>
                        <form onSubmit={submit}>
                            <label htmlFor="register-name">Nama lengkap</label>
                            <div className="field-icon">
                                <User size={17} />
                                <input
                                    id="register-name"
                                    name="name"
                                    type="text"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    placeholder="Nama kamu"
                                    autoComplete="name"
                                />
                            </div>
                            {errors.name && <small className="field-error">{errors.name}</small>}

                            <label htmlFor="register-username">Username</label>
                            <div className="field-icon">
                                <AtSign size={17} />
                                <input
                                    id="register-username"
                                    name="username"
                                    type="text"
                                    value={data.username}
                                    onChange={e => setData('username', e.target.value)}
                                    placeholder="username"
                                    autoComplete="username"
                                />
                            </div>
                            {errors.username && <small className="field-error">{errors.username}</small>}

                            <label htmlFor="register-email">Email</label>
                            <div className="field-icon">
                                <Mail size={17} />
                                <input
                                    id="register-email"
                                    name="email"
                                    type="email"
                                    value={data.email}
                                    onChange={e => setData('email', e.target.value)}
                                    placeholder="nama@email.com"
                                    autoComplete="email"
                                />
                            </div>
                            {errors.email && <small className="field-error">{errors.email}</small>}

                            <label htmlFor="register-password">Password</label>
                            <div className="field-icon">
                                <LockKeyhole size={17} />
                                <input
                                    id="register-password"
                                    name="password"
                                    type="password"
                                    value={data.password}
                                    onChange={e => setData('password', e.target.value)}
                                    placeholder="Minimal 8 karakter"
                                    autoComplete="new-password"
                                />
                            </div>
                            {errors.password && <small className="field-error">{errors.password}</small>}

                            <label htmlFor="register-password-confirmation">Konfirmasi password</label>
                            <div className="field-icon">
                                <LockKeyhole size={17} />
                                <input
                                    id="register-password-confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    value={data.password_confirmation}
                                    onChange={e => setData('password_confirmation', e.target.value)}
                                    placeholder="Ulangi password"
                                    autoComplete="new-password"
                                />
                            </div>

                            <button id="register-submit" type="submit" className="btn btn-primary btn-block" disabled={processing}>
                                {processing ? 'Membuat akun...' : 'Buat akun penulis'} <ArrowRight size={17} />
                            </button>
                        </form>
                        <div className="auth-switch">
                            Sudah punya akun? <Link href="/login">Masuk</Link>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
