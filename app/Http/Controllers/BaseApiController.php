<?php

namespace RESTFulAPI\Http\Controllers;

use Illuminate\Http\Request;
use \PDO;

class BaseApiController extends Controller
{

    // specify the database credentials
    // replace the credentials of the database to match yours
    private $host = 'HOST';
    private $port = 'PORT';
    private $dbname = 'DATABASENAME';
    private $username = 'USERNAME';
    private $password = 'PASSWORD';
    public $conn;

    protected $responseStatus = [
        'status' => [
            'isSuccess' => true,
            'statusCode' => 200,
            'message' => '',
        ]
    ];

    // get the database connection
    public function getDBConnection($api_key) {
    	try {
		    $this->conn = new PDO('mysql:host='.$this->host.';port='.$this->port.';dbname='.$this->dbname, $this->username, $this->password);
		    $this->conn->exec('set names utf8');
		    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
		    $query = "SELECT * FROM api_keys WHERE api_key = :api_key";
		    $stmt = $this->conn->prepare($query);
		    $stmt->bindParam(':api_key', $api_key, PDO::PARAM_STR);
		    $stmt->execute();
		    if ($stmt->rowCount())
		    	return $this->conn;
		    else
		    	die('KEY IS NOT VALID');
		} catch (PDOException $e) {
			return $this->conn;
		}
    }

    // Setter method for the response status
    public function setResponseStatus(bool $isSuccess = true, int $statusCode = 200, string $message = '')
    {
        $this->responseStatus['status']['isSuccess'] = $isSuccess;
        $this->responseStatus['status']['statusCode'] = $statusCode;
        $this->responseStatus['status']['message'] = $message;
    }

    // Returns the response with only status key
    public function sendResponseStatus($isSuccess = true, $statusCode = 200, $message = '')
    {

        $this->responseStatus['status']['isSuccess'] = $isSuccess;
        $this->responseStatus['status']['statusCode'] = $statusCode;
        $this->responseStatus['status']['message'] = $message;

        $json = $this->responseStatus;

        return response()->json($json);

    }

    // If you have additional data to send in the response
    public function sendResponseData($data)
    {
        $json = [
            'status' => $this->responseStatus['status'],
            'data' => $data,
        ];


        return response()->json($json);

    }
}
