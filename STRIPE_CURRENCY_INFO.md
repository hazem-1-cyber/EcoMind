# Information sur la devise Stripe

## Problème
Stripe ne supporte pas directement le Dinar Tunisien (TND) comme devise de paiement.

## Solution implémentée
L'application convertit automatiquement les montants TND en USD pour le paiement Stripe.

### Taux de conversion utilisé
- **1 USD ≈ 3.1 TND** (taux approximatif)

### Comment ça fonctionne
1. L'utilisateur saisit un montant en TND (ex: 100 TND)
2. Le système convertit en USD (100 TND ÷ 3.1 ≈ 32.26 USD)
3. Le paiement Stripe est effectué en USD
4. Le montant original en TND est conservé dans les métadonnées

### Devises supportées par Stripe
Stripe supporte plus de 135 devises, mais pas TND. Les devises les plus courantes sont :
- USD (Dollar américain)
- EUR (Euro)
- GBP (Livre sterling)
- CAD (Dollar canadien)
- AUD (Dollar australien)

### Pour changer la devise
Si vous souhaitez utiliser une autre devise (comme EUR), modifiez le fichier :
`view/FrontOffice/create_payment_intent.php`

Changez la ligne :
```php
'currency' => 'usd',
```

En :
```php
'currency' => 'eur',
```

Et ajustez le taux de conversion en conséquence (1 EUR ≈ 3.3 TND).

### Alternative : Utiliser un service de paiement local
Pour accepter directement des paiements en TND, vous pouvez utiliser :
- **Paymee** (service tunisien)
- **Flouci** (service tunisien)
- **Clictopay** (service tunisien)

Ces services supportent nativement le TND et les cartes bancaires tunisiennes.
