<?php
/*
Plugin Name:        MyWebToolkit Wordpress-Airtable
Plugin URI:         https://github.com/MyWebToolkit/Wordpress-Airtable
Description:        Provides sample code for Read and Update of an Airtable base.
Version:            0.1
Requires at least:  5.2
Requires PHP:       7.2 or later
Author:             Karakus
Author URI:         https://webtechbydesign.com
License:            MIT License
*/

/**
 * Enqueue scripts and basic plugin styling
 */
function my_airtable_scripts() {
    $plugin_url = plugin_dir_url( __FILE__ );
    wp_enqueue_style( 'mwt-style', $plugin_url . 'css/mwt-style.css'  );

    wp_enqueue_script('main_js', $plugin_url . 'js/main.js' , NULL, 1.0, true);

    // Enqueue JQueryUI Date picker to ensure common Date Picker UX across main browsers e.g. Safari
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    

    // Send info to page for Javascript to use.
    wp_localize_script('main_js', 'mwtWebtech', array(
        'nonce' => wp_create_nonce('wp_rest'),
        'siteURL' => get_site_url(),
    ));
}

add_action( 'wp_enqueue_scripts', 'my_airtable_scripts' );

// Register an endpoint on the WP REST API to enable an Airtable Event to be updated

add_action( 'rest_api_init', function () {
    register_rest_route( 'mwtwebtech/v1', '/events', array(
      'methods' => 'POST',
      'callback' => 'updateAirtable',
    ) );
  } );

//   Add the JQuery datepicker script to the footer
  function add_datepicker_in_footer(){ ?>

        <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('.date').datepicker({
                dateFormat: 'yy-mm-dd'
            });
        });
        </script>

    <?php
    } // close add_datepicker_in_footer() here

    //add an action to call add_datepicker_in_footer function
    add_action('wp_footer','add_datepicker_in_footer',10);

// Provide encrypt/decrypt functions for handling the api key - just to avoid plain text in database.
// Taken from: https://stackoverflow.com/questions/10154890/encrypting-strings-in-php
function encrypt($string, $key = 'PrivateKey', $secret = 'SecretKey', $method = 'AES-256-CBC') {
    // hash
    $key = hash('sha256', $key);
    // create iv - encrypt method AES-256-CBC expects 16 bytes
    $iv = substr(hash('sha256', $secret), 0, 16);
    // encrypt
    $output = openssl_encrypt($string, $method, $key, 0, $iv);
    // encode
    return base64_encode($output);
}

function decrypt($string, $key = 'PrivateKey', $secret = 'SecretKey', $method = 'AES-256-CBC') {
    // hash
    $key = hash('sha256', $key);
    // create iv - encrypt method AES-256-CBC expects 16 bytes
    $iv = substr(hash('sha256', $secret), 0, 16);
    // decode
    $string = base64_decode($string);
    // decrypt
    return openssl_decrypt($string, $method, $key, 0, $iv);
}


// Now add an admin facility to to enter the Airtable parameters
// Inspired by https://travis.media/where-do-i-store-an-api-key-in-wordpress/
// Creates a subpage under the Tools section
add_action('admin_menu', 'register_my_airtable_api_parameters');
function register_my_airtable_api_parameters() {
    add_submenu_page(
        'tools.php',
        'Airtable API',
        'Airtable API',
        'manage_options',
        'airtable-api',
        'add_airtable_api_parameters' );
}
 
// The admin page containing the Airtable parameters form
function add_airtable_api_parameters() { ?>
    <div class="wrap"><div id="icon-tools" class="icon32"></div>
        <h2>Airtable API Parameters</h2>
        <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="POST">
            <h3>Your Airtable API Key</h3>
            <?php
            // Provide User feedback to show when API Key is set - but don't display the actual key.
            if (get_option('api_key')) {
                echo "<p>API Key is set</p>";
            } else {
                echo "<p>API Key is Not set</p>";
            }
            ?>
            <input type="text" name="api_key" placeholder="Enter API Key">
            <input type="hidden" name="action" value="process_api_key">			 
            <input type="submit" name="submit" id="submit" class="update-button button button-primary" value="Update API Key"  />
        </form>
    </div>

    <div class="wrap"><div id="icon-tools" class="icon32"></div>
        <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="POST">
            <h3>Your Airtable URL</h3>
            <p>Currently: <?php echo get_option('api_url') ?></p>
            <input type="text" name="api_url" placeholder="Enter API URL">
            <input type="hidden" name="action" value="process_url">			 
            <input type="submit" name="submit" id="submit" class="update-button button button-primary" value="Update API URL"  />
        </form> 
    </div>

    <div class="wrap"><div id="icon-tools" class="icon32"></div>
        <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="POST">
            <h3>Your Volunteers Table Name</h3>
            <p>Currently: <?php echo get_option('api_table_name') ?></p>
            <input type="text" name="api_table_name" placeholder="Enter Volunteers Table Name">
            <input type="hidden" name="action" value="process_table_name">			 
            <input type="submit" name="submit" id="submit" class="update-button button button-primary" value="Update Table Name"  />
        </form> 
    </div>

    <?php
}

// Submit Airtable API key functionality
function submit_api_key() {
    if (isset($_POST['api_key'])) {
        $api_key = sanitize_text_field( $_POST['api_key'] );
        $api_key_secured = encrypt($api_key);
        $api_exists = get_option('api_key');
        if (!empty($api_key_secured) && !empty($api_exists)) {
            update_option('api_key', $api_key_secured);
        } else {
            add_option('api_key', $api_key_secured);
        }
    }
    wp_redirect($_SERVER['HTTP_REFERER']);
}
add_action( 'admin_post_nopriv_process_api_key', 'submit_api_key' );
add_action( 'admin_post_process_api_key', 'submit_api_key' );


// Submit Airtable URL functionality
function submit_api_url() {
    if (isset($_POST['api_url'])) {
        $api_url = sanitize_text_field( $_POST['api_url'] );
        $url_exists = get_option('api_url');
        if (!empty($api_url) && !empty($url_exists)) {
            update_option('api_url', $api_url);
        } else {
            add_option('api_url', $api_url);
        }
    }
    wp_redirect($_SERVER['HTTP_REFERER']);
}

add_action( 'admin_post_nopriv_process_url', 'submit_api_url' );
add_action( 'admin_post_process_url', 'submit_api_url' );

// Submit Airtable Volunteers Table Name functionality
function submit_api_table_name() {
    if (isset($_POST['api_table_name'])) {
        $api_table_name = sanitize_text_field( $_POST['api_table_name'] );
        $table_name_exists = get_option('api_table_name');
        if (!empty($api_table_name) && !empty($table_name_exists)) {
            update_option('api_table_name', $api_table_name);
        } else {
            add_option('api_table_name', $api_table_name);
        }
    }
    wp_redirect($_SERVER['HTTP_REFERER']);
}

add_action( 'admin_post_nopriv_process_table_name', 'submit_api_table_name' );
add_action( 'admin_post_process_table_name', 'submit_api_table_name' );

// Register the shortcode that can be used to get the logged in user's profile - assuming that their wp username and airtable username match.
add_shortcode('my-airtable', 'myAirTable');


// Now we are all setup we can get the airtable data and display it where the shortcode is located

function myAirTable() {
    // Only show profile if eligible role
    if (current_user_can('edit_posts')) {
        $current_user = wp_get_current_user();
        $my_table_name = get_option('api_table_name');
        $my_airtable_api = get_option('api_url');
        $remote_url = $my_airtable_api . '/' . $my_table_name. '?filterByFormula=username%3D%22'. $current_user->user_login . '%22'; // Uses the WP username as a key for airtable
        $id_token = decrypt(get_option('api_key'));
        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $id_token,
            )
            );
        $result = wp_remote_get($remote_url, $args);
        $body = wp_remote_retrieve_body($result);
        $data = json_decode($body);
        
 
        // Use variables to handle JSON Key names that include spaces, which php doesn't like
        $VolunteeringScheduleKey = "Volunteering Schedule"; 
        $since = "Date Applied To Become Volunteer";

        // Convert Date formats as appropriate
        // https://www.php.net/manual/en/datetime.format.php
        $dateVolunteered = new DateTime($data->records[0]->fields->$since);

        $visitform = '<form id="visit-form" action="" method="POST">'
                        . '<input type="text" name="notes" value="" placeholder="Enter any notes from the visit." />'
                        . '<input type="date" id="completeddate" name="completeddate" />' 
                        . '<input type="submit" name="' .$eventKey . ' " value="Completed" />'
                        . '</form>
                    ';

        $profile =  '<div class=mwt-container>'
                    . '<div class="mwt-item">'
                    . '<h5 class="profile-details__header">Profile Details</h5>'
                        . '<ul>'
                        . '<li>Name: ' . $data->records[0]->fields->Name . '</li>'
                        . '<li>Volunteering since: ' . $dateVolunteered->format('M Y') . '</li>'
                        . '</ul>'
                    . '</div>'
                    . '<div class="mwt-item">'
                    . '<h5 class="profile-details__header">My Volunteering Events</h5>'
                    . '<ul>';

        $schedule = $data->records[0]->fields->$VolunteeringScheduleKey;
        if($schedule) {
            foreach($schedule as $eventKey) {
                $eventDetails = getEvent($eventKey);
                $profile .='<div class="mwt-event">';
                $profile .= '<li>' . $eventDetails['Event']. '</li>';
                if ($eventDetails['Completed']) {
                    $profile .= '<p>Closed on ' 
                    . $eventDetails['Completed'] 
                    .  '<br>Notes: ' 
                    . $eventDetails['Notes'] . '</p><hr>'
                    ;
                } else {
                    // Display the form with a sumbit name of $eventKey
                    // Add an action to call a js script to validate the form and submit the update
                $profile .= ' <div id="div-' . $eventKey . '"></div>' 
                . '<form id="visit-form-' . $eventKey . '" action="" method="POST">'
                . '<input class="date" type="text" id="date-' . $eventKey .'" name="completeddate" required placeholder="Enter Date Completed"/>' 
                . '<input type="submit" class="submit-button" data-event="' .$eventKey . '"name="submit" value="Completed" />'
                . '</form>'
                . '<textarea rows="4" cols="30" name="comment" id="note-' . $eventKey .'" form="visit-form-' . $eventKey . '" placeholder="Enter any notes from the visit.">'
                . '</textarea>
                ';
                }
                $profile .= '</div>';
            }
        }
        $profile .= '</ul></div></div>';
        return $profile;

    } else {
        
        return '<p>Please login to view profile.</p>';
    }
}

function getEvent($eventKey) {
    // Get a specific event to which the Volunteer has been assigned in their schedule
    $my_airtable_api = get_option('api_url');
    $remote_url = $my_airtable_api . '/Volunteering%20Events/' . $eventKey;
    $id_token = decrypt(get_option('api_key'));
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $id_token,
        )
        );
    $result = wp_remote_get($remote_url, $args);
    $body = wp_remote_retrieve_body($result);
    $data = json_decode($body);
    if ($data) {
        $event_details = array (
            'Event' => $data->fields->Event,
            'Completed' => $data->fields->Completed,
            'Notes' => $data->fields->Notes,
        );
        return $event_details;
    }
}


function completeEvent($event) {
    // Uses the user submitted data to update  the event in Airtable using the 'PATCH' method.

    // Construct the body data 
        $data = array("fields" => array (
                'Completed' => $event['completeddate'],
                'Notes' => $event['closenote'],
                ),
                "typecast" => true,
            );

        // Get the api key
        $id_token = decrypt(get_option('api_key'));

        // Get the api url 
        $my_airtable_api = get_option('api_url');
        $remote_url = $my_airtable_api . '/Volunteering%20Events/' .$event['eventid'];
        $args = array(
            'method' => 'PATCH',
            'headers' => array (
                'Authorization' => 'Bearer ' . $id_token,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($data),    
        );
        $result = wp_remote_post($remote_url, $args);
    $body = wp_remote_retrieve_body($result);
    $data = json_decode($body);
    return $data;
}


function updateAirtable($request) {

    // The REST api endpoint function for updating Airtable Volunteer Events Table. 
    $event = array(
        'eventid' => $request['eventid'],
        'completeddate' => $request['completeddate'],
        'closenote' => $request['closenote'],
    );

    $posts = completeEvent($event);
     
    return $posts;
}

?>