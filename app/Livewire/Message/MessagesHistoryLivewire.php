<?php
namespace App\Livewire\Message;
use App\Models\MessageHistory;
use Livewire\Component;
use Livewire\WithPagination;

class MessagesHistoryLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $modal = false;
    public $search = '';
    public $status = '';
    public $perPage = 10;
    public $selected_message = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function viewResponse($id)
    {
        $this->selected_message = MessageHistory::find($id);
        $this->modal = true;
    }

    public function cancel(){
        $this->reset([
            'modal',
            'selected_message'
        ]);
    }

    public function render()
    {
        $messages = MessageHistory::with(['mother', 'tip', 'week', 'day'])
            ->when($this->search, function ($query) {
                $query->whereHas('mother', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                })->orWhereHas('tip', function ($q) {
                    $q->where('tip', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                $query->where('message_status', $this->status);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.message.messages-history-livewire', [
            'messages' => $messages
        ]);
    }
}
