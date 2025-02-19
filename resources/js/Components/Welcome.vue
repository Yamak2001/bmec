<script setup>
// No additional script logic is needed for now.
</script>

<template>
    <div class="club-landing">
        <!-- Header Info Block -->
        <div class="header-block">
            <!-- Uncomment and use your logo component if needed -->
            <!-- <ApplicationLogo class="header-logo" /> -->
            <h1 class="header-title">Welcome to the Biomedical Engineering Club</h1>
            <p class="header-text">
                The Biomedical Engineering Club brings together students interested in medical technology and
                engineering. Our aim is to enhance knowledge and develop skills in this evolving field through
                workshops, events, and innovative projects. We strive to cultivate a creative generation of engineers
                capable of making a positive impact in the healthcare sector.
            </p>
        </div>

        <!-- Events Section -->
        <div class="events-section">
            <!-- Row 2: Two Event Cards -->
            <div class="grid-two">
                <div class="event-card">
                    <div class="event-image">Image Placeholder</div>
                    <h3 class="event-title">3D-Printing Basics</h3>
                    <p class="event-description">
                        Learn the fundamentals of 3D printing in biomedical applications.
                    </p>
                    <a href="#3dprinting" class="event-link">Learn More</a>
                </div>
                <div class="event-card">
                    <div class="event-image">Image Placeholder</div>
                    <h3 class="event-title">VR Development Basics</h3>
                    <p class="event-description">
                        Explore how virtual reality is transforming biomedical simulations.
                    </p>
                    <a href="#vrdevelopment" class="event-link">Learn More</a>
                </div>
            </div>

            <!-- Row 3: Full-Width Event Card -->
            <div class="grid-one">
                <div class="event-card">
                    <div class="event-image">Image Placeholder</div>
                    <h3 class="event-title">Arduino Basics</h3>
                    <p class="event-description">
                        Dive into the world of microcontrollers and biomedical instrumentation with Arduino.
                    </p>
                    <a href="#arduinobasics" class="event-link">Learn More</a>
                </div>
            </div>

            <!-- Row 4: Two Event Cards -->
            <div class="grid-two">
                <div class="event-card">
                    <div class="event-image">Image Placeholder</div>
                    <h3 class="event-title">Student Course Guide</h3>
                    <p class="event-description">
                        Find the right courses to shape your biomedical engineering career.
                    </p>
                    <a href="#courseguide" class="event-link">Learn More</a>
                </div>
                <div class="event-card">
                    <div class="event-image">Image Placeholder</div>
                    <h3 class="event-title">BME Paths Explained</h3>
                    <p class="event-description">
                        Hear from our esteemed doctors about various career paths in biomedical engineering.
                    </p>
                    <a href="#bmepathsexplained" class="event-link">Learn More</a>
                </div>
            </div>

            <!-- Row 5: Full-Width Our Team Event Card -->
            <div class="grid-one">
                <div class="event-card">
                    <div class="event-image">Image Placeholder</div>
                    <h3 class="event-title">Our Team</h3>
                    <p class="event-description">
                        Meet the passionate team driving innovation in biomedical engineering.
                    </p>
                    <a href="#ourteam" class="event-link">Learn More</a>
                </div>
            </div>
            <!-- Row 6: Three Meet The Team Cards -->
            <div class="grid-three">
                <div class="event-card">
                    <div class="event-image">Image Placeholder</div>
                    <h3 class="event-title">Meet</h3>
                    <p class="event-description">
                        The 2024/25 BMEC Team
                    </p>
                    <a href="#meettheteam" class="event-link">Learn More</a>
                </div>
                <div class="event-card">
                    <div class="event-image">Image Placeholder</div>
                    <h3 class="event-title">The</h3>
                    <p class="event-description">
                        Hear from our esteemed doctors about various career paths in biomedical engineering.
                    </p>
                    <a href="#bmepathsexplained" class="event-link">Learn More</a>
                </div>
                <div class="event-card">
                    <div class="event-image">Image Placeholder</div>
                    <h3 class="event-title">Team</h3>
                    <p class="event-description">
                        Hear from our esteemed doctors about various career paths in biomedical engineering.
                    </p>
                    <a href="#bmepathsexplained" class="event-link">Learn More</a>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.club-landing {
    /* Define color variables */
    --dark-blue: #014061;
    --blue: #0349AC;
    --light-blue: #6BACC8;
    --baby-blue: #E3F5FF;
    --white: #FFFFFF;

    background-color: var(--white);
    color: var(--dark-blue);
    font-family: Arial, sans-serif;
    padding: 20px;
}

/* Header Block */
.header-block {
    background-color: var(--dark-blue);
    color: var(--white);
    padding: 24px;
    border-radius: 8px;
    margin-bottom: 32px;
}

.header-title {
    font-size: 2rem;
    font-weight: bold;
    margin: 16px 0;
}

.header-text {
    font-size: 1rem;
    line-height: 1.5;
}

/* Events Section */
.events-section {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Grid Layouts */

.grid-one {
    display: grid;
    grid-template-columns: 1fr;
}

.grid-two {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

.grid-three {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 24px;
}

/* Event Card */
.event-card {
    background-color: var(--baby-blue);
    border: 2px solid var(--dark-blue);
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Event Image Placeholder */
.event-image {
    background-color: var(--light-blue);
    height: 160px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    margin-bottom: 16px;
    font-size: 1.2rem;
}

/* Event Title */
.event-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--dark-blue);
    margin-bottom: 8px;
}

/* Event Description */
.event-description {
    font-size: 1rem;
    margin-bottom: 12px;
}

/* Event Link */
.event-link {
    color: var(--blue);
    font-weight: 600;
    text-decoration: none;
}

.event-link:hover {
    text-decoration: underline;
}
</style>
