<?php
// Đường dẫn: app/models/ProductModel.php
class ProductModel
{
    private $conn;
    private $table_name = "products";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllProducts()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductsWithStock($search = '', $category = '', $brand = '', $tags = '', $type = '')
    {
        $query = "
            SELECT p.*, 
            CASE 
                WHEN p.product_type = 'Combo' THEN (
                    SELECT MIN(FLOOR(sub_p.stock / c.quantity))
                    FROM product_combo_details c
                    JOIN products sub_p ON c.product_id = sub_p.id
                    WHERE c.combo_id = p.id
                )
                ELSE p.stock 
            END as ton_kho,
            CASE 
                WHEN p.product_type = 'Combo' THEN (
                    SELECT MIN(FLOOR(sub_p.available / c.quantity))
                    FROM product_combo_details c
                    JOIN products sub_p ON c.product_id = sub_p.id
                    WHERE c.combo_id = p.id
                )
                ELSE p.available 
            END as co_the_ban
            FROM " . $this->table_name . " p 
            WHERE 1=1 ";

        $params = [];

        if (!empty($search)) {
            $query .= " AND (p.product_name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?) ";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if (!empty($category)) {
            $query .= " AND p.category = ? ";
            $params[] = $category;
        }
        if (!empty($brand)) {
            $query .= " AND p.brand = ? ";
            $params[] = $brand;
        }
        if (!empty($tags)) {
            $query .= " AND p.tags LIKE ? ";
            $params[] = "%$tags%";
        }
        if (!empty($type)) {
            if ($type == 'Quy đổi') {
                $query .= " AND p.parent_id IS NOT NULL ";
            } else {
                $query .= " AND p.product_type = ? AND p.parent_id IS NULL ";
                $params[] = $type;
            }
        }

        $query .= " ORDER BY p.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addProduct($name, $brand, $price, $sku, $barcode, $unit, $description, $image, $compare_price, $cost_price, $apply_tax, $category, $tags)
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  (product_name, brand, base_price, sku, barcode, unit, description, image, compare_price, cost_price, apply_tax, category, tags) 
                  VALUES (:name, :brand, :price, :sku, :barcode, :unit, :description, :image, :compare_price, :cost_price, :apply_tax, :category, :tags)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':sku', $sku);
        $stmt->bindParam(':barcode', $barcode);
        $stmt->bindParam(':unit', $unit);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':compare_price', $compare_price);
        $stmt->bindParam(':cost_price', $cost_price);
        $stmt->bindParam(':apply_tax', $apply_tax);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':tags', $tags);

        if ($stmt->execute()) return $this->conn->lastInsertId();
        return false;
    }

    public function updateProduct($id, $name, $brand, $price, $sku, $barcode, $unit, $description, $image, $compare_price, $cost_price, $apply_tax, $category, $tags)
    {
        $query = "UPDATE " . $this->table_name . " SET product_name=:name, brand=:brand, base_price=:price, sku=:sku, barcode=:barcode, unit=:unit, description=:description, image=:image, compare_price=:compare_price, cost_price=:cost_price, apply_tax=:apply_tax, category=:category, tags=:tags WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':sku', $sku);
        $stmt->bindParam(':barcode', $barcode);
        $stmt->bindParam(':unit', $unit);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':compare_price', $compare_price);
        $stmt->bindParam(':cost_price', $cost_price);
        $stmt->bindParam(':apply_tax', $apply_tax);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':tags', $tags);
        return $stmt->execute();
    }

    public function deleteProduct($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getBaseProducts()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE parent_id IS NULL ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addConvertedProduct($parent_id, $name, $unit, $qty, $sku, $barcode, $price)
    {
        $baseProduct = $this->getProductById($parent_id);
        $category = $baseProduct['category'] ?? '';
        $brand = $baseProduct['brand'] ?? '';
        $image = $baseProduct['image'] ?? '';

        $query = "INSERT INTO " . $this->table_name . " 
                  (parent_id, conversion_qty, product_name, unit, sku, barcode, base_price, category, brand, image) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$parent_id, $qty, $name, $unit, $sku, $barcode, $price, $category, $brand, $image]);
    }

    public function addComboProduct($name, $sku, $barcode, $unit, $description, $image, $price, $compare_price, $apply_tax, $category, $brand, $tags, $component_ids, $component_qtys)
    {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO " . $this->table_name . " 
                      (product_type, product_name, sku, barcode, unit, description, image, base_price, compare_price, apply_tax, category, brand, tags) 
                      VALUES ('Combo', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$name, $sku, $barcode, $unit, $description, $image, $price, $compare_price, $apply_tax, $category, $brand, $tags]);
            $combo_id = $this->conn->lastInsertId();

            $queryDetail = "INSERT INTO product_combo_details (combo_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmtDetail = $this->conn->prepare($queryDetail);

            for ($i = 0; $i < count($component_ids); $i++) {
                $p_id = $component_ids[$i];
                $qty = $component_qtys[$i];
                if (!empty($p_id) && $qty > 0) {
                    $stmtDetail->execute([$combo_id, $p_id, $qty]);
                }
            }

            $this->conn->commit();
            return $combo_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function updateInventory($id, $new_stock, $new_available)
    {
        $query = "UPDATE " . $this->table_name . " SET stock = :stock, available = :available WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stock', $new_stock, PDO::PARAM_INT);
        $stmt->bindParam(':available', $new_available, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
