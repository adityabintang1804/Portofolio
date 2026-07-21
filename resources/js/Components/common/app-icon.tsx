import {
    BriefcaseBusiness,
    ChevronRight,
    FileText,
    FolderKanban,
    Home,
    LogOut,
    Menu,
    MessageCircle,
    Monitor,
    Moon,
    Settings,
    Send,
    Sun,
    UserRound,
    Trash2,
    LoaderCircle,
    X,
    type LucideProps,
} from 'lucide-react';

const icons = {
    briefcase: BriefcaseBusiness,
    chevronRight: ChevronRight,
    file: FileText,
    folder: FolderKanban,
    home: Home,
    logout: LogOut,
    menu: Menu,
    chat: MessageCircle,
    send: Send,
    trash: Trash2,
    loader: LoaderCircle,
    monitor: Monitor,
    moon: Moon,
    settings: Settings,
    sun: Sun,
    user: UserRound,
    x: X,
} as const;

export type AppIconName = keyof typeof icons;

type AppIconProps = LucideProps & { name: AppIconName };

export function AppIcon({ name, ...props }: AppIconProps) {
    const Icon = icons[name];
    return <Icon aria-hidden="true" strokeWidth={1.8} {...props} />;
}
