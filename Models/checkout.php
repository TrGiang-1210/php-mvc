<?php
require_once("model.php");

class Checkout extends Model
{
    function save($data)
    {
        $f = "";
        $v = "";

        // Ensure 'PhuongThucTT' is provided in the $data array
        if (!isset($data['PhuongThucTT'])) {
            $data['PhuongThucTT'] = 'DefaultMethod'; // Replace with a valid default value
        }

        // Validate 'MaND' (User ID)
        if (!isset($data['MaND'])) {
            die("Error: MaND is required.");
        }

        // Check if 'MaND' exists in the 'NguoiDung' table
        $maND = $data['MaND'];
        $query_check_maND = "SELECT MaND FROM NguoiDung WHERE MaND = $maND";
        $result_check_maND = $this->conn->query($query_check_maND);

        if ($result_check_maND->num_rows === 0) {
            die("Error: MaND does not exist in NguoiDung table.");
        }

        // Prepare fields and values for the 'HoaDon' table
        foreach ($data as $key => $value) {
            $f .= $key . ",";
            $v .= "'" . $this->conn->real_escape_string($value) . "',";
        }

        $f = trim($f, ",");
        $v = trim($v, ",");
        $query = "INSERT INTO HoaDon($f) VALUES ($v);";

        $status = $this->conn->query($query);

        if (!$status) {
            // Handle query error
            die("Error inserting into HoaDon: " . $this->conn->error);
        }

        // Retrieve the last inserted MaHD
        $query_mahd = "SELECT MaHD FROM HoaDon ORDER BY NgayLap DESC LIMIT 1";
        $data_mahd = $this->conn->query($query_mahd)->fetch_assoc();

        if (!$data_mahd) {
            die("Error fetching MaHD: " . $this->conn->error);
        }

        // Insert into ChiTietHoaDon
        foreach ($_SESSION['sanpham'] as $value) {
            $MaSP = $value['MaSP'];
            $SoLuong = $value['SoLuong'];
            $DonGia = $value['DonGia'];
            $MaHD = $data_mahd['MaHD'];

            $query_ct = "INSERT INTO ChiTietHoaDon(MaHD, MaSP, SoLuong, DonGia) 
                         VALUES ($MaHD, $MaSP, $SoLuong, $DonGia)";

            $status_ct = $this->conn->query($query_ct);

            if (!$status_ct) {
                die("Error inserting into ChiTietHoaDon: " . $this->conn->error);
            }
        }

        // Handle success or failure
        if ($status) {
            setcookie('msg', 'Đăng ký thành công', time() + 2);
            header('location: ?act=checkout&xuli=order_complete');
        } else {
            setcookie('msg', 'Đăng ký không thành công', time() + 2);
            header('location: ?act=checkout');
        }
    }
}
?>
