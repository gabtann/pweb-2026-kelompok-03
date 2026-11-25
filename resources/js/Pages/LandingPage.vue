<script setup>
import { Head } from '@inertiajs/vue3';
import LandingLayout from '@/Layouts/LandingLayout.vue';
import HeroSection from '@/Components/Landing/HeroSection.vue';
import AboutSection from '@/Components/Landing/AboutSection.vue';
import CompetitionCarousel from '@/Components/Landing/CompetitionCarousel.vue';
import TimelineSection from '@/Components/Landing/TimelineSection.vue';
import FaqSection from '@/Components/Landing/FaqSection.vue';
import PrizeSection from '@/Components/Landing/PrizeSection.vue';
</script>

<template>
  <Head title="Iterate 2026 — Youth Powering a New Era" />

  <LandingLayout>
    <!-- 1. Hero Section -->
    <HeroSection />

    <!-- 2. Section: What Is? -->
    <AboutSection />

    <!-- 3. Section: Whats On Iterate 2026? (Competition Slider) -->
    <CompetitionCarousel />

    <!-- 4. Section: Timeline -->
    <TimelineSection />

    <!-- 5. Section: FAQ (Frequently Asked Questions) -->
    <FaqSection />

    <!-- 6. Section: Total Prize -->
    <PrizeSection />
  </LandingLayout>
</template>
