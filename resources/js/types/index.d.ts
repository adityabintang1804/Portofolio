export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    flash: {
        success?: string;
        error?: string;
    };
    site: {
        settings: Record<string, string | null>;
        profile: {
            name: string;
            email: string;
            location?: string;
            linkedin_url?: string;
            github_url?: string;
            availability_status: string;
            availability_text: string;
            cv_file?: string;
        } | null;
    };
};
