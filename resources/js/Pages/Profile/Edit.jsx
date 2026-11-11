import React from 'react';
import { useForm } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Edit({ user }) {
    const { data, setData, put, processing, errors } = useForm({ ...user });
    const submit = e => {
        e.preventDefault();
        put('/profile');
    };

    return (
        <DashboardLayout title="Profil">
            <div className="page-heading">
                <div>
                    <span className="eyebrow">ACCOUNT SETTINGS</span>
                    <h1>Profil penulis<span className="heading-dot">.</span></h1>
                    <p>Perbarui informasi akun dan identitas publikmu.</p>
                </div>
            </div>
            <form className="profile-form" onSubmit={submit}>
                <div className="form-field">
                    <label htmlFor="profile-name">Nama lengkap</label>
                    <input
                        id="profile-name"
                        name="name"
                        type="text"
                        value={data.name}
                        onChange={e => setData('name', e.target.value)}
                    />
                    {errors.name && <small className="field-error">{errors.name}</small>}
                </div>
                <div className="form-field">
                    <label htmlFor="profile-username">Username</label>
                    <input
                        id="profile-username"
                        name="username"
                        type="text"
                        value={data.username}
                        onChange={e => setData('username', e.target.value)}
                    />
                    {errors.username && <small className="field-error">{errors.username}</small>}
                </div>
                <div className="form-field">
                    <label htmlFor="profile-email">Email</label>
                    <input
                        id="profile-email"
                        name="email"
                        type="email"
                        value={data.email}
                        onChange={e => setData('email', e.target.value)}
                    />
                    {errors.email && <small className="field-error">{errors.email}</small>}
                </div>
                <div className="form-field">
                    <label htmlFor="profile-bio">Bio</label>
                    <textarea
                        id="profile-bio"
                        name="bio"
                        rows="4"
                        value={data.bio || ''}
                        onChange={e => setData('bio', e.target.value)}
                        placeholder="Ceritakan sedikit tentang dirimu..."
                    />
                </div>
                <button id="profile-submit" type="submit" className="btn btn-primary" disabled={processing}>
                    Simpan perubahan
                </button>
            </form>
        </DashboardLayout>
    );
}
