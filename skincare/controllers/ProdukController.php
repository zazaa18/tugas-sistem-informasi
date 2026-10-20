<?php
require_once __DIR__ . "/../models/Produk.php";

class ProdukController {

    public function index() {
        $model = new Produk();
        $data = $model->getAll();
        require __DIR__ . "/../views/list.php";
    }

    public function tambah() {
        require __DIR__ . "/../views/tambah.php";
    }

    public function store($data) {
        $model = new Produk();
        $model->insert($data);
        header("Location: ../index.php");
    }

    public function edit($id) {
        $model = new Produk();
        $data = $model->getById($id);
        require __DIR__ . "/../views/edit.php";
    }

    public function update($data) {
        $model = new Produk();
        $model->update($data);
        header("Location: ../index.php");
    }

    public function delete($id) {
        $model = new Produk();
        $model->delete($id);
        header("Location: ../index.php");
    }
}

if (isset($_GET['action'])) {
    $controller = new ProdukController();

    switch ($_GET['action']) {
        case 'tambah':
            $controller->tambah();
            break;
        case 'store':
            $controller->store($_POST);
            break;
        case 'edit':
            $controller->edit($_GET['id']);
            break;
        case 'update':
            $controller->update($_POST);
            break;
        case 'delete':
            $controller->delete($_GET['id']);
            break;
    }
}