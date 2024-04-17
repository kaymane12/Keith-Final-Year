<?php
header('content-type: application/json');

// Include the config.php file to establish a database connection
require('config.php');

// Function to insert successful MPesa payment data (amount paid) into the finance table
function insertPayment($mysqli, $amountPaid) {
    // Prepare an SQL statement to insert amount paid into the finance table
    $stmt = $mysqli->prepare("INSERT INTO finance (amount_paid) VALUES (?)");

    // Bind parameters to the prepared statement
    $stmt->bind_param('d', $amountPaid);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Payment recorded successfully.";
    } else {
        echo "Failed to record payment: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Main script handling POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = file_get_contents("php://input");
    $data = json_decode($data, true);

    Mpesa::stkSend($data);

    echo Mpesa::$response;
} else {
    echo json_encode(['code' => 0, 'method' => $_SERVER['REQUEST_METHOD']]);
}

// Define the Mpesa class
class Mpesa
{
    public static $credentials, $token, $payload, $response, $url;

    public static function qrSend(){
        // Your qrSend code here (not required for the main question)
    }

    public static function load() : void {
        self::$credentials = array(
            'consumer_key' => 'KxtlCDTS9KgmsTXiEIfzldsseQzhvf9RIFvbhumXgfbpbtqt', // Add your own
            'consumer_secret' => 'ciiBn85MNmN6ssxwPVHb72gs0mWlcrxSENsElo2lJ6lvlNRbExNuqcPIEGTTPDgI', // Add your own
        );
    }

    public static function stkSend($data) {
        // Load the config file
        require('config.php');

        // Extract data from the input argument
        $Amount = $data['amount'];
        $AccountReference = 'CompanyXLTD';
        $CallBackURL = 'https://mydomain.com/path'; // Update with your actual callback URL
        $PhoneNumber = $data['phone_num'];
        $PartyA = $PhoneNumber; // PartyA is usually the same as the phone number

        $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $Timestamp = date('YmdHis');
        $TransactionType = 'CustomerPayBillOnline';
        $TransactionDesc = 'Payment of X';

        $PartyB = $BusinessShortCode = 174379;
        $Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);

        self::$payload = compact(
            'Password',
            'BusinessShortCode',
            'PhoneNumber',
            'PartyB',
            'PartyA',
            'Timestamp',
            'AccountReference',
            'Amount',
            'TransactionDesc',
            'CallBackURL',
            'TransactionType'
        );

        self::$url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

        self::curlPost();
        
        // Process MPesa API response
        $responseArray = json_decode(self::$response, true);

        // Check if the payment was successful
        if (isset($responseArray['ResponseCode']) && $responseArray['ResponseCode'] == '0') {
            // Prepare data for insertion into finance table
            $amountPaid = $responseArray['Amount']; // Extract amount paid from the response

            // Insert only the amount paid into the finance table
            insertPayment($mysqli, $amountPaid);
        }
    }

    public static function curlPost() {
        self::AccessToken();
        $ch = curl_init();
        curl_setopt_array(
            $ch,
            array(
                CURLOPT_URL => self::$url,
                CURLINFO_HEADER_OUT => true,
                CURLOPT_HTTPHEADER => array('Content-Type: application/json', 'Authorization:Bearer ' . self::$token),
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => json_encode(self::$payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            )
        );
        self::$response = curl_exec($ch);
        curl_close($ch);
    }

    public static function AccessToken() {
        self::load();
        
        $curl = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf8'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_USERPWD => self::$credentials['consumer_key'] . ':' . self::$credentials['consumer_secret']
            )
        );
        $result = json_decode(curl_exec($curl));
        curl_close($curl);
        self::$token = $result->access_token;
    }
}
?>
