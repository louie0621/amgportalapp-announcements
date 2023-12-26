<?php

namespace AMGPortal\Announcements\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AMGPortal\Announcements\Announcement;

class AnnouncementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Announcement::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->title,
            'body' => $this->faker->paragraph(2),
            'user_id' => function () {
                return \AMGPortal\User::factory()->create()->id;
            },
        ];
    }
}
