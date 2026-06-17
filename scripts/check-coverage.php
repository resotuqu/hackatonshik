<?php

declare(strict_types=1);

$cloverPath = $argv[1] ?? 'coverage/clover.xml';
$minimumPercent = (float) ($argv[2] ?? 75);

if (! is_file($cloverPath)) {
    fwrite(STDERR, "Coverage file not found: {$cloverPath}\n");
    exit(1);
}

$xml = simplexml_load_file($cloverPath);

if ($xml === false) {
    fwrite(STDERR, "Failed to parse coverage file: {$cloverPath}\n");
    exit(1);
}

$metrics = $xml->project->metrics ?? null;

if ($metrics === null) {
    fwrite(STDERR, "Coverage metrics missing in {$cloverPath}\n");
    exit(1);
}

$statements = (int) $metrics['statements'];
$coveredStatements = (int) $metrics['coveredstatements'];
$percent = $statements > 0
    ? round(($coveredStatements / $statements) * 100, 2)
    : 0.0;

echo "Coverage: {$percent}% ({$coveredStatements}/{$statements} statements)\n";

if ($percent < $minimumPercent) {
    fwrite(STDERR, "Coverage {$percent}% is below minimum {$minimumPercent}%\n");
    exit(1);
}

exit(0);
