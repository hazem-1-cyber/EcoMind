<?php
class Categorie {
    private ?int $id;
    private ?string $nom;
    private ?string $code;
    private ?string $description;
    private ?DateTime $created_at;

    public function __construct(
        ?int $id = null,
        ?string $nom = null,
        ?string $code = null,
        ?string $description = null,
        ?DateTime $created_at = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->code = $code;
        $this->description = $description;
        $this->created_at = $created_at;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function getCode(): ?string { return $this->code; }
    public function getDescription(): ?string { return $this->description; }
    public function getCreatedAt(): ?DateTime { return $this->created_at; }

    // Setters
    public function setId(?int $id): void { $this->id = $id; }
    public function setNom(?string $nom): void { $this->nom = $nom; }
    public function setCode(?string $code): void { $this->code = $code; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setCreatedAt(?DateTime $created_at): void { $this->created_at = $created_at; }
}
?>