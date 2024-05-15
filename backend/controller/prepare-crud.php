<?php
class Prepare_crud
{
    private $conn;
    private $result = array(); // Any results from a query will be stored here

    public function __construct($host, $username, $password, $database)
    {
        // Establish database connection
        $this->conn = new mysqli($host, $username, $password, $database);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function insert($tablename, $data, $location = null)
    {
        $columns = implode(",", array_keys($data));
        $placeholders = rtrim(str_repeat('?,', count($data)), ',');
        $sql = "INSERT INTO `$tablename`($columns) VALUES($placeholders)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return 'Error preparing statement: ' . $this->conn->error;
        }
        $types = '';
        $values = array_values($data);
        foreach ($values as $value) {
            if (is_int($value)) {
                $types .= 'i'; // Integer
            } elseif (is_float($value)) {
                $types .= 'd'; // Double
            } elseif (is_string($value)) {
                $types .= 's'; // String
            } else {
                $types .= 's'; // Default to string
            }
        }
        $stmt->bind_param($types, ...$values);
        $query = $stmt->execute();
        if ($query) {
            if ($location != null) {
                header('location: ' . $location);
            }
            array_push($this->result, $this->conn->insert_id);
            return true;
        } else {
            echo "Error executing statement: " . $stmt->error;
            return false;
        }
    }

    public function select($tablename, $columns = '*', $join = null, $where = null, $params = array(), $order = null, $limit = null, $alias = null)
    {
        $sql = "SELECT $columns FROM `$tablename`";
        if ($alias != null) {
            $sql .= " AS " . $alias;
        }
        if ($join != null) {
            $sql .= " JOIN " . $join;
        }
        if ($where != null) {
            $sql .= " WHERE " . $where;
        }
        if ($order != null) {
            $sql .= ' ORDER BY ' . $order;
        }
        if ($limit != null) {
            if (isset($_GET["page"])) {
                $page = $_GET["page"];
            } else {
                $page = 1;
            }
            $start = ($page - 1) * $limit;

            $sql .= ' LIMIT ' . $start . ',' . $limit;
        }
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return 'Error  Preparing SQL: ' . $this->conn->error;
        }

        if (!empty($params)) {
            // Determine the types of parameters
            $types = '';
            $values = array_values($params);
            foreach ($values as $value) {
                if (is_int($value)) {
                    $types .= 'i'; // Integer
                } elseif (is_float($value)) {
                    $types .= 'd'; // Double
                } elseif (is_string($value)) {
                    $types .= 's'; // String
                } else {
                    $types .= 's'; // Default to string
                }
            }
            // Bind parameters to the prepared statement
            $stmt->bind_param($types, ...$values);
        }

        $query = $stmt->execute();
        if ($query === false) {
            return  'Error Executing Query: ' . $stmt->error;
        }
        $results = $stmt->get_result();
        if ($results->num_rows > 0) {
            if ($results->num_rows > 1) {
                while ($row = $results->fetch_assoc()) {
                    $records[] = $row;
                }
            } else {
                $records = $results->fetch_assoc();
            }
            return $records;
        } else {
            return  'No record Found!';
        }
    }

    public function update($tablename, $data, $condition, $location = null)
    {
        $updatecolumn = array();
        foreach ($data as $key => $value) {
            $updatecolumn[] = "`$key`= ?";
        }
        $updatecolstring = implode(",", $updatecolumn);
        $sql = "UPDATE `$tablename` SET $updatecolstring WHERE $condition";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return "Error preparing statement: " . $this->conn->error;
        }
        $types = '';
        $values = array_values($data);
        foreach ($values as $value) {
            if (is_int($value)) {
                $types .= 'i'; // Integer
            } elseif (is_float($value)) {
                $types .= 'd'; // Double
            } elseif (is_string($value)) {
                $types .= 's'; // String
            } else {
                $types .= 's'; // Default to string
            }
        }
        $stmt->bind_param($types, ...$values);
        $result = $stmt->execute();
        if ($result) {
            if ($stmt->affected_rows === 0) {
                return 'No rows affected.';
            } else {
                return true;
            }
        } else {
            return "Error executing query: " . $stmt->error;
        }
    }

    public function delete($tablename, $condition, $params = array(), $location = null)
    {
        $sql = "DELETE FROM `$tablename` WHERE $condition";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return "Error preparing statement: " . $this->conn->error;
        }
        if (!empty($params)) {
            // Determine the types of parameters
            $types = '';
            $values = array_values($params);
            foreach ($values as $value) {
                if (is_int($value)) {
                    $types .= 'i'; // Integer
                } elseif (is_float($value)) {
                    $types .= 'd'; // Double
                } elseif (is_string($value)) {
                    $types .= 's'; // String
                } else {
                    $types .= 's'; // Default to string
                }
            }
            // Bind parameters to the prepared statement
            $stmt->bind_param($types, ...$values);
        }
        $query = $stmt->execute();
        if ($query) {
            if ($location != null) {
                header("Location: " . $location);
            }
            return true;
        } else {
            return "Error executing statement: " . $stmt->error;
        }
    }

    public function getResult()
    {
        $val = $this->result;
        $this->result = array();
        return $val;
    }
    public function __destruct()
    {
        $this->conn->close();
    }
}
