<?php
// you shold create a database and table table before using this class
class database_utilities_oop {
	protected $link;

	public function __construct($db_hostname, $db_username, $db_password, $db_name) {
		// create and check connection
		$this->link = new mysqli($db_hostname, $db_username, $db_password, $db_name);
		if($this->link->connect_error) {
			die("Connection failed: " . $this->link->connect_error);
		}
	}

	public function __deconstruct() {
		$this->link->close();
	}

	/*
	$data should be dictionary having two arrays
	$keys should be array
	*/

	public function getData($table_name, $keys=[]) {
		if(empty($keys)) {
			$sql = "SELECT * FROM {$table_name}";
		}
		else {
			$sql = "SELECT ";
			foreach($keys as $key) {
				$sql .= "{$key},";
			}
			$sql = substr($sql, 0, -1);
			$sql .= " FROM {$table_name}";
		}

		$result = $this->link->query($sql);

		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				foreach($keys as $key) {
					echo $key . " : " . $row[$key] . ", ";
				}
				echo "<br />";
			}
		}
		else {
			echo "0 results";
		}
	}

	public function getRecentData($table_name, $keys=[], $orderVal="id", $limitVal=2) {
		$sql = "SELECT * FROM {$table_name} ORDER BY {$orderVal} DESC LIMIT {$limitVal}";

		$result = $this->link->query($sql);

		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				foreach($keys as $key) {
					echo $key . " : " . $row[$key] . ", ";
				}
				echo "<br />";
			}
		}
		else {
			echo "0 results";
		}
	}

	public function insertData($table_name, $data) {
		$sql = "";
		
		foreach($data as $x) {
			$sql .= "INSERT INTO {$table_name} (";
			foreach($x[0] as $key) {
				$sql .= "{$key},";
			}
			$sql = substr($sql, 0, -1);
			$sql .= ") VALUES(";
			foreach($x[1] as $value) {
				$sql .= "'{$value}',";
			}
			$sql = substr($sql, 0, -1);
			$sql .= ");";
		}

		if($this->link->multi_query($sql) === TRUE) {
			echo "New records created successfully";
		}
		else {
			echo "Error: " . $sql . "<br />" . $this->link->error;
		}
	}

	public function deleteData($table_name, $data) {
		if(empty($data)) {
			$sql = "DELETE FROM {$table_name}";
		}
		else {
			$sql = "";
			foreach($data as $x) {
				$sql .= "DELETE FROM {$table_name} WHERE ";
				for($i = 0; $i < count($x[0]); $i++) {
					$sql .= $x[0][$i] . "='" . $x[1][$i] . "'";

					if( (count($x[0]) - $i) > 1) {
						$sql .= " AND ";
					}
				}
				$sql .= ";";
			}
		}

		if($this->link->multi_query($sql) === TRUE) {
			echo "Record deleted successfully";
		}
		else {
			echo "Error deleting record:" . $this->link->error;
		}
	}

	public function updateData($table_name, $data, $updatedData=[]) {
		$sql = "";
		$index = 0;

		foreach($data as $x) {
			$sql .= "UPDATE {$table_name} SET {$updatedData[$index]['key']}='{$updatedData[$index]['value']}' WHERE ";
			for($i = 0; $i < count($x[0]); $i++) {
				$sql .= $x[0][$i] . "='" . $x[1][$i] . "'";

				if( (count($x[0]) - $i) > 1) {
					$sql .= " AND ";
				}
			}
			$sql .= ";";
			$index++;
		}

		if($this->link->multi_query($sql) === TRUE) {
			echo "Record updated successfully";
		}
		else {
			echo "Error updating record:" . $this->link->error;
		}
	}
}

		/* with prepared statement
		$sql = "";
		$keys = $data[0]["keys"];
		$values = [];
		foreach($data as $vs) {
			$temp = [];
			foreach($vs as $v) {
				$temp[count($temp)] = $v;
			}
			$values[count($values)] = $temp;
		}
		
		// create prepared statement
		$sql .= "INSERT INTO {$table_name} (";
		foreach($keys as $key) {
			$sql .= "{$key}, ";
		}
		$sql .= ") VALUES(";
		foreach($keys as $key) {
			$sql .= "?,";
		}
		$sql .= ");"

		$stmt = $this->link->prepare($sql);
		$stmt->bind_param();

		foreach($values as $value) {
			for($i = 0; $i < count(keys); $i++) {
				$$keys[$i] = $value[$i];
			}
			$stmt->execute();
		}
		*/


class database_utilities_pdo {
	protected $link;

	public function __construct($db_hostname, $db_username, $db_password, $db_name) {
		try {
			$this->link = new PDO("mysql:host={$db_hostname};dbname={$db_name}", $db_username, $db_password);
			$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			echo "Connected successfully<br />";
		}
		catch(PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
	}

	public function __deconstruct() {
		$this->link = null;
	}

	public function getData($table_name, $keys=[]) {
		if(empty($keys)) {
			$sql = "SELECT * FROM {$table_name}";
		}
		else {
			$sql = "SELECT ";
			foreach($keys as $key) {
				$sql .= $key . ",";
			}
			$sql = substr($sql, 0, -1);
			$sql .= " FROM {$table_name}";
		}

		try {
			$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $this->link->prepare($sql);
			$stmt->execute();

			$result = $stmt->fetchAll();
			foreach($result as $res) {
				foreach($keys as $key) {
					echo $key . " : " . $res[$key] . ", ";
				}
				echo "<br />";
			}
		}
		catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	}

	public function getRecentData($table_name, $keys=[], $orderVal="id", $limitVal=2) {
		$sql = "SELECT * FROM {$table_name} ORDER BY {$orderVal} DESC LIMIT {$limitVal}";

		try {
			$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $this->link->prepare($sql);
			$stmt->execute();

			$result = $stmt->fetchAll();
			foreach($result as $res) {
				foreach($keys as $key) {
					echo $key . " : " . $res[$key] . ", ";
				}
				echo "<br />";
			}
		}
		catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	}

	public function insertData($table_name, $data) {
		try {
			$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->link->beginTransaction();

			foreach($data as $x) {
				$sql = "INSERT INTO {$table_name} (";
				foreach($x[0] as $key) {
					$sql .= $key . ",";
				}
				$sql = substr($sql, 0, -1);
				$sql .= ") VALUES(";
				foreach($x[1] as $value) {
					$sql .= "'" . $value . "',";
				}
				$sql = substr($sql, 0, -1);
				$sql .= ");";

				echo $sql . "<br />";
				$this->link->exec($sql);
			}

			$this->link->commit();
			echo "New records created successfully";
		}
		catch(PDOException $e) {
			$this->link->rollback();
			echo "Error: " . $e->getMessage();
		}
	}

	public function deleteData($table_name, $data) {
		try {
			$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->link->beginTransaction();

			if(empty($data)) {
				$sql = "DELETE FROM {$table_name}";
			}
			else {
				foreach($data as $x) {
					$sql = "DELETE FROM {$table_name} WHERE ";
					for($i = 0; $i < count($x[0]); $i++) {
						$sql .= $x[0][$i] . "='" . $x[1][$i] . "'";

						if( (count($x[0]) - $i) > 1) {
							$sql .= " AND ";
						}
					}
					$sql .= ";";

					$this->link->exec($sql);
				}
			}

			$this->link->commit();
			echo "Record deleted successfully";
		}
		catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	}

	public function updateData($table_name, $data, $updatedData=[]) {
		$index = 0;

		try {
			$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->link->beginTransaction();

			foreach($data as $x) {
				$sql = "UPDATE {$table_name} SET {$updatedData[$index]['key']}='{$updatedData[$index]['value']}' WHERE ";
				for($i = 0; $i < count($x[0]); $i++) {
					$sql .= $x[0][$i] . "='" . $x[1][$i] . "'";

					if( (count($x[0]) - $i) > 1) {
						$sql .= " AND ";
					}
				}
				$sql .= ";";

				$this->link->exec($sql);
				$index++;
			}

			$this->link->commit();
			echo "Record updating successfully";
		}
		catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	}
}
?>