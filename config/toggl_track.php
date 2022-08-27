<?php

return [
    'base_uri' => env('TOGGL_TRACK_BASE_URI', 'https://api.track.toggl.com/'),
    'api_token' => env('TOGGL_TRACK_API_TOKEN'),
    'user_agent' => env('TOGGL_TRACK_USER_AGENT'),
    'workspace_id' => env('TOGGL_TRACK_WORKSPACE_ID'),
];
