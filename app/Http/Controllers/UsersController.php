<?php

namespace RESTFulAPI\Http\Controllers;

use Illuminate\Http\Request;
use RESTFulAPI\Http\Inc\Test;
use \PDO;

class UsersController extends BaseApiController
{
	private $tableName = "users";
	private function getConnection($key) {
		$key = Test::testInput($key);
		$conn = $this->getDBConnection($key);
		if (isset($conn->errorInfo()[2])) {
			$message = 'ERROR: '.$conn->errorInfo()[2];
			return $this->sendResponseStatus(false, 600, $message);
		} else {
			return $conn;
		}
	}

    public function getAll(Request $request) {
    	if ($request->has('key')) {
	    	$conn = $this->getConnection($request->input('key'));
	    	$query = "SELECT * FROM $this->tableName ORDER BY id ASC";
			$stmt = $conn->prepare($query);
			$stmt->execute();
	        $data = [];
	        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	          	extract($row);
	            $data[] = ['id' => $id, 'username' => $username, 'password' => $password, 
	            	'created' => $created, 'modified' => $modified];
	        }
	        $this->setResponseStatus(true, 200, 'SUCESS');
	        return $this->sendResponseData($data);
		} else {
			return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
		}
    }

    public function getById(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		$query = "SELECT * FROM $this->tableName WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
	    		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	    		$stmt->execute();
	    		$data = [];
	    		if ($stmt->rowCount()) {
		        	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		            	extract($row);
		            	$data = ['id' => $id, 'username' => $username, 'password' => $password,
		            		'created' => $created, 'modified' => $modified];
		          	}
		          	$this->setResponseStatus(true, 200, 'SUCCESS');
	          	} else {
	          		$this->setResponseStatus(false, 404, 'USER DOESN\'T EXIST');
	          	}
	          	return $this->sendResponseData($data);
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: ID IS NOT PROVIDED');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}

    }

    public function getByUsername(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		$query = "SELECT * FROM $this->tableName WHERE username = :username";
    		$stmt = $conn->prepare($query);
    		if ($request->has('username')) {
				$username = Test::testInput($request->input('username'));
	    		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
	    		$stmt->execute();
	    		$data = [];
	    		if ($stmt->rowCount()) {
		        	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		            	extract($row);
		            	$data = ['id' => $id, 'username' => $username, 'password' => $password,
		            		'created' => $created, 'modified' => $modified];
		          	}
		          	$this->setResponseStatus(true, 200, 'SUCCESS');
	          	} else {
	          		$this->setResponseStatus(false, 404, 'ERROR: USER DOESN\'T EXIST');
	          	}
	          	return $this->sendResponseData($data);
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: USERNAME IS NOT PROVIDED');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    public function insert(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		$query = "INSERT INTO $this->tableName VALUES (:id, :username, :password, :created, :modified)";
    		$stmt = $conn->prepare($query);
    		if ($request->has('id') && $request->has('username') && $request->has('password') && $request->has('created') && $request->has('modified')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$username = Test::testInput($request->input('username'));
    			$password = Test::testInput($request->input('password'));
    			$created = Test::testInput($request->input('created'));
    			$modified = Test::testInput($request->input('modified'));
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':username', $username, PDO::PARAM_STR);
    			$stmt->bindParam(':password', $password, PDO::PARAM_STR);
    			$stmt->bindParam(':created', $created);
    			$stmt->bindParam(':modified', $modified);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: USER INSERTED SUCCESSFULLY');
    			} else {
    				$message = 'ERROR: '.$stmt->errorInfo()[2];
    				return $this->sendResponseStatus(false, 500, $message);
    			}

    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: MISSING PARAMETERS');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    private function checkIdExists($id, $conn) {
    	$query = "SELECT * FROM $this->tableName WHERE id = :id";
    	$stmt = $conn->prepare($query);
    	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    	$stmt->execute();
    	if (!$stmt->rowCount()) {
    		$this->sendResponseStatus(false, 404, 'ERROR: USER DOESN\'T EXIST');
    	}
    }

    public function updateId(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: USER DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "UPDATE $this->tableName SET id = :new_id WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('new_id')) {
    			$new_id = filter_var(Test::testInput($request->input('new_id')), FILTER_VALIDATE_INT);
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':new_id', $new_id, PDO::PARAM_INT);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: USER ID UPDATED SUCCESSFULLY');
    			} else {
    				$message = 'ERROR: '.$stmt->errorInfo()[2];
    				return $this->sendResponseStatus(false, 500, $message);
    			}
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE NEW_ID PARAMETER');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    public function updateUsername(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: USER DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "UPDATE $this->tableName SET username = :username WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('username')) {
    			$username = Test::testInput($request->input('username'));
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':username', $username, PDO::PARAM_STR);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: USER USERNAME UPDATED SUCCESSFULLY');
    			} else {
    				$message = 'ERROR: '.$stmt->errorInfo()[2];
    				return $this->sendResponseStatus(false, 500, $message);
    			}
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOUR MUST PROVIDE USERNAME PARAMETER');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    public function updatePassword(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: USER DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "UPDATE $this->tableName SET password = :password WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('password')) {
    			$password = Test::testInput($request->input('password'));
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':password', $password, PDO::PARAM_STR);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: USER PASSWORD UPDATED SUCCESSFULLY');
    			} else {
    				$message = 'ERROR: '.$stmt->errorInfo()[2];
    				return $this->sendResponseStatus(false, 500, $message);
    			}
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE PASSWORD PARAMETER');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    public function updateCreated(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: USER DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "UPDATE $this->tableName SET created = :created WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('created')) {
    			$created = Test::testInput($request->input('created'));
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':created', $created);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: USER CREATED DATE UPDATED SUCCESSFULLY');
    			} else {
    				$message = 'ERROR: '.$stmt->errorInfo()[2];
    				return $this->sendResponseStatus(false, 500, $message);
    			}
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE CREATED PARAMETER');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    public function updateModified(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: USER DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "UPDATE $this->tableName SET modified = :modified WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('modified')) {
    			$modified = Test::testInput($request->input('modified'));
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':modified', $modified);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: USER MODIFIED DATE UPDATED SUCCESSFULLY');
    			} else {
    				$message = 'ERROR: '.$stmt->errorInfo()[2];
    				return $this->sendResponseStatus(false, 500, $message);
    			}
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE MODIFIED PARAMETER');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    public function deleteById(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: USER DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "DELETE FROM $this->tableName WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    		$stmt->execute();
    		return $this->sendResponseStatus(true, 200, 'SUCCESS: USER HAS BEEN DELETED SUCCESSFULLY');
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}

    }

    public function deleteByUsername(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('username')) {
    			$username = Test::testInput($request->input('username'));
    			$query = "SELECT * FROM $this->tableName WHERE username = :username";
    			$stmt = $conn->prepare($query);
    			$stmt->bindParam(':username', $username, PDO::PARAM_STR);
    			$stmt->execute();
    			if ($stmt->rowCount()) {
		    		$query = "DELETE FROM $this->tableName WHERE username = :username";
		    		$stmt = $conn->prepare($query);
		    		$stmt->bindParam(':username', $username, PDO::PARAM_INT);
		    		$stmt->execute();
		    		return $this->sendResponseStatus(true, 200, 'SUCCESS: USER HAS BEEN DELETED SUCCESSFULLY');
    			} else {
    				return $this->sendResponseStatus(false, 404, 'ERROR: USER DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE A USERNAME');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}

    }
}
