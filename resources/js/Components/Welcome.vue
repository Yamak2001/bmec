<script setup>
// No additional logic required for now.
</script>

<template>
  <div class="min-h-screen bg-[#E3F5FF] text-[#014061]">
    <!-- Header Info Block -->
    <div class="bg-[#014061] text-white p-8 rounded-lg mb-8">
      <div class="flex items-center mb-4">
        <!-- Uncomment and update if you have a logo component -->
        <!-- <ApplicationLogo class="h-12 w-auto mr-4" /> -->
        <h1 class="text-3xl font-bold">
          Welcome to the Biomedical Engineering Club
        </h1>
      </div>
      <p class="text-lg">
        The Biomedical Engineering Club brings together students interested in medical technology and engineering. Our aim is to enhance knowledge and develop skills in this evolving field through workshops, events, and innovative projects. We strive to cultivate a creative generation of engineers capable of making a positive impact in the healthcare sector.
      </p>
    </div>

    <!-- Events Section -->
    <div class="space-y-6">
      <!-- Row 2: Two Event Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Event 1: 3D-Printing Basics -->
        <div class="bg-white border border-[#0349AC] rounded-lg p-6 shadow-md">
          <div class="h-40 bg-[#6BACC8] rounded mb-4 flex items-center justify-center text-white">
            Image Placeholder
          </div>
          <h3 class="text-2xl font-semibold text-[#0349AC] mb-2">
            3D-Printing Basics
          </h3>
          <p class="mb-4">
            Learn the fundamentals of 3D printing in biomedical applications.
          </p>
          <a href="#3dprinting" class="text-[#0349AC] font-semibold hover:underline">
            Learn More
          </a>
        </div>

        <!-- Event 2: VR Development Basics -->
        <div class="bg-white border border-[#0349AC] rounded-lg p-6 shadow-md">
          <div class="h-40 bg-[#6BACC8] rounded mb-4 flex items-center justify-center text-white">
            Image Placeholder
          </div>
          <h3 class="text-2xl font-semibold text-[#0349AC] mb-2">
            VR Development Basics
          </h3>
          <p class="mb-4">
            Explore how virtual reality is transforming biomedical simulations.
          </p>
          <a href="#vrdevelopment" class="text-[#0349AC] font-semibold hover:underline">
            Learn More
          </a>
        </div>
      </div>

      <!-- Row 3: Full-Width Event Card -->
      <div class="bg-white border border-[#0349AC] rounded-lg p-6 shadow-md">
        <div class="h-40 bg-[#6BACC8] rounded mb-4 flex items-center justify-center text-white">
          Image Placeholder
        </div>
        <h3 class="text-2xl font-semibold text-[#0349AC] mb-2">
          Arduino Basics
        </h3>
        <p class="mb-4">
          Dive into the world of microcontrollers and biomedical instrumentation with Arduino.
        </p>
        <a href="#arduinobasics" class="text-[#0349AC] font-semibold hover:underline">
          Learn More
        </a>
      </div>

      <!-- Row 4: Two Event Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Event 4: Student Course Guide -->
        <div class="bg-white border border-[#0349AC] rounded-lg p-6 shadow-md">
          <div class="h-40 bg-[#6BACC8] rounded mb-4 flex items-center justify-center text-white">
            Image Placeholder
          </div>
          <h3 class="text-2xl font-semibold text-[#0349AC] mb-2">
            Student Course Guide
          </h3>
          <p class="mb-4">
            Find the right courses to shape your biomedical engineering career.
          </p>
          <a href="#courseguide" class="text-[#0349AC] font-semibold hover:underline">
            Learn More
          </a>
        </div>

        <!-- Event 5: BME Paths Explained -->
        <div class="bg-white border border-[#0349AC] rounded-lg p-6 shadow-md">
          <div class="h-40 bg-[#6BACC8] rounded mb-4 flex items-center justify-center text-white">
            Image Placeholder
          </div>
          <h3 class="text-2xl font-semibold text-[#0349AC] mb-2">
            BME Paths Explained
          </h3>
          <p class="mb-4">
            Hear from our esteemed doctors about various career paths in biomedical engineering.
          </p>
          <a href="#bmepathsexplained" class="text-[#0349AC] font-semibold hover:underline">
            Learn More
          </a>
        </div>
      </div>

      <!-- Row 5: Full-Width Event Card for Our Team -->
      <div class="bg-white border border-[#0349AC] rounded-lg p-6 shadow-md">
        <div class="h-40 bg-[#6BACC8] rounded mb-4 flex items-center justify-center text-white">
          Image Placeholder
        </div>
        <h3 class="text-2xl font-semibold text-[#0349AC] mb-2">
          Our Team
        </h3>
        <p class="mb-4">
          Meet the passionate team driving innovation in biomedical engineering.
        </p>
        <a href="#ourteam" class="text-[#0349AC] font-semibold hover:underline">
          Learn More
        </a>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* You can add additional custom styles if needed */
</style>
