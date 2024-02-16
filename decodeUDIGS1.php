<?php
/**
 * File: decodeUDIGS1.php
 * Author: Johan MÉLINE
 * GitHub: https://github.com/JohanMeline/decodeUDIGS1
 * Version: 1.0.0
 * Last modified: February 16, 2024
 * Description: This script decodes UDI GS1 codes into structured data.
 */

function decodeUDIGS1($udiCode) {
    // Initialize an empty array to store decoded data
    $decodedData = array();

    // Define an array to map identifier lengths
    $identifierLengths = [
        '01' => 14,
        '11' => 6,
        '17' => 6,
        '10' => 20,
        '21' => 20
    ];

    // Start index for the current identifier
    $startIndex = 0;

    // Iterate through the UDI code
    while ($startIndex < strlen($udiCode)) {
        // Extract the identifier
        $identifier = substr($udiCode, $startIndex, 2);
        // Check if the identifier is valid
        if (isset($identifierLengths[$identifier])) {
            // Extract the value based on the predefined length for the identifier
            $valueLength = $identifierLengths[$identifier];
            $value = substr($udiCode, $startIndex + 2, $valueLength);
            // Store the identifier and its value in the decoded data array
            $decodedData[$identifier] = $value;
            // Move the start index to the next identifier
            $startIndex += 2 + $valueLength;
        } else {
            // Move to the next character if the current one is not a valid identifier
            $startIndex++;
        }
    }

    return $decodedData;
}

// Example usage:
$udiCode = "010084794601633317290413100224";
$decodedData = decodeUDIGS1($udiCode);
print_r($decodedData);
