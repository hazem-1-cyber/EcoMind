<?php
declare(strict_types=1);

class Conseil
{
    private ?int $idConseil = null;
    private ?string $type = null;           // 'eau', 'energie', 'transport'
    private ?string $texte_conseil = null;

    public function __construct(?int $id = null, ?string $type = null, ?string $texte = null)
    {
        $this->idConseil     = $id;
        $this->type          = $type;
        $this->texte_conseil = $texte;
    }

    // ------ GETTERS ------
    public function getIdConseil(): ?int    { return $this->idConseil; }
    public function getType(): ?string      { return $this->type; }
    public function getTexteConseil(): ?string { return $this->texte_conseil; }

    // ------ SETTERS (fluent) ------
    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function setTexteConseil(?string $texte): self
    {
        $this->texte_conseil = $texte;
        return $this;
    }
}

/**
 * Modèle Réponse du formulaire
 */
class ReponseFormulaire
{
    private ?int    $idReponse      = null;
    private ?string $email_utilisateur = null;
    private ?int    $nbPersonne     = null;
    private ?int    $doucheFreq      = null;
    private ?int    $dureeDouche     = null;
    private ?string $chauffageType   = null;
    private ?int    $tempHiver       = null;
    private ?string $typeTransport   = null;
    private ?int    $distTravail     = null;
    private ?string $dateSoumis      = null;

    public function __construct(
        ?int    $id           = null,
        ?string $email        = null,
        ?int    $nbPers       = null,
        ?int    $freqDouche   = null,
        ?int    $durDouche    = null,
        ?string $typeChauff   = null,
        ?int    $temperHiver  = null,
        ?string $typeTransp   = null,
        ?int    $distaTravail = null,
        ?string $dtSoumis     = null
    ) {
        $this->idReponse       = $id;
        $this->email_utilisateur = $email;
        $this->nbPersonne      = $nbPers;
        $this->doucheFreq      = $freqDouche;
        $this->dureeDouche     = $durDouche;
        $this->chauffageType   = $typeChauff;
        $this->tempHiver       = $temperHiver;
        $this->typeTransport   = $typeTransp;
        $this->distTravail     = $distaTravail;
        $this->dateSoumis      = $dtSoumis;
    }

    // ------ GETTERS ------
    public function getIdReponse(): ?int    { return $this->idReponse; }
    public function getEmail(): ?string            { return $this->email_utilisateur; }
    public function getNbPersonne(): ?int   { return $this->nbPersonne; }
    public function getDoucheFreq(): ?int   { return $this->doucheFreq; }
    public function getDureeDouche(): ?int  { return $this->dureeDouche; }
    public function getChauffageType(): ?string { return $this->chauffageType; }
    public function getTempHiver(): ?int    { return $this->tempHiver; }
    public function getTypeTransport(): ?string { return $this->typeTransport; }
    public function getDistTravail(): ?int  { return $this->distTravail; }
    public function getDateSoumis(): ?string { return $this->dateSoumis; }

    // ------ SETTERS (fluent) ------
    public function setEmail(?string $val): self         { $this->email_utilisateur = $val; return $this; }
    public function setNbPersonne(?int $val): self       { $this->nbPersonne = $val; return $this; }
    public function setDoucheFreq(?int $val): self       { $this->doucheFreq = $val; return $this; }
    public function setDureeDouche(?int $val): self      { $this->dureeDouche = $val; return $this; }
    public function setChauffageType(?string $val): self { $this->chauffageType = $val; return $this; }
    public function setTempHiver(?int $val): self        { $this->tempHiver = $val; return $this; }
    public function setTypeTransport(?string $val): self   { $this->typeTransport = $val; return $this; }
    public function setDistTravail(?int $val): self      { $this->distTravail = $val; return $this; }
    public function setDateSoumis(?string $val): self    { $this->dateSoumis = $val; return $this; }
}