<?php

namespace App\Livewire\Waybill;

use App\Models\Waybill;
use Livewire\Component;
use Livewire\WithPagination;

class WaybillApproved extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $waybills = Waybill::with(['items', 'preparer'])
            ->where('status', 'approved')
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('reference_no', 'like', '%' . $this->search . '%')
                      ->orWhere('waybill_no', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.waybill.waybill-approved', [
            'waybills' => $waybills,
        ]);
    }
}
