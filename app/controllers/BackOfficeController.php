<?php

require_once('C:\xampp\htdocs\projet_web\config\config.php');
include('C:\xampp\htdocs\projet_web\app\models\User.php');

class BackOfficeController {
    private $db;

    public function __construct() {
        $this->db = Config::getConnection();
    }

    public function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public function showUsers() {
        $sql = "SELECT * FROM users";
        try {
            $liste = $this->db->query($sql);
            return $liste->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }

    public function deleteUser($id)
{
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([':id' => $id]);
}

    public function updateUser($id, $name, $email, $address)
{
    $sql = "UPDATE users SET name = :name, email = :email, address = :address WHERE id = :id";
    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':address' => $address,
        ':id'      => $id
    ]);
}


    public function getUserById($id) {
        try {
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get user error: " . $e->getMessage());
            return null;
        }
    }

    public function handleRequests() {
        // Handle delete action
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            if ($this->deleteUser($id)) {
                $_SESSION['success_message'] = "User deleted successfully!";
            } else {
                $_SESSION['error_message'] = "Error deleting user!";
            }
            header("Location: " . str_replace("?action=delete&id=" . $id, "", $_SERVER['REQUEST_URI']));
            exit();
        }

        // Handle update action
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
            $id = intval($_POST['id']);
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $address = htmlspecialchars($_POST['address']);
            
            if ($this->updateUser($id, $name, $email, $address)) {
                $_SESSION['success_message'] = "User updated successfully!";
            } else {
                $_SESSION['error_message'] = "Error updating user!";
            }
            
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }

        // Get users for display
        $users = $this->showUsers();
        return $users;
    }
}
?>

