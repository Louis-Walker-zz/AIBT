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
$mysql_user = 'bagonly';
$mysql_pass = 'REDACTED';
mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Couldn't connect");

$mysql_db = 'amibagtoday';
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
			$welcome_msg = "Welcome back";
			$repeat = false;
			$q = "DELETE FROM Baggots WHERE `ip`='" .$ip. "'";
			$qr = mysql_query($q);
		} else {
			$welcome_msg = "As we've said before, come back in " .(18-$hours) ." hours";
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
$answer_written = ($answers == 0 ? "No." : "Yes.");

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
    <meta charset="utf-8" />

    <title>Am I Bag Today?</title>

    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://fonts.googleapis.com/css?family=Roboto:900,700,500' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" type="text/css" href="reset.css" />
    <link rel="stylesheet" type="text/css" href="styles.css" />
  </head>

  <body>
    <header class="pg-header">
      <span>© Yuuber 2015</span>

      <span>Co-Created @ <a class="hdr-link" href="http://yuuber.com/">Yuuber.com</a></span>
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
      if ($answer == 1) {
      	echo "Welcome Bag";
      } else {
      	echo "Welcome back, you are not Bag today."; 
      }
      ?>
      </article>

      <div class="ratio-bar">
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
	echo '<div id="ratio-yes" class="ratio-ans" style="width: '. $yes_width .'%"></div>';
	echo '<div id="ratio-no" class="ratio-ans" style="width: '. $no_width .'%"></div>';
      ?>
      </div>
    </section>

    <section class="bag-module module-expandable" data-moduletype="expand">
      <button class="module-expand-button" type="button">More Info</button>

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
                    echo '<tr class="mra-row mra-' .$answer_written. '"><td>';
                    echo "<img src=./flags/" .strtolower($country) .".png> ";
                    echo getCountryName($country) .'</td>';

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

    <script type="text/javascript" src="scripts.js"></script>
  </body>
</html>
