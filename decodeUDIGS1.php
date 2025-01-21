<?php
/**
 * File: decodeUDIGS1.php
 * Author: Johan MÉLINE
 * GitHub: https://github.com/JohanMeline/decodeUDIGS1
 * Version: 1.2.0
 * Last modified: January 21, 2025
 * Description: This script decodes UDI GS1 codes into structured data.
 */

function decodeUDIGS1($udiCode) {
    // Initialize an empty array to store decoded data
    $decodedData = array();
    $remainingData = '';

    // Define an array to map identifier lengths, types, and fixed/dynamic status
    $identifierSpecifications = [
        '01' => ['length' => 14, 'type' => 'numeric', 'fixed' => true], // GTIN
        '11' => ['length' => 6, 'type' => 'numeric', 'fixed' => true], // Production Date
        '17' => ['length' => 6, 'type' => 'numeric', 'fixed' => true], // Expiration Date
        '10' => ['length' => 20, 'type' => 'alphanumeric', 'fixed' => false], // Batch/Lot Number
        '21' => ['length' => 20, 'type' => 'alphanumeric', 'fixed' => false] // Serial Number
        // '91' => ['length' => 30, 'type' => 'alphanumeric', 'fixed' => false] // Custom Identifier
    ];

    // Initialize decodedData with all possible identifiers
    foreach ($identifierSpecifications as $identifier => $spec) {
        $decodedData[$identifier] = null;
    }

    // Remove parentheses and spaces from the UDI code
    $udiCode = str_replace(['(', ')', ' '], '', $udiCode);

    // Start index for the current identifier
    $startIndex = 0;

    // Iterate through the UDI code
    while ($startIndex < strlen($udiCode)) {
        // Extract the identifier
        $identifier = substr($udiCode, $startIndex, 2);
        
        // Check for FNC1 character (ASCII 29)
        if (ord($udiCode[$startIndex]) == 29) {
            $startIndex++;
            continue;
        }

        // Check if the identifier is valid
        if (isset($identifierSpecifications[$identifier])) {
            // Determine the length of the value
            $maxLength = $identifierSpecifications[$identifier]['length'];
            $isFixed = $identifierSpecifications[$identifier]['fixed'];
            $value = '';

            if ($isFixed) {
                // For fixed length identifiers
                $value = substr($udiCode, $startIndex + 2, $maxLength);
            } else {
                // For dynamic length identifiers
                for ($i = $startIndex + 2; $i < strlen($udiCode) && $i < $startIndex + 2 + $maxLength; $i++) {
                    if (ord($udiCode[$i]) == 29) {
                        break;
                    }
                    $value .= $udiCode[$i];
                }
            }

            // Store the identifier and its value in the decoded data array
            $decodedData[$identifier] = $value;
            // Move the start index to the next identifier
            $startIndex += 2 + strlen($value);
        } else {
            // Append the current character to remainingData if it's not a valid identifier
            $remainingData .= $udiCode[$startIndex];
            // Move to the next character
            $startIndex++;
        }
    }

    // Add remainingData to the decodedData array
    $decodedData['remainingData'] = $remainingData;

    return json_encode($decodedData);
}

if (isset($_POST['udiCode'])) {
    // Call the decodeUDIGS1 function and echo the result
    $udiCode = $_POST['udiCode'];
    $decodedResult = decodeUDIGS1($udiCode); // Assuming decodeUDIGS1 function is defined
    print_r($decodedResult);
} else {
    echo "UDI code not provided";
}

// Example usage:
// $udiCode = "010084794601633317290413100224";
// $udiCode = "01050601671212411725100110P587591PADPAK04/HeartSineP5875"; //still an issue with (91)
// $udiCode = "010506016712124110L020517280801";
// $udiCode = "0100812394020935112408211726112110240821-01";
// $udiCode = "(01) 00812394020881(11) 240729(10) 16421210";
// $decodedData = decodeUDIGS1($udiCode);
// print_r($decodedData);
