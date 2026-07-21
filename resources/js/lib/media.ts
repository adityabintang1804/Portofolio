export function mediaUrl(path?: string | null) {
    return path ? `/storage/${path}` : '/storage/placeholders/project-placeholder.svg';
}
