<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../model/DonModel.php');
require_once(__DIR__ . '/helpers/EmailHelper.php');
require_once(__DIR__ . '/helpers/ReceiptHelper.php');
require_once(__DIR__ . '/config/SettingsManager.php');

class DonController {
    public function showDon(Don $don) {
        $don->show();
    }

    public function listDons() {
        // Exclure les dons supprimés (dans la corbeille)
        $sql = "SELECT * FROM dons WHERE statut != 'deleted'";
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
        // Soft delete - marquer comme supprimé au lieu de supprimer définitivement
        $sql = "UPDATE dons SET statut = 'deleted', deleted_at = NOW() WHERE id = :id";
        $db = Config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
            return true;
        } catch (Exception $e) {
            error_log('Erreur deleteDon: ' . $e->getMessage());
            return false;
        }
    }

    public function addDon(Don $don) {
        $sql = "INSERT INTO dons (type_don, montant, email, association_id, statut, livraison, ville, cp, adresse, localisation, tel, description_don, image_don, created_at) 
                VALUES (:type_don, :montant, :email, :association_id, :statut, :livraison, :ville, :cp, :adresse, :localisation, :tel, :description_don, :image_don, NOW())";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
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
                'image_don' => $don->getImageDon()
            ]);
            
            if ($result) {
                $donId = $db->lastInsertId();
                error_log("Don ajouté avec succès - ID: " . $donId);
                
                // Envoyer une notification email à EcoMind si activé
                try {
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
                } catch (Exception $emailError) {
                    error_log("Erreur envoi email: " . $emailError->getMessage());
                    // Ne pas faire échouer l'ajout du don pour un problème d'email
                }
                
                return $donId;
            } else {
                throw new Exception("Échec de l'insertion en base de données");
            }
            
        } catch (Exception $e) {
            error_log('Erreur addDon: ' . $e->getMessage());
            throw new Exception('Erreur lors de l\'enregistrement du don: ' . $e->getMessage());
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
        // Vérifier d'abord que le don existe et n'est pas monétaire
        $donData = $this->getDon($id);
        if (!$donData) {
            error_log('Erreur rejectDon: Don non trouvé - ID: ' . $id);
            return false;
        }
        
        // Seuls les dons non monétaires peuvent être rejetés
        if ($donData['type_don'] === 'money') {
            error_log('Erreur rejectDon: Tentative de rejet d\'un don monétaire - ID: ' . $id);
            return false;
        }
        
        // Rejeter = envoyer à la corbeille au lieu de marquer comme "rejected"
        $sql = "UPDATE dons SET statut='deleted', deleted_at=NOW() WHERE id=:id AND type_don != 'money' AND statut = 'pending'";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute(['id' => $id]);
            
            error_log("Requête rejet - SQL: $sql, ID: $id, Résultat: " . ($result ? 'true' : 'false') . ", Lignes affectées: " . $query->rowCount());
            
            if ($result && $query->rowCount() > 0) {
                // Envoyer un email de notification au donneur (optionnel)
                try {
                    // Désactiver temporairement l'email pour éviter les erreurs
                    /*
                    $settingsManager = new SettingsManager();
                    if ($settingsManager->get('email_notifications', true)) {
                        EmailHelper::notifyDonationRejected($donData, $reason ?: 'Don matériel non conforme aux critères');
                    }
                    */
                } catch (Exception $emailError) {
                    error_log("Erreur envoi email rejet: " . $emailError->getMessage());
                    // Ne pas faire échouer le rejet pour un problème d'email
                }
                
                error_log("Don rejeté avec succès - ID: $id, Type: " . $donData['type_don']);
                return true;
            } else {
                error_log('Erreur rejectDon: Aucune ligne mise à jour - ID: ' . $id . ', Statut actuel: ' . $donData['statut']);
                return false;
            }
            
        } catch (Exception $e) {
            error_log('Erreur rejectDon: ' . $e->getMessage());
            return false;
        }
    }

    public function getDonsByEmail($email) {
        $sql = "SELECT * FROM dons WHERE email = :email ORDER BY created_at DESC";
        $db = Config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':email', strtolower($email));
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

    // =====================================================
    // MÉTHODES POUR LA CORBEILLE
    // =====================================================

    public function listDonsCorbeille() {
        // Récupérer seulement les dons dans la corbeille
        $sql = "SELECT * FROM dons WHERE statut = 'deleted' ORDER BY deleted_at DESC";
        $db = Config::getConnexion();
        try {
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur listDonsCorbeille: ' . $e->getMessage());
            return [];
        }
    }

    public function restaurerDon($id) {
        // Restaurer un don de la corbeille (remettre en pending)
        $sql = "UPDATE dons SET statut = 'pending', deleted_at = NULL WHERE id = :id AND statut = 'deleted'";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute(['id' => $id]);
            return $result;
        } catch (Exception $e) {
            error_log('Erreur restaurerDon: ' . $e->getMessage());
            return false;
        }
    }

    public function supprimerDefinitivement($id) {
        // Supprimer définitivement un don de la base de données
        $sql = "DELETE FROM dons WHERE id = :id AND statut = 'deleted'";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute(['id' => $id]);
            return $result;
        } catch (Exception $e) {
            error_log('Erreur supprimerDefinitivement: ' . $e->getMessage());
            return false;
        }
    }

    public function viderCorbeille() {
        // Supprimer définitivement tous les dons de la corbeille
        $sql = "DELETE FROM dons WHERE statut = 'deleted'";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute();
            return $result;
        } catch (Exception $e) {
            error_log('Erreur viderCorbeille: ' . $e->getMessage());
            return false;
        }
    }

    public function getStatsCorbeille() {
        // Obtenir les statistiques de la corbeille
        $sql = "SELECT 
                    COUNT(*) as total_corbeille,
                    COUNT(CASE WHEN type_don = 'money' THEN 1 END) as dons_monetaires,
                    COUNT(CASE WHEN type_don = 'material' THEN 1 END) as dons_materiels,
                    SUM(CASE WHEN type_don = 'money' THEN montant ELSE 0 END) as montant_total
                FROM dons WHERE statut = 'deleted'";
        $db = Config::getConnexion();
        try {
            $stmt = $db->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur getStatsCorbeille: ' . $e->getMessage());
            return [
                'total_corbeille' => 0,
                'dons_monetaires' => 0,
                'dons_materiels' => 0,
                'montant_total' => 0
            ];
        }
    }
}
?>