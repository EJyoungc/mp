<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class History extends Model
{
    use HasFactory;


    protected $fillable =
    [
        'mother_id',
        'infant_number',
        'last_menstrual_cycle',
    ];


    protected $casts = [

        // 'last_menstrual_cycle'=>'date'
    ];

    public function week(){
        return $this->belongsTo(Week::class);
    }

    public function mother(){
        return $this->belongsTo(User::class,'mother_id');
    }


    public function calculate_week()
    {

        // Calculate the number of weeks since the last menstrual cycle
        $date = Carbon::parse($this->last_menstrual_cycle);
        $now = Carbon::now();
        (int)$weeks = max(1, ceil($date->diffInDays($now) / 7));

        // Get the current day of the week (1 for Monday, 7 for Sunday)
        $dayOfWeek = $now->isoWeekday(); // 1 (Monday) to 7 (Sunday)

        // Check if current time is within a specified range
        $startTime = Carbon::createFromTimeString('11:00'); // Example start time
        $endTime = Carbon::createFromTimeString('15:00');   // Example end time

        if ($now->between($startTime, $endTime)) {
            // Execute your logic here if the current time is within the range
            Log::info('Time is within the specified range.');
        } else {
            Log::info('Time is outside the specified range.');
        }

        return [
            'weeks' => $weeks,
            'day_of_week' => $dayOfWeek,
            'within_time_range' => $now->between($startTime, $endTime)
        ];
    }

    public function day_range()
    {
        return $this->belongsTo(DayRange::class);
    }



    public function calculate_weekv2()
    {
        // Calculate the number of weeks since the last menstrual cycle
        $date = Carbon::parse($this->last_menstrual_cycle);
        $now = Carbon::now();
        (int)$weeks = max(1, ceil($date->diffInDays($now) / 7));

        // Get the current day of the week (1 for Monday, 7 for Sunday)
        $dayOfWeek = $now->isoWeekday(); // 1 (Monday) to 7 (Sunday)

        // Check if current time is within a specified range
        $startTime = Carbon::createFromTimeString('11:00'); // Example start time
        $endTime = Carbon::createFromTimeString('15:00');   // Example end time


        $last_menstrual_cycle = Carbon::parse($this->last_menstrual_cycle);
        $isToday = $last_menstrual_cycle->isToday();



        $now = Carbon::now();

        $days = (int)abs(round($now->diffInDays($last_menstrual_cycle), 0, PHP_ROUND_HALF_DOWN));
        // dd($days, $now->diffInDays($last_menstrual_cycle), $last_menstrual_cycle, $now);

        return [
            'days' => $days,
            'weeks' => $weeks,
            'day_of_week' => $dayOfWeek,
            'within_time_range' => $now->between($startTime, $endTime)
        ];

    }
}
