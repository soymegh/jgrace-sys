<template>


        <b-row>
            <b-col md="1" class="form-inline sm-text-center mt-1">
                <select class="form-control" v-model="currentLimit" @change="changeLimit">
                    <option v-for="limit in limitOptions">{{ limit }}</option>
                </select>
            </b-col>
            <b-col md="6" class="form-inline mt-1 sm-text-center">Mostrando registros del {{ showing.first }} al {{
                showing.last }}. Total de registros: {{ total }}
            </b-col>

            <b-col md="5" class="mt-1">
                <div class="overflow-auto">
                    <ul class="pagination b-pagination-pills">
                        <li class="page-item" v-for="pagination in paginations" :class="{ 'active': pagination.active }"
                            @click="changePage(pagination.value)">
                            <a class="page-link" v-if="pagination.text === 'First'" href="javascript:;">Primera</a>
                            <a class="page-link" v-else-if="pagination.text === 'Prev'" href="javascript:;">Anterior</a>
                            <a class="page-link" v-else-if="pagination.text === 'Next'"
                               href="javascript:;">Siguiente</a>
                            <a class="page-link" v-else-if="pagination.text === 'Last'" href="javascript:;">Última</a>
                            <a class="page-link" v-else href="javascript:;">{{ pagination.text }}</a>
                        </li>
                    </ul>
                </div>
            </b-col>
        </b-row>

</template>

<script type="text/ecmascript-6">
    import {BFormDatepicker, BRow, BCol, BCard, BCardFooter} from 'bootstrap-vue'

    export default {
        components: {
            BRow,
            BCol,
            BCard,
            BCardFooter,
        },
        data() {
            return {
                currentLimit: 0,
                paginations: [],
                showing: {
                    first: '',
                    last: ''
                }
            }
        },
        props: ['items', 'total', 'page', 'limitOptions', 'limit'],
        watch: {
            items() {
                this.pagination()
            }
        },
        methods: {
            pagination() {
                var totalPage = Math.ceil(this.total / this.limit)

                // Pagination info
                this.showing.first = (this.page * this.limit) - this.limit + 1
                this.showing.last = this.showing.first + this.items.length - 1

                // Reset pagination
                this.paginations = []

                // First
                if (this.page > 1) {
                    this.paginations.push({active: false, text: 'First', value: 1})
                } else {
                    this.paginations.push({active: false, text: 'First', value: 0})
                }

                // Prev
                if (this.page > 1) {
                    this.paginations.push({active: false, text: 'Prev', value: this.page - 1})
                } else {
                    this.paginations.push({active: false, text: 'Prev', value: 0})
                }

                // Page before
                var start
                if (this.page - 3 <= 0) {
                    start = 1
                } else {
                    var append = 0
                    if (this.page + 3 > totalPage) {
                        var margin = (this.page + 3) - totalPage
                        if (this.page - 3 - margin > 0) {
                            append = margin
                        } else {
                            append = (this.page - 1) - 3
                        }
                    } else {
                        append = 0
                    }

                    start = this.page - 3 - append
                }

                for (var i = start; i < this.page; i++) {
                    this.paginations.push({active: false, text: i, value: i})
                }

                // Current page
                this.paginations.push({active: true, text: this.page, value: 0})

                // Page after
                var end
                if (this.page + 3 > totalPage) {
                    end = totalPage
                } else {
                    var append = 0
                    if (this.page - 3 <= 0) {
                        var margin = 3 - (this.page - 1)
                        if (this.page + 3 + margin > totalPage) {
                            append = totalPage - this.page - 3
                        } else {
                            append = margin
                        }
                    } else {
                        append = 0
                    }

                    end = this.page + 3 + append
                }

                for (var i = this.page + 1; i <= end; i++) {
                    this.paginations.push({active: false, text: i, value: i})
                }

                // Next
                if (this.page < totalPage) {
                    this.paginations.push({active: false, text: 'Next', value: this.page + 1})
                } else {
                    this.paginations.push({active: false, text: 'Next', value: 0})
                }

                // Last
                if (this.page < totalPage) {
                    this.paginations.push({active: false, text: 'Last', value: totalPage})
                } else {
                    this.paginations.push({active: false, text: 'Last', value: 0})
                }
            },
            changePage(page) {
                if (page) {
                    this.$emit('changePage', page)
                }
            },
            changeLimit() {
                this.$emit('changeLimit', this.currentLimit)
            }
        },
        mounted() {
            this.currentLimit = this.limit
        }
    }
</script>

<style lang="scss" scoped>
    @import "../../scss/vue/libs/vue-good-table.scss";

</style>
