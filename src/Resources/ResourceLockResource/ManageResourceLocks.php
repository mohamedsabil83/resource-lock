<?php

namespace Kenepa\ResourceLock\Resources\ResourceLockResource;


use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Kenepa\ResourceLock\Models\ResourceLock;
use Kenepa\ResourceLock\Resources\ResourceLockResource;
use const _PHPStan_cbfb23d84\__;

class ManageResourceLocks extends ManageRecords
{
    protected static string $resource = ResourceLockResource::class;

    protected function getActions(): array
    {
        return [
            Action::make(__('resource-lock::manager.unlock_all'))
                ->icon('heroicon-o-lock-open')
                ->action(fn () => ResourceLock::truncate())
                ->requiresConfirmation()
        ];
    }
}
