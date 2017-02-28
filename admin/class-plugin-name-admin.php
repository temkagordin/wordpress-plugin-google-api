<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        require_once plugin_dir_path(__DIR__) . 'includes/librares/vendor/autoload.php';
        define('APPLICATION_NAME', 'Drive API PHP Quickstart');
        define('CLIENT_SECRET_PATH', plugin_dir_path(__FILE__) . 'includes/librares/client_id.json');
        define('DEVELOPER_KEY', ' ************ ');
        define('CREDENTIALS_PATH', plugin_dir_path(__DIR__) . 'includes/librares/my_credential.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/drive-php-quickstart.json
        define('SCOPES', implode(' ', array(
                'https://www.googleapis.com/auth/drive',
                'https://www.googleapis.com/auth/drive.appdata',
                'https://www.googleapis.com/auth/drive.apps.readonly',
                'https://www.googleapis.com/auth/drive.file',
                'https://www.googleapis.com/auth/drive.metadata',
                'https://www.googleapis.com/auth/drive.metadata.readonly',
                'https://www.googleapis.com/auth/drive.photos.readonly',
                'https://www.googleapis.com/auth/drive.readonly'
            )
        ));
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Plugin_Name_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Plugin_Name_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/plugin-name-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Plugin_Name_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Plugin_Name_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/plugin-name-admin.js', array('jquery'), $this->version, false);

    }

    public function my_plugin_menu()
    {

        add_menu_page(
            'Table Options',
            'Table',
            'manage_options',
            'class-plugin-admin-display',
            array(&$this, 'admin_template'),
            '',
            '3.0'
        );
    }

    public function admin_template()
    {
        include_once plugin_dir_path(__FILE__) . 'partials/plugin-name-admin-display.php';
       // $this->sheet_info();
        $this->displayTable();


    }

    function expandHomeDirectory($path)
    {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }



    function num_to_letter($num, $uppercase = TRUE)
    {
        $arr = [];
        $addon = 64;
        for ($i = 1; $i <= 700; $i++) {
            $prefix = "";
            if ($i > 26) {
                $remainder = floor($i / 26);
                $prefix = chr($remainder + $addon);
            }
            $ivalue = ($i % 26 == 0) ? 26 : $i % 26;

            array_push($arr, $prefix . chr($addon + $ivalue));
        }
        return $arr[$num];
    }

    public function sheet_info()
    {        $redirect_uri = '';
        $client = new Google_Client();
// Get your credentials from the console
        $client->setAuthConfig(plugin_dir_path(__DIR__) . 'includes/librares/client_id.json');
        $client->setDeveloperKey('AIzaSyAC5ZkoMuqhFV3quyMa_cXDZAVQU6xnq0E');
        $client->setRedirectUri('http://local.wordpress.dev/wp-admin/admin.php?page=class-plugin-admin-display');
        $client->setScopes(SCOPES);
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $credentialsPath=$this->expandHomeDirectory(CREDENTIALS_PATH);
        if (isset($_GET['code']) || (isset($_SESSION['access_token']) && $_SESSION['access_token'])) {
            if (isset($_GET['code'])) {
                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                $client->getAccessToken();
                if (!isset($token['error'])) {
                    $client->setAccessToken(json_encode($token, true));
                    file_put_contents($credentialsPath, json_encode($client->getAccessToken(),true));
                }else{
                    $client->setAccessToken(json_decode(file_get_contents($credentialsPath), true));
                    if ($client->isAccessTokenExpired()) {
                        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    }
                }
            } else {
                $client->authenticate($_GET['code']);
                $_SESSION['access_token'] = $client->getAccessToken();
                $client->setAccessToken(json_encode($_SESSION['access_token'], true));
                file_put_contents($credentialsPath, json_encode( $client->getAccessToken(),true));
            };
        } else {
            $authUrl = $client->createAuthUrl();
            echo '<a href="' . $authUrl . '">' .'Sign in Google.'. '</a>';
            exit();
        }
        require_once plugin_dir_path(__DIR__) . 'includes/class-plugin-name-database.php';
        $service = new Google_Service_Drive($client);

        $pageToken = NULL;
        do {
            try {
                $parameters = array(
                    'q' =>
                        'mimeType = "application/vnd.google-apps.spreadsheet"',
                    'fields' => 'nextPageToken, files(id, name)',
                );
                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }
                $results = $service->files->listFiles($parameters);
                $count = count($results->getFiles());
                if (count($results->getFiles()) == 0) {
                    print "No files found.\n";
                } else {
                    foreach ($results->getFiles() as $file) {
                        $list = [];
                        $listid = [];
                        echo '<a href="#" class="sheetid" id=' . $file->getId() . '>' . $file->getName() . '</a></br>';
                        $refreshToken = file_get_contents(CREDENTIALS_PATH);
                        $service = new Google_Service_Sheets($client);
                        $response = $service->spreadsheets->get($file->getId());
                        $range = 'A1:ZZ99';
                        $count = 1;
                        foreach ($response->sheets as $sheet) {

                            $list[$count] = $sheet['modelData']['properties']['title'];
                            $count++;
                        }
                        foreach ($list as $item => $key) {
                            if ($key == 0) {
                                $data = $service->spreadsheets_values->get($file->getId(), $range);
                            } else {
                                $data = $service->spreadsheets_values->get($file->getId(), $key . '!' . $range);
                            }
                            $count = 0;
                            $values = $data->getValues();
                            $tableXmpl = new TableDb();
                            foreach ($values as $value  => $keyz) {
                                ++$count;
                                foreach ($keyz as $itemz => $sitem) {
                                    if (!(empty($sitem))) {
                                        $tableXmpl->sheetsDb($this->num_to_letter($itemz), $count, $file->getId(), $key, rawurlencode($sitem));
                                        // echo $this->num_to_letter($itemz) . ' ' . $count . ' ' . $file->getId() . ' ' . $key . '=>' . $sitem . '<br/>';
                                    }
                                }
                            }

                        }
                    }
                }
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
                $pageToken = NULL;
            }

        } while ($pageToken);
    }

    public function displayTable()
    {
        require_once plugin_dir_path(__DIR__) . 'includes/class-plugin-name-database.php';
        include_once plugin_dir_path(__FILE__) . 'partials/plugin-name-admin-display.php';
    }

    public function work_sheet()
    {   require_once plugin_dir_path(__DIR__) . 'includes/class-plugin-name-database.php';
        $tabledb= new TableDb();
        if (isset($_POST['id'])) {
            $ids=$_POST['id'];
            $gid=$tabledb->GetGidIdInfoBySheetId($ids);
            foreach ($gid as $id)
            {
                echo '<a id="'.$id->gid_id.'" name='.$ids.' class="gridid" href="#">'.$id->gid_id .'</a> ';
            }
            exit();
        }
        if (isset($_POST['gridid']) && isset($_POST['name'])) {
            $gridid= $_POST['gridid'];
            $sheetID= $_POST['name'];
            $gridIdDB=$tabledb->GetInfoGrid($gridid,$sheetID);
            var_dump($gridIdDB);
            exit();
        }
    }


}
