<?php
// Đường dẫn file: app/models/CategoryModel.php
class CategoryModel
{
    private $conn;
    private $table_name = "categories";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllCategories($search = '', $type = '')
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1 ";
        $params = [];

        if (!empty($search)) {
            $query .= " AND category_name LIKE ? ";
            $params[] = "%$search%";
        }

        if (!empty($type)) {
            if ($type == 'manual') {
                $query .= " AND selection_type = 'manual' ";
            } elseif ($type == 'auto') {
                $query .= " AND selection_type = 'auto' ";
            }
        }

        $query .= " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ========================================================
        // BỘ NÃO ĐẾM SỐ LƯỢNG SẢN PHẨM (XỬ LÝ ĐƯỢC CẢ AUTO & MANUAL)
        // ========================================================
        foreach ($categories as $key => $cat) {
            if (($cat['selection_type'] ?? '') == 'auto' && !empty($cat['auto_rules'])) {

                // 1. Dịch chuỗi luật JSON thành mảng
                $rules = json_decode($cat['auto_rules'], true);

                if (is_array($rules) && count($rules) > 0) {
                    $autoQuery = "SELECT COUNT(*) as total FROM products WHERE 1=1 ";
                    $autoParams = [];

                    // Kiểm tra là thỏa mãn "Tất cả (AND)" hay "Một trong các (OR)"
                    $matchType = (($cat['match_type'] ?? 'all') == 'any') ? ' OR ' : ' AND ';
                    $conditions = [];

                    // Lặp qua từng điều kiện để ghép lệnh SQL
                    foreach ($rules as $rule) {
                        $field = $rule['field'] ?? '';
                        $operator = $rule['operator'] ?? '';
                        $value = $rule['value'] ?? '';

                        if ($operator == 'equals') {
                            $conditions[] = "$field = ?";
                            $autoParams[] = $value;
                        } elseif ($operator == 'greater_than') {
                            $conditions[] = "$field > ?";
                            $autoParams[] = $value;
                        } elseif ($operator == 'contains') {
                            $conditions[] = "$field LIKE ?";
                            $autoParams[] = "%$value%";
                        }
                    }

                    if (count($conditions) > 0) {
                        $autoQuery .= " AND (" . implode($matchType, $conditions) . ")";
                    }

                    // Chạy lệnh đếm tự động
                    $countStmt = $this->conn->prepare($autoQuery);
                    $countStmt->execute($autoParams);
                    $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
                    $categories[$key]['product_count'] = $countResult['total'] ?? 0;
                } else {
                    $categories[$key]['product_count'] = 0;
                }
            } else {
                // 2. Đếm cho Danh mục Thủ công (Chỉ đếm những SP được gán đích danh)
                $countQuery = "SELECT COUNT(*) as total FROM products WHERE category = ?";
                $countStmt = $this->conn->prepare($countQuery);
                $countStmt->execute([$cat['category_name']]);
                $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
                $categories[$key]['product_count'] = $countResult['total'] ?? 0;
            }
        }

        return $categories;
    }

    public function getCategoryById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCategory($name, $desc, $alias, $seo_title, $seo_desc, $status, $sel_type, $match_type, $auto_rules, $sort_order)
    {
        $query = "INSERT INTO " . $this->table_name . " (category_name, description, alias, seo_title, seo_description, status, selection_type, match_type, auto_rules, sort_order) VALUES (:n, :d, :a, :st, :sd, :stat, :selt, :mt, :ar, :so)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':n', $name);
        $stmt->bindParam(':d', $desc);
        $stmt->bindParam(':a', $alias);
        $stmt->bindParam(':st', $seo_title);
        $stmt->bindParam(':sd', $seo_desc);
        $stmt->bindParam(':stat', $status);
        $stmt->bindParam(':selt', $sel_type);
        $stmt->bindParam(':mt', $match_type);
        $stmt->bindParam(':ar', $auto_rules);
        $stmt->bindParam(':so', $sort_order);
        if ($stmt->execute()) return $this->conn->lastInsertId();
        return false;
    }

    public function updateCategory($id, $name, $desc, $alias, $seo_title, $seo_desc, $status, $sel_type, $match_type, $auto_rules, $sort_order)
    {
        $query = "UPDATE " . $this->table_name . " SET category_name=:n, description=:d, alias=:a, seo_title=:st, seo_description=:sd, status=:stat, selection_type=:selt, match_type=:mt, auto_rules=:ar, sort_order=:so WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':n', $name);
        $stmt->bindParam(':d', $desc);
        $stmt->bindParam(':a', $alias);
        $stmt->bindParam(':st', $seo_title);
        $stmt->bindParam(':sd', $seo_desc);
        $stmt->bindParam(':stat', $status);
        $stmt->bindParam(':selt', $sel_type);
        $stmt->bindParam(':mt', $match_type);
        $stmt->bindParam(':ar', $auto_rules);
        $stmt->bindParam(':so', $sort_order);
        return $stmt->execute();
    }

    public function deleteCategory($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    public function getCategoriesOfProduct($product)
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $allCats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $matched_cats = [];
        foreach ($allCats as $cat) {
            // 1. Quét danh mục Tự động
            if (($cat['selection_type'] ?? '') == 'auto' && !empty($cat['auto_rules'])) {
                $rules = json_decode($cat['auto_rules'], true);
                if (is_array($rules) && count($rules) > 0) {
                    $isMatch = (($cat['match_type'] ?? 'all') == 'all') ? true : false;
                    foreach ($rules as $rule) {
                        $field = $rule['field'] ?? '';
                        $operator = $rule['operator'] ?? '';
                        $value = $rule['value'] ?? '';

                        $productValue = $product[$field] ?? '';
                        $ruleMatch = false;

                        if ($operator == 'equals') {
                            $ruleMatch = (strcasecmp((string)$productValue, (string)$value) == 0);
                        } elseif ($operator == 'greater_than') {
                            $ruleMatch = ((float)$productValue > (float)$value);
                        } elseif ($operator == 'contains') {
                            $ruleMatch = (stripos((string)$productValue, (string)$value) !== false);
                        }

                        if (($cat['match_type'] ?? 'all') == 'all') {
                            $isMatch = $isMatch && $ruleMatch;
                        } else {
                            $isMatch = $isMatch || $ruleMatch;
                        }
                    }
                    if ($isMatch) {
                        $matched_cats[] = $cat['category_name'];
                    }
                }
            } else {
                // 2. Quét danh mục Thủ công
                if (!empty($product['category']) && strcasecmp(trim($product['category']), trim($cat['category_name'])) == 0) {
                    $matched_cats[] = $cat['category_name'];
                }
            }
        }
        return empty($matched_cats) ? '---' : implode(', ', $matched_cats);
    }
}
