<?php

return [
    // Google OAuth client for student sign-in (staging-only release).
    'google_client_id' => env('GOOGLE_CLIENT_ID'),
    'google_client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'google_redirect_uri' => env('GOOGLE_REDIRECT_URI', rtrim((string) env('APP_URL'), '/').'/auth/google/callback'),

    // Only verified Google Workspace accounts on this exact domain may sign in.
    'allowed_domain' => 'apps.ipb.ac.id',

    // Frontend origin that receives the post-login redirect and may call student endpoints.
    'frontend_url' => rtrim((string) env('STUDENT_FRONTEND_URL', env('ALLOWED_ORIGIN', '')), '/'),

    // Separate cookie so student sessions never share state with the Filament CMS session.
    'session_cookie' => env('STUDENT_SESSION_COOKIE', 'himalkom_student_session'),

    // Emails of actual staff-portal administrators (spatie role "superadmin"), comma separated.
    // The portal lives in a separate database, so its list is supplied explicitly.
    'portal_admin_emails' => env('STUDENT_PORTAL_ADMIN_EMAILS', ''),
];
