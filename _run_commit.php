<?php

declare(strict_types=1);

/**
 * Multi-commit staging script for production-readiness work.
 * Run: php _run_commit.php
 */

$root = __DIR__;
chdir($root);

@mkdir($root.'/tmp', 0777, true);
putenv('TEMP='.$root.'/tmp');
putenv('TMP='.$root.'/tmp');

$outputFile = $root.'/_commit_verify.txt';
$lines = [];
$hashes = [];

$run = static function (string $command) use (&$lines): int {
    $lines[] = '$ '.$command;
    exec($command.' 2>&1', $out, $code);
    foreach ($out as $line) {
        $lines[] = $line;
    }

    return $code;
};

$unstage = static function (array $paths) use ($run): void {
    foreach ($paths as $path) {
        if (file_exists($path) || is_dir($path)) {
            $run('git reset HEAD '.escapeshellarg($path));
        }
    }
};

$neverCommit = [
    '.env',
    '.env.sail',
    '.env.local',
    'first()',
    'test-output.txt',
    '_run_commit.php',
    '_commit_verify.txt',
    '_do_commit.cmd',
    '_do_commit.ps1',
    'tmp/agent_commit.bat',
    'tmp/run_git.bat',
    'tmp/agent_commit_output.txt',
    'tmp',
    'storage/framework/cache',
    'storage/framework/views',
    'storage/logs',
];

$commits = [
    [
        'paths' => [
            'app',
            'bootstrap',
            'config',
            'routes',
            'database/migrations',
        ],
        'message' => 'Harden backend security, policies, and data integrity for production',
    ],
    [
        'paths' => [
            'resources',
            'lang',
        ],
        'message' => 'Improve frontend UX, accessibility, and critical i18n coverage',
    ],
    [
        'paths' => [
            'tests',
            'phpunit.xml',
        ],
        'message' => 'Update tests for Filament admin and production readiness checks',
    ],
    [
        'paths' => [
            '.gitignore',
            'README.md',
            'boost.json',
            'compose.yaml',
            'compose.prod.yaml',
            'composer.json',
            'phpstan.neon.dist',
        ],
        'message' => 'Add deployment tooling, PHPStan script, and production runbook updates',
    ],
];

$run('git reset HEAD');
$overallExit = 0;

foreach ($commits as $index => $commit) {
    $existing = array_values(array_filter(
        $commit['paths'],
        static fn (string $path): bool => file_exists($path) || is_dir($path),
    ));

    if ($existing === []) {
        $lines[] = 'SKIP commit '.($index + 1).': no paths on disk';
        continue;
    }

    $run('git add '.implode(' ', array_map('escapeshellarg', $existing)));
    $unstage($neverCommit);

    $stagedOutput = [];
    exec('git diff --cached --name-only 2>&1', $stagedOutput);
    if ($stagedOutput === []) {
        $lines[] = 'SKIP commit '.($index + 1).': nothing staged';
        continue;
    }

    $message = $commit['message'];
    $code = $run('git commit -m '.escapeshellarg($message));
    if ($code !== 0) {
        $lines[] = 'FAILED commit '.($index + 1);
        $overallExit = $code;
        break;
    }

    $hashOutput = [];
    exec('git rev-parse HEAD 2>&1', $hashOutput);
    $hash = $hashOutput[0] ?? 'unknown';

    $fileOutput = [];
    exec('git diff-tree --no-commit-id --name-only -r HEAD 2>&1', $fileOutput);
    $fileCount = count($fileOutput);

    $lines[] = '---';
    $lines[] = 'COMMIT '.($index + 1);
    $lines[] = 'HASH='.$hash;
    $lines[] = 'MESSAGE='.$message;
    $lines[] = 'FILES='.$fileCount;
    $hashes[] = $hash;
}

$lines[] = '=== GIT_LOG_5 ===';
$run('git log -5 --oneline');
$lines[] = '=== GIT_STATUS ===';
$run('git status --short');
$lines[] = 'COMMIT_HASHES='.implode(',', $hashes);
$lines[] = 'HEAD='.(exec('git rev-parse HEAD 2>&1', $headOut) !== false ? ($headOut[0] ?? '') : '');
$lines[] = 'EXIT_CODE='.$overallExit;

file_put_contents($outputFile, implode(PHP_EOL, $lines).PHP_EOL);
file_put_contents($root.'/tmp/_commit_verify_full.txt', implode(PHP_EOL, $lines).PHP_EOL);

fwrite(STDOUT, file_get_contents($outputFile));

exit($overallExit);
