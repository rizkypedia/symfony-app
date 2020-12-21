<template>
    <div>
        <div class="row">
            <div class="col-12">
                <h1>
                    Products
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-6 mb-2 pb-2">
                TODO - load some products!
            </div>
        </div>
        <div class="row">
            <legend-component :title="legend" />
        </div>
    </div>
</template>

<script>
    import LegendComponent from '@/components/legend'
    import axios from 'axios';
    export default {
        name: 'Catalog',
        components: {LegendComponent},
        data: () => ({
            legend: 'Shipping takes 10-12 weeks, and products probably won\'t work'
        }),
        mounted() {
            axios.get('/index.php/show').then((response) => {
               console.log(response.data)
            });
        }
    }
</script>