<script setup>

/*Hier worden enkele bibliotheken en componenten geïmporteerd die in de component gebruikt worden.
    `@inertiajs/vue3` is een bibliothek voor Vue die het makkelijker maakt om met Laravel te werken.
    `@heroicons/vue/24/solid` is een collectie van iconen die in de component gebruikt kunnen worden.
 */
import { Head, useForm, router, Link } from '@inertiajs/vue3'
import {
    UserIcon,
    PencilSquareIcon,
    PlusIcon,
    XMarkIcon,
    TrashIcon
} from '@heroicons/vue/24/solid'
import { ref, watch } from 'vue'

/*
    Hier worden de props gedefinieerd die de component kan ontvangen van zijn ouder-component.
    Props zijn variabelen die van buitenaf aan de component doorgegeven worden.
    In dit geval zijn er drie props: `users`, `showTrashed` en `filters`.
  */
const props = defineProps({
    users: Object,
    showTrashed: Boolean,
    filters: Object
})
/*
   Hier worden enkele variabelen gedefinieerd die in de component gebruikt worden.
   `showCreateModal`, `showEditModal` en `showDeleteModal` zijn allemaal boolean-variabelen die aangeven of een bepaalde modal zichtbaar moet zijn.
   `editingUser` en `deletingUser` zijn objecten die de gebruiker bevatten die op dat moment wordt bewerkt of verwijderd.
 */
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)

const editingUser = ref(null)
const deletingUser = ref(null)

/*Hier wordt een formulierobject gemaakt met behulp van de useForm-functie uit de @inertiajs/vue3-bibliotheek.
Het formulierobject heeft drie eigenschappen: name, email en password, die de velden van het formulier voorstellen.*/
const form = useForm({
    name: '',
    email: '',
    password: ''
})

/*
Deze regel code maakt een lege formulierinstantie aan met behulp van de useForm-hook uit @inertiajs/vue3.
De formulierinstantie wordt opgeslagen in de variabele deleteForm.
In de context van resources/js/Pages/Users/Index.vue wordt dit formulier waarschijnlijk gebruikt om een gebruiker te verwijderen, zoals de naam deleteForm suggereert.
*/
const deleteForm = useForm({})

/*
Deze regels code initialiseren drie variabelen met behulp van de ref-functie uit Vue.
De variabelen zijn:
- search: de zoekterm, die standaard leeg is ('') of de waarde van props.filters.search als die bestaat.
- sort: de sorteersleutel, die standaard 'id' is of de waarde van props.filters.sort als die bestaat.
- direction: de sorteerrichting, die standaard 'asc' is of de waarde van props.filters.direction als die bestaat.
Deze variabelen worden waarschijnlijk gebruikt om data te filteren en sorteren in een tabel of lijst.
*/
const search = ref(props.filters?.search || '')
const sort = ref(props.filters?.sort || 'id')
const direction = ref(props.filters?.direction || 'asc')

/*
Deze regel code definieert een watcher voor de 'search' variabele.
Wanneer de waarde van 'search' verandert, wordt de router gebruikt om een GET-verzoek te doen naar '/users' met de volgende parameters:
- search: de nieuwe waarde van de 'search' variabele
- trashed: de waarde van props.showTrashed
- sort: de waarde van de 'sort' variabele
- direction: de waarde van de 'direction' variabele
De opties { preserveState: true, replace: true } zorgen ervoor dat de huidige staat van de router wordt behouden en dat de URL in de browser wordt vervangen.
Dit watcher wordt waarschijnlijk gebruikt om de gebruikerslijst te filteren op basis van de ingevoerde zoekterm.
*/
watch(search, (value) => {
    router.get('/users', { search: value, trashed: props.showTrashed, sort: sort.value, direction: direction.value }, { preserveState: true, replace: true })
})


/*
Deze `sortBy` functie sorteert een lijst op basis van een bepaald veld.
Als het veld al gesorteerd is, wordt de sorteerrichting omgedraaid.
Als een ander veld wordt geselecteerd, wordt de sorteerrichting oplopend ingesteld.
Vervolgens wordt een GET-verzoek verstuurd naar '/users' met de bijgewerkte sorteeropties,
waarbij de huidige staat wordt behouden en de URL in de browser wordt vervangen.
*/
function sortBy(field) {

    if (sort.value === field) {
        direction.value = direction.value === 'asc' ? 'desc' : 'asc'
    } else {
        sort.value = field
        direction.value = 'asc'
    }
    router.get('/users', { search: search.value, trashed: props.showTrashed, sort: sort.value, direction: direction.value }, { preserveState: true, replace: true })
}

/*
Deze `openCreate` functie opent een modal venster voor het aanmaken van een nieuw item.
Het formulier wordt gereset en de modal wordt zichtbaar gemaakt.
*/
function openCreate() {

    form.reset()
    showCreateModal.value = true
}

/*
Deze `openEdit` functie opent een modal venster voor het bewerken van een bestaand gebruiker.
De huidige gebruiker wordt gekopieerd naar de `editingUser` variabele.
Het formulier wordt voorbereid met de naam, email en een leeg wachtwoord.
De modal wordt vervolgens zichtbaar gemaakt.
*/
function openEdit(user) {
    editingUser.value = { ...user }
    form.name = user.name
    form.email = user.email
    form.password = ''
    showEditModal.value = true
}

/*
Deze `openDelete` functie opent een modal venster voor het verwijderen van een gebruiker.
De te verwijderen gebruiker wordt opgeslagen in de `deletingUser` variabele.
De modal wordt vervolgens zichtbaar gemaakt.
*/
function openDelete(user) {
    deletingUser.value = user
    showDeleteModal.value = true
}

/*
Deze `submit` functie verzendt het formulier naar de server om een nieuwe gebruiker aan te maken.
Het formulier wordt verstuurd als form data.
Bij succes wordt het formulier gereset en de aanmaak-modal wordt gesloten.
*/
function submit() {
    form.post('/users', {
        forceFormData: true,
        onSuccess: () => {
            form.reset()
            showCreateModal.value = false
        },
    })
}

/*
Deze `update` functie verzendt het formulier naar de server om een bestaande gebruiker te bewerken.
Het formulier wordt verstuurd als een PUT-verzoek naar de URL van de te bewerken gebruiker.
Bij succes wordt het formulier gereset, de bewerk-modal wordt gesloten en de `editingUser` variabele wordt gereset.
*/
function update() {

    form.put(`/users/${editingUser.value.id}`, {
        onSuccess: () => {
            form.reset()
            showEditModal.value = false
            editingUser.value = null
        },
    })
}

/*
Deze `destroy` functie verzendt een DELETE-verzoek naar de server om een gebruiker te verwijderen.
Het verzoek wordt verstuurd naar de URL van de te verwijderen gebruiker.
Bij succes wordt de verwijder-modal gesloten en de `deletingUser` variabele wordt gereset.
*/
function destroy() {
    deleteForm.delete(`/users/${deletingUser.value.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false
            deletingUser.value = null
        }
    })
}

function restore(user) {
    router.put(`/users/${user.id}/restore`)
}

function forceDelete(user) {
    router.delete(`/users/${user.id}/force`)
}

/*
Deze `toggleTrashView` functie wisselt tussen het weergeven van normale gebruikers en verwijderde gebruikers.
Het verzoek wordt verstuurd naar de server om de gebruikerslijst op te halen met de omgekeerde waarde van `showTrashed`.
De huidige scrollpositie wordt behouden.
*/
function toggleTrashView() {

    router.get('/users', { trashed: !props.showTrashed, search: search.value, sort: sort.value, direction: direction.value }, { preserveScroll: true })
}
</script>

<template>
    <Head title="Gebruikers" />

    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-2">
                <UserIcon class="w-7 h-7 text-indigo-600" />
                <h1 class="text-3xl font-bold text-gray-800">Gebruikers</h1>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Zoeken..."
                        class="border rounded px-3 py-2 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 pr-10"
                    />
                    <button
                        v-if="search"
                        @click="search = ''"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        title="Wis zoekopdracht"
                    >
                        <XMarkIcon class="w-4 h-4" />
                    </button>
                </div>
                <button
                    @click="toggleTrashView"
                    :class="[
            'inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition',
            props.showTrashed
              ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              : 'bg-white border text-gray-600 hover:bg-gray-50'
          ]"
                >
                    <TrashIcon class="w-4 h-4" />
                    <span v-if="!props.showTrashed">Prullenbak</span>
                    <span v-else>Toon actieve</span>
                </button>
                <button
                    @click="openCreate"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition font-medium"
                >
                    <PlusIcon class="w-5 h-5" />
                    Nieuwe gebruiker
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                <tr>
                    <th @click="sortBy('id')" class="px-6 py-4 text-left cursor-pointer">
                        ID
                        <span v-if="sort === 'id'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th @click="sortBy('name')" class="px-6 py-4 text-left cursor-pointer">
                        Naam
                        <span v-if="sort === 'name'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th @click="sortBy('email')" class="px-6 py-4 text-left cursor-pointer">
                        Email
                        <span v-if="sort === 'email'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th class="px-6 py-4 text-left">Acties</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                <tr
                    v-for="user in props.users.data"
                    :key="user.id"
                    :class="user.deleted_at ? 'text-gray-400 bg-gray-50 italic' : 'hover:bg-gray-50 transition'"
                >
                    <td class="px-6 py-3">{{ user.id }}</td>
                    <td class="px-6 py-3 font-medium">{{ user.name }}</td>
                    <td class="px-6 py-3">{{ user.email }}</td>
                    <td class="px-6 py-3 flex gap-3">
                        <template v-if="user.deleted_at">
                            <button @click="restore(user)" class="text-indigo-600 hover:underline text-sm">Herstellen</button>
                            <button @click="forceDelete(user)" class="text-red-600 hover:underline text-sm">Definitief verwijderen</button>
                        </template>
                        <template v-else>
                            <button
                                @click="openEdit(user)"
                                class="text-indigo-600 hover:underline flex items-center gap-1"
                            >
                                <PencilSquareIcon class="w-4 h-4" />
                                Bewerken
                            </button>
                            <button
                                @click="openDelete(user)"
                                class="text-red-600 hover:underline text-sm"
                            >
                                Verwijder
                            </button>
                        </template>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div v-if="props.users.links.length > 3" class="flex justify-end gap-2 mt-4 text-sm">
            <template v-for="(link, i) in props.users.links" :key="i">
                <component
                    :is="link.url ? Link : 'span'"
                    :href="link.url"
                    class="px-3 py-1 rounded border"
                    :class="{
            'bg-indigo-600 text-white border-indigo-600': link.active,
            'text-gray-600 hover:bg-gray-100': !link.active && link.url,
            'text-gray-400': !link.url
          }"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>

    <!-- Modals -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">
            <button @click="showCreateModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                <XMarkIcon class="w-5 h-5" />
            </button>
            <h2 class="text-xl font-bold mb-4">Nieuwe gebruiker</h2>
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Naam</label>
                    <input v-model="form.name" type="text" class="w-full rounded border-gray-300 focus:ring-indigo-500" />
                    <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium">E-mail</label>
                    <input v-model="form.email" type="email" class="w-full rounded border-gray-300 focus:ring-indigo-500" />
                    <p v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium">Wachtwoord</label>
                    <input v-model="form.password" type="password" class="w-full rounded border-gray-300 focus:ring-indigo-500" />
                    <p v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</p>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showCreateModal = false" class="text-gray-600 hover:underline">Annuleer</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded" :disabled="form.processing">Opslaan</button>
                </div>
            </form>
        </div>
    </div>

    <div v-if="showEditModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">
            <button @click="showEditModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                <XMarkIcon class="w-5 h-5" />
            </button>
            <h2 class="text-xl font-bold mb-4">Gebruiker bewerken</h2>
            <form @submit.prevent="update" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Naam</label>
                    <input v-model="form.name" type="text" class="w-full rounded border-gray-300 focus:ring-indigo-500" />
                    <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium">E-mail</label>
                    <input v-model="form.email" type="email" class="w-full rounded border-gray-300 focus:ring-indigo-500" />
                    <p v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium">Wachtwoord <span class="text-sm text-gray-400">(optioneel)</span></label>
                    <input v-model="form.password" type="password" class="w-full rounded border-gray-300 focus:ring-indigo-500" />
                    <p v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</p>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showEditModal = false" class="text-gray-600 hover:underline">Annuleer</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded" :disabled="form.processing">Bijwerken</button>
                </div>
            </form>
        </div>
    </div>

    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">
            <button @click="showDeleteModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                <XMarkIcon class="w-5 h-5" />
            </button>
            <h2 class="text-xl font-bold mb-4 text-red-700">Gebruiker verwijderen</h2>
            <p class="mb-4 text-gray-700">
                Weet je zeker dat je <strong>{{ deletingUser?.name }}</strong> wilt verwijderen?
                Deze actie kan niet ongedaan gemaakt worden.
            </p>
            <div class="flex justify-end gap-2">
                <button @click="showDeleteModal = false" class="text-gray-600 hover:underline">Annuleer</button>
                <button @click="destroy" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded" :disabled="deleteForm.processing">
                    Verwijderen
                </button>
            </div>
        </div>
    </div>
</template>
