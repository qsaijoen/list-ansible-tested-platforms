#!/usr/bin/php
<?php
$searchDir = count($argv) == 2 ? $argv[1] : '.';

if (!is_dir($searchDir)) {
    echo("Error: $searchDir is not a directory\n");

    exit(1);
}

$uniquePlatforms  = [];
$platformsPerRole = [];

$roles = getAnsibleRoles($searchDir);

foreach ($roles as $role) {
    $platforms = getTestedPlatforms("$searchDir/$role");

    $uniquePlatforms = array_unique([...$platforms, ...$uniquePlatforms]);
    $platformsPerRole[$role] = $platforms;
}

echo(generateMarkdownOutput($uniquePlatforms, $platformsPerRole));

// ----------------------------------------------------------------------------

function getAnsibleRoles(string $path)
{
    $roles = [];

    $dirs  = scanDir($path);

    foreach ($dirs as $dir) {
        if (isAnsibleRole("$path/$dir")) {
            $roles[] = $dir;
        }
    }

    return $roles;
}

// ----------------------------------------------------------------------------

function isAnsibleRole(string $path)
{
    if (is_dir($path) && is_dir("$path/tasks") && is_file("$path/tasks/main.yml")) {
        return true;
    }

    return false;
}

// ----------------------------------------------------------------------------

function getTestedPlatforms(string $path)
{
    $platforms = [];

    if (is_file("$path/molecule/default/molecule.yml")) {
        $data = yaml_parse_file("$path/molecule/default/molecule.yml");

        foreach ($data['platforms'] as $platform) {
            $platforms[] = $platform['name'];
        }
    }

    return $platforms;
}

// ----------------------------------------------------------------------------

function generateMarkdownOutput(array $platforms, array $roles)
{
    $results = '| ' . str_pad('role', 32) . ' |';
    $borderLine = '| ' . str_pad('---', 32, '-') . ' |';
    sort($platforms);

    foreach ($platforms as $platform) {
        $results .= " {$platform} |";
        $borderLine .= ':' . str_pad('---', strlen($platform), '-') . ':|';
    }

    $results .= "\n{$borderLine}\n";

    foreach ($roles as $role => $testedPlatforms) {
        $results .= '| ' . str_pad($role, 32) . ' |';

        foreach ($platforms as $platform) {
            if (in_array($platform, $testedPlatforms)) {
                $results .= str_pad(' x', strlen($platform) + 1) . ' |';
            } else {
                $results .= str_pad(' -', strlen($platform) + 1) . ' |';
            }
        }

        $results .= "\n";
    }

    return $results;
}
