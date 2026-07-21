import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';

type Profile = { [key: string]: string | number | null; id: number; name: string; headline: string; hero_badge: string | null; hero_description: string; about_description: string; location: string | null; email: string; phone: string | null; linkedin_url: string | null; github_url: string | null; availability_status: string; availability_text: string; profile_image: string | null; cv_file: string | null };
type ProfileForm = Record<string, string | File | null> & { availability_status: string; profile_image: File | null; cv_file: File | null; _method: string };

const fields = [
    ['name', 'Nama', 'text'], ['headline', 'Headline', 'text'], ['hero_badge', 'Badge hero', 'text'], ['hero_description', 'Deskripsi hero', 'textarea'],
    ['about_description', 'Deskripsi lengkap', 'textarea'], ['location', 'Lokasi', 'text'], ['email', 'Email publik', 'email'], ['phone', 'Telepon', 'text'],
    ['linkedin_url', 'URL LinkedIn', 'url'], ['github_url', 'URL GitHub', 'url'], ['availability_text', 'Teks ketersediaan', 'text'],
] as const;

export default function ProfileEdit({ profile }: { profile: Profile }) {
    const initialData: Record<string, string | File | null> = {
        ...Object.fromEntries(fields.map(([name]) => [name, profile[name] ?? ''])),
        availability_status: profile.availability_status,
        profile_image: null as File | null,
        cv_file: null as File | null,
        _method: 'put',
    };
    const { data, setData, post, processing, errors } = useForm<ProfileForm>(initialData as ProfileForm);
    const submit = (event: FormEvent) => { event.preventDefault(); post(route('admin.profile.update'), { forceFormData: true, preserveScroll: true }); };

    return <AdminLayout><Head title="Profil" /><div><p className="text-sm text-accent">Konten utama</p><h1 className="mt-1 font-heading text-3xl font-semibold">Profil</h1></div><form onSubmit={submit} className="mt-7 max-w-4xl space-y-5 rounded-lg border bg-surface p-5 sm:p-7">{fields.map(([name, label, type]) => <div key={name} className="space-y-2"><label htmlFor={name} className="text-sm font-medium">{label}</label>{type === 'textarea' ? <textarea id={name} rows={name === 'about_description' ? 8 : 4} value={String(data[name] ?? '')} onChange={(event) => setData(name, event.target.value)} className="block w-full rounded-md border bg-background px-3 py-2" /> : <input id={name} type={type} value={String(data[name] ?? '')} onChange={(event) => setData(name, event.target.value)} className="block min-h-11 w-full rounded-md border bg-background px-3" />}<InputError message={errors[name]} /></div>)}<div className="space-y-2"><label className="text-sm font-medium">Status ketersediaan</label><select value={data.availability_status} onChange={(event) => setData('availability_status', event.target.value)} className="block min-h-11 w-full rounded-md border bg-background px-3"><option value="available">Tersedia</option><option value="open_to_work">Terbuka untuk pekerjaan</option><option value="unavailable">Tidak tersedia</option></select></div><div className="grid gap-5 sm:grid-cols-2"><FileInput label="Foto profil" current={profile.profile_image} onChange={(file) => setData('profile_image', file)} error={errors.profile_image} accept="image/jpeg,image/png,image/webp" /><FileInput label="CV (PDF)" current={profile.cv_file} onChange={(file) => setData('cv_file', file)} error={errors.cv_file} accept="application/pdf" /></div><div className="flex justify-end border-t pt-5"><Button type="submit" disabled={processing}>{processing ? 'Menyimpan...' : 'Simpan profil'}</Button></div></form></AdminLayout>;
}

function FileInput({ label, current, onChange, error, accept }: { label: string; current: string | null; onChange: (file: File | null) => void; error?: string; accept: string }) {
    return <div className="space-y-2"><label className="text-sm font-medium">{label}</label>{current && <p className="truncate text-xs text-muted">Saat ini: {current}</p>}<input type="file" accept={accept} onChange={(event) => onChange(event.target.files?.[0] ?? null)} className="block w-full rounded-md border bg-background p-2 text-sm" /><InputError message={error} /></div>;
}
