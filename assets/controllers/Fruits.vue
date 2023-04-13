<template>
    <div>
        Filters: <input v-model="name" type="text" placeholder="Filter by Name">
        <input v-model="family" type="text" placeholder="Filter by Family">
        <div v-if="loading">Loading...</div>
        <div v-else>
                <div class="divTable">
                    <div class="divTableBody">
                        <div class="divTableRow divTableFirstRow">
                            <div class="divTableCell white">Name</div>
                            <div class="divTableCell white">Family</div>
                            <div class="divTableCell white">Order</div>
                            <div class="divTableCell white">Genus</div>
                            <div class="divTableCell white">Calories</div>
                            <div class="divTableCell white">Fat</div>
                            <div class="divTableCell white">Sugar</div>
                            <div class="divTableCell white">Carbohydrates</div>
                            <div class="divTableCell white">Protein</div>
                            <div class="divTableCell white"></div>
                        </div>
                        <div class="divTableRow odd" v-for="fruit in filteredFruits" :key="fruit.id">
                            <div class="divTableCell white">{{ fruit.name }}</div>
                            <div class="divTableCell white">{{ fruit.family }}</div>
                            <div class="divTableCell white">{{ fruit.order }}</div>
                            <div class="divTableCell white">{{ fruit.genus }}</div>
                            <div class="divTableCell white">{{ fruit.nutritions.calories }}</div>
                            <div class="divTableCell white">{{ fruit.nutritions.fat }}</div>
                            <div class="divTableCell white">{{ fruit.nutritions.sugar }}</div>
                            <div class="divTableCell white">{{ fruit.nutritions.carbohydrates }}</div>
                            <div class="divTableCell white">{{ fruit.nutritions.protein }}</div>
                            <div class="divTableCell white"><button @click="addToFavorites(fruit)" :disabled="favorites.length >= 10 || isFavorite(fruit.id)" class="fav-btn">Add to favorites</button></div>
                        </div>
                    </div>

                </div>
            </div>
        <div v-if="totalPages > 1">
            Paging
            <ul>
                <li v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }">
                    <a href="#" @click.prevent="fetchFruits(page)">{{ page }}</a>
                </li>
            </ul>
        </div>
    </div>

</template>

<script>

export default {
    props: {
        url: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            fruits: [],
            favorites: [],
            currentPage: 1,
            totalPages: 0,
            loading: false,
            name: '',
            family: '',
        }
    },
    methods: {
        async fetchFruits(page) {
            this.loading = true;

            const queryParams = new URLSearchParams({
                page: page,
                name: this.name,
                family: this.family
            });

            const response = await fetch(`${this.url}?${queryParams.toString()}`);
            const data = await response.json();

            this.fruits = data.fruits;
            this.totalPages = data.totalPages;
            this.currentPage = data.page;

            this.loading = false;
            return this.fruits;
        },
        async addToFavorites(fruit) {
            const response = await fetch(`/add-favorite/${fruit.id}`, { method: 'POST' });
            const data = await response.json()
            if (data.message) {
                alert(data.message)
            }
        },
        isFavorite(fruit) {
            return this.favorites.includes(fruit.id);
        }
    },
    computed: {
        filteredFruits() {
            if (!this.name && !this.family) {
                return this.fruits;
            }
            const nameRegex = new RegExp(this.name, 'i');
            const familyRegex = new RegExp(this.family, 'i');
            return this.fruits.filter((fruit) => {
                return nameRegex.test(fruit.name) && familyRegex.test(fruit.family);
            });
        },
    },
    watch: {
        name: function(newName, oldName) {
            if (newName !== oldName) {
                this.fetchFruits(this.currentPage);
            }
        },
        family: function(newFamily, oldFamily) {
            if (newFamily !== oldFamily) {
                this.fetchFruits(this.currentPage);
            }
        },
    },
    mounted() {
        this.fetchFruits(this.currentPage);
    }
};
</script>

<style>
.active {
    font-weight: bold;
}
</style>