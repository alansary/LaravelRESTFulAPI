<?php

namespace RESTFulAPI\Http\Controllers;

use Illuminate\Http\Request;
use RESTFulAPI\Http\Inc\Test;
use \PDO;

class MilestonesController extends BaseApiController
{
	private $tableName = "milestones";
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
	            $data[] = ['id' => $id, 'work_id' => $work_id, 'deliverables' => $deliverables, 
	            	'payment' => $payment, 'deadline' => $deadline, 'image' => base64_encode($image), 
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
			            $data[] = ['id' => $id, 'work_id' => $work_id, 'deliverables' => $deliverables, 
			            	'payment' => $payment, 'deadline' => $deadline, 'image' => base64_encode($image), 
			            	'created' => $created, 'modified' => $modified];
		          	}
		          	$this->setResponseStatus(true, 200, 'SUCCESS');
	          	} else {
	          		$this->setResponseStatus(false, 404, 'MILESTONE DOESN\'T EXIST');
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
    		$query = "SELECT $this->tableName.id AS id, $this->tableName.work_id AS work_id, $this->tableName.deliverables AS deliverables, $this->tableName.payment AS payment, $this->tableName.deadline AS deadline, $this->tableName.image AS image, $this->tableName.created AS created, $this->tableName.modified AS modified FROM $this->tableName INNER JOIN works ON $this->tableName.work_id = works.id WHERE works.user_id = :user_id ORDER BY id DESC";
    		$stmt = $conn->prepare($query);
    		if ($request->has('user_id')) {
    			$user_id = filter_var(Test::testInput($request->input('user_id')), FILTER_VALIDATE_INT);
	    		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
	    		$stmt->execute();
	    		$data = [];
	    		if ($stmt->rowCount()) {
		        	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		            	extract($row);
		            	$data[] = ['id' => $id, 'work_id' => $work_id, 'deliverables' => $deliverables, 
			            	'payment' => $payment, 'deadline' => $deadline, 'image' => base64_encode($image), 
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

    public function getByWorkId(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		$query = "SELECT * FROM $this->tableName WHERE work_id = :work_id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('work_id')) {
    			$work_id = filter_var(Test::testInput($request->input('work_id')), FILTER_VALIDATE_INT);
	    		$stmt->bindParam(':work_id', $work_id, PDO::PARAM_INT);
	    		$stmt->execute();
	    		$data = [];
	    		if ($stmt->rowCount()) {
		        	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		            	extract($row);
		            	$data[] = ['id' => $id, 'work_id' => $work_id, 'deliverables' => $deliverables, 
			            	'payment' => $payment, 'deadline' => $deadline, 'image' => base64_encode($image), 
			            	'created' => $created, 'modified' => $modified];
		          	}
	          	}
	          	$this->setResponseStatus(true, 200, 'SUCCESS');
          		return $this->sendResponseData($data);
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: WORK ID IS NOT PROVIDED');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}

    }

    public function insert(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		$query = "INSERT INTO $this->tableName VALUES (:id, :work_id, :deliverables, :payment, :deadline, :image, :created, :modified)";
    		$stmt = $conn->prepare($query);
    		if ($request->has('id') && $request->has('work_id') && $request->has('deliverables') && $request->has('payment') && $request->has('deadline') && $request->has('image') && $request->has('created') && $request->has('modified')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$work_id = filter_var(Test::testInput($request->input('work_id')), FILTER_VALIDATE_INT);
    			$deliverables = Test::testInput($request->input('deliverables'));
    			$payment = Test::testInput($request->input('payment'));
    			$deadline = Test::testInput($request->input('deadline'));
    			$image = $request->input('image');
    			$image = trim($image);
    			$image = htmlspecialchars($image);
    			$image = base64_decode($image);
    			$created = Test::testInput($request->input('created'));
    			$modified = Test::testInput($request->input('modified'));
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':work_id', $work_id, PDO::PARAM_INT);
    			$stmt->bindParam(':deliverables', $deliverables, PDO::PARAM_STR);
    			$stmt->bindParam(':payment', $payment);
    			$stmt->bindParam(':deadline', $deadline);
    			$stmt->bindParam(':image', $image, PDO::PARAM_LOB);
    			$stmt->bindParam(':created', $created);
    			$stmt->bindParam(':modified', $modified);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: MILESTONE INSERTED SUCCESSFULLY');
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
    		$this->sendResponseStatus(false, 404, 'ERROR: MILESTONE DOESN\'T EXIST');
    	}
    }

    public function updateId(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: MILESTONE DOESN\'T EXIST');
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
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: MILESTONE ID UPDATED SUCCESSFULLY');
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

    public function updateDeliverables(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: MILESTONE DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "UPDATE $this->tableName SET deliverables = :deliverables WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('deliverables')) {
    			$deliverables = Test::testInput($request->input('deliverables'));
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':deliverables', $deliverables, PDO::PARAM_STR);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: MILESTONE DELIVERABLES UPDATED SUCCESSFULLY');
    			} else {
    				$message = 'ERROR: '.$stmt->errorInfo()[2];
    				return $this->sendResponseStatus(false, 500, $message);
    			}
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE DELIVERABLES PARAMETER');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    public function updatePayment(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: MILESTONE DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "UPDATE $this->tableName SET payment = :payment WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('payment')) {
    			$payment = Test::testInput($request->input('payment'));
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':payment', $payment);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: MILESTONE PAYMENT UPDATED SUCCESSFULLY');
    			} else {
    				$message = 'ERROR: '.$stmt->errorInfo()[2];
    				return $this->sendResponseStatus(false, 500, $message);
    			}
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE PAYMENT PARAMETER');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    public function updateWorkId(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: MILESTONE DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "UPDATE $this->tableName SET work_id = :work_id WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('work_id')) {
    			$work_id = filter_var(Test::testInput($request->input('work_id')), FILTER_VALIDATE_INT);
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':work_id', $work_id, PDO::PARAM_INT);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: MILESTONE WORK ID UPDATED SUCCESSFULLY');
    			} else {
    				$message = 'ERROR: '.$stmt->errorInfo()[2];
    				return $this->sendResponseStatus(false, 500, $message);
    			}
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE WORK_ID PARAMETER');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    public function updateImage(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: MILESTONE DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "UPDATE $this->tableName SET image = :image WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('image')) {
    			$image = $request->input('image');
    			$image = trim($image);
    			$image = htmlspecialchars($image);
    			$image = base64_decode($image);
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':image', $image);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: MILESTONE IMAGE UPDATED SUCCESSFULLY');
    			} else {
    				$message = 'ERROR: '.$stmt->errorInfo()[2];
    				return $this->sendResponseStatus(false, 500, $message);
    			}
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE IMAGE PARAMETER');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    public function updateDeadline(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('id')) {
    			$id = filter_var(Test::testInput($request->input('id')), FILTER_VALIDATE_INT);
    			$this->checkIdExists($id, $conn);
    			if ($this->responseStatus['status']['isSuccess'] == false) {
    				return $this->sendResponseStatus(false, 404, 'ERROR: MILESTONE DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "UPDATE $this->tableName SET deadline = :deadline WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		if ($request->has('deadline')) {
    			$deadline = Test::testInput($request->input('deadline'));
    			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    			$stmt->bindParam(':deadline', $deadline);
    			$stmt->execute();
    			if (!isset($stmt->errorInfo()[2])) {
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: MILESTONE DEADLINE UPDATED SUCCESSFULLY');
    			} else {
    				$message = 'ERROR: '.$stmt->errorInfo()[2];
    				return $this->sendResponseStatus(false, 500, $message);
    			}
    		} else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE DEADLINE PARAMETER');
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
    				return $this->sendResponseStatus(false, 404, 'ERROR: MILESTONE DOESN\'T EXIST');
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
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: MILESTONE CREATED UPDATED SUCCESSFULLY');
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
    				return $this->sendResponseStatus(false, 404, 'ERROR: MILESTONE DOESN\'T EXIST');
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
    				return $this->sendResponseStatus(true, 200, 'SUCCESS: MILESTONE MODIFIED UPDATED SUCCESSFULLY');
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
    				return $this->sendResponseStatus(false, 404, 'ERROR: MILESTONE DOESN\'T EXIST');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE AN ID');
    		}
    		$query = "DELETE FROM $this->tableName WHERE id = :id";
    		$stmt = $conn->prepare($query);
    		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    		$stmt->execute();
    		return $this->sendResponseStatus(true, 200, 'SUCCESS: MILESTONE HAS BEEN DELETED SUCCESSFULLY');
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    public function deleteByWorkId(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('work_id')) {
    			$work_id = filter_var(Test::testInput($request->input('work_id')), FILTER_VALIDATE_INT);
    			$query = "SELECT * FROM $this->tableName WHERE work_id = :work_id";
    			$stmt = $conn->prepare($query);
    			$stmt->bindParam(':work_id', $work_id, PDO::PARAM_INT);
    			$stmt->execute();
    			if ($stmt->rowCount()) {
		    		$query = "DELETE FROM $this->tableName WHERE work_id = :work_id";
		    		$stmt = $conn->prepare($query);
		    		$stmt->bindParam(':work_id', $work_id, PDO::PARAM_INT);
		    		$stmt->execute();
		    		return $this->sendResponseStatus(true, 200, 'SUCCESS: MILESTONES HAS BEEN DELETED SUCCESSFULLY');
    			} else {
    				return $this->sendResponseStatus(false, 404, 'ERROR: NO MILESTONE WITH THE PROVIDED WORK ID');
    			}
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE A WORK_ID');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }

    public function deleteByUserId(Request $request) {
    	if ($request->has('key')) {
    		$conn = $this->getConnection($request->input('key'));
    		if ($request->has('user_id')) {
    			$user_id = filter_var(Test::testInput($request->input('user_id')), FILTER_VALIDATE_INT);
	    		$query = "DELETE FROM $this->tableName WHERE work_id IN (SELECT id FROM works WHERE user_id = :user_id)";
	    		$stmt = $conn->prepare($query);
	    		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
	    		$stmt->execute();
	    		if ($stmt->rowCount())
	    			return $this->sendResponseStatus(true, 200, 'SUCCESS: MILESTONE HAS BEEN DELETED SUCCESSFULLY');
	    		else
	    			return $this->sendResponseStatus(false, 404, 'ERROR: NO MILESTONE WITH THE PROVIDED USER ID');
    		}
    		else {
    			return $this->sendResponseStatus(false, 500, 'ERROR: YOU MUST PROVIDE A USER_ID');
    		}
    	} else {
    		return $this->sendResponseStatus(false, 500, 'ERROR: KEY IS NOT PROVIDED');
    	}
    }
}
