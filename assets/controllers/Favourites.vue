<template>
    <div>
        <div v-if="loading">Loading...</div>
        <div v-else>
                <div class="divTable">
                    <div class="divTableBody">
                        <div class="divTableRow divTableFirstRow">
                            <div class="divTableCell white">Name</div>
                            <div class="divTableCell white">Family</div>
                            <div class="divTableCell white">Order</div>
                            <div class="divTableCell white">Genus</div>
                            <div class="divTableCell white">Nutritions (per 100g) </div>
                        </div>
                        <div class="divTableRow odd" v-for="fruit in favourites">
                            <div class="divTableCell white">{{ fruit.name }}</div>
                            <div class="divTableCell white">{{ fruit.family }}</div>
                            <div class="divTableCell white">{{ fruit.order }}</div>
                            <div class="divTableCell white">{{ fruit.genus }}</div>
                            <div class="divTableCell white">{{ getNutritionSum(fruit) }}</div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</template>
<script>

export default {
    props: {
        favourites: Array
    },
    methods: {
        getNutritionSum(item) {
            return Math.round(
                item.nutritions.calories +
                item.nutritions.fat +
                item.nutritions.sugar +
                item.nutritions.carbohydrates +
                item.nutritions.protein
            );
        }
    }
};
</script>