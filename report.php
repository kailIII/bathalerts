<?php

include "Database.php";
include "Modules/Module.php";
include "Modules/Crime.php";
include "Modules/PlanningApplication.php";
include "Modules/HousePrice.php";

// Checking email form
$emailAdded = false;
if(isset($_POST['email']) && isset($_POST['postcode']) && isset($_POST['crime']) && isset($_POST['planning']) && isset($_POST['houses'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $postcode = $conn->real_escape_string($_POST['postcode']);
    
    $houses = ($_POST['crime'] == 'Yes' ? "TRUE" : "FALSE");
    $crime = ($_POST['crime'] == 'Yes' ? "TRUE" : "FALSE");
    $planning = ($_POST['planning'] == 'Yes' ? "TRUE" : "FALSE");
    
    $conn->query("INSERT IGNORE INTO Users (`Email`, `PostCode`, `Crime`, `Planning`, `Houses`) VALUES ('$email', '$postcode', $crime, $planning, $houses)");
    $emailAdded = true;
}

$pc = "";
if(isset($_GET['pc'])) {
    $pc = strtoupper($_GET['pc']);
} else {
    header( 'Location: /index.php?noPostcode=1' ) ;
}

// See if this is a postcode we know about and can geo code.
if ( !Module::postcodeExists($pc) ){
    header( 'Location: /index.php?unknownPostcode=1' ) ;
}

$searchedForPostcodeLocation = Module::getPostCodeLocation($pc);

$pa = new PlanningApplication($pc);
$planningData = $pa->getData();
$crimeGetter = new Crime($pc);
$crimeData = $crimeGetter->getData();
$hd = new HousePrice($pc);
$houseData = $hd->getData();

include('header.php');

?>

    <body class="report">

        <header>
            <div class="wrap">
                <div class="first">
                    <h1>BathAlerts</h1>
                    <h2>Get monthly email alerts about things near you</h2>
                </div>

                <form method="post" class="last">
                    <input type="email" name="email" placeholder="Sign-up for email alerts" />
                    <input type="text" name="postcode" id="pc-hidden" value="<?php echo $pc; ?>" />
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-envelope-o"></i>
                    </button>
                </form>
            </div>
        </header>

        <section id="map-section">
            <div class="wrap">
                <h3>Showing information local to <span><?php echo $pc; ?></span><a href="/">Change</a></h3>
            </div>

            <?php if ($pc) { ?>
                <script type="application/javascript">
                    var emailAdded = <?php echo ($emailAdded ? "true" : "false") ?>;
                    var crimeData = <?php echo json_encode($crimeData); ?>;
                    var planningData = <?php echo json_encode($planningData); ?>;
                    var houseData = <?php echo json_encode($houseData); ?>;

                    var searchedForPostcode = <?php echo json_encode($searchedForPostcodeLocation); ?>;
                </script>

                <div id="map"></div>
            <?php } else {} ?>
        </section>

        <section id="list-section">
            <div class="wrap">
                <h3>Customise your monthly email alerts by ticking the sections on or off below.</h3>

                <div id="planning-applications" class="fourcol first">
                    <input type="checkbox" name="planning-applications" id="planning-applications_check" checked>
                    <label for="planning-applications_check">Planning Applications</label>

                    <ul>
                        <?php foreach ($planningData as $plan) {
                            echo '<li>' . $plan['casedate'] . ' - ' . $plan['banesstatus'] . '<br />' . $plan['locationtext'] . '<br />' . $plan['casetext'] . '</li>';
                        } ?>
                    </ul>
                </div>

                <div id="crimes" class="fourcol">
                    <input type="checkbox" name="crimes" id="crimes_check" checked>
                    <label for="crimes_check">Crimes</label>

                    <ul>
                        <?php foreach ($crimeData as $crime) {
                            $crime_nice_name = str_replace("-", " ", $crime['crime_category']);
                            echo '<li><strong>' . $crime_nice_name . '</strong><br />' . $crime['street_name'] . '</li>';
                        } ?>
                    </ul>
                </div>

                <div id="house-sales" class="fourcol last">
                    <input type="checkbox" name="house-sales" id="house-sales_check" checked>
                    <label for="house-sales_check">House Sales</label>

                    <ul>
                        <?php foreach ($houseData as $houses) {
                            echo '<li>' . $houses['date_of_transfer'] . ' - £' . $houses['price'] . '<br />' . $houses['secondary_addressable_object_name'] . ', ' . $houses['locality'] . ', ' . $houses['district'] . ', ' . $houses['postcode'] . '</li>';
                        } ?>
                    </ul>
                </div>
            </div>
        </section>
        <script src="/library/js/map.js"></script>
        <footer></footer>
    </body>
</html>