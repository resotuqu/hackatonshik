<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\OrganizerApplicationStatus;
use App\Enums\OrganizerEntityType;
use App\Support\OrganizerApplicationRules;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class OrganizerApplicationModal extends Component
{
    use Toast;

    public bool $showModal = false;

    public string $organizerEntityType = 'individual';

    public string $organizerCompanyName = '';

    public string $organizerNote = '';

    public ?string $adminNote = null;

    public ?OrganizerApplicationStatus $applicationStatus = null;

    public function mount(): void
    {
        $this->loadApplicationState(openModal: true);
    }

    public function openModal(): void
    {
        $this->loadApplicationState(openModal: true);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function resubmit(): void
    {
        $user = Auth::user();
        abort_if($user === null, 403);

        $application = $user->organizerApplication;

        if ($application === null || ! $application->isRejected()) {
            return;
        }

        $this->validate(
            OrganizerApplicationRules::forFields(entityType: $this->organizerEntityType),
        );

        $application->resubmit(
            OrganizerEntityType::from($this->organizerEntityType),
            $this->organizerEntityType === OrganizerEntityType::Company->value
                ? $this->organizerCompanyName
                : null,
            $this->organizerNote,
        );

        $this->applicationStatus = OrganizerApplicationStatus::Pending;
        $this->adminNote = null;
        $this->showModal = true;

        $this->success('Заявка отправлена повторно', position: 'toast-center toast-top');
    }

    public function render()
    {
        return view('livewire.organizer-application-modal');
    }

    private function loadApplicationState(bool $openModal): void
    {
        $user = Auth::user();

        if ($user === null || $user->isOrganizer()) {
            $this->applicationStatus = null;

            return;
        }

        $application = $user->organizerApplication;

        if ($application === null) {
            $this->applicationStatus = null;

            return;
        }

        if (! in_array($application->status, [
            OrganizerApplicationStatus::Pending,
            OrganizerApplicationStatus::Rejected,
        ], true)) {
            $this->applicationStatus = null;

            return;
        }

        $this->applicationStatus = $application->status;
        $this->organizerEntityType = $application->entity_type->value;
        $this->organizerCompanyName = (string) ($application->company_name ?? '');
        $this->organizerNote = $application->note;
        $this->adminNote = $application->admin_note;

        if ($openModal) {
            $this->showModal = true;
        }
    }
}
