# Gebruikersbeheer Project – Laravel + Inertia + Vue 3 + Tailwind

Een moderne Laravel 11-applicatie met Inertia.js, Vue 3 en Tailwind CSS. Deze app bevat gebruikersbeheer met soft deletes, avatar uploads (met preview), filtering, sortering en modale vensters.

---

## Systeemvereisten

Zorg ervoor dat de volgende tools geïnstalleerd zijn:

- PHP ^8.2
- Composer
- Node.js + npm (Node >= 18)
- MySQL of een andere database
- Git
- Laravel CLI (`composer global require laravel/installer`)
- Een lokale serveromgeving zoals Laravel Valet, XAMPP of Laravel Sail

---

## Installatiestappen

### 1. Project clonen

```
git clone https://github.com/gebruikersnaam/projectnaam.git
cd projectnaam
```

### 2. Composer dependencies installeren

```
composer install
```

### 3. NPM dependencies installeren

```
npm install
```

### 4. `.env` bestand instellen

```
cp .env.example .env
```

Pas deze `.env`-waarden aan:

```env
APP_NAME="Gebruikersbeheer"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_DATABASE=gebruikersbeheer
DB_USERNAME=root
DB_PASSWORD=
```

### 5. App key genereren

```
php artisan key:generate
```

### 6. Database migraties uitvoeren

```
php artisan migrate
```

### 7. Start de ontwikkelservers

**In één terminal:**

```
php artisan serve
```

**In een tweede terminal:**

```
npm run dev
```

---

## Gebruikte technologieën & libraries

- **Laravel 11**
- **Inertia.js** via [@inertiajs/inertia](https://inertiajs.com/)
- **Vue 3** (`<script setup>`)
- **Tailwind CSS**
- **Heroicons** (`@heroicons/vue`)
- **Vite** als bundler
- **Form uploads** met `FormData`
- **Soft deletes** + force delete + restore

---

## Functionaliteit

- Aanmaken, bewerken en verwijderen van gebruikers
- Soft deletes met prullenbakweergave
- Uploaden van avatars met live preview
- Sorteren & filteren op naam, e-mail, ID
- Paginated gebruikerslijst
- Modalgebaseerde UI voor CRUD-acties

---

## Projectstructuur

```txt
resources/
└── js/
    └── Pages/
        └── Users/
            └── Index.vue      ← bevat alle logica & modals
app/
└── Http/
    └── Controllers/
        └── UserController.php
routes/
└── web.php                   ← routes voor gebruikersbeheer

```


## Licentie

MIT © [Tom Vanhoutte]
"# laravelvuetailwind" 
