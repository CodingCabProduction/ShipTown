<?php

namespace Database\Factories;

use App\Mail\OrderMail;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailTemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'mailable' => OrderMail::class,
            'html_template' => $this->faker->randomHtml(),
        ];
    }
}
