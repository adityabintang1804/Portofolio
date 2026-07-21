import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';

type Metrics = { projects: number; draftProjects: number; skills: number; experiences: number; certificates: number; messages: number; unreadMessages: number };
type Project = { id: number; title: string; status: string; updated_at: string };
type Message = { id: number; name: string; subject: string; status: string; created_at: string };

export default function Dashboard({ metrics, chatbotEnabled, activeFaqs, latestProjects, latestMessages }: { metrics: Metrics; chatbotEnabled: boolean; activeFaqs: number; latestProjects: Project[]; latestMessages: Message[] }) {
    const cards = [
        ['Proyek', metrics.projects], ['Proyek draft', metrics.draftProjects], ['Skills', metrics.skills],
        ['Pengalaman', metrics.experiences], ['Sertifikat', metrics.certificates], ['Pesan belum dibaca', metrics.unreadMessages],
    ];

    return (
        <AdminLayout>
            <Head title="Dashboard" />
            <div className="flex flex-wrap items-end justify-between gap-4">
                <div><p className="text-sm text-accent">Ringkasan CMS</p><h1 className="mt-1 font-heading text-3xl font-semibold">Dashboard</h1></div>
                <div className={`rounded-full px-3 py-1 text-xs ${chatbotEnabled ? 'bg-success/15 text-success' : 'bg-warning/15 text-warning'}`}>Chatbot {chatbotEnabled ? `aktif · ${activeFaqs} FAQ` : 'nonaktif'}</div>
            </div>
            <div className="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">{cards.map(([label, value]) => <div key={label} className="rounded-lg border bg-surface p-5"><p className="text-sm text-muted">{label}</p><p className="mt-3 font-heading text-3xl font-semibold">{value}</p></div>)}</div>
            <div className="mt-7 grid gap-6 xl:grid-cols-2">
                <section className="rounded-lg border bg-surface p-5"><div className="flex justify-between"><h2 className="font-heading font-semibold">Proyek terbaru</h2><Link href={route('admin.projects.index')} className="text-sm text-accent">Kelola</Link></div><div className="mt-4 divide-y">{latestProjects.map((project) => <div key={project.id} className="flex justify-between py-3 text-sm"><span>{project.title}</span><span className="text-muted">{project.status}</span></div>)}</div></section>
                <section className="rounded-lg border bg-surface p-5"><div className="flex justify-between"><h2 className="font-heading font-semibold">Pesan terbaru</h2><Link href={route('admin.messages.index')} className="text-sm text-accent">Buka inbox</Link></div><div className="mt-4 divide-y">{latestMessages.length ? latestMessages.map((message) => <Link key={message.id} href={route('admin.messages.show', message.id)} className="block py-3 text-sm"><span className="font-medium">{message.name}</span><p className="truncate text-muted">{message.subject}</p></Link>) : <p className="py-6 text-sm text-muted">Belum ada pesan masuk.</p>}</div></section>
            </div>
        </AdminLayout>
    );
}
