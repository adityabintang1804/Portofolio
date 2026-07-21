import {
    siGithub,
    siLaravel,
    siMysql,
    siPython,
    siReact,
    siTypescript,
    type SimpleIcon,
} from 'simple-icons/icons';

const technologyIcons = {
    github: siGithub,
    laravel: siLaravel,
    mysql: siMysql,
    python: siPython,
    react: siReact,
    typescript: siTypescript,
} as const satisfies Record<string, SimpleIcon>;

export type TechnologyIconName = keyof typeof technologyIcons;

type TechnologyIconProps = {
    name: TechnologyIconName;
    className?: string;
    title?: string;
};

export function TechnologyIcon({ name, className, title }: TechnologyIconProps) {
    const icon = technologyIcons[name];

    return (
        <svg className={className} viewBox="0 0 24 24" role="img" aria-label={title ?? icon.title}>
            <path fill="currentColor" d={icon.path} />
        </svg>
    );
}
