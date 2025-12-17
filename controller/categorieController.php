<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../model/categorieModel.php');

class CategorieController {

    public function listCategories() {
        $sql = "SELECT * FROM categories ORDER BY nom";
        $db = Config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            error_log('Erreur listCategories: ' . $e->getMessage());
            return [];
        }
    }

    public function deleteCategorie($id) {
        $sql = "DELETE FROM categories WHERE id = :id";
        $db = Config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
            return true;
        } catch (Exception $e) {
            error_log('Erreur deleteCategorie: ' . $e->getMessage());
            return false;
        }
    }

    public function addCategorie(Categorie $categorie) {
        $sql = "INSERT INTO categories (nom, code, description) VALUES (:nom, :code, :description)";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $categorie->getNom(),
                'code' => $categorie->getCode(),
                'description' => $categorie->getDescription()
            ]);
            return true;
        } catch (Exception $e) {
            error_log('Erreur addCategorie: ' . $e->getMessage());
            return false;
        }
    }

    public function updateCategorie(Categorie $categorie, $id) {
        $sql = "UPDATE categories SET nom = :nom, code = :code, description = :description WHERE id = :id";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $id,
                'nom' => $categorie->getNom(),
                'code' => $categorie->getCode(),
                'description' => $categorie->getDescription()
            ]);
            return true;
        } catch (PDOException $e) {
            error_log('Erreur updateCategorie: ' . $e->getMessage());
            return false;
        }
    }

    public function showCategorie($id) {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $db = Config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id);
        try {
            $query->execute();
            $cat = $query->fetch();
            return $cat;
        } catch (Exception $e) {
            error_log('Erreur showCategorie: ' . $e->getMessage());
            return false;
        }
    }
}
?>