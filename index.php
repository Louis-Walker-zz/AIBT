<?php
// BY ADRIAN "BAG"
// IF YOU STEAL THIS, I WOULD BE VERY CONFUSED

// VARIABLES ////////////////////////////////////
$answer = 0;
$repeat = false;
$country = "";
$country_name = "";
$timestamp = date('Y-m-d H:i:s');
$ip = $_SERVER['REMOTE_ADDR'];
$welcome_msg = '';

// MYSQL ////////////////////////////////////////
$mysql_host = 'localhost';
$mysql_user = 'bag_the_second';
$mysql_pass = 'yumyums';
mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Couldn't connect");

$mysql_db = 'aibt';
mysql_select_db($mysql_db);

// BAGS FUNCTIONS ////////////////////////////////
function getResponse($url) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url,
        CURLOPT_USERAGENT => 'dronespot',
        CURLOPT_SSL_VERIFYPEER => false
    ));
    $result = curl_exec($curl);
    if(!curl_exec($curl)){
        die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
    }
    curl_close($curl);
    return $result;
}

function ago($time)
{
	 $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	 $lengths = array("60","60","24","7","4.35","12","10");
	 $now = time();
	 $difference = time() - strtotime($time);
	 $tense = "ago";
	 for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
	 	$difference /= $lengths[$j];
	 }
	 $difference = round($difference);
	 if($difference != 1) {
	 	$periods[$j].= "s";
	 }
	 return "$difference $periods[$j] ago ";
}

// COUNTRY NAME
function getCountryName($code) {
	$query = 'SELECT * FROM Countries WHERE CountryCode="' .$code .'"';
	if ($query_run = mysql_query($query)) {
		while($row = mysql_fetch_assoc($query_run)) {
			return $row['CountryName'];
		}
	}
}

// GET VALUE
function getValue($str1, $str2, $resp) {
    $i = strpos($resp, $str1) + strlen($str1) + 2;
    $p = strpos($resp, $str2);
    $res = addslashes(substr($resp, $i, $p - $i-5));
    return $res;
}

// CHECK THE MYSQL IF THEY EXIST & GET OLD INFO
$query = 'SELECT * FROM Baggots WHERE IP="' .$ip .'"';
if ($query_run = mysql_query($query)) {
	while($row = mysql_fetch_assoc($query_run)) {
		$repeat = true;
		$answer = $row['Answer'];
		$timestamp = $row['TimeStamp'];
		$country = $row['Country'];

		// 18 HOURS HAS PASSED?
		date_default_timezone_set('America/Los_Angeles');
		$time_diff = time() - strtotime($timestamp);
		// times
		$hours = floor($time_diff/3600);
		if ($hours > 17) {
			$welcome_msg = "Another day, another answer";

			$repeat = false;
			$q = "DELETE FROM Baggots WHERE `ip`='" .$ip. "'";
			$qr = mysql_query($q);
		} else {
			if ($answer == 1){
				$welcome_msg = "Welcome Bag!";
				$wildcard = rand(1, 100);
				if ($wildcard == 69) {
					$welcome_msg = "Welcome Douchebag!";
				}
			} else {
				$welcome_msg = "Welcome back, you are not Bag today.";
			}
		}
	}
} else {
    echo mysql_error();
}

// DON'T EXIST? NO PROBLEM
if (!$repeat) {
	$answer = rand(0,1);

	// STEAL SOME INFORMATION AND FAX IT OVER TO GCHQ
	$result = getResponse('http://ipinfo.io/'. $ip .'/json');

	$region = getValue('"region":', '"country"', $result);
	$country = getValue('"country":', '"loc"', $result);

	$query = "INSERT INTO Baggots (IP, Answer, Region, Country, TimeStamp) VALUES ('" .$ip ."', '" .$answer. "', '" .$region. "', '" .$country. "', CURRENT_TIMESTAMP )";
	$query_run = mysql_query($query);

	/*if ($answer == 0)
		$query_run = mysql_query("");
		$answers = mysql_fetch_assoc($query_run);
	}*/
}

// Yes or No?
$answer_written = ($answers == 0 ? "No" : "Yes");

// TELL EM STR8
/*echo "<h2>" .$answer_written ."</h2>";
echo $welcome_msg.'<br><br>';
echo $timestamp .'<br>';
echo "<img src=./flags/" .strtolower($country) .".png> " .getCountryName($country);
echo "<br><br><br>More People<br>";*/
?>

<!DOCTYPE html>
<html>
  <head>
    <!-- Generic Meta Data -->
    <meta charset="utf-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Am I Bag Today?</title>

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="http://www.amibag.today/assets/fav/favicon-16x16.png" sizes="16x16" />
    <link rel="icon" type="image/png" href="http://www.amibag.today/assets/fav/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="http://www.amibag.today/assets/fav/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/png" href="http://www.amibag.today/assets/fav/favicon-128x128.png" sizes="128x128" />
    <link rel="icon" type="image/png" href="http://www.amibag.today/assets/fav/favicon-196x196.png" sizes="196x196" />

    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="http://www.amibag.today/assets/fav/apple-touch-icon-57x57.png" />
    <link rel="apple-touch-icon-precomposed" sizes="60x60" href="http://www.amibag.today/assets/fav/apple-touch-icon-60x60.png" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="http://www.amibag.today/assets/fav/apple-touch-icon-72x72.png" />
    <link rel="apple-touch-icon-precomposed" sizes="76x76" href="http://www.amibag.today/assets/fav/apple-touch-icon-76x76.png" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="http://www.amibag.today/assets/fav/apple-touch-icon-114x114.png" />
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="http://www.amibag.today/assets/fav/apple-touch-icon-120x120.png" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="http://www.amibag.today/assets/fav/apple-touch-icon-144x144.png" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="http://www.amibag.today/assets/fav/apple-touch-icon-152x152.png" />

    <meta name="application-name" content="AIMB"/>
    <meta name="msapplication-TileColor" content="#FFFFFF" />
    <meta name="msapplication-TileImage" content="http://www.amibag.today/assets/fav/mstile-144x144.png" />
    <meta name="msapplication-square70x70logo" content="http://www.amibag.today/assets/fav/mstile-70x70.png" />
    <meta name="msapplication-square150x150logo" content="http://www.amibag.today/assets/fav/mstile-150x150.png" />
    <meta name="msapplication-wide310x150logo" content="http://www.amibag.today/assets/fav/mstile-310x150.png" />
    <meta name="msapplication-square310x310logo" content="http://www.amibag.today/assets/fav/mstile-310x310.png" />

    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" href="assets/stylesheets/reset.css" />
    <link rel="stylesheet" type="text/css" href="assets/stylesheets/styles.css" />
    <link rel="stylesheet" type="text/css" href="assets/stylesheets/module.css" />
    <link rel="stylesheet" type="text/css" href="assets/stylesheets/flags.css" />
  </head>

  <body>
    <?php include_once("analyticstracking.php") ?>

    <header class="pg-header flex">
      <span>© PhetaJS 2015</span>

      <span>Co-Created @ <a class="hdr-link" href="#">PhetaJS.com</a></span>
    </header>

    <section class="bag-module">
      <h1>Am I Bag Today?</h1>

      <?php
      $answer_written = ($answer == 0 ? 'no' : 'yes');
      echo '<span class="answer ans-' .$answer_written .'">';

      $answer_written = ($answer == 0 ? 'No.' : 'Yes.');
      echo $answer_written . '</span>';
      ?>

      <article class="ans-subtxt">
      <?php
      echo $welcome_msg;
      ?>
      </article>

      <div class="ratio-bar flex">
      <?php
      	// GET YES AND NO
      	function getSQLValue($query) {
		if ($query_run = mysql_query($query)) {
			while($row = mysql_fetch_assoc($query_run)) {
				return $row['answers'];
			}

		}
	}
	$yeses = getSQLValue('SELECT count(*) AS answers FROM Baggots WHERE Answer=1');
	$nos = getSQLValue('SELECT count(*) AS answers FROM Baggots WHERE Answer=0');
	$total = $yeses + $nos;

	$yes_width = ($yeses / $total) * 100;
	$no_width = ($nos / $total) * 100;
	echo '<div class="ratio-ans ratio-yes" style="width: '. $yes_width .'%"></div>';
	echo '<div class="ratio-ans ratio-no" style="width: '. $no_width .'%"></div>';
      ?>
      </div>

      <div class="ratio-meta flex">
        <?php echo '<div class="ratio-meta-ans flex" style="width: '. $yes_width .'%">' ?>
          <div class="ratio-meta-spacer"></div>

          <?php echo '<span class="txt-module ratio-yes">'. round($yes_width) .'%</span>' ?>
        </div>

        <?php echo '<div class="ratio-meta-ans flex" style="width: '.$no_width .'%">' ?>
          <div class="ratio-meta-spacer"></div>

          <?php echo '<span class="txt-module ratio-no">'. round($no_width) .'%</span>' ?>
        </div>
      </div>
    </section>

    <section class="bag-module module-expandable module-collapse" data-moduletype="expand">
      <button class="module-expander" type="button">More Info</button>

      <h2>Most Recent Answers</h2>

      <table class="mranswers">
        <?php
            // OTHER BAGS & NON-BAGS
            echo "<tbody>";
            $query = 'SELECT * FROM Baggots ORDER BY `Baggots`.`TimeStamp` DESC LIMIT 0, 8';
            if ($query_run = mysql_query($query)) {
                while($row = mysql_fetch_assoc($query_run)) {
                    $answer = $row['Answer'];
                    $timestamp = $row['TimeStamp'];
                    $country = $row['Country'];

                    $answer_written = ($answer == 0 ? "no" : "yes");
                    echo '<tr class="mra-row flex mra-' .$answer_written. '">';
                    echo '<td class="mra-arrow arrow-' .$answer_written. '"></td>';
                    echo '<td class="flag-' .strtolower($country) .'"> </td>';
                    echo '<td>' .getCountryName($country) .'</td>';

                    echo '<td> ' .ago($timestamp) .'</td>';
                    echo '</tr>';
                }
            } else {
                echo mysql_error();
            }
            echo "</tbody>";
        ?>
      </table>
    </section>

    <script type="text/javascript" src="assets/scripts/userinterface.js"></script>
  </body>
</html>
