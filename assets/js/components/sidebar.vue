<template>
    <div v-show="!collapsed">
        <h5 class="text-center">
            Categories
        </h5>
        <ul class="nav flex-column mb4">
            <li v-for="category in categories" class="nav-item">
                <a :href="category.link" class="nav-link">
                    {{category.name}}
                </a>
            </li>
        </ul>
        <hr />
        <button class="btn btn-secondary btn-sm" v-text="collapsed ? '>>' : '<< Collapse'" @click="toggleCollapsed">
        </button>
    </div>
</template>
<script>
    export default {
        name: 'Sidebar',
        data() {
            return {
                categories: [
                    {
                        name: 'Dot Matrix Printers',
                        link: '#',
                    },
                    {
                        name: 'Iomega Zip Drives',
                        link: '#',
                    },
                ],
                collapsed: false,
            };
        },
        created() {
            console.log(this)
        },
        methods: {
            toggleCollapsed() {
                this.collapsed = !this.collapsed;
            }
        }
    };
</script>
