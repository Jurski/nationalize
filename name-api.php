<?php

echo "Find out age, gender, nationality based on name..." . PHP_EOL;

$nameInput = trim(strtolower(readline("Enter the name of person: ")));

if ($nameInput === "") {
    echo "Empty input detected, please enter a name" . PHP_EOL;
    exit;
}

$urls = [
    "agify" => "https://api.agify.io/?name=$nameInput",
    "genderize" => "https://api.genderize.io/?name=$nameInput",
    "nationalize" => "https://api.nationalize.io/?name=$nameInput",
];

$responses = [];

foreach ($urls as $index => $url) {
    try {
        $nameJSON = file_get_contents($url);

        if ($nameJSON === false) {
            throw new Exception("Network error or invalid input");
        }

        $nameData = json_decode($nameJSON);

        if ($nameData === null) {
            throw new Exception("Error parsing name data");
        }

        $responses[$index] = $nameData;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . PHP_EOL;
        exit;
    }
}

$age = $responses["agify"]->age;
$gender = $responses["genderize"]->gender;
$genderProbability = $responses["genderize"]->probability;
$nationality = $responses["nationalize"]->country[0]->country_id;
$nationalityProbability = $responses["nationalize"]->country[0]->probability;

function probabilityTransform(float $probability): string
{
    if ($probability === 0.99) return "100%";

    $percentage = floor($probability * 100);
    return "{$percentage}%";
}

$genderProbabilityTransformed = probabilityTransform($genderProbability);
$nationalityProbabilityTransformed = probabilityTransform($nationalityProbability);

echo "$nameInput is $age years old" . PHP_EOL;
echo "$nameInput is $gender with probablity of $genderProbabilityTransformed" . PHP_EOL;
echo "$nameInput's nationality is $nationality with probablity of $nationalityProbabilityTransformed" . PHP_EOL;

