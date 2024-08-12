<?php
require_once 'config.php';
require_once 'tuners.php';
require_once 'User.class.php';
require_once 'Tuner.class.php';

$tuners = file_get_contents('tuners.json');
if (!$tuners) die('Failed to load tuner data.');

$tuners = json_decode($tuners);
if (!$tuners) die('Failed to parse tuner data.');

$tuners = filter_tuners($tuners);

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$user_ip = $_SERVER['REMOTE_ADDR'];
$user = new User($_SERVER['REMOTE_ADDR']);

// Update when the user was last seen
$db->query("UPDATE tuners_ip_locations SET last_seen = NOW() WHERE ip = '{$user_ip}'");

if ($user->location) {
    $db->query("INSERT IGNORE INTO tuners_ip_locations (ip, continent, country, region, city) VALUES ('{$user_ip}', '{$user->location->continent}', '{$user->location->country}', '{$user->location->region}', '{$user->location->city}')");

    /* if ($db->affected_rows) {
        $continent = $user->location->continent;
        $country   = $user->location->country;
        $region    = $user->location->region;
        $city      = $user->location->city;

        if (!isset($tuners->{$continent})) $tuners->{$continent} = new stdClass();
        if (!isset($tuners->{$continent}->{$country})) $tuners->{$continent}->{$country} = new stdClass();
        if (!isset($tuners->{$continent}->{$country}->{$region})) $tuners->{$continent}->{$country}->{$region} = new stdClass();
        if (!isset($tuners->{$continent}->{$country}->{$region}->{$city})) $tuners->{$continent}->{$country}->{$region}->{$city} = new stdClass();

        file_put_contents('tuners.json', json_encode($tuners, JSON_PRETTY_PRINT));
    } */
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Honda Tuner Directory - Hondatabase.com</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1 style="text-transform: uppercase;">Honda Tuner Directory</h1>
    </header>
    <main>
        <h2>Find a Honda tuner near you!</h2>
        
        <?php
        foreach ($tuners as $continent => $countries) {
            echo "<h3>$continent</h3>";

            foreach ($countries as $country => $regions) {
                echo "<h4>$country</h4>";

                foreach ($regions as $region => $cities) {
                    echo "<h5>$region</h5>";

                    foreach ($cities as $city => $tuners) {
                        // echo "$tuners" type
                        echo print_r($tuners);
                        echo '<div class="tuners-container"><div style="width: 100%">' . $city . '</div>';

                        foreach ($tuners as $tuner => $details) {
                            $tuner = new Tuner($tuner, $details->name, $details->website, $details->email, $details->logo, $details->rating ?? rand(1, 5));

                            echo $tuner->renderCard();
                        }

                        echo '</div>';
                    }
                }
            }
        }
        ?>
    </main>
    <footer>

    </footer>
</body>
</html>