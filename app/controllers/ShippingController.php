<?php
// Đường dẫn: app/controllers/ShippingController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/ShippingModel.php';

class ShippingController
{

    public function list()
    {
        $db = (new Database())->getConnection();
        $partners = (new ShippingModel($db))->getAllPartners();
        require_once __DIR__ . '/../views/shipping/list.php';
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $db = (new Database())->getConnection();
            $shippingModel = new ShippingModel($db);

            $fee = (float)str_replace(['.', ','], '', $_POST['base_fee'] ?? 0);

            if ($shippingModel->addPartner(
                $_POST['partner_name'] ?? '',
                $_POST['partner_code'] ?? '',
                $fee,
                isset($_POST['allow_cod']) ? 1 : 0,
                (int)($_POST['max_retry'] ?? 3),
                $_POST['status'] ?? 'Đang kết nối',
                $_POST['notes'] ?? ''
            )) {
                header("Location: index.php?action=shipping_list&success=1");
                exit;
            }
        }
        require_once __DIR__ . '/../views/shipping/add.php';
    }

    public function edit()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
        $db = (new Database())->getConnection();
        $shippingModel = new ShippingModel($db);

        $partner = $shippingModel->getPartnerById($id);
        if (empty($partner)) {
            header("Location: index.php?action=shipping_list");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $fee = (float)str_replace(['.', ','], '', $_POST['base_fee'] ?? 0);

            if ($shippingModel->updatePartner(
                $id,
                $_POST['partner_name'] ?? '',
                $_POST['partner_code'] ?? '',
                $fee,
                isset($_POST['allow_cod']) ? 1 : 0,
                (int)($_POST['max_retry'] ?? 3),
                $_POST['status'] ?? 'Đang kết nối',
                $_POST['notes'] ?? ''
            )) {
                header("Location: index.php?action=shipping_list&success=1");
                exit;
            }
        }
        require_once __DIR__ . '/../views/shipping/edit.php';
    }

    public function delete()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
        if ($id > 0) {
            $db = (new Database())->getConnection();
            (new ShippingModel($db))->deletePartner($id);
        }
        header("Location: index.php?action=shipping_list&success_delete=1");
        exit;
    }
}
