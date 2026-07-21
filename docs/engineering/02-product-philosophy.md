# 02 — Philosophie Produit

## Table des matières

1. [Pourquoi LOWLY existe](#1-pourquoi-lowly-existe)
2. [Pourquoi une marketplace](#2-pourquoi-une-marketplace)
3. [Pourquoi Laravel](#3-pourquoi-laravel)
4. [Pourquoi un monolithe](#4-pourquoi-un-monolithe)
5. [Principes UX](#5-principes-ux)
6. [Principes métier](#6-principes-métier)

---

## 1. Pourquoi LOWLY existe

Le marché de l'hébergement meublé et de la location de véhicules, dans les contextes où LOWLY opère, souffre d'un déficit de mise en relation structurée : les partenaires (propriétaires de résidences, loueurs de véhicules) gèrent souvent leur offre de façon informelle (réseaux personnels, messageries, annonces dispersées), sans outil pour centraliser disponibilités, tarifs et demandes. Les clients, de leur côté, manquent de visibilité fiable sur l'offre réellement disponible.

LOWLY existe pour combler ce vide : offrir aux partenaires un outil professionnel simple, et aux clients une source fiable et centralisée d'offres vérifiées.

## 2. Pourquoi une marketplace

Le choix du modèle marketplace (plutôt que, par exemple, un modèle d'agence intégrée où LOWLY gérerait elle-même l'inventaire) répond à plusieurs contraintes structurelles :

- **Scalabilité de l'offre** — un modèle d'agence obligerait LOWLY à posséder ou gérer directement chaque bien, ce qui limiterait fortement la croissance du catalogue.
- **Respect de l'autonomie du partenaire** — les partenaires connaissent mieux que quiconque leur bien, leurs clients habituels et leurs contraintes ; leur laisser la décision finale de chaque réservation est à la fois plus respectueux et plus sûr pour eux.
- **Répartition naturelle du risque** — LOWLY n'assume pas la responsabilité opérationnelle de la prestation (état du bien, qualité du service), qui reste entre le client et le partenaire ; LOWLY assume la responsabilité de la fiabilité de la mise en relation.

Ce choix structure directement le cycle de réservation défini dans [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) : chaque demande passe par une validation explicite du partenaire, jamais par une confirmation automatique.

## 3. Pourquoi Laravel

Laravel a été retenu comme framework backend unique pour plusieurs raisons pragmatiques, documentées en détail dans l'ADR correspondant (`18-adr.md`) :

- un écosystème mature et cohérent (ORM Eloquent, système de files d'attente, notifications, autorisation via Policies) qui couvre nativement l'essentiel des besoins du produit (réservations, notifications, validation) ;
- une productivité élevée pour une équipe de taille réduite, sans sacrifier la rigueur architecturale si les conventions sont respectées ;
- une communauté et une documentation abondantes, réduisant le risque technique à long terme ;
- la possibilité de servir à la fois le rendu HTML (Blade) et une couche API interne cohérente depuis une seule base de code.

## 4. Pourquoi un monolithe

Le choix d'un monolithe modulaire plutôt que des microservices est motivé par :

- la taille de l'équipe au démarrage, pour laquelle l'orchestration de plusieurs services indépendants représenterait un coût opérationnel disproportionné par rapport à la valeur ajoutée ;
- le besoin de cohérence transactionnelle forte entre les domaines `Reservation` et `Availability` — le blocage du calendrier doit être garanti atomique avec la confirmation d'une réservation, ce qui est nettement plus simple dans un même processus applicatif ;
- la volonté de conserver une vélocité de développement élevée durant la phase de croissance du MVP, sans les coûts de coordination inter-services.

Ce choix n'est pas figé pour toujours : la structuration en domaines métier isolés (voir [`ARCHITECTURE.md`](../../ARCHITECTURE.md) §5-7) est justement pensée pour permettre une extraction future en services séparés si la croissance du produit le justifiait, sans réécriture complète.

## 5. Principes UX

1. **Aucune ambiguïté sur le tarif** — le client voit toujours le tarif total avant de soumettre une demande ; ce tarif est contractuel.
2. **Le statut d'une demande est toujours visible** — un client ne doit jamais se demander où en est sa demande de réservation.
3. **Le refus n'est jamais une fin sans option** — chaque fois que c'est possible, une contre-proposition est offerte plutôt qu'un simple refus.
4. **Le partenaire n'est jamais submergé** — le tableau de bord partenaire présente en priorité ce qui requiert une action (nouvelles demandes) avant les informations passives.
5. **La simplicité prime sur l'exhaustivité** — chaque écran du MVP se limite aux informations strictement nécessaires à la décision de l'utilisateur.

## 6. Principes métier

1. **La validation humaine du partenaire est un principe fondateur**, jamais un simple détail d'implémentation — voir [`BUSINESS_RULES.md`](../../BUSINESS_RULES.md) §2.
2. **Le calendrier est la source de vérité de la disponibilité** — aucune réservation ne peut être confirmée en contradiction avec un blocage existant, garanti au niveau base de données (voir [`DATABASE.md`](../../DATABASE.md) §12).
3. **Chaque règle métier doit être testée explicitement** — voir [`TESTING.md`](../../TESTING.md) §7.
4. **Le MVP est un périmètre fermé** — toute extension doit être validée au regard de [`PRODUCT.md`](../../PRODUCT.md) avant d'être développée, jamais ajoutée de façon opportuniste en cours de sprint.
