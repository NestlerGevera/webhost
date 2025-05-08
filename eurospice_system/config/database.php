<?php
class Database
{
    private $host = "localhost";
    private $db_name = "eurospice_database";
    private $username = "root";
    private $password = "";
    public $conn;

    public function connect()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
            exit;
        }

        return $this->conn;
    }

    // ✅ Get all products
    public function getProducts()
    {
        $query = "SELECT * FROM products";
        return $this->conn->query($query);
    }

    // ✅ Get single product by ID
    public function getSingleProduct($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ Add new product
    public function createProduct($data)
    {
        $sql = "INSERT INTO products (name, brand, stock, price, batchCode, weight, packtype, packsize, shelftype, expirationDate, country, delivered, image, approved_by_finance, finance_notes, status)
                VALUES (:name, :brand, :stock, :price, :batchCode, :weight, :packtype, :packsize, :shelftype, :expirationDate, :country, :delivered, :image, :approved_by_finance, :finance_notes, :status)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // ✅ Update existing product
    public function updateProduct($data)
    {
        $sql = "UPDATE products SET 
                name = :name, brand = :brand, stock = :stock, price = :price, batchCode = :batchCode, 
                weight = :weight, packtype = :packtype, packsize = :packsize, shelftype = :shelftype, 
                expirationDate = :expirationDate, country = :country, delivered = :delivered, 
                image = :image, approved_by_finance = :approved_by_finance, finance_notes = :finance_notes, 
                status = :status 
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // ✅ Delete product by ID
    public function deleteProduct($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
