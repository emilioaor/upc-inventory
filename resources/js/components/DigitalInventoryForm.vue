<template>
    <div class="container">
        <form class="techland-form" @submit.prevent="validateForm()">
            <div class="card">
                <div class="card-header">
                    <div v-if="editData">
                        <i class="fa fa-edit"></i> {{ t('form.edit') }} {{ t('menu.digitalInventory') }}
                    </div>
                    <div v-else>
                        <i class="fa fa-plus"></i> {{ t('form.add') }} {{ t('menu.digitalInventory') }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-lg-4 form-group">
                            <label for="description">{{ t('validation.attributes.description') }}</label>
                            <input
                                type="text"
                                id="description"
                                name="description"
                                class="form-control"
                                :class="{'is-invalid': errors.has('description')}"
                                v-model="form.description"
                                v-validate
                                data-vv-rules="required"
                                :disabled="editData"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('description', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'description'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-lg-4 form-group" v-if="! editData">
                            <label for="file">{{ t('validation.attributes.file') }}</label>
                            <input
                                    type="file"
                                    id="file"
                                    name="file"
                                    class="form-control"
                                    :class="{'is-invalid': errors.has('file')}"
                                    v-validate
                                    data-vv-rules="required|mimes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                    @change="changeFile()"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('file', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'file'}) }}</strong>
                            </span>

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('file', 'mimes')">
                                <strong>{{ t('validation.mimes', {attribute: 'file', values: 'Excel'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-lg-4 form-group" v-if="editData">
                            <label>{{ t('validation.attributes.createdBy') }}</label>
                            <input
                                    type="text"
                                    class="form-control"
                                    :value="form.user ? form.user.name : ''"
                                    :disabled="true"
                            >
                        </div>

                        <div class="col-sm-6 col-lg-4 form-group" v-if="editData">
                            <label>{{ t('validation.attributes.createdAt') }}</label>
                            <input
                                    type="text"
                                    class="form-control"
                                    :value="form.created_at |date(true)"
                                    :disabled="true"
                            >
                        </div>

                        <div class="col-sm-6 col-lg-4 form-group" v-if="crossoverSelect">
                            <label>{{ t('validation.attributes.inventory_crossover_enabled') }}</label>
                            <div>
                                <div>
                                    <div
                                            class="confirmation-checkbox text-white d-flex justify-content-start"
                                            @click="form.inventory_crossover_enabled = ! form.inventory_crossover_enabled"
                                            :class="{
                                            'justify-content-end': form.inventory_crossover_enabled
                                        }"
                                    >
                                        <div v-if="form.inventory_crossover_enabled" class="bg-success ">
                                            {{ t('form.yes') }}
                                        </div>
                                        <div v-else class="bg-danger">
                                            {{ t('form.no') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-4 form-group" v-else>
                            <button
                                    type="button"
                                    class="btn btn-success"
                                    data-toggle="modal"
                                    data-target="#modalInventory"
                                    @click="startInventoryCrossover()"
                            >
                                <i class="fa fa-barcode"></i>
                                {{ t('form.startInventoryCrossover') }}
                            </button>
                        </div>

                        <div class="col-sm-6 col-lg-4 form-group" v-if="! editData">
                            <label>{{ t('form.template') }}</label>

                            <div>
                                <a href="/excel/digital_inventory_template.xlsx" download>
                                    <i class="fa fa-download"></i>
                                    {{ t('form.download') }}
                                </a>
                            </div>
                        </div>

                    </div>

                    <div class="card mt-3" v-if="editData">
                        <div class="card-header bg-secondary">
                            {{ t('form.inventory') }}
                        </div>
                        <div class="card-body">

                            <table class="table table-responsive">
                                <thead>
                                    <tr>
                                        <th>{{ t('validation.attributes.product') }}</th>
                                        <th>{{ t('validation.attributes.upc') }}</th>
                                        <th>{{ t('validation.attributes.serial') }}</th>
                                        <th width="1%" class="text-center">{{ t('validation.attributes.digital') }}</th>
                                        <th width="1%" class="text-center">{{ t('validation.attributes.physical') }}</th>
                                        <th width="1%" class="text-center">{{ t('validation.attributes.diff') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="product in inventoryUnified">
                                        <td>{{ product.name }}</td>
                                        <td>{{ product.upc }}</td>
                                        <td>{{ product.serial }}</td>
                                        <td class="text-center">{{ product.digital }}</td>
                                        <td class="text-center">{{ product.physical }}</td>
                                        <td class="text-center">{{ product.physical - product.digital }}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <div class="card-footer" v-if="crossoverSelect">
                    <button v-if="!loading" class="btn btn-success">
                        <i class="fa fa-save"></i>
                        {{ t('form.save') }}
                    </button>

                    <i v-if="loading" class="spinner-border spinner-border-sm"></i>
                </div>
            </div>
        </form>

        <!-- Modal -->
        <button
                type="button"
                class="d-none"
                id="openModalErrors"
                data-toggle="modal"
                data-target="#modalErrors"
        ></button>

        <div class="modal fade" id="modalErrors" tabindex="-1" role="dialog" aria-labelledby="modalErrors" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">

                        <h5 class="bg-danger text-white p-2 rounded">
                            {{ t('validation.regex', {'attribute': 'file'}) }}
                            <strong>{{ t('form.line') }}:</strong> {{ modal.line }}
                        </h5>

                        <div class="pt-2">
                            <div>
                                <strong>{{ t('form.errors') }}:</strong>
                            </div>
                            <ul>
                                <li v-for="error in modal.errors">{{ error }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ t('form.close') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalInventory" tabindex="-1" role="dialog" aria-labelledby="modalInventory" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header pb-0 pt-2">
                        <div></div>
                        <div>
                            <button class="btn btn-link" type="button" data-dismiss="modal">X</button>
                        </div>
                    </div>
                    <div class="modal-body">


                        <div class="row">
                            <div class="col-5">
                                <label>
                                    {{ t('validation.attributes.upc') }}
                                    <i class="fa fa-barcode"></i>
                                </label>
                                <div class="input-group mb-3">
                                    <input
                                        type="text"
                                        id="newUPC"
                                        class="form-control"
                                        :class="{'is-invalid': newUPC.error}"
                                        v-model="newUPC.upc"
                                        @keyup.13="addProduct()"
                                        autocomplete="off"
                                    >
                                    <div class="input-group-append">
                                        <button
                                            class="btn btn-success"
                                            type="button"
                                            :disabled="newUPC.loading"
                                            @click="addProduct()"
                                        >

                                            <i class="spinner-border spinner-border-sm" v-if="newUPC.loading"></i>
                                            <i class="fa fa-plus" v-else></i>
                                        </button>
                                    </div>

                                    <span class="invalid-feedback d-block" role="alert" v-if="newUPC.error">
                                        <strong>{{ t('form.upcNotExists') }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="upc-table-container">
                            <table class="table table-striped table-responsive" v-if="newUPC.physical_inventory_movements.length">
                                <thead>
                                <tr>
                                    <th width="1%">{{ t('validation.attributes.upc') }}</th>
                                    <th>{{ t('validation.attributes.product') }}</th>
                                    <th width="15%" class="text-center">{{ t('validation.attributes.qty') }}</th>
                                    <th width="1%"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(movement, i) in newUPC.physical_inventory_movements">
                                    <td>{{ movement.product.upc }}</td>
                                    <td>{{ movement.product.name }}</td>
                                    <td class="text-center">

                                        <input
                                            type="number"
                                            :id="'movement-product-' + i"
                                            class="form-control"
                                            v-model="movement.qty"
                                            @blur="changeQTY(i)"
                                            :disabled="newUPC.loadingInventory === movement.id"
                                        >
                                    </td>
                                    <td>
                                        <i v-if="newUPC.loadingInventory === movement.id" class="spinner-border spinner-border-sm"></i>

                                        <button
                                            v-else
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            @click="deleteMovement(i)"
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

            crossoverSelect: {
                type: Boolean,
                required: false,
                default: false
            },

            user: {
                type: Object,
                required: true
            }
        },

        mounted() {
            if (this.editData) {
                this.form = {...this.editData};

                this.newUPC.physical_inventory_movements = [
                    ...this.form.physical_inventory_movements.filter(m => m.user_id === this.user.id)
                ];
            }
        },

        created() {
            if (this.editData) {

                Echo.join(`digital-inventory-${this.editData.id}`)
                    .here((users) => {
                        // console.log(users);
                    })
                    .joining((user) => {
                        // console.log(user.name);
                    })
                    .leaving((user) => {
                        // console.log(user.name);
                    })
                    .listen('DigitalInventoryUpdated', (event) => {
                        this.form.physical_inventory_movements = event.physicalInventoryMovements;
                    });

            }
        },

        data() {
            return {
                loading: false,
                exists: false,
                form: {
                    description: null,
                    file: null,
                    digital_inventory_movements: [],
                    physical_inventory_movements: [],
                    inventory_crossover_enabled: false
                },
                modal: {
                    errors: [],
                    line: []
                },
                newUPC: {
                    upc: null,
                    loading: false,
                    loadingInventory: null,
                    error: false,
                    physical_inventory_movements: []
                }
            }
        },

        methods: {
            validateForm() {
                this.$validator.validateAll().then(res => res && this.sendForm());
            },

            sendForm() {
                const apiService = this.editData ?
                    ApiService.put('/manager/digital-inventory/' + this.editData.uuid, this.form) :
                    ApiService.post('/manager/digital-inventory', this.form)
                ;

                this.loading = true;

                apiService.then(res => {
                    if (res.data.success) {
                        location.href = res.data.redirect;
                    }
                }).catch(err => {
                    this.loading = false;
                    this.modal = {...err.response.data};
                    document.querySelector('#openModalErrors').click();
                })
            },

            changeFile() {
                const file = $('#file')[0].files[0];

                if (!file) {
                    return false;
                }

                const reader = new FileReader();

                reader.addEventListener('load', () => {
                    if (reader.result) {
                        this.form.file = reader.result;
                    }
                });

                reader.readAsDataURL(file);
            },

            startInventoryCrossover() {
                window.setTimeout(() => {
                    document.querySelector('#newUPC').focus();
                }, 500)
            },

            addProduct() {
                if (this.newUPC.upc) {

                    this.newUPC.loading = true;

                    ApiService.post('/warehouse/inventory', {
                        upc: this.newUPC.upc,
                        digital_inventory_id: this.form.id
                    })
                        .then(res => {

                            if (! res.data.data) {
                                this.newUPC.error = true;
                            } else {
                                this.newUPC.physical_inventory_movements.unshift({
                                    ...res.data.data
                                });
                                this.newUPC.upc = null;
                                document.querySelector('#newUPC').focus();
                            }

                            this.newUPC.loading = false;
                        })
                        .catch(err => {
                            this.newUPC.loading = false;
                        })
                }
            },

            changeQTY(index) {
                const movement = this.newUPC.physical_inventory_movements[index];
                const qty = parseInt(movement.qty);

                if (qty && qty > 0) {

                    this.newUPC.loadingInventory = movement.id;

                    ApiService.put('/warehouse/inventory/' + movement.id, {qty})
                        .then(res => {

                            if (res.data.success) {
                                this.newUPC.loadingInventory = null;
                            }

                        }).catch(err => {
                            this.newUPC.loadingInventory = null;
                        });

                } else {
                    this.newUPC.physical_inventory_movements[index].qty = 1;
                    document.querySelector('#movement-product-' + index).focus();
                }
            },

            deleteMovement(index) {
                const movement = this.newUPC.physical_inventory_movements[index];
                this.newUPC.loadingInventory = movement.id;

                ApiService.delete('/warehouse/inventory/' + movement.id)
                    .then(res => {
                        if (res.data.success) {
                            this.newUPC.loadingInventory = false;
                            this.newUPC.physical_inventory_movements.splice(index, 1);
                        }
                    })
                    .catch(err => {
                        this.newUPC.loadingInventory = false;
                    })
            }
        },

        computed: {
            inventoryUnified() {
                const products = [];

                this.form.digital_inventory_movements.forEach(movement => {

                    const currentProduct = products.find(p => p.id === movement.product_id);

                    if (currentProduct) {
                        currentProduct.digital += parseInt(movement.qty);
                    } else {
                        products.push({
                            ...movement.product,
                            digital: movement.qty,
                            physical: 0
                        })
                    }
                });

                this.form.physical_inventory_movements.forEach(movement => {

                    const currentProduct = products.find(p => p.id === movement.product_id);

                    if (currentProduct) {
                        currentProduct.physical += parseInt(movement.qty);
                    } else {
                        products.push({
                            ...movement.product,
                            digital: 0,
                            physical: movement.qty
                        })
                    }
                });

                return products;
            }
        },

        watch: {
            "newUPC.upc"(old, value) {
                this.newUPC.error = false;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .confirmation-checkbox {
        border: solid 1px #ccc;
        border-radius: 20px;
        cursor: pointer;
        padding: .2rem;
        background-color: #cddd;
        max-width: 100px;

        div {
            padding: .3rem .4rem;
            border-radius: 50%;
        }
    }

    .upc-table-container {
        max-height: 65vh;
        overflow: auto;
        border-top: solid 1px #1A3660;
    }
</style>