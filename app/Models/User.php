<?php

namespace App\Models;

/**
 * Alias de compatibilité — NE PAS UTILISER DANS DU CODE NOUVEAU.
 *
 * Ce fichier ne pouvait pas être supprimé de l'espace de travail lors de la
 * réorganisation du modèle en domaines métier (voir ARCHITECTURE.md §7).
 * Le modèle canonique est désormais {@see \App\Domains\Identity\Models\User}.
 * Cette classe n'est conservée que pour éviter une erreur de chargement si
 * une référence historique à `App\Models\User` subsiste quelque part.
 *
 * @deprecated Utilisez App\Domains\Identity\Models\User.
 */
class User extends \App\Domains\Identity\Models\User
{
    //
}
