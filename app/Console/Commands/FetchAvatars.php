<?php

namespace App\Console\Commands;

use App\Support\AvatarLibrary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FetchAvatars extends Command
{
    protected $signature = 'avatars:fetch
        {--force : Re-download files that already exist}
        {--sleep=150 : Microseconds to sleep between downloads}';

    protected $description = 'Download every avatar SVG into public/avatars so the picker never depends on the remote API';

    public function handle(): int
    {
        $root = public_path('avatars');
        $sleep = (int) $this->option('sleep');
        $downloaded = 0;
        $skipped = 0;
        $failed = [];

        foreach (AvatarLibrary::options() as $avatar) {
            [$style, $seed] = explode(':', $avatar, 2);
            $path = $root.'/'.$style.'/'.$seed.'.svg';

            if (File::exists($path) && ! $this->option('force')) {
                $skipped++;

                continue;
            }

            $svg = $this->download(AvatarLibrary::remoteUrl($avatar));

            if ($svg === null) {
                $failed[] = $avatar;

                continue;
            }

            File::ensureDirectoryExists(dirname($path));
            File::put($path, $svg);
            $downloaded++;

            if ($sleep > 0) {
                usleep($sleep * 1000);
            }
        }

        $this->components->info(sprintf('%d downloaded, %d skipped, %d failed', $downloaded, $skipped, count($failed)));

        if ($failed !== []) {
            $this->components->warn('Failed: '.implode(', ', $failed));

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function download(string $url): ?string
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 20,
                'user_agent' => 'act-tracker-avatar-fetch/1.0',
            ],
        ]);

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $body = @file_get_contents($url, false, $context);

            if ($body !== false && str_contains($body, '<svg')) {
                return $body;
            }

            sleep($attempt);
        }

        return null;
    }
}
