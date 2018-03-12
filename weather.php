<!DOCTYPE html>
<html lang="en">
	<head>
		<title>World Weather</title>

	    <!-- Required meta tags always come first -->
	    <meta charset="utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	    <meta http-equiv="x-ua-compatible" content="ie=edge">
	    <!-- Bootstrap CSS -->
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

	    <style type="text/css">
	    	html { 
  				background: url(images/weatherbg.jpg) no-repeat center center fixed; 
  				-webkit-background-size: cover;
  				-moz-background-size: cover;
  				-o-background-size: cover;
  				background-size: cover;
			}	
	    	body {
	    		background: none;
	    		text-align: center;
	    	}
	    	h1 {
	    		margin-top: 150px;
	    		font-size: 300%;
	    	}
	    	h6 {
	    		margin-top: 25px;
	    		margin-bottom: 25px;
	    	}
	    	#city {
	    		margin-top: 35px;
	    		width: 425px;
	    		margin: 0 auto;
	    		float: none;
	    	}
			#submitForm {
				margin-top: 20px;
			}
			#cityMsg {
				padding-top: 30px;
				width: 450px;
				margin: 0 auto;
	    		float: none;
			}

	    </style>
	</head>

	<body>
		<?php
			$cityError = "";
			$city = "";
			$cityMsg = $weatherMsg = "";
			$forecastPage = $scrapeString = "";
			$failError = false;

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if (empty($_POST["city"])) {
					$cityError = "<strong>Please enter a valid city name</strong>";
				} else {
					$city = clean_input($_POST["city"]);
				}
				// replace space with a dash
				$city = str_replace(' ', '-', $city);
				if (empty($cityError)) {
					$forecastPage = file_get_contents('https://weather-forecast.com/locations/'.$city.'/forecasts/latest');
					if (empty($forecastPage)) {
						$failError = true;
					} else {
						$scrapeString = '</span></p></td><td colspan="9"><span class="b-forecast__table-description-title">';
						//$scrapeString = '</title>';
						$contentArray = explode($scrapeString, $forecastPage);
						if (sizeof($contentArray) < 2) {
							$failError = true;
						} else {
							// Get the city name to display in message
							$scrapeString2 = '</b> is';
							$cityArray = explode($scrapeString2, $contentArray[0]);
							$cityName = "";
							if (sizeof($cityArray) > 0) {
								$scrapeString4 = '<p class="large-loc"><b>';
								$cityArray = explode($scrapeString4, $cityArray[0]);
								$cityName = $cityArray[1];
							}
							// Get the actual weather contents
							$scrapeString3 = '<span class="phrase">';
							$contentArray = explode($scrapeString3, $contentArray[0]);
						}
						if (empty($contentArray[0]) || !$cityName) {
							$failError = true;
						} else {
							$weatherMsg = $contentArray[1];
						}
					}
					if ($failError) {
						$cityMsg = '<div class="alert alert-danger fade show" role="alert">Cannot find weather for: '.$city.'</div>';
					} else {
						$cityMsg = '<div class="alert alert-primary fade show" role="alert"><strong>Weather for the next few days for: '.$cityName.'</strong></br>'.$weatherMsg.'</div>';	
					}
					
				} else {
					$cityMsg = '<div class="alert alert-danger fade in" role="alert">'.$cityError.'</div>';
				}
			}

			function clean_input($data)
			{
				$data = trim($data);
  				$data = stripslashes($data);
  				$data = htmlspecialchars($data);
  				return $data;
			}
		?>

		<div class="container">
			<h1>What's the Weather like?</h1>
			<h6>Enter the name of a city.</h6>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<fieldset class="form-group">
					<input type="text" class="form-control" id="city" name="city" placeholder="Enter city name. Eg. London, Paris, Ferntree Gully" value="<?php echo $_POST["city"]; ?>">
				</fieldset>
				<button type="submit" id="submitForm" class="btn btn-primary">Submit</button>
			</form>
			<div id="cityMsg">
				<? echo $cityMsg; ?>
			</div>
		</div>

		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
    	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>

	    <script type="text/javascript">
	    	$("form").submit(function(e) {
	    		return(validateForm(e));
	    	});

	    	function validateForm(e) {
				var errorMessage = "";

				if ($("#city").val() == "") {
					errorMessage = "<strong>Please enter a valid city name.</strong>"
				}

				if (errorMessage != "") {					
					$("#cityMsg").html('<div class="alert alert-danger fade show" role="alert">' + errorMessage + '</div>');
					return false;
				} else {
					return true;
				}
			}

	    </script>

	</body>

</html>