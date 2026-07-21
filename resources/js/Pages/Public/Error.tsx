import { Button } from '@/Components/ui/button';
import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';

const messages: Record<number, { title: string; description: string }> = {
    403: { title: 'Akses ditolak', description: 'Anda tidak memiliki izin untuk membuka halaman ini.' },
    404: { title: 'Halaman tidak ditemukan', description: 'Halaman mungkin sudah dipindahkan atau alamat yang dibuka tidak tepat.' },
    500: { title: 'Terjadi kesalahan', description: 'Server mengalami kendala saat memproses permintaan. Silakan coba kembali.' },
    503: { title: 'Sedang dalam pemeliharaan', description: 'Website sedang diperbarui dan akan kembali tersedia segera.' },
};

export default function ErrorPage({ status }: { status: number }) {
    const message = messages[status] ?? messages[500];
    return <PublicLayout><Head title={`${status} — ${message.title}`} /><section className="mx-auto grid min-h-[65vh] max-w-3xl place-items-center px-4 py-20 text-center"><div><p className="font-mono text-6xl font-semibold text-primary sm:text-8xl">{status}</p><h1 className="mt-6 font-heading text-3xl font-semibold sm:text-4xl">{message.title}</h1><p className="mx-auto mt-4 max-w-xl leading-7 text-muted">{message.description}</p><div className="mt-8 flex justify-center gap-3"><Button asChild><Link href={route('home')}>Kembali ke beranda</Link></Button><Button type="button" variant="outline" onClick={() => window.history.back()}>Kembali</Button></div></div></section></PublicLayout>;
}
