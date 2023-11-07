<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BenfordsLawController extends Controller
{
    /**
     * Check if a given set of numbers conforms to Benford's Law.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkBenfordsLaw(Request $request)
    {
        // Retrieve the 'numbers' array from the request input
        $numbers = $request->input('numbers');

        // Validate input: ensure it's an array and not empty
        if (!is_array($numbers) || empty($numbers)) {
            // If validation fails, return an error response with a 400 Bad Request status code
            return response()->json(['error' => 'Invalid input. Please provide an array of numbers.'], 400);
        }

        // Check each number to ensure it is a positive integer
        foreach ($numbers as $number) {
            if (!is_int($number) || $number <= 0) {
                // If any number is not a positive integer, return an error response
                return response()->json(['error' => 'All elements must be positive integers.'], 400);
            }
        }

        // Use array_map to extract the first digit from each number using the firstDigit function
        $firstDigits = array_map(function ($number) {
            return $this->firstDigit($number);
        }, $numbers);

        // Count the frequency of each first digit
        $digitCount = array_count_values($firstDigits);
        // Sort the array by keys (digit) to ensure order from 1 to 9
        ksort($digitCount);

        // Calculate the expected Benford's distribution for each first digit from 1 to 9
        $benfordsDistribution = [];
        for ($digit = 1; $digit <= 9; $digit++) {
            $benfordsDistribution[$digit] = log10(1 + 1 / $digit);
        }

        // Calculate the observed distribution by dividing the count of each digit by the total number of numbers
        $totalNumbers = count($numbers);
        $observedDistribution = [];
        foreach ($digitCount as $digit => $count) {
            $observedDistribution[$digit] = $count / $totalNumbers;
        }

        // Determine whether the set of numbers conforms to Benford's Law based on the observed and expected distributions
        $conformsToBenford = $this->doesConformToBenford($observedDistribution, $benfordsDistribution);

        // Return the result as a JSON response, including conformity status and distributions
        return response()->json([
            'conforms_to_benford' => $conformsToBenford,
            'observed_distribution' => $observedDistribution,
            'benfords_distribution' => $benfordsDistribution
        ]);
    }

    /**
     * Extract the first digit of a given number.
     *
     * @param int $num The number to extract from.
     * @return int The first digit of the number.
     */
    function firstDigit(int $num): int
    {
        // Continuously divide the number by 10 until it is less than 10, then floor it to get the first digit
        while ($num >= 10) {
            $num /= 10;
        }
        return (int) floor($num);
    }

    /**
     * Check if the observed distribution of first digits conforms to the expected Benford's distribution.
     *
     * @param array $observed The observed distribution of first digits.
     * @param array $expected The expected Benford's distribution.
     * @return bool True if the observed distribution conforms to the expected distribution, False otherwise.
     */
    private function doesConformToBenford(array $observed, array $expected)
    {
        // Define a threshold for how much the observed distribution can deviate from the expected distribution
        $threshold = 0.1; // This value is somewhat arbitrary and can be adjusted to be more or less strict.

        // Loop through the expected distribution and compare it to the observed distribution
        foreach ($expected as $digit => $probability) {
            // If an expected digit is missing in the observed distribution, set its frequency to 0
            if (!isset($observed[$digit])) {
                $observed[$digit] = 0;
            }
            // If the absolute difference between the observed and expected frequencies is greater than the threshold, return False
            if (abs($observed[$digit] - $probability) > $threshold) {
                return false;
            }
        }

        // If the loop completes without returning False, the numbers conform to Benford's Law
        return true;
    }
}
