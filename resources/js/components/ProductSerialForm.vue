<template>
    <div class="container">
        <div class="techland-form">
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
                            <label>{{ t('validation.attributes.wholesaler') }}</label>
                            <input
                                type="text"
                                name="wholesaler"
                                class="form-control"
                                :class="{'is-invalid': errors.has('wholesaler')}"
                                v-model="form.wholesaler"
                                v-validate
                                data-vv-rules="required"
                            >
                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('wholesaler', 'required')">
                                <strong>
                                    {{ t('validation.required', {attribute: 'wholesaler'}) }}
                                </strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group">
                            <label>{{ t('validation.attributes.invoiceNumber') }}</label>
                            <input
                                    type="text"
                                    name="invoiceNumber"
                                    class="form-control"
                                    :class="{'is-invalid': errors.has('invoiceNumber')}"
                                    v-model="form.invoice_number"
                                    v-validate
                                    data-vv-rules="required"
                            >
                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('invoiceNumber', 'required')">
                                <strong>
                                    {{ t('validation.required', {attribute: 'invoiceNumber'}) }}
                                </strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group" v-if="editData">
                            <label>{{ t('validation.attributes.createdAt') }}</label>
                            <input type="text" class="form-control" :value="form.created_at | date(true)" disabled>
                        </div>
                    </div>

                    <div class="card" v-if="editData">
                        <div class="card-header bg-secondary d-flex pointer" @click="accordion.serial = ! accordion.serial">
                            <div class="col-8">
                                <i class="fa fa-barcode"></i>
                                {{ t('form.serials') }}
                            </div>
                            <div class="text-right col-4">
                                <i class="fa fa-caret-up" v-if="accordion.serial"></i>
                                <i class="fa fa-caret-down" v-else></i>
                            </div>
                        </div>
                        <div class="card-body" v-show="accordion.serial">
                            <div class="row">

                                <div class="col-sm-6 col-lg-5 form-group">
                                    <label>{{ t('validation.attributes.upc_or_sku') }}</label>

                                    <div class="input-group">
                                        <input
                                                type="text"
                                                id="newUPC"
                                                class="form-control"
                                                v-model="newSerial.upc"
                                                @keyup.13="changeUPC()"
                                                @focus="resetProduct()"
                                                autocomplete="off"
                                                :disabled="newSerial.loading"
                                        >

                                        <span class="input-group-btn">
                                            <button
                                                    type="button"
                                                    class="btn btn-secondary"
                                                    :disabled="newSerial.loading"
                                                    @click="changeUPC()"
                                            >
                                                <i class="spinner-border spinner-border-sm" v-if="newSerial.loading"></i>
                                                <i class="fa fa-check" v-else ></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-5 form-group" v-if="newSerial.product">
                                    <label>
                                        {{ t('form.addSerial') }}
                                        <small>({{ newSerial.product.name }})</small>
                                    </label>
                                    <div class="input-group">
                                        <input
                                                type="text"
                                                id="newSerial"
                                                class="form-control"
                                                :class="{'is-invalid': newSerial.error}"
                                                v-model="newSerial.serial"
                                                :disabled="! newSerial.product || newSerial.loading"
                                                @keyup.prevent.13="addSerial()"
                                                autocomplete="off"
                                        >
                                        <span class="input-group-btn">
                                            <button
                                                    type="button"
                                                    id="addSerial"
                                                    class="btn btn-success"
                                                    :disabled="newSerial.loading || ! newSerial.product"
                                                    @click="addSerial()"
                                            >
                                                <i class="spinner-border spinner-border-sm" v-if="newSerial.loading"></i>
                                                <i class="fa fa-plus" v-else ></i>
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

                                <div class="col-12" v-if="form.product_serials.length">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th width="40%">{{ t('validation.attributes.product') }}</th>
                                            <th width="60%">{{ t('validation.attributes.serials') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="product in productSerialsOrdered">
                                                <td>{{ product.name }}</td>
                                                <td>
                                                    <span class="serial" v-for="productSerial in product.product_serials">
                                                        {{ productSerial.serial }}
                                                        <button
                                                            type="button"
                                                            class="btn btn-danger btn-sm"
                                                            @click="deleteSerial(productSerial)"
                                                            :disabled="loadingDelete === productSerial.id"
                                                        >
                                                            <i class="fa fa-remove"></i>
                                                        </button>
                                                    </span>

                                                    <button type="button" class="btn btn-success" @click="addNewSerial(product)">
                                                        <i class="fa fa-plus"></i>
                                                        {{ t('form.add') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <i class="spinner-border spinner-border-sm" v-if="loading"></i>

                    <button class="btn btn-success" v-if="! loading" @click="validateForm()">
                        <i class="fa fa-save"></i>
                        {{ t('form.save') }}
                    </button>
                </div>
            </div>
        </div>
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
                this.form = {
                    ...this.editData
                }
            }
        },

        data() {
            return {
                loading: false,
                exists: false,
                form: {
                    wholesaler: null,
                    invoice_number: null,
                    product_serials: []
                },
                accordion: {
                    serial: true
                },
                newSerial: {
                    product: null,
                    upc: null,
                    serial: null,
                    loading: null,
                    product_id: null,
                    error: null
                },
                loadingDelete: null
            }
        },

        methods: {

            validateForm() {
                return this.$validator.validateAll().then(res => res && this.sendForm());
            },

            sendForm() {
                const {product_serials, ...payload} = this.form;

                const apiService = this.editData ?
                    ApiService.put('/warehouse/product-serial/' + this.form.uuid, payload) :
                    ApiService.post('/warehouse/product-serial', payload)
                ;

                this.loading = true;

                apiService.then(res => {

                    location.href = res.data.redirect;

                }).catch(err => {
                    this.loading = false;
                })
            },

            changeUPC(product) {

                this.newSerial.loading = true;

                ApiService.get('/warehouse/product/' + this.newSerial.upc).then(res => {

                    if (res.data.data) {
                        this.newSerial.product = res.data.data;
                        this.newSerial.product_id = res.data.data.id;
                        this.newSerial.loading = false;
                        window.setTimeout(() => document.querySelector('#newSerial').focus())
                    } else {
                        // TODO agregar producto
                    }

                }).catch(err => {
                    this.newSerial.loading = false;
                });
            },

            addNewSerial(product) {
                this.newSerial.upc = product.upc ? product.upc : product.sku;
                this.newSerial.serial = null;
                this.newSerial.product = product;
                this.newSerial.product_id = product.id;
                window.setTimeout(() =>document.querySelector('#newSerial').focus());
            },

            addSerial() {
                if (this.newSerial.serial) {

                    this.newSerial.loading = true;

                    ApiService.post('/warehouse/product-serial/serial', {
                        serial: this.newSerial.serial,
                        product_id: this.newSerial.product_id,
                        product_serial_group_id: this.form.id
                    }).then(res => {

                        this.form.product_serials.push({
                            ...res.data.data,
                            product: {... this.newSerial.product}
                        });

                        this.newSerial.loading = false;
                        this.newSerial.serial = null;
                        window.setTimeout(() => document.querySelector('#newSerial').focus());

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

                    const index = this.form.product_serials.findIndex(ps => ps.id === productSerial.id);
                    this.form.product_serials.splice(index, 1);
                    this.loadingDelete = null;

                }).catch(err => {
                    this.loadingDelete = null;
                })
            },

            resetProduct() {
                this.newSerial.upc = null;
                this.newSerial.serial = null;
                this.newSerial.product = null;
                this.newSerial.product_id = null;
            }
        },

        computed: {
            serialsByProduct() {
              const products = [];
              let current;

              this.form.product_serials.forEach(serial => {

                  current = products.find(p => p.id === serial.product_id);

                  if (! current) {

                      current = {
                          ... serial.product,
                          product_serials: []
                      };

                      products.push(current)
                  }

                  current.product_serials.push({... serial});
              });

              return products;
            },

            productSerialsOrdered() {
                return this.serialsByProduct.sort((a, b) => {
                    if (this.newSerial.product_id && this.newSerial.product_id === a.id) {
                        return -1;
                    }

                    return 1;
                })
            }
        },

        watch: {
            "newSerial.serial"() {
                this.newSerial.error = null;
            }
        }
    }
</script>

<style lang="scss">
    .serial {
        display: inline-block;
        background-color: #6c757d;
        color: #fff;
        padding: .3rem;
        border-radius: 5px;
        font-size: 12px;
        margin: .3rem;
    }
</style>
