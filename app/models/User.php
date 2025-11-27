<?php
class User {
    private $id;
    private $name;
    private $email;
    private $address;
    private $role;
    private $password;
    private $created_at;
    private $db;

  

     public function __construct($id, $name, $email, $address, $password,$role) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->address = $address;
        $this->role = $role;
        $this->password = $password;
    }
    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
        public function getRole() { return $this->role; }
    public function getAddress() { return $this->address; }
    public function getPassword() { return $this->password; }
    public function getCreatedAt() { return $this->created_at; }

    // Setters
    public function setName($name) { $this->name = $name; }
    public function setEmail($email) { $this->email = $email; }
    public function setRole($role) { $this->role = $role; }
    public function setAddress($address) { $this->address = $address; }
    public function setPassword($password) { $this->password = $password; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }

}
?>
