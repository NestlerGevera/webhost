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
            echo "Connection Error: " . $e->getMessage();
        }

        return $this->conn;
    }

    // Get all products
    public function getProducts()
    {
        $query = "SELECT * FROM products ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Create product
    public function createProduct($data)
    {
        $query = "INSERT INTO products (name, brand, stock, price, batchCode, weight, packtype, packsize, 
                shelftype, expirationDate, country, delivered, image, approved_by_finance, finance_notes, status) 
                VALUES (:name, :brand, :stock, :price, :batchCode, :weight, :packtype, :packsize, :shelftype, 
                :expirationDate, :country, :delivered, :image, :approved_by_finance, :finance_notes, :status)";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind data
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':brand', $data['brand']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':batchCode', $data['batchCode']);
        $stmt->bindParam(':weight', $data['weight']);
        $stmt->bindParam(':packtype', $data['packtype']);
        $stmt->bindParam(':packsize', $data['packsize']);
        $stmt->bindParam(':shelftype', $data['shelftype']);
        $stmt->bindParam(':expirationDate', $data['expirationDate']);
        $stmt->bindParam(':country', $data['country']);
        $stmt->bindParam(':delivered', $data['delivered']);
        $stmt->bindParam(':image', $data['image']);
        $stmt->bindParam(':approved_by_finance', $data['approved_by_finance']);
        $stmt->bindParam(':finance_notes', $data['finance_notes']);
        $stmt->bindParam(':status', $data['status']);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read single product
    public function getSingleProduct($id)
    {
        $query = "SELECT * FROM products WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update product
    public function updateProduct($data)
    {
        $query = "UPDATE products SET 
                name = :name,
                brand = :brand,
                stock = :stock,
                price = :price,
                batchCode = :batchCode,
                weight = :weight,
                packtype = :packtype,
                packsize = :packsize,
                shelftype = :shelftype,
                expirationDate = :expirationDate,
                country = :country,
                delivered = :delivered,
                image = :image,
                approved_by_finance = :approved_by_finance,
                finance_notes = :finance_notes,
                status = :status
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind data
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':brand', $data['brand']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':batchCode', $data['batchCode']);
        $stmt->bindParam(':weight', $data['weight']);
        $stmt->bindParam(':packtype', $data['packtype']);
        $stmt->bindParam(':packsize', $data['packsize']);
        $stmt->bindParam(':shelftype', $data['shelftype']);
        $stmt->bindParam(':expirationDate', $data['expirationDate']);
        $stmt->bindParam(':country', $data['country']);
        $stmt->bindParam(':delivered', $data['delivered']);
        $stmt->bindParam(':image', $data['image']);
        $stmt->bindParam(':approved_by_finance', $data['approved_by_finance']);
        $stmt->bindParam(':finance_notes', $data['finance_notes']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':id', $data['id']);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete product
    public function deleteProduct($id)
    {
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
