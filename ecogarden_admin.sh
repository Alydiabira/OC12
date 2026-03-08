#!/bin/bash

# ============================
#   CONFIGURATION & COULEURS
# ============================
GREEN="\033[0;32m"
BLUE="\033[0;34m"
YELLOW="\033[1;33m"
RED="\033[0;31m"
NC="\033[0m"

BASE_URL="http://localhost:8000"
ENV_FILE=".env_token"
LOG_FILE="ecogarden.log"

DEBUG=false

# ============================
#   CHARGER TOKEN AUTOMATIQUE
# ============================
if [ -f "$ENV_FILE" ]; then
    TOKEN=$(cat $ENV_FILE)
    echo -e "${GREEN}Token chargé automatiquement depuis $ENV_FILE${NC}"
else
    echo -e "${YELLOW}Entrez votre token ADMIN :${NC}"
    read TOKEN
    echo "$TOKEN" > $ENV_FILE
    echo -e "${GREEN}Token sauvegardé dans $ENV_FILE${NC}"
fi

# ============================
#   FONCTION DEBUG
# ============================
debug() {
    if [ "$DEBUG" = true ]; then
        echo -e "${BLUE}[DEBUG] $1${NC}"
    fi
}

log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') — $1" >> $LOG_FILE
}

# ============================
#   FONCTIONS API
# ============================

add_conseil() {
    echo -e "${YELLOW}Contenu du conseil :${NC}"
    read CONTENU

    echo -e "${YELLOW}Liste des mois (ex: 3,4,5) :${NC}"
    read MOIS

    JSON="{\"contenu\":\"$CONTENU\",\"mois\":[$MOIS]}"

    debug "POST /conseil — $JSON"
    RESULT=$(curl -s -X POST $BASE_URL/conseil \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer $TOKEN" \
        -d "$JSON")

    echo -e "${GREEN}$RESULT${NC}"
    log "ADD CONSEIL: $RESULT"
}

edit_conseil() {
    echo -e "${YELLOW}ID du conseil :${NC}"
    read ID

    echo -e "${YELLOW}Nouveau contenu (laisser vide pour ne pas changer) :${NC}"
    read CONTENU

    echo -e "${YELLOW}Nouveaux mois (ex: 2,5 — laisser vide pour ne pas changer) :${NC}"
    read MOIS

    JSON="{"
    FIRST=true

    if [ ! -z "$CONTENU" ]; then
        JSON="$JSON\"contenu\":\"$CONTENU\""
        FIRST=false
    fi

    if [ ! -z "$MOIS" ]; then
        if [ "$FIRST" = false ]; then JSON="$JSON,"; fi
        JSON="$JSON\"mois\":[$MOIS]"
    fi

    JSON="$JSON}"

    debug "PUT /conseil/$ID — $JSON"
    RESULT=$(curl -s -X PUT $BASE_URL/conseil/$ID \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer $TOKEN" \
        -d "$JSON")

    echo -e "${GREEN}$RESULT${NC}"
    log "EDIT CONSEIL $ID: $RESULT"
}

delete_conseil() {
    echo -e "${YELLOW}ID du conseil à supprimer :${NC}"
    read ID

    debug "DELETE /conseil/$ID"
    RESULT=$(curl -s -X DELETE $BASE_URL/conseil/$ID \
        -H "Authorization: Bearer $TOKEN")

    echo -e "${GREEN}$RESULT${NC}"
    log "DELETE CONSEIL $ID: $RESULT"
}

view_conseil_month() {
    echo -e "${YELLOW}Mois (1-12) :${NC}"
    read MOIS

    debug "GET /conseil/$MOIS"
    RESULT=$(curl -s -X GET $BASE_URL/conseil/$MOIS \
        -H "Authorization: Bearer $TOKEN")

    echo -e "${GREEN}$RESULT${NC}"
    log "VIEW CONSEIL MONTH $MOIS: $RESULT"
}

view_conseils_all() {
    debug "GET /conseil"
    RESULT=$(curl -s -X GET $BASE_URL/conseil \
        -H "Authorization: Bearer $TOKEN")

    echo -e "${GREEN}$RESULT${NC}"
    log "VIEW CONSEILS ALL: $RESULT"
}

# ============================
#   MENU UTILISATEURS
# ============================

user_menu() {
    echo -e "${BLUE}=== MENU UTILISATEURS ===${NC}"
    echo "1) Voir tous les utilisateurs"
    echo "2) Supprimer un utilisateur"
    echo "3) Modifier un utilisateur"
    echo "0) Retour"
    read CHOICE

    case $CHOICE in
        1)
            debug "GET /user"
            RESULT=$(curl -s -X GET $BASE_URL/user \
                -H "Authorization: Bearer $TOKEN")
            echo -e "${GREEN}$RESULT${NC}"
            log "VIEW USERS: $RESULT"
        ;;
        2)
            echo -e "${YELLOW}ID utilisateur à supprimer :${NC}"
            read ID
            debug "DELETE /user/$ID"
            RESULT=$(curl -s -X DELETE $BASE_URL/user/$ID \
                -H "Authorization: Bearer $TOKEN")
            echo -e "${GREEN}$RESULT${NC}"
            log "DELETE USER $ID: $RESULT"
        ;;
        3)
            echo -e "${YELLOW}ID utilisateur :${NC}"
            read ID
            echo -e "${YELLOW}Nouvelle ville :${NC}"
            read VILLE
            JSON="{\"ville\":\"$VILLE\"}"
            debug "PUT /user/$ID — $JSON"
            RESULT=$(curl -s -X PUT $BASE_URL/user/$ID \
                -H "Content-Type: application/json" \
                -H "Authorization: Bearer $TOKEN" \
                -d "$JSON")
            echo -e "${GREEN}$RESULT${NC}"
            log "EDIT USER $ID: $RESULT"
        ;;
    esac
}

# ============================
#   MENU METEO
# ============================

meteo_menu() {
    echo -e "${YELLOW}Ville météo :${NC}"
    read VILLE

    debug "GET /meteo/$VILLE"
    RESULT=$(curl -s -X GET $BASE_URL/meteo/$VILLE \
        -H "Authorization: Bearer $TOKEN")

    echo -e "${GREEN}$RESULT${NC}"
    log "METEO $VILLE: $RESULT"
}

# ============================
#   MENU PRINCIPAL
# ============================

while true; do
    echo -e "\n${BLUE}=== MENU PRINCIPAL ===${NC}"
    echo "1) Ajouter un conseil"
    echo "2) Modifier un conseil"
    echo "3) Supprimer un conseil"
    echo "4) Voir conseils d’un mois"
    echo "5) Voir tous les conseils"
    echo "6) Gestion utilisateurs"
    echo "7) Tester météo"
    echo "8) Activer/Désactiver debug"
    echo "9) Voir le fichier log"
    echo "0) Quitter"
    read CHOICE

    case $CHOICE in
        1) add_conseil ;;
        2) edit_conseil ;;
        3) delete_conseil ;;
        4) view_conseil_month ;;
        5) view_conseils_all ;;
        6) user_menu ;;
        7) meteo_menu ;;
        8)
            DEBUG=!$DEBUG
            echo -e "${GREEN}Debug = $DEBUG${NC}"
        ;;
        9)
            echo -e "${BLUE}=== LOG ===${NC}"
            cat $LOG_FILE
        ;;
        0)
            echo -e "${GREEN}Au revoir !${NC}"
            exit 0
        ;;
        *)
            echo -e "${RED}Option invalide${NC}"
        ;;
    esac
done
