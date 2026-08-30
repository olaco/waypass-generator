<?php

namespace App\Livewire\Waybill;

use App\Models\Waybill;
use Livewire\Component;
use Livewire\WithPagination;

class WaybillList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'page' => ['except' => 1],
    ];

    protected function getWaybills()
{
    $query = Waybill::with(['items', 'preparer'])  // Load preparer
        ->withCount('items');  // Count items efficiently

    if ($this->search) {
        $query->where(function($q) {
            $q->where('reference_no', 'like', '%' . $this->search . '%')
              ->orWhere('waybill_no', 'like', '%' . $this->search . '%')
              ->orWhere('gatepass_no', 'like', '%' . $this->search . '%');
        });
    }

    if ($this->statusFilter) {
        $query->where('status', $this->statusFilter);
    }

    return $query->orderBy($this->sortField, $this->sortDirection);
}
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'pending_warehouse', 'pending_auditor', 'pending_supply_chain', 'pending_final' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'returned', 'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabel($status)
    {
        return ucfirst(str_replace('_', ' ', $status));
    }

    public function render()
    {
        $query = Waybill::with(['items', 'preparer']);

        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('reference_no', 'like', '%' . $this->search . '%')
                  ->orWhere('waybill_no', 'like', '%' . $this->search . '%')
                  ->orWhere('gatepass_no', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $waybills = $query->paginate($this->perPage);

        return view('livewire.waybill.waybill-list', [
            'waybills' => $waybills,
        ]);
    }
}
