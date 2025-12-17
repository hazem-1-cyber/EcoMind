<?php

/**
 * Générateur de conseils écologiques avec IA
 */
class IAConseilGenerator
{
    private $apiKey;
    private $apiUrl;
    
    public function __construct()
    {
        // Charger la clé API OpenAI avec fallback
        $apiKeyFromEnv = getenv('OPENAI_API_KEY');
        if ($apiKeyFromEnv !== false && !empty($apiKeyFromEnv)) {
            $this->apiKey = $apiKeyFromEnv;
        } elseif (isset($_ENV['OPENAI_API_KEY']) && !empty($_ENV['OPENAI_API_KEY'])) {
            $this->apiKey = $_ENV['OPENAI_API_KEY'];
        } elseif (isset($_SERVER['OPENAI_API_KEY']) && !empty($_SERVER['OPENAI_API_KEY'])) {
            $this->apiKey = $_SERVER['OPENAI_API_KEY'];
        } else {
            $this->apiKey = 'votre_cle_api_ici';
        }
        $this->apiUrl = 'https://api.openai.com/v1/chat/completions';
    }
    
    /**
     * Génère des conseils personnalisés basés sur les réponses du formulaire
     */
    public function genererConseils(ReponseFormulaire $reponse)
    {
        // Préparer le contexte pour l'IA
        $contexte = $this->preparerContexte($reponse);
        
        // Générer les 3 conseils
        $conseils = [
            'eau' => $this->genererConseilEau($contexte),
            'energie' => $this->genererConseilEnergie($contexte),
            'transport' => $this->genererConseilTransport($contexte)
        ];
        
        return $conseils;
    }
    
    /**
     * Prépare le contexte basé sur les réponses
     */
    private function preparerContexte(ReponseFormulaire $reponse)
    {
        return [
            'nbPersonne' => $reponse->getNbPersonne(),
            'doucheFreq' => $reponse->getDoucheFreq(),
            'dureeDouche' => $reponse->getDureeDouche(),
            'chauffageType' => $reponse->getChauffageType(),
            'tempHiver' => $reponse->getTempHiver(),
            'typeTransport' => $reponse->getTypeTransport(),
            'distTravail' => $reponse->getDistTravail()
        ];
    }
    
    /**
     * Génère un conseil personnalisé pour l'eau
     */
    private function genererConseilEau($contexte)
    {
        $consoEstimee = $contexte['doucheFreq'] * $contexte['dureeDouche'] * 10; // litres par semaine
        $consoJournaliere = $consoEstimee / 7;
        
        $prompt = "Tu es un expert en écologie spécialisé dans la gestion de l'eau. 

CONTEXTE UTILISATEUR :
- Foyer de {$contexte['nbPersonne']} personnes
- {$contexte['doucheFreq']} douches par semaine de {$contexte['dureeDouche']} minutes chacune
- Consommation estimée : {$consoEstimee} litres/semaine ({$consoJournaliere} litres/jour)

MISSION : Génère UN conseil personnalisé et actionnable (maximum 150 caractères) pour réduire la consommation d'eau de cette personne spécifiquement.

RÈGLES :
- Commence par un verbe d'action (Installez, Réduisez, Adoptez, etc.)
- Inclus des chiffres concrets d'économie
- Adapte le conseil au profil (famille nombreuse vs célibataire, douches longues vs courtes)
- Sois motivant et pratique
- Donne un conseil différent selon la situation

CONSEIL PERSONNALISÉ :";
        
        return $this->appelIA($prompt, 'eau', $contexte);
    }
    
    /**
     * Génère un conseil personnalisé pour l'énergie
     */
    private function genererConseilEnergie($contexte)
    {
        $economieParDegre = ($contexte['tempHiver'] - 19) * 7; // % d'économie possible
        
        $prompt = "Tu es un expert en efficacité énergétique et écologie.

CONTEXTE UTILISATEUR :
- Foyer de {$contexte['nbPersonne']} personnes
- Chauffage : {$contexte['chauffageType']}
- Température actuelle en hiver : {$contexte['tempHiver']}°C
- Économie potentielle si baisse à 19°C : {$economieParDegre}%

MISSION : Génère UN conseil personnalisé et actionnable (maximum 150 caractères) pour réduire la consommation d'énergie de cette personne spécifiquement.

RÈGLES :
- Commence par un verbe d'action (Baissez, Installez, Programmez, etc.)
- Adapte au type de chauffage (électrique, gaz, pompe à chaleur, bois)
- Inclus des chiffres d'économie concrets
- Considère la température actuelle (trop haute = conseil de baisse, correcte = conseil d'optimisation)
- Adapte à la taille du foyer
- Sois motivant et pratique

CONSEIL PERSONNALISÉ :";
        
        return $this->appelIA($prompt, 'energie', $contexte);
    }
    
    /**
     * Génère un conseil personnalisé pour le transport
     */
    private function genererConseilTransport($contexte)
    {
        $emissionAnnuelle = 0;
        if ($contexte['typeTransport'] === 'voiture') {
            $emissionAnnuelle = $contexte['distTravail'] * 2 * 250 * 0.12; // kg CO2/an approximatif
        }
        
        $prompt = "Tu es un expert en mobilité durable et écologie.

CONTEXTE UTILISATEUR :
- Transport actuel : {$contexte['typeTransport']}
- Distance domicile-travail : {$contexte['distTravail']} km (aller simple)
- Émissions CO2 estimées : {$emissionAnnuelle} kg/an

MISSION : Génère UN conseil personnalisé et actionnable (maximum 150 caractères) pour réduire l'empreinte carbone transport de cette personne spécifiquement.

RÈGLES :
- Commence par un verbe d'action (Essayez, Adoptez, Passez, etc.)
- Adapte au moyen de transport actuel et à la distance
- Courte distance (<5km) = vélo/marche, moyenne (5-20km) = covoiturage/transport commun, longue (>20km) = éco-conduite
- Si déjà écologique (vélo, transport commun) = encouragement + conseil complémentaire
- Inclus des bénéfices concrets (économies, santé, CO2)
- Sois motivant et réaliste

CONSEIL PERSONNALISÉ :";
        
        return $this->appelIA($prompt, 'transport', $contexte);
    }
    
    /**
     * Appelle l'API d'IA pour générer un conseil
     */
    private function appelIA($prompt, $type, $contexte)
    {
        // Si pas de clé API, utiliser des conseils par défaut intelligents
        if ($this->apiKey === 'votre_cle_api_ici' || empty($this->apiKey) || strlen($this->apiKey) < 20) {
            return $this->conseilParDefaut($type, $contexte);
        }
        
        try {
            $data = [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un expert en écologie qui donne des conseils courts, pratiques et motivants pour réduire l\'empreinte carbone.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 100,
                'temperature' => 0.7
            ];
            
            $ch = curl_init($this->apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $result = json_decode($response, true);
                if (isset($result['choices'][0]['message']['content'])) {
                    return trim($result['choices'][0]['message']['content']);
                }
            }
            
            // En cas d'erreur, utiliser le conseil par défaut
            return $this->conseilParDefaut($type, $contexte);
            
        } catch (Exception $e) {
            // En cas d'erreur, utiliser le conseil par défaut
            return $this->conseilParDefaut($type, $contexte);
        }
    }
    
    /**
     * Génère des conseils par défaut intelligents basés sur le contexte avec variations
     */
    private function conseilParDefaut($type, $contexte)
    {
        switch ($type) {
            case 'eau':
                $consoSemaine = $contexte['doucheFreq'] * $contexte['dureeDouche'] * 10;
                $economieParMinute = 10; // litres économisés par minute réduite
                
                if ($contexte['dureeDouche'] > 12) {
                    $economie = ($contexte['dureeDouche'] - 8) * $economieParMinute * $contexte['doucheFreq'];
                    $variations = [
                        "Réduisez vos douches de {$contexte['dureeDouche']} à 8 minutes : économisez {$economie}L/semaine !",
                        "Passez de {$contexte['dureeDouche']} à 8 minutes de douche et économisez {$economie}L par semaine !",
                        "Coupez l'eau pendant le savonnage : de {$contexte['dureeDouche']} à 8 min = {$economie}L économisés !",
                        "Chronomètrez vos douches à 8 min max : {$economie}L d'eau économisés chaque semaine !"
                    ];
                    return $variations[array_rand($variations)];
                } elseif ($contexte['doucheFreq'] > 10) {
                    $variations = [
                        "Installez un pommeau économique : avec {$contexte['doucheFreq']} douches/semaine, économisez 40% !",
                        "Pommeau à débit réduit recommandé : {$contexte['doucheFreq']} douches = gros potentiel d'économie !",
                        "Réduisez à 1 douche/jour max et installez un pommeau économique !",
                        "Alternez douches et bains rapides : moins de {$contexte['doucheFreq']} douches/semaine !"
                    ];
                    return $variations[array_rand($variations)];
                } elseif ($contexte['nbPersonne'] > 3) {
                    $variations = [
                        "Foyer de {$contexte['nbPersonne']} : organisez les douches pour récupérer l'eau de chauffe !",
                        "Famille nombreuse ? Douches enchaînées = eau chaude optimisée !",
                        "Avec {$contexte['nbPersonne']} personnes, installez un mitigeur thermostatique !",
                        "Foyer {$contexte['nbPersonne']} personnes : pommeau économique = économies multipliées !"
                    ];
                    return $variations[array_rand($variations)];
                } else {
                    $variations = [
                        "Récupérez l'eau froide en attendant l'eau chaude pour arroser vos plantes !",
                        "Placez un seau sous la douche en attendant l'eau chaude : arrosage gratuit !",
                        "Collectez l'eau froide de la douche pour vos plantes : zéro gaspillage !",
                        "Installez un récupérateur d'eau de pluie pour compléter vos économies !"
                    ];
                    return $variations[array_rand($variations)];
                }
                
            case 'energie':
                $economieParDegre = ($contexte['tempHiver'] - 19) * 7;
                
                if ($contexte['tempHiver'] > 21) {
                    $variations = [
                        "Chauffage {$contexte['chauffageType']} à {$contexte['tempHiver']}°C ? Baissez à 19°C = {$economieParDegre}% d'économie !",
                        "De {$contexte['tempHiver']}°C à 19°C avec votre {$contexte['chauffageType']} : économisez {$economieParDegre}% !",
                        "Foyer {$contexte['nbPersonne']} personnes : 19°C suffisent, économisez {$economieParDegre}% !",
                        "Thermostat à {$contexte['tempHiver']}°C trop élevé ! 19°C = {$economieParDegre}% d'économie !"
                    ];
                    return $variations[array_rand($variations)];
                } elseif ($contexte['chauffageType'] === 'electrique' && $contexte['nbPersonne'] > 2) {
                    $variations = [
                        "Foyer {$contexte['nbPersonne']} + électrique : thermostat programmable = 20% d'économie !",
                        "Chauffage électrique + {$contexte['nbPersonne']} personnes : programmation par zones !",
                        "Électrique pour {$contexte['nbPersonne']} : radiateurs intelligents recommandés !",
                        "Optimisez votre électrique : programmation selon présence de chacun !"
                    ];
                    return $variations[array_rand($variations)];
                } elseif ($contexte['chauffageType'] === 'gaz') {
                    $variations = [
                        "Chauffage gaz : entretien annuel + purge radiateurs = 15% d'économie !",
                        "Gaz + isolation fenêtres : combo gagnant pour votre facture !",
                        "Votre gaz à {$contexte['tempHiver']}°C : purgez les radiateurs pour l'efficacité !",
                        "Chauffage gaz : thermostat d'ambiance = confort + économies !"
                    ];
                    return $variations[array_rand($variations)];
                } elseif ($contexte['chauffageType'] === 'pompe_a_chaleur') {
                    $variations = [
                        "Pompe à chaleur : optimisez le COP pour maximiser l'efficacité !",
                        "PAC + {$contexte['tempHiver']}°C : température idéale pour performance optimale !",
                        "Pompe à chaleur : entretien régulier = efficacité maximale !",
                        "PAC : programmation intelligente = confort + économies !"
                    ];
                    return $variations[array_rand($variations)];
                } else {
                    $variations = [
                        "Chauffage {$contexte['chauffageType']} : portez un pull, baissez d'1°C = 7% d'économie !",
                        "Habillez-vous chaud à la maison : 1°C de moins = facture allégée !",
                        "Superposez les couches : confort à {$contexte['tempHiver']}°C - 1 = économies !",
                        "Pull + chaussettes chaudes = thermostat baissé sans inconfort !"
                    ];
                    return $variations[array_rand($variations)];
                }
                
            case 'transport':
                if ($contexte['typeTransport'] === 'voiture' && $contexte['distTravail'] < 5) {
                    $variations = [
                        "Passez au vélo pour vos trajets de moins de 5km : santé + planète gagnantes !",
                        "5km en vélo = 20 minutes : bon pour vous et la planète !",
                        "Troquezvotre voiture contre un vélo pour ces {$contexte['distTravail']}km : double bénéfice !",
                        "Vélo électrique pour {$contexte['distTravail']}km : zéro émission, zéro effort !"
                    ];
                    return $variations[array_rand($variations)];
                } elseif ($contexte['typeTransport'] === 'voiture' && $contexte['distTravail'] < 20) {
                    $variations = [
                        "Essayez le covoiturage ou les transports en commun 2 fois par semaine pour commencer !",
                        "Covoiturez 2 jours/semaine : divisez vos frais et votre empreinte carbone !",
                        "Alternez voiture et transports en commun : économies et écologie !",
                        "Testez le covoiturage : convivialité et réduction de CO2 garanties !"
                    ];
                    return $variations[array_rand($variations)];
                } elseif ($contexte['typeTransport'] === 'voiture') {
                    $variations = [
                        "Adoptez l'éco-conduite : anticipez, roulez à 110 km/h sur autoroute, économisez 20% !",
                        "Éco-conduite = -20% de carburant : anticipation et vitesse modérée !",
                        "110 km/h au lieu de 130 : économisez 20% de carburant et réduisez le stress !",
                        "Conduite souple et anticipation : jusqu'à 20% d'économie de carburant !"
                    ];
                    return $variations[array_rand($variations)];
                } elseif ($contexte['typeTransport'] === 'transport_commun') {
                    $variations = [
                        "Bravo ! Continuez les transports en commun et testez le vélo pour les courtes distances !",
                        "Excellent choix ! Complétez avec le vélo pour les trajets de proximité !",
                        "Transports en commun + vélo = combo gagnant pour la planète !",
                        "Vous êtes sur la bonne voie ! Ajoutez le vélo à votre routine !"
                    ];
                    return $variations[array_rand($variations)];
                } elseif ($contexte['typeTransport'] === 'velo') {
                    $variations = [
                        "Excellent choix ! Partagez votre expérience vélo pour inspirer vos collègues !",
                        "Champion de l'éco-mobilité ! Motivez votre entourage à vous suivre !",
                        "Bravo pour le vélo ! Devenez ambassadeur de la mobilité douce !",
                        "Vélo quotidien : vous êtes un exemple ! Partagez vos astuces !"
                    ];
                    return $variations[array_rand($variations)];
                } else {
                    $variations = [
                        "Privilégiez la marche ou le vélo pour les trajets de moins de 3km au quotidien !",
                        "Moins de 3km ? Marchez ou pédalez : santé et planète vous remercient !",
                        "Courtes distances = opportunité parfaite pour marcher ou pédaler !",
                        "Transformez vos petits trajets en moments actifs : marche ou vélo !"
                    ];
                    return $variations[array_rand($variations)];
                }
                
            default:
                return "Adoptez des gestes éco-responsables au quotidien pour réduire votre empreinte carbone !";
        }
    }
    
    /**
     * Version alternative avec Hugging Face (gratuit)
     */
    private function appelHuggingFace($prompt)
    {
        $apiUrl = 'https://api-inference.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.1';
        $apiKey = getenv('HUGGINGFACE_API_KEY') ?: 'votre_cle_huggingface';
        
        if ($apiKey === 'votre_cle_huggingface') {
            return '';
        }
        
        try {
            $data = [
                'inputs' => $prompt,
                'parameters' => [
                    'max_new_tokens' => 100,
                    'temperature' => 0.7
                ]
            ];
            
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ]);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            $result = json_decode($response, true);
            if (isset($result[0]['generated_text'])) {
                return trim($result[0]['generated_text']);
            }
            
            return '';
            
        } catch (Exception $e) {
            return '';
        }
    }
}