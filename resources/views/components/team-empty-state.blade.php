@props([
    'title',
    'description' => null,
    'icon' => 'heroicons:sparkles',
    'actionHref' => null,
    'actionLabel' => null,
    'testId' => null,
])

<x-empty-state
    :title="$title"
    :description="$description"
    :icon="$icon"
    :action-href="$actionHref"
    :action-label="$actionLabel"
    :test-id="$testId"
    {{ $attributes }}
/>
