<?php

namespace RESTFulAPI\Http\Controllers;

use Illuminate\Http\Request;
use RESTFulAPI\Http\Inc\Test;
use \PDO;

class WorksController extends BaseApiController
{
	private $tableName = "works";
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
	    	$query = "SELECT * FROM $this->tableName ORDER BY user_id ASC";
			$stmt = $conn->prepare($query);
			$stmt->execute();
	        $data = [];
	        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	          	extract($row);
	            $data[] = ['id' => $id, 'user_id' => $user_id, 'description' => $description, 
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
		            	$data = ['id' => $id, 'user_id' => $user_id, 'description' => $description, 
		            		'created' => $created, 'modified' => $modified];
		          	}
		          	$this->setResponseStatus(true, 200, 'SUCCESS');
	          	} else {
	          		$this->setResponseStatus(false, 404, 'WORK DOESN\'T EXIST');
	          	}
	          	return $this->sendResponseData($data);
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: ID IS NOT PROVIDED');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}

    }

    public function getByUserId(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		$query = "SELECT * FROM $this->tableName WHERE user_id = :user_id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('user_id')) {
    			$user_id = filter_var(Test::testInput($request->input('user_id')), FILTER_VALIDATE_INT);
	    		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
	    		$stmt->execute();
	    		$data = [];
	    		if ($stmt->rowCount()) {
		        	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		            	extract($row);
		            	$data[] = ['id' => $id, 'user_id' => $user_id, 'description' => $description, 
		            		'created' => $created, 'modified' => $modified];
		          	}
	          	}
	          	$this->setResponseStatus(true, 200, 'SUCCESS');
          		return $this->sendResponseData($data);
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: USER ID IS NOT PROVIDED');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}

    }

    public function insert(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		$query = "INSERT INTO $this->tableName VALUES (:id, :user_id, :description, :created, :modified)";
    		$stmt = $conn->prepare($query);
    		if ($request->has('id') && $request->has('user_id') && $request->has('description') && $request->has('created') && $request->has('modified')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$user_id = filter_var(Test::testInput($request->input('user_id')), FILTER_VALIDATE_INT);
    			$description = Test::testInput($request->input('description'));
    			$created = Test::testInput($request->input('created'));
    			$modified = Test::testInput($request->input('modified'));
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    			$stmt->bindParam(':description', $description, PDO::PARAM_STR);
    			$stmt->bindParam(':created', $created);
    			$stmt->bindParam(':modified', $modified);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: WORK INSERTED SUCCESSFULLY');
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
    		$this->sendResponseStatus(false, 404, 'ERROR: WORK DOESN\'T EXIST');
    	}
    }

    public function updateId(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: WORK DOESN\'T EXIST');
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
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: WORK ID UPDATED SUCCESSFULLY');
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

    public function updateUserId(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: WORK DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "UPDATE $this->tableName SET user_id = :user_id WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('user_id')) {
    			$user_id = filter_var(Test::testInput($request->input('user_id')), FILTER_VALIDATE_INT);
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: WORK USER ID UPDATED SUCCESSFULLY');
    			} else {
    				$message = 'ERROR: '.$stmt->errorInfo()[2];
    				return $this->sendResponseStatus(false, 500, $message);
    			}
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE USER_ID PARAMETER');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    public function updateDescription(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: WORK DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "UPDATE $this->tableName SET description = :description WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('description')) {
    			$description = Test::testInput($request->input('description'));
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':description', $description, PDO::PARAM_STR);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: WORK DESCRIPTION UPDATED SUCCESSFULLY');
    			} else {
    				$message = 'ERROR: '.$stmt->errorInfo()[2];
    				return $this->sendResponseStatus(false, 500, $message);
    			}
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOUR MUST PROVIDE DESCRIPTION PARAMETER');
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
    				return $this->sendResponseStatus(false, 404, 'ERROR: WORK DOESN\'T EXIST');
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
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: WORK CREATED DATE UPDATED SUCCESSFULLY');
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
    				return $this->sendResponseStatus(false, 404, 'ERROR: WORK DOESN\'T EXIST');
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
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: WORK MODIFIED DATE UPDATED SUCCESSFULLY');
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
    				return $this->sendResponseStatus(false, 404, 'ERROR: WORK DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "DELETE FROM $this->tableName WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    		$stmt->execute();
    		return $this->sendResponseStatus(true, 200, 'SUCCESS: WORK HAS BEEN DELETED SUCCESSFULLY');
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    public function deleteByUserId(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('user_id')) {
    			$user_id = filter_var(Test::testInput($request->input('user_id')), FILTER_VALIDATE_INT);
		    	$query = "SELECT * FROM $this->tableName WHERE user_id = :user_id";
		    	$stmt = $conn->prepare($query);
		    	$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		    	$stmt->execute();
		    	if ($stmt->rowCount()) {
		    		$query = "DELETE FROM $this->tableName WHERE user_id = :user_id";
		    		$stmt = $conn->prepare($query);
		    		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		    		$stmt->execute();
		    		return $this->sendResponseStatus(true, 200, 'SUCCESS: WORKS HAS BEEN DELETED SUCCESSFULLY');
		    	} else {
		    		return $this->sendResponseStatus(false, 404, 'ERROR: NO WORK WITH THE PROVIDED USER ID');
		    	}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE A USER ID');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}

    }
}
