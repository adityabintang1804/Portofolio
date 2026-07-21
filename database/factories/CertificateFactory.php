<?php

namespace Database\Factories;

use App\Models\CertificateCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'certificate_category_id' => CertificateCategory::factory(),
            'title' => fake()->sentence(4),
            'issuer' => fake()->company(),
            'issued_at' => fake()->dateTimeBetween('-3 years', 'now'),
            'certificate_image' => 'placeholders/certificate-placeholder.svg',
            'is_active' => true,
        ];
    }
}
