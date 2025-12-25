<?php
class Database {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $charset;
    public $conn;

    public function __construct() {
        // Lưu ý đường dẫn: index.php ở Public nên đi vào Config/Database.php
        $config = require __DIR__ . '/../Config/Config_database.php';

        $this->host = $config['host'];
        $this->dbname = $config['dbname'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->charset = $config['charset'];
    }

    public function connect() {
        $this->conn = null;
        try {
            // Sửa lỗi: Thêm dấu "=" sau host
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Lỗi kết nối: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>