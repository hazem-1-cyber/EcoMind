<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../model/DonModel.php');
require_once(__DIR__ . '/../helpers/EmailHelper.php');
require_once(__DIR__ . '/../helpers/ReceiptHelper.php');
require_once(__DIR__ . '/../config/SettingsManager.php');

class DonController {
    public function showDon(Don $don) {
        $don->show();
    }

    public function listDons() {
        $sql = "SELECT * FROM dons";
        $db = Config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Récupérer les derniers dons classés par date de création décroissante
    public function listRecentDons($limit = 5) {
        $sql = "SELECT * FROM dons ORDER BY created_at DESC, id DESC LIMIT :limit";
        $db = Config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            // bindValue with explicit type to avoid SQL injection and ensure integer
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur listRecentDons: ' . $e->getMessage());
            return [];
        }
    }

    public function deleteDon($id) {
        $sql = "DELETE FROM dons WHERE id = :id";
        $db = Config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function addDon(Don $don) {
        $sql = "INSERT INTO dons (type_don, montant, email, association_id, statut, livraison, ville, cp, adresse, localisation, tel, description_don, image_path, created_at) 
                VALUES (:type_don, :montant, :email, :association_id, :statut, :livraison, :ville, :cp, :adresse, :localisation, :tel, :description_don, :image_path, NOW())";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'type_don' => $don->getTypeDon(),
                'montant' => $don->getMontant(),
                'email' => $don->getEmail(),
                'association_id' => $don->getAssociationId(),
                'statut' => $don->getStatut() ?? 'pending',
                'livraison' => $don->getLivraison(),
                'ville' => $don->getVille(),
                'cp' => $don->getCp(),
                'adresse' => $don->getAdresse(),
                'localisation' => $don->getLocalisation(),
                'tel' => $don->getTel(),
                'description_don' => $don->getDescriptionDon(),
                'image_path' => $don->getImageDon()
            ]);
            
            // Envoyer une notification email à EcoMind si activé
            $settingsManager = new SettingsManager();
            if ($settingsManager->get('email_notifications', true)) {
                $donData = [
                    'type_don' => $don->getTypeDon(),
                    'montant' => $don->getMontant(),
                    'email' => $don->getEmail(),
                    'description' => $don->getDescriptionDon()
                ];
                EmailHelper::notifyNewDonation($donData);
            }
            
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function acceptDon($id) {
        $sql = "UPDATE dons SET statut='validated' WHERE id=:id";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            
            // Récupérer les données du don pour l'email
            $donData = $this->getDon($id);
            if ($donData) {
                $settingsManager = new SettingsManager();
                
                if ($settingsManager->get('email_notifications', true)) {
                    // Générer le reçu PDF
                    $receipt = ReceiptHelper::generateReceipt($donData);
                    
                    // Envoyer UN SEUL email avec remerciement + reçu en pièce jointe
                    EmailHelper::sendAcceptanceWithReceipt($donData, $receipt['filepath']);
                }
            }
            
            return true;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return false;
        }
    }

    public function rejectDon($id, $reason = '') {
        $sql = "UPDATE dons SET statut='rejected' WHERE id=:id";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            
            // Récupérer les données du don pour l'email
            $donData = $this->getDon($id);
            if ($donData) {
                // Envoyer un email de notification au donneur
                $settingsManager = new SettingsManager();
                if ($settingsManager->get('email_notifications', true)) {
                    EmailHelper::notifyDonationRejected($donData, $reason);
                }
            }
            
            return true;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return false;
        }
    }

    public function getDonsByEmail($email) {
        $sql = "SELECT * FROM dons WHERE email = :email ORDER BY created_at DESC";
        $db = Config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':email', $email);
        try {
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log('Erreur getDonsByEmail: ' . $e->getMessage());
            return [];
        }
    }

    public function getDon($id) {
        $sql = "SELECT * FROM dons WHERE id = :id";
        $db = Config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id);
        try {
            $query->execute();
            return $query->fetch();
        } catch (Exception $e) {
            error_log('Erreur getDon: ' . $e->getMessage());
            return false;
        }
    }

    public function getDonById($id) {
        return $this->getDon($id);
    }

    public function updateDonMoney(Don $don) {
        $sql = "UPDATE dons SET montant = :montant, email = :email, statut = :statut WHERE id = :id AND type_don = 'money'";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $don->getId(),
                'montant' => $don->getMontant(),
                'email' => $don->getEmail(),
                'statut' => $don->getStatut()
            ]);
            return true;
        } catch (Exception $e) {
            error_log('Erreur updateDonMoney: ' . $e->getMessage());
            return false;
        }
    }
}
?>