<?php
	namespace RESTFulAPI\Http\Inc;
	// testing function to test user parameters
	class Test {
		public static function testInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
		}
	}