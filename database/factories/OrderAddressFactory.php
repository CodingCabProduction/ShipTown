<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderAddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company'       => $this->faker->company(),
            'gender'        => $this->faker->title(),
            'first_name'    => $this->faker->firstName(),
            'last_name'     => $this->faker->lastName(),
            'email'         => $this->faker->email(),
            'address1'      => $this->faker->streetAddress(),
            'address2'      => $this->faker->words(3, true),
            'postcode'      => $this->faker->postcode(),
            'city'          => $this->faker->city(),
            'state_code'    => $this->faker->countryCode(),
            'state_name'    => $this->faker->state(),
            'country_code'  => $this->faker->countryCode(),
            'country_name'  => $this->faker->country(),
            'phone'         => $this->faker->phoneNumber(),
            'fax'           => $this->faker->phoneNumber(),
            'website'       => $this->faker->url(),
            'region'        => $this->faker->word(),
        ];
    }
}
