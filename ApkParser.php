<?php

  /**
   * Class         ApkParser
   * Author:       Xuxinhua
   * Modified By:  Elad Karako (eladkarako@gmail.com)
   * Dependent:    ZIP package functions support needs PHP.
   *
   * Description:  Package-Parser for Android APK's Binary-Compressed-XML Files.
   *
   * implementation example
   * -----------------------------------------------------------------------------------------------------
   *   $apkParser = new ApkParser();
   *   $apkParser->open('./ce386c89d7696d0b5aede43107e2bf2b.apk');
   *   echo $apkParser->getPackage() . "\n";
   *   echo $apkParser->getVersionName() . "\n";
   *   echo $apkParser->getVersionCode() . "\n";
   *   echo $apkParser->getXML() . "\n";
   * -----------------------------------------------------------------------------------------------------
   * and
   * -----------------------------------------------------------------------------------------------------
   *    $parser = new ApkParser();
   *    $parser->open('./ce386c89d7696d0b5aede43107e2bf2b.apk');
   *
   *   'Version Name', "\t", $parser->getVersionName(), "\n",
   *   'Version Code', "\t", $parser->getVersionCode(), "\n",
   *   'Package Name', "\t", $parser->getPackage(), "\n",
   *   'SDK Minimal-Version Support', "\t", $parser->getUsesSDKMin(), "\n",
   *   'SDK Target-Version', "\t", $parser->getUsesSDKTarget(), "\n";
   *
   *   echo "\n";
   *
   *   echo 'Application Meta-Data', "\t";
   *   print_r($parser->getApplicationMetaData());
   *   echo "\n";
   *
   *   echo "\n";
   *
   *   echo 'Permissions', "\t";
   *   print_r($parser->getUsesPermissions());
   *   echo "\n";
   *
   *   echo 'Hardware-Features', "\t";
   *   print_r($parser->getUsesFeature());
   *   echo "\n";
   * -----------------------------------------------------------------------------------------------------
   *
   *
   * @link          https://github.com/polaris1119/myutility/blob/master/php/apk_parser.php
   * @link          https://github.com/eladkarako/Google-Developer-Related
   * @link          http://icompile.eladkarako.com/google-android-developer-php-reverse-engineering-binary-androidmanifest-xml-and-apk-data/
   */
  class ApkParser {
    //----------------------
    // Public function for external calls
    //----------------------

    const AXML_FILE       = 0x00080003;
    const STRING_BLOCK    = 0x001C0001;
    const RESOURCEIDS     = 0x00080180;
    const START_NAMESPACE = 0x00100100;
    const END_NAMESPACE   = 0x00100101;
    const START_TAG       = 0x00100102;
    const END_TAG         = 0x00100103;
    const TEXT            = 0x00100104;
    const TYPE_NULL       = 0;
    const TYPE_REFERENCE  = 1;

    //----------------------
    // Type constant definitions
    //----------------------
    const TYPE_ATTRIBUTE       = 2;
    const TYPE_STRING          = 3;
    const TYPE_FLOAT           = 4;
    const TYPE_DIMENSION       = 5;
    const TYPE_FRACTION        = 6;
    const TYPE_INT_DEC         = 16;
    const TYPE_INT_HEX         = 17;
    const TYPE_INT_BOOLEAN     = 18;
    const TYPE_INT_COLOR_ARGB8 = 28;
    const TYPE_INT_COLOR_RGB8  = 29;
    const TYPE_INT_COLOR_ARGB4 = 30;
    const TYPE_INT_COLOR_RGB4  = 31;
    const UNIT_MASK            = 15;
    public static $dictionary = [
      "com.android.vending.CHECK_LICENSE"                                          => [
        "description" => "Android Licensing Permission: Uses Google Play application for sending a license check, also known as License Verification Library (LVL).",
        "when_added"  => "API level 3"
      ],
      "com.android.vending.BILLING"                                                => [
        "description" => "Android In-app Billing requests and managing In-app Billing transactions using Google Play.",
        "when_added"  => "API level 3"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.YouTubeUser"           => [
        "description" => "Allows apps to see the YouTube username(s) associated with the Google account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.c2dm.permission.RECEIVE"                                 => [
        "description" => "Allows apps to accept cloud to device messages sent by the app's service. Using this service will incur data usage. Malicious apps could cause excess data usage.",
        "when_added"  => "API level UNKNOWN"
      ],
      "android.permission-group.NETWORK"                                           => [
        "description" => "Access various network features.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.youtube.permission.C2D_MESSAGE"                          => [
        "description" => "Push notifications.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH"                       => [
        "description" => "Access And View All Available Accounts: Allows apps to see the usernames (email addresses) of the Google account(s) you have configured.",
        "when_added"  => "API level UNKNOWN"
      ],
      "android.permission-group.ACCOUNTS"                                          => [
        "description" => "Access And View All Available Accounts: Allows apps to see the usernames (email addresses) of all of the account(s) you have configured. Not just Google, but Facebook, LinkedIn, ... too.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.youtube"               => [
        "description" => "Allows apps to sign in to YouTube using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.talk"                  => [
        "description" => "Allows apps to sign in to Google Talk using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.ig"                    => [
        "description" => "Allows apps to sign in to iGoogle using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.androidsecure"         => [
        "description" => "Allows apps to sign in to Android services using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.news"                  => [
        "description" => "Allows apps to sign in to Google News using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.print"                 => [
        "description" => "Allows apps to sign in to Google Book Search using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.writely"               => [
        "description" => "Allows apps to sign in to Google Docs using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.sierrasandbox"         => [
        "description" => "Allows apps to sign in to Google Checkout Sandbox using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.health"                => [
        "description" => "Allows apps to sign in to Google Health using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.speech"                => [
        "description" => "Allows apps to sign in to Google Voice Search using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.sierraqa"              => [
        "description" => "Allows apps to sign in to Google Checkout QA using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.blogger"               => [
        "description" => "Allows apps to sign in to Blogger using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.knol"                  => [
        "description" => "Allows apps to sign in to Knol using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.goanna_mobile"         => [
        "description" => "Allows applications to sign in to the Google Tasks service using the account(s) stored on this phone.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.groups2"               => [
        "description" => "Allows apps to sign in to Google Groups using the account(s) stored on this Android device",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.sierra"                => [
        "description" => "Allows apps to sign in to Google Checkout (and potentially make purchases) using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.notebook"              => [
        "description" => "Allows apps to sign in to Google Notebook using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.speechpersonalization" => [
        "description" => "Allows apps to sign in to the Personalized Speech Recognition service using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.wise"                  => [
        "description" => "Allows apps to sign in to Google Spreadsheets using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.lh2"                   => [
        "description" => "Allows apps to sign in to Picasa Web Albums using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.doraemon"              => [
        "description" => "Allows applications to sign in to the Google Catalogs service using the account(s) stored on this phone.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.ah"                    => [
        "description" => "Allows apps to sign in to Google App Engine using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.orkut"                 => [
        "description" => "Allows apps to sign in to Orkut using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.googleapps.permission.GOOGLE_AUTH.android"               => [
        "description" => "Allows apps to sign in to Android services using the account(s) stored on this Android device.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.android.voicemail.permission.READ_WRITE_ALL_VOICEMAIL"                  => [
        "description" => "Allows the app to store and retrieve all voicemails that this device can access.",
        "when_added"  => "API level UNKNOWN"
      ],
      "com.google.android.gms.permission.ACTIVITY_RECOGNITION"                     => [
        "description" => "Allows an app to receive periodic updates of your activity level from Google, for example, if you are walking, driving, cycling, or stationary.",
        "when_added"  => "API level UNKNOWN"
      ],
      "android.permission.RETRIEVE_WINDOW_CONTENT"                                 => [
        "description" => "Allows the app to retrieve the content of the active window. Malicious apps may retrieve the entire window content and examine all its text except passwords.",
        "when_added"  => "API level UNKNOWN"
      ],
      "android.permission-group.PERSONAL_INFO"                                     => [
        "description" => "Direct access to information about you, stored in on your contact card.",
        "when_added"  => "API level UNKNOWN"
      ],
      "android.permission.ACCESS_CHECKIN_PROPERTIES"                               => [
        "description" => "Allows read/write access to the properties table in the checkin database, to change values that get uploaded. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.ACCESS_COARSE_LOCATION"                                  => [
        "description" => "Allows an app to access approximate location derived from network location sources such as cell towers and Wi-Fi.",
        "when_added"  => "API level 1"
      ],
      "android.permission.ACCESS_FINE_LOCATION"                                    => [
        "description" => "Allows an app to access precise location from location sources such as GPS, cell towers, and Wi-Fi.",
        "when_added"  => "API level 1"
      ],
      "android.permission.ACCESS_LOCATION_EXTRA_COMMANDS"                          => [
        "description" => "Allows an application to access extra location provider commands",
        "when_added"  => "API level 1"
      ],
      "android.permission.ACCESS_MOCK_LOCATION"                                    => [
        "description" => "Allows an application to create mock location providers for testing",
        "when_added"  => "API level 1"
      ],
      "android.permission.ACCESS_NETWORK_STATE"                                    => [
        "description" => "Allows applications to access information about networks",
        "when_added"  => "API level 1"
      ],
      "android.permission.ACCESS_SURFACE_FLINGER"                                  => [
        "description" => "Allows an application to use SurfaceFlinger's low level features. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.ACCESS_WIFI_STATE"                                       => [
        "description" => "Allows applications to access information about Wi-Fi networks",
        "when_added"  => "API level 1"
      ],
      "android.permission.ACCOUNT_MANAGER"                                         => [
        "description" => "Allows applications to call into AccountAuthenticators. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "com.android.voicemail.permission.ADD_VOICEMAIL"                             => [
        "description" => "Allows an application to add voicemails into the system.",
        "when_added"  => "API level 5"
      ],
      "android.permission.AUTHENTICATE_ACCOUNTS"                                   => [
        "description" => "Allows an application to act as an AccountAuthenticator for the AccountManager: Allows the app to use the account authenticator capabilities of the AccountManager, including creating accounts and getting and setting their passwords.",
        "when_added"  => "API level 14"
      ],
      "android.permission.BATTERY_STATS"                                           => [
        "description" => "Allows an application to collect battery statistics",
        "when_added"  => "API level 5"
      ],
      "android.permission.BIND_ACCESSIBILITY_SERVICE"                              => [
        "description" => "Must be required by an AccessibilityService, to ensure that only the system can bind to it.",
        "when_added"  => "API level 1"
      ],
      "android.permission.BIND_APPWIDGET"                                          => [
        "description" => "Allows an application to tell the AppWidget service which application can access AppWidget's data. The normal user flow is that a user picks an AppWidget to go into a particular host, thereby giving that host application access to the private data from the AppWidget app. An application that has this permission should honor that contract. Not for use by third-party applications.",
        "when_added"  => "API level 16"
      ],
      "android.permission.BIND_DEVICE_ADMIN"                                       => [
        "description" => "Must be required by device administration receiver, to ensure that only the system can interact with it.",
        "when_added"  => "API level 3"
      ],
      "android.permission.BIND_DREAM_SERVICE"                                      => [
        "description" => "Must be required by an DreamService, to ensure that only the system can bind to it.",
        "when_added"  => "API level 8"
      ],
      "android.permission.BIND_INPUT_METHOD"                                       => [
        "description" => "Must be required by an InputMethodService, to ensure that only the system can bind to it.",
        "when_added"  => "API level 21"
      ],
      "android.permission.BIND_NFC_SERVICE"                                        => [
        "description" => "Must be required by a HostApduService or OffHostApduService to ensure that only the system can bind to it.",
        "when_added"  => "API level 3"
      ],
      "android.permission.BIND_NOTIFICATION_LISTENER_SERVICE"                      => [
        "description" => "Must be required by an NotificationListenerService, to ensure that only the system can bind to it.",
        "when_added"  => "API level 19"
      ],
      "android.permission.BIND_PRINT_SERVICE"                                      => [
        "description" => "Must be required by a PrintService, to ensure that only the system can bind to it.",
        "when_added"  => "API level 18"
      ],
      "android.permission.BIND_REMOTEVIEWS"                                        => [
        "description" => "Must be required by a RemoteViewsService, to ensure that only the system can bind to it.",
        "when_added"  => "API level 19"
      ],
      "android.permission.BIND_TEXT_SERVICE"                                       => [
        "description" => "Must be required by a TextService (e.g. SpellCheckerService) to ensure that only the system can bind to it.",
        "when_added"  => "API level 11"
      ],
      "android.permission.BIND_TV_INPUT"                                           => [
        "description" => "Must be required by a TvInputService to ensure that only the system can bind to it.",
        "when_added"  => "API level 14"
      ],
      "android.permission.BIND_VOICE_INTERACTION"                                  => [
        "description" => "Must be required by a VoiceInteractionService, to ensure that only the system can bind to it.",
        "when_added"  => "API level 21"
      ],
      "android.permission.BIND_VPN_SERVICE"                                        => [
        "description" => "Must be required by a VpnService, to ensure that only the system can bind to it.",
        "when_added"  => "API level 21"
      ],
      "android.permission.BIND_WALLPAPER"                                          => [
        "description" => "Must be required by a WallpaperService, to ensure that only the system can bind to it.",
        "when_added"  => "API level 14"
      ],
      "android.permission.BLUETOOTH"                                               => [
        "description" => "Allows applications to connect to paired bluetooth devices",
        "when_added"  => "API level 8"
      ],
      "android.permission.BLUETOOTH_ADMIN"                                         => [
        "description" => "Allows applications to discover and pair bluetooth devices",
        "when_added"  => "API level 1"
      ],
      "android.permission.BLUETOOTH_PRIVILEGED"                                    => [
        "description" => "Allows applications to pair bluetooth devices without user interaction, and to allow or disallow phonebook access or message access. This is not available to third party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.BODY_SENSORS"                                            => [
        "description" => "Allows an application to access data from sensors that the user uses to measure what is happening inside his/her body, such as heart rate.",
        "when_added"  => "API level 19"
      ],
      "android.permission.BRICK"                                                   => [
        "description" => "Required to be able to disable the device (very dangerous!). Not for use by third-party applications..",
        "when_added"  => "API level 20"
      ],
      "android.permission.BROADCAST_PACKAGE_REMOVED"                               => [
        "description" => "Allows an application to broadcast a notification that an application package has been removed. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.BROADCAST_SMS"                                           => [
        "description" => "Allows an application to broadcast an SMS receipt notification. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.BROADCAST_STICKY"                                        => [
        "description" => "Allows an application to broadcast sticky intents. These are broadcasts whose data is held by the system after being finished, so that clients can quickly retrieve that data without having to wait for the next broadcast.",
        "when_added"  => "API level 2"
      ],
      "android.permission.BROADCAST_WAP_PUSH"                                      => [
        "description" => "Allows an application to broadcast a WAP PUSH receipt notification. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.CALL_PHONE"                                              => [
        "description" => "Allows an application to initiate a phone call without going through the Dialer user interface for the user to confirm the call being placed.",
        "when_added"  => "API level 2"
      ],
      "android.permission.CALL_PRIVILEGED"                                         => [
        "description" => "Allows an application to call any phone number, including emergency numbers, without going through the Dialer user interface for the user to confirm the call being placed. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.CAMERA"                                                  => [
        "description" => "Required to be able to access the camera device. This will automatically enforce the \<uses-feature\> manifest element for all camera features. If you do not require all camera features or can properly operate if a camera is not available, then you must modify your manifest as appropriate in order to install on devices that don't support all camera features.",
        "when_added"  => "API level 1"
      ],
      "android.permission.CAPTURE_AUDIO_OUTPUT"                                    => [
        "description" => "Allows an application to capture audio output. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.CAPTURE_SECURE_VIDEO_OUTPUT"                             => [
        "description" => "Allows an application to capture secure video output. Not for use by third-party applications.",
        "when_added"  => "API level 19"
      ],
      "android.permission.CAPTURE_VIDEO_OUTPUT"                                    => [
        "description" => "Allows an application to capture video output. Not for use by third-party applications.",
        "when_added"  => "API level 19"
      ],
      "android.permission.CHANGE_COMPONENT_ENABLED_STATE"                          => [
        "description" => "Allows an application to change whether an application component (other than its own) is enabled or not. Not for use by third-party applications.",
        "when_added"  => "API level 19"
      ],
      "android.permission.CHANGE_CONFIGURATION"                                    => [
        "description" => "Allows an application to modify the current configuration, such as locale.",
        "when_added"  => "API level 1"
      ],
      "android.permission.CHANGE_NETWORK_STATE"                                    => [
        "description" => "Allows applications to change network connectivity state",
        "when_added"  => "API level 1"
      ],
      "android.permission.CHANGE_WIFI_MULTICAST_STATE"                             => [
        "description" => "Allows applications to enter Wi-Fi Multicast mode",
        "when_added"  => "API level 1"
      ],
      "android.permission.CHANGE_WIFI_STATE"                                       => [
        "description" => "Allows applications to change Wi-Fi connectivity state",
        "when_added"  => "API level 4"
      ],
      "android.permission.CLEAR_APP_CACHE"                                         => [
        "description" => "Allows an application to clear the caches of all installed applications on the device.",
        "when_added"  => "API level 1"
      ],
      "android.permission.CLEAR_APP_USER_DATA"                                     => [
        "description" => "Allows an application to clear user data. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.CONTROL_LOCATION_UPDATES"                                => [
        "description" => "Allows enabling/disabling location update notifications from the radio. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.DELETE_CACHE_FILES"                                      => [
        "description" => "Allows an application to delete cache files. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.DELETE_PACKAGES"                                         => [
        "description" => "Allows an application to delete packages. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.DEVICE_POWER"                                            => [
        "description" => "Allows low-level access to power management. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.DIAGNOSTIC"                                              => [
        "description" => "Allows applications to RW to diagnostic resources. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.DISABLE_KEYGUARD"                                        => [
        "description" => "Allows applications to disable the keyguard",
        "when_added"  => "API level 1"
      ],
      "android.permission.DUMP"                                                    => [
        "description" => "Allows an application to retrieve state dump information from system services. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.EXPAND_STATUS_BAR"                                       => [
        "description" => "Allows an application to expand or collapse the status bar.",
        "when_added"  => "API level 1"
      ],
      "android.permission.FACTORY_TEST"                                            => [
        "description" => "Run as a manufacturer test application, running as the root user. Only available when the device is running in manufacturer test mode. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.FLASHLIGHT"                                              => [
        "description" => "Allows access to the flashlight",
        "when_added"  => "API level 1"
      ],
      "android.permission.FORCE_BACK"                                              => [
        "description" => "Allows an application to force a BACK operation on whatever is the top activity. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.GET_ACCOUNTS"                                            => [
        "description" => "Allows access to the list of accounts in the Accounts Service",
        "when_added"  => "API level 1"
      ],
      "android.permission.GET_PACKAGE_SIZE"                                        => [
        "description" => "Allows an application to find out the space used by any package.",
        "when_added"  => "API level 1"
      ],
      "android.permission.GET_TASKS"                                               => [
        "description" => "",
        "when_added"  => "API level 1"
      ],
      "android.permission.GET_TOP_ACTIVITY_INFO"                                   => [
        "description" => "Allows an application to retrieve private information about the current top activity, such as any assist context it can provide. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.GLOBAL_SEARCH"                                           => [
        "description" => "This permission can be used on content providers to allow the global search system to access their data. Typically it used when the provider has some permissions protecting it (which global search would not be expected to hold), and added as a read-only permission to the path in the provider where global search queries are performed. This permission can not be held by regular applications; it is used by applications to protect themselves from everyone else besides global search.",
        "when_added"  => "API level 18"
      ],
      "android.permission.HARDWARE_TEST"                                           => [
        "description" => "Allows access to hardware peripherals. Intended only for hardware testing. Not for use by third-party applications.",
        "when_added"  => "API level 4"
      ],
      "android.permission.INJECT_EVENTS"                                           => [
        "description" => "Allows an application to inject user events (keys, touch, trackball) into the event stream and deliver them to ANY window. Without this permission, you can only deliver events to windows in your own process. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.INSTALL_LOCATION_PROVIDER"                               => [
        "description" => "Allows an application to install a location provider into the Location Manager. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.INSTALL_PACKAGES"                                        => [
        "description" => "Allows an application to install packages. Not for use by third-party applications.",
        "when_added"  => "API level 4"
      ],
      "com.android.launcher.permission.INSTALL_SHORTCUT"                           => [
        "description" => "Allows an application to install a shortcut in Launcher",
        "when_added"  => "API level 1"
      ],
      "android.permission.INTERNAL_SYSTEM_WINDOW"                                  => [
        "description" => "Allows an application to open windows that are for use by parts of the system user interface. Not for use by third-party applications.",
        "when_added"  => "API level 19"
      ],
      "android.permission.INTERNET"                                                => [
        "description" => "Allows applications to open network sockets.",
        "when_added"  => "API level 1"
      ],
      "android.permission.KILL_BACKGROUND_PROCESSES"                               => [
        "description" => "Allows an application to call killBackgroundProcesses(String).",
        "when_added"  => "API level 1"
      ],
      "android.permission.LOCATION_HARDWARE"                                       => [
        "description" => "Allows an application to use location features in hardware, such as the geofencing api. Not for use by third-party applications.",
        "when_added"  => "API level 8"
      ],
      "android.permission.MANAGE_ACCOUNTS"                                         => [
        "description" => "Allows an application to manage the list of accounts in the AccountManager",
        "when_added"  => "API level 18"
      ],
      "android.permission.MANAGE_APP_TOKENS"                                       => [
        "description" => "Allows an application to manage (create, destroy, Z-order) application tokens in the window manager. Not for use by third-party applications.",
        "when_added"  => "API level 5"
      ],
      "android.permission.MANAGE_DOCUMENTS"                                        => [
        "description" => "Allows an application to manage access to documents, usually as part of a document picker.",
        "when_added"  => "API level 1"
      ],
      "android.permission.MASTER_CLEAR"                                            => [
        "description" => "Not for use by third-party applications.",
        "when_added"  => "API level 19"
      ],
      "android.permission.MEDIA_CONTENT_CONTROL"                                   => [
        "description" => "Allows an application to know what content is playing and control its playback. Not for use by third-party applications due to privacy of media consumption",
        "when_added"  => "API level 1"
      ],
      "android.permission.MODIFY_AUDIO_SETTINGS"                                   => [
        "description" => "Allows an application to modify global audio settings",
        "when_added"  => "API level 19"
      ],
      "android.permission.MODIFY_PHONE_STATE"                                      => [
        "description" => "Allows modification of the telephony state - power on, mmi, etc. Does not include placing calls. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.MOUNT_FORMAT_FILESYSTEMS"                                => [
        "description" => "Allows formatting file systems for removable storage. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.MOUNT_UNMOUNT_FILESYSTEMS"                               => [
        "description" => "Allows mounting and unmounting file systems for removable storage. Not for use by third-party applications.",
        "when_added"  => "API level 3"
      ],
      "android.permission.NFC"                                                     => [
        "description" => "Allows applications to perform I/O operations over NFC",
        "when_added"  => "API level 1"
      ],
      "android.permission.PERSISTENT_ACTIVITY"                                     => [
        "description" => "",
        "when_added"  => "API level 9"
      ],
      "android.permission.PROCESS_OUTGOING_CALLS"                                  => [
        "description" => "Allows an application to see the number being dialed during an outgoing call with the option to redirect the call to a different number or abort the call altogether.",
        "when_added"  => "API level 1"
      ],
      "android.permission.READ_CALENDAR"                                           => [
        "description" => "Allows an application to read the user's calendar data.",
        "when_added"  => "API level 1"
      ],
      "android.permission.READ_CALL_LOG"                                           => [
        "description" => "Allows an application to read the user's call log. Note: If your app uses the READ_CONTACTS permission and both your minSdkVersion and targetSdkVersion values are set to 15 or lower, the system implicitly grants your app this permission. If you don't need this permission, be sure your targetSdkVersion is 16 or higher.",
        "when_added"  => "API level 1"
      ],
      "android.permission.READ_CONTACTS"                                           => [
        "description" => "Allows an application to read the user's contacts data.",
        "when_added"  => "API level 16"
      ],
      "android.permission.READ_EXTERNAL_STORAGE"                                   => [
        "description" => "Allows an application to read from external storage. Any app that declares the WRITE_EXTERNAL_STORAGE permission is implicitly granted this permission. This permission is enforced starting in API level 19. Before API level 19, this permission is not enforced and all apps still have access to read from external storage. You can test your app with the permission enforced by enabling Protect USB storage under Developer options in the Settings app on a device running Android 4.1 or higher. Also starting in API level 19, this permission is not required to read/write files in your application-specific directories returned by getExternalFilesDir(String) and getExternalCacheDir(). Note: If both your minSdkVersion and targetSdkVersion values are set to 3 or lower, the system implicitly grants your app this permission. If you don't need this permission, be sure your targetSdkVersion is 4 or higher.",
        "when_added"  => "API level 1"
      ],
      "android.permission.READ_FRAME_BUFFER"                                       => [
        "description" => "Allows an application to take screen shots and more generally get access to the frame buffer data. Not for use by third-party applications.",
        "when_added"  => "API level 16"
      ],
      "com.android.browser.permission.READ_HISTORY_BOOKMARKS"                      => [
        "description" => "Allows an application to read (but not write) the user's browsing history and bookmarks.",
        "when_added"  => "API level 1"
      ],
      "android.permission.READ_INPUT_STATE"                                        => [
        "description" => "Allows an application to retrieve the current state of keys and switches. Not for use by third-party applications.",
        "when_added"  => "API level 4"
      ],
      "android.permission.READ_LOGS"                                               => [
        "description" => "Allows an application to read the low-level system log files. Not for use by third-party applications, because Log entries can contain the user's private information.",
        "when_added"  => "API level 1"
      ],
      "android.permission.READ_PHONE_STATE"                                        => [
        "description" => "Allows read only access to phone state. Note: If both your minSdkVersion and targetSdkVersion values are set to 3 or lower, the system implicitly grants your app this permission. If you don't need this permission, be sure your targetSdkVersion is 4 or higher.",
        "when_added"  => "API level 1"
      ],
      "android.permission.READ_PROFILE"                                            => [
        "description" => "Allows an application to read the user's personal profile data.",
        "when_added"  => "API level 1"
      ],
      "android.permission.READ_SMS"                                                => [
        "description" => "Allows an application to read SMS messages.",
        "when_added"  => "API level 14"
      ],
      "android.permission.READ_SOCIAL_STREAM"                                      => [
        "description" => "Allows an application to read from the user's social stream.",
        "when_added"  => "API level 1"
      ],
      "android.permission.READ_SYNC_SETTINGS"                                      => [
        "description" => "Allows applications to read the sync settings",
        "when_added"  => "API level 15"
      ],
      "android.permission.READ_SYNC_STATS"                                         => [
        "description" => "Allows applications to read the sync stats",
        "when_added"  => "API level 1"
      ],
      "android.permission.READ_USER_DICTIONARY"                                    => [
        "description" => "Allows an application to read the user dictionary. This should really only be required by an IME, or a dictionary editor like the Settings app.",
        "when_added"  => "API level 1"
      ],
      "com.android.voicemail.permission.READ_VOICEMAIL"                            => [
        "description" => "Allows an application to read voicemails in the system.",
        "when_added"  => "API level 16"
      ],
      "android.permission.REBOOT"                                                  => [
        "description" => "Required to be able to reboot the device. Not for use by third-party applications.",
        "when_added"  => "API level 21"
      ],
      "android.permission.ACTION_BOOT_COMPLETED"                                  => [
        "description" => "Broadcast Action: This is broadcast once, after the system has finished booting. It can be used to perform application-specific initialization, such as installing alarms. You must hold the RECEIVE_BOOT_COMPLETED permission in order to receive this broadcast. This is a protected intent that can only be sent by the system.",
        "when_added"  => "API level 1"
      ],
      "android.permission.RECEIVE_BOOT_COMPLETED"                                  => [
        "description" => "Allows an application to receive the ACTION_BOOT_COMPLETED that is broadcast after the system finishes booting. If you don't request this permission, you will not receive the broadcast at that time. Though holding this permission does not have any security implications, it can have a negative impact on the user experience by increasing the amount of time it takes the system to start and allowing applications to have themselves running without the user being aware of them. As such, you must explicitly declare your use of this facility to make that visible to the user.",
        "when_added"  => "API level 1"
      ],
      "android.permission.RECEIVE_MMS"                                             => [
        "description" => "Allows an application to monitor incoming MMS messages, to record or perform processing on them.",
        "when_added"  => "API level 1"
      ],
      "android.permission.RECEIVE_SMS"                                             => [
        "description" => "Allows an application to monitor incoming SMS messages, to record or perform processing on them.",
        "when_added"  => "API level 1"
      ],
      "android.permission.RECEIVE_WAP_PUSH"                                        => [
        "description" => "Allows an application to monitor incoming WAP push messages.",
        "when_added"  => "API level 1"
      ],
      "android.permission.RECORD_AUDIO"                                            => [
        "description" => "Allows an application to record audio",
        "when_added"  => "API level 1"
      ],
      "android.permission.REORDER_TASKS"                                           => [
        "description" => "Allows an application to change the Z-order of tasks",
        "when_added"  => "API level 1"
      ],
      "android.permission.RESTART_PACKAGES"                                        => [
        "description" => "",
        "when_added"  => "API level 1"
      ],
      "android.permission.SEND_RESPOND_VIA_MESSAGE"                                => [
        "description" => "Allows an application (Phone) to send a request to other applications to handle the respond-via-message action during incoming calls. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.SEND_SMS"                                                => [
        "description" => "Allows an application to send SMS messages.",
        "when_added"  => "API level 18"
      ],
      "android.permission.SET_ACTIVITY_WATCHER"                                    => [
        "description" => "Allows an application to watch and control how activities are started globally in the system. Only for is in debugging (usually the monkey command). Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "com.android.alarm.permission.SET_ALARM"                                     => [
        "description" => "Allows an application to broadcast an Intent to set an alarm for the user.",
        "when_added"  => "API level 1"
      ],
      "android.permission.SET_ALWAYS_FINISH"                                       => [
        "description" => "Allows an application to control whether activities are immediately finished when put in the background. Not for use by third-party applications.",
        "when_added"  => "API level 9"
      ],
      "android.permission.SET_ANIMATION_SCALE"                                     => [
        "description" => "Modify the global animation scaling factor. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.SET_DEBUG_APP"                                           => [
        "description" => "Configure an application for debugging. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.SET_ORIENTATION"                                         => [
        "description" => "Allows low-level access to setting the orientation (actually rotation) of the screen. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.SET_POINTER_SPEED"                                       => [
        "description" => "Allows low-level access to setting the pointer speed. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.SET_PREFERRED_APPLICATIONS"                              => [
        "description" => "",
        "when_added"  => "API level 13"
      ],
      "android.permission.SET_PROCESS_LIMIT"                                       => [
        "description" => "Allows an application to set the maximum number of (not needed) application processes that can be running. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.SET_TIME"                                                => [
        "description" => "Allows applications to set the system time. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.SET_TIME_ZONE"                                           => [
        "description" => "Allows applications to set the system time zone",
        "when_added"  => "API level 8"
      ],
      "android.permission.SET_WALLPAPER"                                           => [
        "description" => "Allows applications to set the wallpaper",
        "when_added"  => "API level 1"
      ],
      "android.permission.SET_WALLPAPER_HINTS"                                     => [
        "description" => "Allows applications to set the wallpaper hints",
        "when_added"  => "API level 1"
      ],
      "android.permission.SIGNAL_PERSISTENT_PROCESSES"                             => [
        "description" => "Allow an application to request that a signal be sent to all persistent processes. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.STATUS_BAR"                                              => [
        "description" => "Allows an application to open, close, or disable the status bar and its icons. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.SUBSCRIBED_FEEDS_READ"                                   => [
        "description" => "Allows an application to allow access the subscribed feeds ContentProvider.",
        "when_added"  => "API level 1"
      ],
      "android.permission.SUBSCRIBED_FEEDS_WRITE"                                  => [
        "description" => "",
        "when_added"  => "API level 1"
      ],
      "android.permission.SYSTEM_ALERT_WINDOW"                                     => [
        "description" => "Allows an application to open windows using the type TYPE_SYSTEM_ALERT, shown on top of all other applications. Very few applications should use this permission; these windows are intended for system-level interaction with the user.",
        "when_added"  => "API level 1"
      ],
      "android.permission.TRANSMIT_IR"                                             => [
        "description" => "Allows using the device's IR transmitter, if available",
        "when_added"  => "API level 1"
      ],
      "com.android.launcher.permission.UNINSTALL_SHORTCUT"                         => [
        "description" => "Allows an application to uninstall a shortcut in Launcher",
        "when_added"  => "API level 19"
      ],
      "android.permission.UPDATE_DEVICE_STATS"                                     => [
        "description" => "Allows an application to update device statistics. Not for use by third-party applications.",
        "when_added"  => "API level 19"
      ],
      "android.permission.USE_CREDENTIALS"                                         => [
        "description" => "Allows an application to request authtokens from the AccountManager",
        "when_added"  => "API level 3"
      ],
      "android.permission.USE_SIP"                                                 => [
        "description" => "Allows an application to use SIP service",
        "when_added"  => "API level 5"
      ],
      "android.permission.VIBRATE"                                                 => [
        "description" => "Allows access to the vibrator",
        "when_added"  => "API level 9"
      ],
      "android.permission.WAKE_LOCK"                                               => [
        "description" => "Allows using PowerManager WakeLocks to keep processor from sleeping or screen from dimming",
        "when_added"  => "API level 1"
      ],
      "android.permission.WRITE_APN_SETTINGS"                                      => [
        "description" => "Allows applications to write the apn settings. Not for use by third-party applications.",
        "when_added"  => "API level 1"
      ],
      "android.permission.WRITE_CALENDAR"                                          => [
        "description" => "Allows an application to write (but not read) the user's calendar data.",
        "when_added"  => "API level 1"
      ],
      "android.permission.WRITE_CALL_LOG"                                          => [
        "description" => "Allows an application to write (but not read) the user's contacts data. Note: If your app uses the WRITE_CONTACTS permission and both your minSdkVersion and targetSdkVersion values are set to 15 or lower, the system implicitly grants your app this permission. If you don't need this permission, be sure your targetSdkVersion is 16 or higher.",
        "when_added"  => "API level 1"
      ],
      "android.permission.WRITE_CONTACTS"                                          => [
        "description" => "Allows an application to write (but not read) the user's contacts data.",
        "when_added"  => "API level 16"
      ],
      "android.permission.WRITE_EXTERNAL_STORAGE"                                  => [
        "description" => "Allows an application to write to external storage. Note: If both your minSdkVersion and targetSdkVersion values are set to 3 or lower, the system implicitly grants your app this permission. If you don't need this permission, be sure your targetSdkVersion is 4 or higher. Starting in API level 19, this permission is not required to read/write files in your application-specific directories returned by getExternalFilesDir(String) and getExternalCacheDir().",
        "when_added"  => "API level 1"
      ],
      "com.google.android.providers.gsf.permission.READ_GSERVICES"                 => [
        "description" => "Allows this app to read Google service configuration data.",
        "when_added"  => "API level UNKNOWN"
      ],
      "android.permission.WRITE_GSERVICES"                                         => [
        "description" => "Allows an application to modify the Google service map. Not for use by third-party applications.",
        "when_added"  => "API level 4"
      ],
      "com.android.browser.permission.WRITE_HISTORY_BOOKMARKS"                     => [
        "description" => "Allows an application to write (but not read) the user's browsing history and bookmarks.",
        "when_added"  => "API level 1"
      ],
      "android.permission.WRITE_PROFILE"                                           => [
        "description" => "Allows an application to write (but not read) the user's personal profile data.",
        "when_added"  => "API level 4"
      ],
      "android.permission.WRITE_SECURE_SETTINGS"                                   => [
        "description" => "Allows an application to read or write the secure system settings. Not for use by third-party applications.",
        "when_added"  => "API level 14"
      ],
      "android.permission.WRITE_SETTINGS"                                          => [
        "description" => "Allows an application to read or write the system settings.",
        "when_added"  => "API level 3"
      ],
      "android.permission.WRITE_SMS"                                               => [
        "description" => "Allows an application to write SMS messages.",
        "when_added"  => "API level 1"
      ],
      "android.permission.WRITE_SOCIAL_STREAM"                                     => [
        "description" => "Allows an application to write (but not read) the user's social stream data.",
        "when_added"  => "API level 1"
      ],
      "android.permission.WRITE_SYNC_SETTINGS"                                     => [
        "description" => "Allows applications to write the sync settings",
        "when_added"  => "API level 15"
      ],
      "android.permission.WRITE_USER_DICTIONARY"                                   => [
        "description" => "Allows an application to write to the user dictionary.",
        "when_added"  => "API level 1"
      ],
      "com.android.voicemail.permission.WRITE_VOICEMAIL"                           => [
        "description" => "Allows an application to modify and remove existing voicemails in the system",
        "when_added"  => "API level 16"
      ],
      "android.permission.ACCESS_SUPERUSER"                           => [
        "description" => "From SuperSU version 1.20 and onwards, the android.permission.ACCESS_SUPERUSER permission is declared by SuperSU. All root apps should from now on declare this permission in their AndroidManifest.xml. Due to changes in Android 5.0 Lollipop, this permission has been deprecated and is completely ignored from SuperSU v2.30 onwards. If this permission is not present, SuperSU will present a warning in its superuser request popup (this is configurable in SuperSU settings). At the time of this writing this permission is not enforced, but it is expected that sometime in the future it will be, and apps requesting root that do not have this permission set will be silently denied. If this permission is declared, the user will be able to see in the app permissions list that the app requests superuser access.",
        "when_added"  => "API level UNKNOWN"
      ]
    ];


    /**
     * got most from the android developer website.
     * got some of the missing permissions from:
     *
     * @link http://androidpermissions.com/
     * @var array
     */
    private static $RADIX_MULTS = [0.00390625, 3.051758E-005, 1.192093E-007, 4.656613E-010];
    private static $DIMENSION_UNITS = ["px", "dip", "sp", "pt", "in", "mm", "", ""];
    private static $FRACTION_UNITS = ["%", "%p", "", "", "", "", "", ""];
    private $xml = '';
    private $length = 0;
    private $stringCount = 0;
    private $styleCount = 0;
    private $stringTab = [];
    private $styleTab = [];
    private $resourceIDs = [];
    private $ns = [];
    private $cur_ns = null;
    private $root = null;
    private $line = 0;

    public
    function open($apk_file, $xml_file = 'AndroidManifest.xml') {
      $zip = new ZipArchive();
      if ($zip->open($apk_file) !== true) return false;
      $xml = $zip->getFromName($xml_file);
      $zip->close();


      //$xml = @file_get_contents('zip://' . $apk_file . '#' . $xml_file) || "";
      if ($xml === "") return false;
      try {
        return $this->parseString($xml);
      } catch (Exception $e) {
      }

      return false;
    }

    public
    function parseString($xml) {
      $this->xml = $xml;
      $this->length = strlen($xml);

      $this->root = $this->parseBlock(self::AXML_FILE);

      return true;
    }

    private
    function parseBlock($need = 0) {
      $o = 0;
      $type = $this->get32($o);
      if ($need && $type != $need) throw new Exception('Block Type Error', 1);
      $size = $this->get32($o);
      if ($size < 8 || $size > $this->length) throw new Exception('Block Size Error', 2);
      $left = $this->length - $size;

      $props = false;
      switch ($type) {
        case self::AXML_FILE:
          $props = [
            'line' => 0,
            'tag'  => '<?xml version="1.0" encoding="utf-8"?>'
          ];
          break;
        case self::STRING_BLOCK:
          $this->stringCount = $this->get32($o);
          $this->styleCount = $this->get32($o);
          $o += 4;
          $strOffset = $this->get32($o);
          $styOffset = $this->get32($o);
          $strListOffset = $this->get32array($o, $this->stringCount);
          $styListOffset = $this->get32array($o, $this->styleCount);
          $this->stringTab = $this->stringCount > 0 ? $this->getStringTab($strOffset, $strListOffset) : [];
          $this->styleTab = $this->styleCount > 0 ? $this->getStringTab($styOffset, $styListOffset) : [];
          $o = $size;
          break;
        case self::RESOURCEIDS:
          $count = $size / 4 - 2;
          $this->resourceIDs = $this->get32array($o, $count);
          break;
        case self::START_NAMESPACE:
          $o += 8;
          $prefix = $this->get32($o);
          $uri = $this->get32($o);

          if (empty($this->cur_ns)) {
            $this->cur_ns = [];
            $this->ns[] = &$this->cur_ns;
          }
          $this->cur_ns[ $uri ] = $prefix;
          break;
        case self::END_NAMESPACE:
          $o += 8;
          $prefix = $this->get32($o);
          $uri = $this->get32($o);

          if (empty($this->cur_ns)) break;
          unset($this->cur_ns[ $uri ]);
          break;
        case self::START_TAG:
          $line = $this->get32($o);

          $o += 4;
          $attrs = [];
          $props = [
            'line'  => $line,
            'ns'    => $this->getNameSpace($this->get32($o)),
            'name'  => $this->getString($this->get32($o)),
            'flag'  => $this->get32($o),
            'count' => $this->get16($o),
            'id'    => $this->get16($o) - 1,
            'class' => $this->get16($o) - 1,
            'style' => $this->get16($o) - 1,
            'attrs' => &$attrs
          ];
          $props['ns_name'] = $props['ns'] . $props['name'];
          for ($i = 0; $i < $props['count']; $i++) {
            $a = [
              'ns'       => $this->getNameSpace($this->get32($o)),
              'name'     => $this->getString($this->get32($o)),
              'val_str'  => $this->get32($o),
              'val_type' => $this->get32($o),
              'val_data' => $this->get32($o)
            ];
            $a['ns_name'] = $a['ns'] . $a['name'];
            $a['val_type'] >>= 24;
            $attrs[] = $a;
          }
          // TAG string handling
          $tag = "<{$props['ns_name']}";
          foreach ($this->cur_ns as $uri => $prefix) {
            $uri = $this->getString($uri);
            $prefix = $this->getString($prefix);
            $tag .= " xmlns:{$prefix}=\"{$uri}\"";
          }
          foreach ($props['attrs'] as $a) {
            $tag .= " {$a['ns_name']}=\"" .
              $this->getAttributeValue($a) .
              '"';
          }
          $tag .= '>';
          $props['tag'] = $tag;

          unset($this->cur_ns);
          $this->cur_ns = [];
          $this->ns[] = &$this->cur_ns;
          $left = -1;
          break;
        case self::END_TAG:
          $line = $this->get32($o);
          $o += 4;
          $props = [
            'line' => $line,
            'ns'   => $this->getNameSpace($this->get32($o)),
            'name' => $this->getString($this->get32($o))
          ];
          $props['ns_name'] = $props['ns'] . $props['name'];
          $props['tag'] = "</{$props['ns_name']}>";
          if (count($this->ns) > 1) {
            array_pop($this->ns);
            unset($this->cur_ns);
            $this->cur_ns = array_pop($this->ns);
            $this->ns[] = &$this->cur_ns;
          }
          break;
        case self::TEXT:
          $o += 8;
          $props = [
            'tag' => $this->getString($this->get32($o))
          ];
          $o += 8;
          break;
        default:
          throw new Exception('Block Type Error', 3);
          break;
      }

      $this->skip($o);
      $child = [];
      while ($this->length > $left) {
        $c = $this->parseBlock();
        if ($props && $c) $child[] = $c;
        if ($left == -1 && $c['type'] == self::END_TAG) {
          $left = $this->length;
          break;
        }
      }
      if ($this->length != $left) throw new Exception('Block Overflow Error', 4);
      if ($props) {
        $props['type'] = $type;
        $props['size'] = $size;
        $props['child'] = $child;

        return $props;
      }
      else {
        return false;
      }
    }

    private
    function get32(&$off) {
      $int = unpack('V', substr($this->xml, $off, 4));
      $off += 4;

      return array_shift($int);
    }

    private
    function get32array(&$off, $size) {
      if ($size <= 0) return null;
      $arr = unpack('V*', substr($this->xml, $off, 4 * $size));
      if (count($arr) != $size) throw new Exception('Array Size Error', 10);
      $off += 4 * $size;

      return $arr;
    }

    private
    function getStringTab($base, $list) {
      $tab = [];
      foreach ($list as $off) {
        $off += $base;
        $len = $this->get16($off);
        $mask = ($len >> 0x8) & 0xFF;
        $len = $len & 0xFF;
        if ($len == $mask) {
          if ($off + $len > $this->length) throw new Exception('String Table Overflow', 11);
          $tab[] = substr($this->xml, $off, $len);
        }
        else {
          if ($off + $len * 2 > $this->length) throw new Exception('String Table Overflow', 11);
          $str = substr($this->xml, $off, $len * 2);
          $tab[] = mb_convert_encoding($str, 'UTF-8', 'UCS-2LE');
        }
      }

      return $tab;
    }

    private
    function get16(&$off) {
      $int = unpack('v', substr($this->xml, $off, 2));
      $off += 2;

      return array_shift($int);
    }

    private
    function getNameSpace($uri) {
      for ($i = count($this->ns); $i > 0;) {
        $ns = $this->ns[ --$i ];
        if (isset($ns[ $uri ])) {
          $ns = $this->getString($ns[ $uri ]);
          if (!empty($ns)) $ns .= ':';

          return $ns;
        }
      }

      return '';
    }

    private
    function getString($id) {
      if ($id > -1 && $id < $this->stringCount) {
        return $this->stringTab[ $id ];
      }
      else {
        return '';
      }
    }

    //----------------------
    // Internal private function
    //----------------------

    private
    function getAttributeValue($a) {
      $type = &$a['val_type'];
      $data = &$a['val_data'];
      switch ($type) {
        case self::TYPE_STRING:
          return $this->getString($a['val_str']);
        case self::TYPE_ATTRIBUTE:
          return sprintf('?%s%08X', self::_getPackage($data), $data);
        case self::TYPE_REFERENCE:
          return sprintf('@%s%08X', self::_getPackage($data), $data);
        case self::TYPE_INT_HEX:
          return sprintf('0x%08X', $data);
        case self::TYPE_INT_BOOLEAN:
          return ($data != 0 ? 'true' : 'false');
        case self::TYPE_INT_COLOR_ARGB8:
        case self::TYPE_INT_COLOR_RGB8:
        case self::TYPE_INT_COLOR_ARGB4:
        case self::TYPE_INT_COLOR_RGB4:
          return sprintf('#%08X', $data);
        case self::TYPE_DIMENSION:
          return $this->_complexToFloat($data) . self::$DIMENSION_UNITS[ $data & self::UNIT_MASK ];
        case self::TYPE_FRACTION:
          return $this->_complexToFloat($data) . self::$FRACTION_UNITS[ $data & self::UNIT_MASK ];
        case self::TYPE_FLOAT:
          return $this->_int2float($data);
      }
      if ($type >= self::TYPE_INT_DEC && $type < self::TYPE_INT_COLOR_ARGB8) {
        return (string)$data;
      }

      return sprintf('<0x%X, type 0x%02X>', $data, $type);
    }

    private static
    function _getPackage($data) {
      return ($data >> 24 == 1) ? 'android:' : '';
    }

    private
    function _complexToFloat($data) {
      return (float)($data & 0xFFFFFF00) * self::$RADIX_MULTS[ ($data >> 4) & 3 ];
    }

    private
    function _int2float($v) {
      $x = ($v & ((1 << 23) - 1)) + (1 << 23) * ($v >> 31 | 1);
      $exp = ($v >> 23 & 0xFF) - 127;

      return $x * pow(2, $exp - 23);
    }

    private
    function skip($size) {
      $this->xml = substr($this->xml, $size);
      $this->length -= $size;
    }

    public
    function getPackage() {
      return $this->getAttribute('manifest', 'package');
    }

    public
    function getAttribute($path, $name) {
      $r = $this->getElement($path);
      if (is_null($r)) return null;

      if (isset($r['attrs'])) {
        foreach ($r['attrs'] as $a) {
          if ($a['ns_name'] == $name) return $this->getAttributeValue($a);
        }
      }

      return null;
    }

    /**
     * @param {string} $path
     *
     * @return null
     */
    private
    function getElement($path) {
      if (!$this->root) return null;
      $ps = explode('/', $path);
      $r = $this->root;
      foreach ($ps as $v) {
        if (preg_match('/([^\[]+)\[([0-9]+)\]$/', $v, $ms)) {
          $v = $ms[1];
          $off = $ms[2];
        }
        else {
          $off = 0;
        }
        foreach ($r['child'] as $c) {
          if ($c['type'] == self::START_TAG && $c['ns_name'] == $v) {
            if ($off == 0) {
              $r = $c;
              continue 2;
            }
            else {
              $off--;
            }
          }
        }

        // Did not find node
        return null;
      }

      return $r;
    }



    //--------------------------------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------------------

    /**
     * Get Plain-Text XML
     *
     * @param {string|null} $node
     * @param int $lv
     *
     * @return string
     */
    public
    function getXML($node = null, $lv = -1) {
      $xml = "";

      if ($lv == -1) $node = $this->root;
      if (!$node) return $xml;

      if ($node['type'] == self::END_TAG) $lv--;
      $xml = ($node['line'] == 0 || $node['line'] == $this->line) ? "" : "\n" . str_repeat('  ', $lv);
      $xml .= $node['tag'];
      $this->line = $node['line'];
      foreach ($node['child'] as $c) {
        $xml .= $this->getXML($c, $lv + 1);
      }

      return trim($xml);
    }

    public
    function getAppName() {
      return $this->getAttribute('manifest/application', 'android:name');
    }

    public
    function getVersionName() {
      return $this->getAttribute('manifest', 'android:versionName');
    }

    public
    function getVersionCode() {
      return $this->getAttribute('manifest', 'android:versionCode');
    }

    /**
     * flat array of all-permissions the application uses
     *
     * @return array
     */
    public
    function getUsesPermissionsDictionary() {
      $collection = [];
      $permissions = $this->getUsesPermissions();
      foreach ($permissions as $permission)
        $collection[ $permission ] = self::getPermissionDictionary($permission);

      return $collection;
    }


    /**
     * flat array of all-permissions the application uses
     *
     * @return array
     */
    public
    function getUsesPermissions() {
      $collection = [];
      for ($i = 0; true; $i += 1) {
        $item = $this->getAttribute("manifest/uses-permission[{$i}]", 'android:name');
        if (!$item) break;

        $collection[ $item ] = isset(self::$dictionary[ $item ]) ? isset(self::$dictionary[ $item ]['description']) ? self::$dictionary[ $item ]['description'] : "" : "";
        //array_push($collection, $item);
      }

      return $collection;
    }


    /**
     * input valid permission that exist                       --- get back ['description'=>'..', 'when_added'=>'..'],
     * input invalid permission (or one that does not exist)   --- get back empty string (''),
     * input nothing (by default input is '')                  --- get back the entire dictionary
     *
     * @link http://icompile.eladkarako.com/ripping-developer-android-com-data-mining-from-manifest-permission-page/
     * @link http://developer.android.com/reference/android/Manifest.permission.html
     *
     * @param string $permission
     *
     * @return array|string
     */
    public static
    function getPermissionDictionary($permission = '') {
      return ($permission !== '') ? isset(self::$dictionary[ $permission ]) ? self::$dictionary[ $permission ] : '' : self::$dictionary;
    }


    public
    function getUsesFeature() {
      $collection = [];
      for ($i = 0; true; $i += 1) {
        $item_name = $this->getAttribute("manifest/uses-feature[{$i}]", 'android:name');
        if (!$item_name) break;
        $item_requirement = $this->getAttribute("manifest/uses-feature[{$i}]", 'android:required');
        array_push($collection, [
          "name"        => $item_name,
          "is_required" => $item_requirement
        ]);
      }

      return $collection;
    }


    public
    function getUsesSDKMin() {
      return $this->getAttribute('manifest/uses-sdk', 'android:minSdkVersion');
    }


    public
    function getUsesSDKTarget() {
      return $this->getAttribute('manifest/uses-sdk', 'android:targetSdkVersion');
    }


    public
    function getApplicationMetaData() {
      $collection = [];
      for ($i = 0; true; $i += 1) {
        $item_name = $this->getAttribute("manifest/application/meta-data[{$i}]", 'android:name');
        $item_value = $this->getAttribute("manifest/application/meta-data[{$i}]", 'android:value');

        if (!$item_name) break;
        if (!$item_value) $item_value = '';

        $collection[ $item_name ] = $item_value;
      }

      return $collection;
    }


  }


?>
