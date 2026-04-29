<?php

namespace Database\Seeders;

use App\Models\Day;
use App\Models\DayRange;
use App\Models\Tip;
use App\Models\Week;
use Illuminate\Database\Seeder;

class TipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $week1 = Week::where('week', 1)->first();
        $days = Day::all();
        $ranges = DayRange::all();

        if (! $week1 || $days->isEmpty() || $ranges->isEmpty()) {
            return;
        }

        $tips_data = [
            'Morning' => [
                'Start your day with a healthy breakfast. Proper nutrition is key for your baby\'s development.',
                'Remember to take your folic acid supplement this morning.',
                'Stay hydrated! Drink a glass of water first thing in the morning.',
                'A gentle stretch in the morning can help with early pregnancy fatigue.',
                'Try to eat small, frequent meals if you feel morning sickness.',
                'Plan your healthy snacks for the day.',
                'Take a moment to breathe and connect with your body.',
            ],
            'Afternoon' => [
                'Take a short walk during your lunch break to keep your blood flowing.',
                'Rest for 15 minutes if you\'re feeling tired. Your body is working hard!',
                'Choose healthy snacks like fruit or nuts for your afternoon break.',
                'Avoid caffeine in the afternoon to help you sleep better tonight.',
                'Keep drinking water throughout the afternoon.',
                'If you\'re at work, make sure your chair is comfortable.',
                'Listen to some calming music to reduce stress.',
            ],
            'Evening' => [
                'Wind down with a warm bath or a good book this evening.',
                'Connect with your partner or a friend and share your feelings about the pregnancy.',
                'Eat a light, nutritious dinner. Avoid heavy meals close to bedtime.',
                'Prepare your clothes and meals for tomorrow to reduce morning stress.',
                'Dim the lights an hour before bed to signal your body it\'s time to rest.',
                'Practice some deep breathing exercises.',
                'Write in a pregnancy journal about your day.',
            ],
            'Night' => [
                'Try to get 8 hours of sleep. Your body needs it for the baby\'s growth.',
                'Sleep on your side if you can; it\'s generally recommended during pregnancy.',
                'Keep a glass of water by your bed in case you wake up thirsty.',
                'If you can\'t sleep, try some gentle relaxation techniques.',
                'Avoid screens before bed for better sleep quality.',
                'Think positive thoughts about your baby before you drift off.',
                'Rest is the best medicine for your growing baby.',
            ],
        ];

        foreach ($days as $index => $day) {
            if ($index >= 7) {
                break;
            } // Only first 7 days

            foreach ($ranges as $range) {
                $tip_text = $tips_data[$range->name][$index] ?? 'Stay healthy and positive!';

                Tip::create([
                    'week_id' => $week1->id,
                    'day_id' => $day->id,
                    'day_range_id' => $range->id,
                    'tip' => $tip_text,
                    'organization_id' => 1,
                    'status' => Tip::STATUS_APPROVED,
                    'is_template' => 1,
                ]);
            }
        }
    }
}
