import { Button } from '@/Components/ui/button';
import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';

type Profile = { name: string; headline: string; cv_file?: string | null; email: string };

export default function Cv({ profile }: { profile: Profile }) {
    return <PublicLayout><Head title={`CV — ${profile.name}`} /><section className="mx-auto max-w-5xl px-4 py-20 text-center sm:px-6"><p className="text-sm text-accent">Curriculum Vitae</p><h1 className="mt-3 font-heading text-5xl font-semibold">CV {profile.name}</h1><p className="mx-auto mt-5 max-w-2xl text-lg text-muted">{profile.headline}</p>{profile.cv_file ? <><div className="mt-10 overflow-hidden rounded-xl border bg-surface"><iframe src={`/storage/${profile.cv_file}`} title={`CV ${profile.name}`} className="h-[75vh] w-full" /></div><Button asChild className="mt-6"><a href={route('cv.download')}>Download CV</a></Button></> : <div className="mx-auto mt-10 max-w-xl rounded-xl border border-dashed p-10"><h2 className="font-heading text-xl font-semibold">CV belum tersedia</h2><p className="mt-3 text-muted">File CV belum diunggah melalui dashboard. Silakan hubungi saya untuk informasi terbaru.</p><Button asChild className="mt-6"><Link href={route('contact.create')}>Hubungi saya</Link></Button></div>}</section></PublicLayout>;
}
