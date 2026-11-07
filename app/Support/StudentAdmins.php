<?php

namespace App\Support;

class StudentAdmins
{
    /**
     * Union of actual administrator emails: API CMS configured admins and staff-portal superadmins.
     *
     * @return list<string>
     */
    public static function emails(): array
    {
        $cms = [
            config('admin.admin_email'),
            config('admin.admin2_email'),
            config('admin.admin_education_email'),
        ];

        $portal = explode(',', (string) config('student_auth.portal_admin_emails'));

        return array_values(array_unique(array_filter(
            array_map(fn ($email) => strtolower(trim((string) $email)), [...$cms, ...$portal]),
            fn ($email) => $email !== '',
        )));
    }

    public static function isAdmin(?string $email): bool
    {
        if (! filled($email)) {
            return false;
        }

        return in_array(strtolower(trim($email)), self::emails(), true);
    }
}
