<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = [
            [
                'title' => 'Full Stack Developer',
                'description' => 'We are looking for an experienced Full Stack Developer to join our team. You will be responsible for developing both frontend and backend applications using modern technologies like Laravel, Vue.js, and React.',
            ],
            [
                'title' => 'Frontend Developer',
                'description' => 'Join our frontend team to build beautiful and responsive user interfaces. Experience with Vue.js, React, or Angular is required. You will work closely with designers to implement pixel-perfect designs.',
            ],
            [
                'title' => 'Backend Developer',
                'description' => 'We need a skilled Backend Developer proficient in Laravel and PHP. You will design and implement RESTful APIs, work with databases, and ensure the security and performance of our applications.',
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => 'Looking for a DevOps Engineer to manage our cloud infrastructure. Experience with AWS, Docker, Kubernetes, and CI/CD pipelines is essential. You will ensure our applications run smoothly in production.',
            ],
            [
                'title' => 'UI/UX Designer',
                'description' => 'We are seeking a creative UI/UX Designer to craft intuitive user experiences. You should be proficient in Figma or Adobe XD and have a strong portfolio showcasing your design skills.',
            ],
            [
                'title' => 'Mobile Developer',
                'description' => 'Join our mobile development team to create cross-platform applications using React Native or Flutter. You will work on both iOS and Android platforms to deliver high-quality mobile experiences.',
            ],
        ];

        foreach ($jobs as $job) {
            Job::create($job);
        }
    }
}
