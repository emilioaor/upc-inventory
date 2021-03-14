<template>
    <div class="container">
        <form class="techland-form" @submit.prevent="validateForm()">
            <div class="card">
                <div class="card-header">
                    <div v-if="editData">
                        <i class="fa fa-edit"></i> {{ t('form.edit') }} {{ t('menu.productSerials') }}
                    </div>
                    <div v-else>
                        <i class="fa fa-plus"></i> {{ t('form.add') }} {{ t('menu.productSerials') }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-4 form-group">
                            <label>{{ t('validation.attributes.product') }}</label>
                            <search-input
                                :route="'/warehouse/product'"
                                :description-fields="['name', 'upc', 'sku']"
                                @selectResult="changeProduct($event)"
                                :read-only="false"
                                :value="product ? product.name : ''"
                            ></search-input>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group" v-if="product">
                            <label>{{ t('form.addSerial') }}</label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="{'is-invalid': newSerial.error}"
                                    v-model="newSerial.serial"
                                >
                                <span class="input-group-btn">
                                    <button
                                        id="addSerial"
                                        class="btn btn-success"
                                        :disabled="newSerial.loading"
                                        @click="addSerial()"
                                        @keyup.13="addSerial()"
                                    >
                                        <i v-if="newSerial.loading" class="spinner-border spinner-border-sm"></i>
                                        <i v-else class="fa fa-plus"></i>
                                    </button>
                                </span>

                                <span class="invalid-feedback d-block" role="alert" v-if="newSerial.error">
                                    <strong>
                                        {{ t('validation.unique', {attribute: 'serial'}) }}
                                        ({{ newSerial.error.product.name }})
                                    </strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-sm-6 col-md-3 mb-3" v-if="product">
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-outline-default">
                                        <i class="fa fa-filter"></i>
                                    </button>
                                </div>
                                <input
                                    type="text"
                                    class="form-control"
                                    :placeholder="t('form.filter')"
                                    v-model="filter"
                                >
                            </div>
                        </div>

                        <div class="col-12">
                            <i v-if="loading" class="spinner-border spinner-border-sm"></i>

                            <table v-else-if="product" class="table">
                                <thead>
                                    <tr>
                                        <th width="50%">{{ t('validation.attributes.serial') }}</th>
                                        <th>{{ t('validation.attributes.createdBy') }}</th>
                                        <th>{{ t('validation.attributes.createdAt') }}</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="productSerial in productSerialsFiltered">
                                        <td>{{ productSerial.serial }}</td>
                                        <td>{{ productSerial.user.name }}</td>
                                        <td>{{ productSerial.created_at |date(true) }}</td>
                                        <td>
                                            <i v-if="loadingDelete === productSerial.id" class="spinner-border spinner-border-sm"></i>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger"
                                                v-else-if="user.role === 'administrator' || productSerial.user_id === user.id"
                                                :disalbed="newSerial.loading"
                                                @click="deleteSerial(productSerial)"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</template>

<script>
    import ApiService from "../services/ApiService";

    export default {
        props: {
            editData: {
                type: Object,
                required: false
            },
            user: {
                type: Object,
                required: true
            }
        },

        mounted() {
            if (this.editData) {
                this.changeProduct(this.editData);
            }
        },

        data() {
            return {
                loading: false,
                exists: false,
                form: {
                    product_serials: []
                },
                product: null,
                newSerial: {
                    serial: null,
                    loading: null,
                    product_id: null,
                    error: null
                },
                loadingDelete: null,
                filter: null
            }
        },

        methods: {
            changeProduct(product) {
                this.product = product;
                this.form.product_serials = [];
                this.newSerial.product_id = product.id;
                this.newSerial.serial = null;
                this.getProductSerials();
            },

            getProductSerials() {
                this.loading = true;
                this.getSerials();
            },

            getSerials() {
                return ApiService.get('/warehouse/product-serial/' + this.product.uuid + '/product')
                    .then(res => {

                        this.form.product_serials = res.data.data;
                        this.loading = false;
                    })
                    .catch(err => {
                        this.loading = false;
                    })
            },

            addSerial() {
                if (this.newSerial.serial) {

                    this.newSerial.loading = true;

                    ApiService.post('/warehouse/product-serial', {
                        serial: this.newSerial.serial,
                        product_id: this.newSerial.product_id
                    }).then(res => {

                        this.getSerials().then(res => {
                            this.newSerial.serial = null;
                            this.newSerial.loading = false;
                            document.querySelector('#addSerial').focus();
                        });

                    }).catch(err => {
                        this.newSerial.loading = false;

                        if (err.response.data.err === 'serial_exists') {
                            this.newSerial.error = err.response.data.data;
                        }
                    })
                }
            },

            deleteSerial(productSerial) {
                this.loadingDelete = productSerial.id;

                ApiService.delete('/warehouse/product-serial/' + productSerial.uuid).then(res => {

                    this.getSerials().then(res => {
                        this.loadingDelete = null;
                    });

                }).catch(err => {
                    this.loadingDelete = null;
                })
            }
        },

        computed: {
            productSerialsFiltered() {
                if (! this.filter) {
                    return this.form.product_serials;
                }

                return this.form.product_serials.filter(ps => ps.serial.indexOf(this.filter) >= 0)
            }
        },

        watch: {
            "newSerial.serial"() {
                this.newSerial.error = null;
            }
        }
    }
</script>
