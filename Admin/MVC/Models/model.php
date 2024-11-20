<?php
require_once("connection.php");

class Model
{
    var $conn;
    var $table;
    var $contens;

    function __construct()
    {
        $conn_obj = new Connection();
        $this->conn = $conn_obj->conn;
    }

    // Fetch all records from the table
    function All()
    {
        $query = "SELECT * FROM $this->table ORDER BY $this->contens DESC";
        require("result.php");
        return $data;
    }

    // Find a record by ID
    function find($id)
    {
        $query = "SELECT * FROM $this->table WHERE $this->contens = $id";
        return $this->conn->query($query)->fetch_assoc();
    }

    // Store a new record in the table
    function store($data)
    {
        $f = "";
        $v = "";
        foreach ($data as $key => $value) {
            $f .= $key . ",";
            $v .= "'" . $value . "',";
        }
        $f = trim($f, ",");
        $v = trim($v, ",");
        $query = "INSERT INTO $this->table($f) VALUES ($v);";

        $status = $this->conn->query($query);

        if ($status == true) {
            setcookie('msg', 'Thêm mới thành công', time() + 2);
            header('Location: ?mod=' . $this->table);
        } else {
            setcookie('msg', 'Thêm vào không thành công', time() + 2);
            header('Location: ?mod=' . $this->table . '&act=add');
        }
    }

    // Update an existing record in the table
    function update($data)
    {
        $v = "";
        foreach ($data as $key => $value) {
            $v .= $key . "='" . $value . "',";
        }
        $v = trim($v, ",");

        $query = "UPDATE $this->table SET $v WHERE $this->contens = " . $data[$this->contens];
        $result = $this->conn->query($query);

        if ($result == true) {
            setcookie('msg', 'Duyệt thành công', time() + 2);
            header('Location: ?mod=' . $this->table);
        } else {
            setcookie('msg', 'Update vào không thành công', time() + 2);
            header('Location: ?mod=' . $this->table . '&act=edit&id=' . $data['id']['id']);
        }
    }

    function delete($id)
    {
        $queryDeleteChild = "DELETE FROM hoadon WHERE MaND = $id";
        $this->conn->query($queryDeleteChild);

        $query = "DELETE FROM $this->table WHERE $this->contens = $id";
        $status = $this->conn->query($query);

        if ($status == true) {
            setcookie('msg', 'Xóa thành công', time() + 2);
        } else {
            setcookie('msg', 'Xóa không thành công', time() + 2);
        }
        header('Location: ?mod=' . $this->table);
    }
}
?>
