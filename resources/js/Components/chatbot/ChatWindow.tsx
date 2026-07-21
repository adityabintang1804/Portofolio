import { AppIcon } from '@/Components/common/app-icon';
import { Button } from '@/Components/ui/button';
import { Link } from '@inertiajs/react';
import { motion, useReducedMotion } from 'motion/react';
import { useEffect, useRef, useState, type FormEvent } from 'react';

type Action = { label: string; url: string };
type ChatMessage = { id: number; role: 'user' | 'assistant'; content: string; actions?: Action[]; error?: boolean };
type Reply = { message: string; source: string; actions: Action[] };

export default function ChatWindow({ welcomeMessage, onClose }: { welcomeMessage: string; onClose: () => void }) {
    const [messages, setMessages] = useState<ChatMessage[]>([{ id: 1, role: 'assistant', content: welcomeMessage }]);
    const [suggestions, setSuggestions] = useState<string[]>([]);
    const [input, setInput] = useState('');
    const [sending, setSending] = useState(false);
    const [offline, setOffline] = useState(!navigator.onLine);
    const scrollArea = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLInputElement>(null);
    const reduceMotion = useReducedMotion();

    useEffect(() => {
        fetch(route('chatbot.suggestions'), { headers: { Accept: 'application/json' } }).then((response) => response.ok ? response.json() : Promise.reject()).then((data: { suggestions: string[] }) => setSuggestions(data.suggestions)).catch(() => setSuggestions([]));
        const online = () => setOffline(false); const offlineHandler = () => setOffline(true);
        window.addEventListener('online', online); window.addEventListener('offline', offlineHandler);
        inputRef.current?.focus();
        return () => { window.removeEventListener('online', online); window.removeEventListener('offline', offlineHandler); };
    }, []);

    useEffect(() => { scrollArea.current?.scrollTo({ top: scrollArea.current.scrollHeight, behavior: reduceMotion ? 'auto' : 'smooth' }); }, [messages, sending, reduceMotion]);

    const send = async (question: string) => {
        const content = question.trim();
        if (!content || sending || offline) return;
        const userMessage: ChatMessage = { id: Date.now(), role: 'user', content };
        const history = messages.slice(-8).map((message) => ({ role: message.role, content: message.content }));
        setMessages((current) => [...current, userMessage]); setInput(''); setSending(true);
        const controller = new AbortController(); const timeout = window.setTimeout(() => controller.abort(), 32000);
        try {
            const token = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
            const response = await fetch(route('chatbot.message'), { method: 'POST', signal: controller.signal, credentials: 'same-origin', headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ message: content, history }) });
            const data = await response.json() as Reply & { message?: string; errors?: Record<string, string[]> };
            if (!response.ok) throw new Error(data.errors?.message?.[0] ?? data.message ?? 'Chatbot tidak dapat memproses pertanyaan.');
            setMessages((current) => [...current, { id: Date.now() + 1, role: 'assistant', content: data.message, actions: data.actions }]);
        } catch (error) {
            const message = error instanceof Error && error.name !== 'AbortError' ? error.message : 'Waktu respons habis. Silakan coba lagi.';
            setMessages((current) => [...current, { id: Date.now() + 1, role: 'assistant', content: message, error: true }]);
        } finally { window.clearTimeout(timeout); setSending(false); }
    };

    const submit = (event: FormEvent) => { event.preventDefault(); void send(input); };
    const clear = () => { setMessages([{ id: Date.now(), role: 'assistant', content: welcomeMessage }]); setInput(''); };

    return <motion.section initial={reduceMotion ? false : { opacity: 0, y: 20, scale: 0.98 }} animate={{ opacity: 1, y: 0, scale: 1 }} exit={{ opacity: 0, y: 12, scale: 0.98 }} transition={{ duration: reduceMotion ? 0 : 0.28 }} className="fixed inset-2 flex flex-col overflow-hidden rounded-xl border bg-surface shadow-2xl sm:inset-auto sm:bottom-24 sm:right-6 sm:h-[620px] sm:w-[390px]" aria-label="Chatbot portfolio">
        <header className="flex items-center justify-between border-b p-4"><div><h2 className="font-heading font-semibold">Asisten Portfolio</h2><p className={`text-xs ${offline ? 'text-warning' : 'text-success'}`}>{offline ? 'Offline' : 'Online · berbasis data portfolio'}</p></div><div className="flex gap-1"><button type="button" onClick={clear} className="grid size-9 place-items-center rounded-md text-muted hover:bg-surface-secondary" aria-label="Hapus percakapan"><AppIcon name="trash" className="size-4" /></button><button type="button" onClick={onClose} className="grid size-9 place-items-center rounded-md text-muted hover:bg-surface-secondary" aria-label="Tutup chatbot"><AppIcon name="x" className="size-5" /></button></div></header>
        <div ref={scrollArea} className="flex-1 space-y-4 overflow-y-auto p-4" aria-live="polite">{messages.map((message) => <div key={message.id} className={`flex ${message.role === 'user' ? 'justify-end' : 'justify-start'}`}><div className={`max-w-[86%] rounded-xl px-4 py-3 text-sm leading-6 ${message.role === 'user' ? 'bg-primary text-primary-foreground' : message.error ? 'border border-destructive/30 bg-destructive/10' : 'bg-surface-secondary'}`}><p className="whitespace-pre-wrap">{message.content}</p>{message.actions?.length ? <div className="mt-3 flex flex-wrap gap-2">{message.actions.map((action) => <Link key={action.url} href={action.url} className="rounded-md border bg-surface px-2.5 py-1 text-xs text-accent" onClick={onClose}>{action.label}</Link>)}</div> : null}</div></div>)}{sending && <div className="flex items-center gap-2 text-sm text-muted"><AppIcon name="loader" className="size-4 animate-spin" /> Sedang menyiapkan jawaban...</div>}</div>
        {messages.length === 1 && suggestions.length > 0 && <div className="flex gap-2 overflow-x-auto border-t px-4 py-3">{suggestions.map((suggestion) => <button key={suggestion} type="button" onClick={() => void send(suggestion)} className="shrink-0 rounded-full border px-3 py-1.5 text-xs hover:bg-surface-secondary">{suggestion}</button>)}</div>}
        <form onSubmit={submit} className="flex gap-2 border-t p-3"><input ref={inputRef} value={input} onChange={(event) => setInput(event.target.value)} maxLength={500} disabled={sending || offline} placeholder={offline ? 'Chatbot sedang offline' : 'Tanyakan tentang Aditya...'} className="min-h-11 min-w-0 flex-1 rounded-md border bg-background px-3 text-sm" aria-label="Pertanyaan chatbot" /><Button type="submit" size="icon" disabled={sending || offline || !input.trim()} aria-label="Kirim pertanyaan"><AppIcon name="send" className="size-4" /></Button></form>
    </motion.section>;
}
