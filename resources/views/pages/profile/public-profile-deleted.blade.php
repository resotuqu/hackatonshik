<div class="flex items-center justify-center min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="text-center">
        <div class="text-8xl mb-6">☠️</div>

        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Аккаунт удалён
        </h1>

        <p class="text-xl text-gray-600 dark:text-gray-300 mb-4">
            {{ __('ui.deleted_account_description') }}
        </p>

        <p class="text-lg font-mono text-gray-700 dark:text-gray-200 mb-8">
            {{ $user->getDeletedDisplayName() }}
        </p>

        <a href="{{ route('hackatons.index') }}" class="btn btn-primary">
            {{ __('ui.back_to_hackatons') }}
        </a>
    </div>
</div>
