<?php

namespace Tests\Feature;

use Tests\TestCase;

class BenfordsLawTest extends TestCase
{
    /**
     * Test Benford's Law compliance check with a conforming set of numbers.
     *
     * @return void
     */
    public function testConformingSet()
    {
        // This should be a set of numbers that you expect to conform to Benford's Law
        $conformingNumbers = [12, 22, 38, 14, 25, 36, 17, 28, 39, 110, 211, 312, 413, 514, 615, 716, 817, 918, 1019, 1120];

        $response = $this->postJson('/api/check-benfords-law', ['numbers' => $conformingNumbers]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'conforms_to_benford' => true,
            ]);
    }

    /**
     * Test Benford's Law compliance check with a non-conforming set of numbers.
     *
     * @return void
     */
    public function testNonConformingSet()
    {
        // This should be a set of numbers that you expect not to conform to Benford's Law
        $nonConformingNumbers = [222, 223, 224, 225, 226, 227, 228, 229, 230];

        $response = $this->postJson('/api/check-benfords-law', ['numbers' => $nonConformingNumbers]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'conforms_to_benford' => false,
            ]);
    }

    /**
     * Test Benford's Law compliance check with invalid input.
     *
     * @return void
     */
    public function testInvalidInput()
    {
        $invalidInput = 'not an array of numbers';

        $response = $this->postJson('/api/check-benfords-law', ['numbers' => $invalidInput]);

        $response->assertStatus(400);
    }

    /**
     * Test Benford's Law compliance check with a synthetic large set of numbers.
     *
     * @return void
     */
    public function testLargeConformingSet()
    {
        // Generate a large dataset that conforms to Benford's Law
        $conformingNumbers = $this->generateBenfordsLawConformingNumbers(1000); // Generate 1000 numbers

        $response = $this->postJson('/api/check-benfords-law', ['numbers' => $conformingNumbers]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'conforms_to_benford' => true,
            ]);
    }
    /**
     * Generate a set of numbers that conforms to Benford's Law.
     *
     * @param int $size Number of elements to generate.
     * @return array
     */
    private function generateBenfordsLawConformingNumbers($size)
    {
        $numbers = [];
        for ($i = 0; $i < $size; $i++) {
            $digit = $this->randomBenfordDigit();
            // Ensure the magnitude results in a number within integer range
            $magnitude = rand(1, 6);
            // Add a random number to the end to avoid all numbers being multiples of 10
            $numbers[] = $digit * pow(10, $magnitude) + rand(0, pow(10, $magnitude) - 1);
        }

        // Ensure the numbers are all positive integers
        $numbers = array_map('abs', $numbers);
        $numbers = array_map('intval', $numbers);

        return $numbers;
    }
    /**
     * Randomly generate a digit according to Benford's Law distribution.
     *
     * @return int
     */
    private function randomBenfordDigit()
    {
        $probs = [30.1, 17.6, 12.5, 9.7, 7.9, 6.7, 5.8, 5.1, 4.6]; // Approximate Benford's Law probabilities
        $cumulative = [];
        $sum = 0;
        foreach ($probs as $p) {
            $sum += $p;
            $cumulative[] = $sum;
        }
        $randPercent = rand(0, 1000) / 10; // Get a random percentage
        foreach ($cumulative as $index => $cumulativeProbability) {
            if ($randPercent <= $cumulativeProbability) {
                return $index + 1;
            }
        }
        return 1; // Fallback to 1 if something goes wrong
    }
}
