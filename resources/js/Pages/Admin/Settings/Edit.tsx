import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';

type Setting = { id: number; key: string; value: string | null; type: string; group: string };

export default function SettingsEdit({ groups }: { groups: Record<string, Setting[]> }) {
    const settings = Object.values(groups).flat();
    const { data, setData, put, processing, errors } = useForm({ settings: Object.fromEntries(settings.map((setting) => [setting.key, setting.value ?? ''])) });
    const submit = (event: FormEvent) => { event.preventDefault(); put(route('admin.settings.update'), { preserveScroll: true }); };
    return <AdminLayout><Head title="Pengaturan Website" /><div><p className="text-sm text-accent">Konfigurasi CMS</p><h1 className="mt-1 font-heading text-3xl font-semibold">Pengaturan Website</h1></div><form onSubmit={submit} className="mt-7 max-w-4xl space-y-6">{Object.entries(groups).map(([group, items]) => <section key={group} className="rounded-lg border bg-surface p-5 sm:p-7"><h2 className="font-heading text-lg font-semibold capitalize">{group}</h2><div className="mt-5 space-y-5">{items.map((setting) => <div key={setting.key} className="space-y-2"><label htmlFor={setting.key} className="text-sm font-medium">{setting.key.replaceAll('_', ' ')}</label>{setting.type === 'boolean' ? <select id={setting.key} value={data.settings[setting.key]} onChange={(event) => setData('settings', { ...data.settings, [setting.key]: event.target.value })} className="block min-h-11 w-full rounded-md border bg-background px-3"><option value="true">Aktif</option><option value="false">Nonaktif</option></select> : <textarea id={setting.key} rows={setting.type === 'json' ? 4 : 2} value={data.settings[setting.key]} onChange={(event) => setData('settings', { ...data.settings, [setting.key]: event.target.value })} className="block w-full rounded-md border bg-background px-3 py-2" />}<InputError message={errors[`settings.${setting.key}`]} /></div>)}</div></section>)}<div className="flex justify-end"><Button type="submit" disabled={processing}>{processing ? 'Menyimpan...' : 'Simpan pengaturan'}</Button></div></form></AdminLayout>;
}
