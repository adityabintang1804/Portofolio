import { AppIcon } from '@/Components/common/app-icon';
import { AnimatePresence, motion, useReducedMotion } from 'motion/react';
import { lazy, Suspense, useState } from 'react';

const ChatWindow = lazy(() => import('@/Components/chatbot/ChatWindow'));

export function ChatbotLauncher({ welcomeMessage }: { welcomeMessage: string }) {
    const [open, setOpen] = useState(false);
    const reduceMotion = useReducedMotion();

    return <div className="fixed bottom-4 right-4 z-50 sm:bottom-6 sm:right-6">
        <AnimatePresence>{open && <Suspense fallback={<div className="mb-3 rounded-lg border bg-surface p-4 text-sm text-muted">Membuka chatbot...</div>}><ChatWindow welcomeMessage={welcomeMessage} onClose={() => setOpen(false)} /></Suspense>}</AnimatePresence>
        {!open && <motion.button type="button" onClick={() => setOpen(true)} initial={reduceMotion ? false : { scale: 0.8, opacity: 0 }} animate={{ scale: 1, opacity: 1 }} whileHover={reduceMotion ? undefined : { scale: 1.06 }} whileTap={reduceMotion ? undefined : { scale: 0.96 }} className="ms-auto grid size-14 place-items-center rounded-full bg-primary text-primary-foreground shadow-xl shadow-primary/25" aria-label="Buka chatbot"><AppIcon name="chat" className="size-6" /></motion.button>}
    </div>;
}
