<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Site logo link — replaces legacy Mary demo markup; uses {@see resources/views/components/app-brand.blade.php}.
 */
class AppBrand extends Component
{
    public function __construct(
        public string $imgClass = 'h-8 w-auto sm:h-9',
        public bool $wide = false,
    ) {}

    public function render(): View
    {
        return view('components.app-brand');
    }
}
