<?php
require_once 'C:/xampp/htdocs/projet_web/config/config.php';
include('C:\xampp\htdocs\projet_web\app\models\User.php');

class FrontOfficeController {

    private $conn;

    public function __construct() {
        $this->conn = Config::getConnection();
    }

    function adduser($user) {
        $sql = "INSERT INTO users (name, email, address, password, created_at, role)
                VALUES (:name, :email, :address, :password, NOW(), :role)";

        try {
            $req = $this->conn->prepare($sql);

            $req->bindValue(':name', $user->getName());
            $req->bindValue(':email', $user->getEmail());
            $req->bindValue(':address', $user->getAddress());
            $req->bindValue(':password', $user->getPassword());
            $req->bindValue(':role', 'user'); 
            return $req->execute();
        } catch (Exception $e) {
            echo 'Erreur: '.$e->getMessage();
            return false;
        }
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
           
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role']; // Ajouter le rôle dans la session
            return true;
        }
        return false;
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
// récupérer utilisateur par id
public function getUserById($id){
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// mettre à jour utilisateur
public function updateUser($id, $name, $email, $address, $password){
    $sql = "UPDATE users SET name = :name, email = :email, address = :address, password = :password WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([
        'name' => $name,
        'email' => $email,
        'address' => $address,
        'password' => $password,
        'id' => $id
    ]);
}

}
?>
