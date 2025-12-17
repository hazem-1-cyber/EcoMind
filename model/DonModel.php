<?php
class Don {
    private ?int $id;
    private ?string $type_don;
    private ?float $montant;
    private ?string $livraison;
    private ?string $ville;
    private ?string $cp;
    private ?string $adresse;
    private ?string $localisation;
    private ?string $tel;
    private ?string $description_don;
    private ?int $association_id;
    private ?string $email;
    private ?string $statut;
    private ?string $image_don;
    private ?DateTime $created_at;

    public function __construct(
        ?int $id = null,
        ?string $type_don = null,
        ?float $montant = null,
        ?string $livraison = null,
        ?string $ville = null,
        ?string $cp = null,
        ?string $adresse = null,
        ?string $localisation = null,
        ?string $tel = null,
        ?string $description_don = null,
        ?int $association_id = null,
        ?string $email = null,
        ?string $statut = 'pending',
        ?string $image_don = null,
        ?DateTime $created_at = null
    ) {
        $this->id = $id;
        $this->type_don = $type_don;
        $this->montant = $montant;
        $this->livraison = $livraison;
        $this->ville = $ville;
        $this->cp = $cp;
        $this->adresse = $adresse;
        $this->localisation = $localisation;
        $this->tel = $tel;
        $this->description_don = $description_don;
        $this->association_id = $association_id;
        $this->email = $email;
        $this->statut = $statut;
        $this->image_don = $image_don;
        $this->created_at = $created_at;
    }

  public function show() {
        echo "<table border='1' cellpadding='8' cellspacing='0' style='font-family: Arial; margin-top: 20px;'>";
        echo "<tr style='background: #f0f0f0;'><th>Champ</th><th>Valeur</th></tr>";
        echo "<tr><td>ID</td><td>" . ($this->id ?? 'N/A') . "</td></tr>";
        echo "<tr><td>Type de don</td><td>" . ($this->type_don ?? 'N/A') . "</td></tr>";
        echo "<tr><td>Montant</td><td>" . ($this->montant ? number_format($this->montant, 2) . ' TND' : 'N/A') . "</td></tr>";
        echo "<tr><td>Livraison</td><td>" . ($this->livraison ?? 'N/A') . "</td></tr>";
        echo "<tr><td>Ville</td><td>" . ($this->ville ?? 'N/A') . "</td></tr>";
        echo "<tr><td>Code postal</td><td>" . ($this->cp ?? 'N/A') . "</td></tr>";
        echo "<tr><td>Adresse</td><td>" . ($this->adresse ?? 'N/A') . "</td></tr>";
        echo "<tr><td>Téléphone</td><td>" . ($this->tel ?? 'N/A') . "</td></tr>";
        echo "<tr><td>Email</td><td>" . ($this->email ?? 'N/A') . "</td></tr>";
        echo "<tr><td>Association ID</td><td>" . ($this->association_id ?? 'N/A') . "</td></tr>";
        echo "<tr><td>Statut</td><td>" . ($this->statut ?? 'N/A') . "</td></tr>";
        echo "<tr><td>Date création</td><td>" . ($this->created_at ? $this->created_at->format('d/m/Y H:i') : 'N/A') . "</td></tr>";
        echo "</table>";
    }

    // Getters & Setters
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }
    public function getTypeDon(): ?string { return $this->type_don; }
    public function setTypeDon(?string $type_don): void { $this->type_don = $type_don; }
    public function getMontant(): ?float { return $this->montant; }
    public function setMontant(?float $montant): void { $this->montant = $montant; }
    public function getLivraison(): ?string { return $this->livraison; }
    public function setLivraison(?string $livraison): void { $this->livraison = $livraison; }
    public function getVille(): ?string { return $this->ville; }
    public function setVille(?string $ville): void { $this->ville = $ville; }
    public function getCp(): ?string { return $this->cp; }
    public function setCp(?string $cp): void { $this->cp = $cp; }
    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(?string $adresse): void { $this->adresse = $adresse; }
    public function getLocalisation(): ?string { return $this->localisation; }
    public function setLocalisation(?string $localisation): void { $this->localisation = $localisation; }
    public function getTel(): ?string { return $this->tel; }
    public function setTel(?string $tel): void { $this->tel = $tel; }
    public function getDescriptionDon(): ?string { return $this->description_don; }
    public function setDescriptionDon(?string $description_don): void { $this->description_don = $description_don; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): void { $this->email = $email ? strtolower($email) : null; }
    public function getAssociationId(): ?int { return $this->association_id; }
    public function setAssociationId(?int $association_id): void { $this->association_id = $association_id; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(?string $statut): void { $this->statut = $statut; }
    public function getImageDon(): ?string { return $this->image_don; }
    public function setImageDon(?string $image_don): void { $this->image_don = $image_don; }
    public function getCreatedAt(): ?DateTime { return $this->created_at; }
    public function setCreatedAt(?DateTime $created_at): void { $this->created_at = $created_at; }
}
?>