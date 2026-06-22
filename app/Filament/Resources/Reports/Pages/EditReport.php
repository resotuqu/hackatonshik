<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Enums\ReportStatus;
use App\Filament\Resources\Reports\ReportResource;
use Filament\Resources\Pages\EditRecord;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['status'] ?? null) !== ReportStatus::Pending->value) {
            $data['reviewed_by'] = auth()->id();
            $data['reviewed_at'] = now();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record->wasChanged('status') && $this->record->status !== ReportStatus::Pending) {
            activity('report')
                ->performedOn($this->record)
                ->causedBy(auth()->user())
                ->withProperties(['status' => $this->record->status->value])
                ->log('moderation_resolved');
        }
    }
}
