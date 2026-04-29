<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class SettingsLivewire extends Component
{
    use LivewireAlert;

    public $timezone;

    public $serverTime;

    public function mount()
    {
        $this->timezone = Setting::where('key', 'app_timezone')->value('value') ?? config('app.timezone');
        $this->updateServerTime();
    }

    public function updateServerTime()
    {
        $this->serverTime = Carbon::now($this->timezone)->format('Y-m-d H:i:s');
    }

    public function saveTimezone()
    {
        try {
            // Validate timezone
            Carbon::now($this->timezone);

            Setting::updateOrCreate(
                ['key' => 'app_timezone'],
                ['value' => $this->timezone]
            );

            $this->updateServerTime();
            $this->alert('success', 'Timezone updated successfully.');
        } catch (\Exception $e) {
            $this->alert('error', 'Invalid timezone provided.');
        }
    }

    public function render()
    {
        $timezones = \DateTimeZone::listIdentifiers();

        return view('livewire.settings.settings-livewire', [
            'timezones' => $timezones,
        ]);
    }
}
