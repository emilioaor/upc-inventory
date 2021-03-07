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

                    <div class="card" v-if="editData">
                        <div class="card-header d-flex bg-secondary pointer" @click="accordion.observation = !accordion.observation">
                            <div class="col-8">
                                <i class="fa fa-comment"></i>
                                {{ t('form.observations') }}
                            </div>
                            <div class="text-right col-4">
                                <i class="fa fa-caret-up" v-if="accordion.observation"></i>
                                <i class="fa fa-caret-down" v-else></i>
                            </div>
                        </div>
                        <div class="card-body" v-show="accordion.observation">

                            <label for="observation">{{ t('validation.attributes.observation') }}</label>
                            <div class="form-group">
                                <textarea
                                        name="observation"
                                        id="observation"
                                        class="form-control"
                                        cols="30"
                                        rows="3"
                                        v-model="newObservation.content"
                                ></textarea>
                            </div>

                            <i v-if="newObservation.loading" class="spinner-border spinner-border-sm"></i>
                            <button v-else type="button" class="btn btn-success" @click="addObservation()">
                                <i class="fa fa-save"></i>
                                {{ t('form.save') }}
                            </button>

                            <div v-for="observation in form.digital_inventory_observations">
                                <hr>
                                <div>
                                    <small>
                                        <strong>{{ observation.created_at | date(true) }}</strong> -
                                        {{ observation.user.name }}
                                    </small>
                                </div>
                                <div v-html="observation.content.replace(/(?:\r\n|\r|\n)/g, '<br />')"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3" v-if="editData">
                        <div class="card-header d-flex bg-secondary pointer" @click="accordion.inventory = !accordion.inventory">
                            <div class="col-8">
                                <i class="fa fa-list-alt"></i>
                                {{ t('form.inventory') }}
                            </div>
                            <div class="text-right col-4">
                                <i class="fa fa-caret-up" v-if="accordion.inventory"></i>
                                <i class="fa fa-caret-down" v-else></i>
                            </div>
                        </div>
                        <div class="card-body" v-show="accordion.inventory">

                            <table class="table table-responsive">
                                <thead>
                                    <tr>
                                        <th>{{ t('validation.attributes.product') }}</th>
                                        <th>{{ t('validation.attributes.upc') }}</th>
                                        <th>{{ t('validation.attributes.sku') }}</th>
                                        <th width="1%" class="text-center">{{ t('validation.attributes.digital') }}</th>
                                        <th width="1%" class="text-center">{{ t('validation.attributes.physical') }}</th>
                                        <th width="1%" class="text-center">{{ t('validation.attributes.diff') }}</th>
                                        <th width="1%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="product in inventoryUnified">
                                        <td>{{ product.name }}</td>
                                        <td>{{ product.upc }}</td>
                                        <td>{{ product.sku }}</td>
                                        <td class="text-center">{{ product.digital }}</td>
                                        <td class="text-center">{{ product.physical }}</td>
                                        <td class="text-center">{{ product.physical - product.digital }}</td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-primary btn-sm"
                                                data-toggle="modal"
                                                data-target="#modalProductDetail"
                                                @click="openProductDetail(product)"
                                            >
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </td>
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
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header pb-0 pt-2">
                        <div></div>
                        <div>
                            <button class="btn btn-link" type="button" data-dismiss="modal">X</button>
                        </div>
                    </div>
                    <div class="modal-body">


                        <div class="row">
                            <div class="col-lg-3">
                                <label>
                                    {{ t('validation.attributes.scanMethod') }}
                                </label>
                                <div class="mb-3">
                                    <input
                                        type="radio"
                                        name="scan_method"
                                        value="units"
                                        @change="newUPC.qty_per_box = null"
                                        v-model="newUPC.scan_method"> {{ t('form.units') }} |

                                    <input
                                        type="radio"
                                        name="scan_method"
                                        value="boxes"
                                        @change="newUPC.qty_per_box = null"
                                        v-model="newUPC.scan_method"> {{ t('form.boxes') }}
                                </div>
                            </div>

                            <div class="col-lg-3 mb-3">
                                <template v-if="newUPC.scan_method === 'boxes'">
                                    <label>
                                        {{ t('validation.attributes.qtyPerBox') }}
                                    </label>

                                    <input type="number" class="form-control" v-model="newUPC.qty_per_box">
                                </template>
                            </div>

                            <div class="col-lg-5">
                                <label>
                                    {{ t('validation.attributes.upc_or_sku') }}
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

                            <div class="col-12">
                                <div v-if="newUPC.error">
                                    <button
                                            v-if="! newProduct.show"
                                            type="button"
                                            class="btn btn-secondary btn-sm mb-2"
                                            @click="showNewProductForm()"
                                    >
                                        <i class="fa fa-plus"></i>
                                        {{ t('form.addNewProduct') }}
                                    </button>

                                    <div v-if="newProduct.show">
                                        <table class="mb-4">
                                            <thead>
                                            <tr>
                                                <th width="20%">{{ t('validation.attributes.upc') }}</th>
                                                <th width="20%">{{ t('validation.attributes.sku') }}</th>
                                                <th width="20%">{{ t('validation.attributes.name') }}</th>
                                                <th width="20%">{{ t('validation.attributes.serial') }}</th>
                                                <th width="20%">{{ t('validation.attributes.location') }}</th>
                                                <th width="5%"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <input
                                                        type="text"
                                                        id="newProduct_upc"
                                                        name="newProduct_upc"
                                                        class="form-control"
                                                        :class="{'is-invalid': errors.has('newProduct_upc_or_sku', 'product')}"
                                                        v-model="newProduct.upc"
                                                    >

                                                    <input v-if="!newProduct.upc && !newProduct.sku" type="hidden" name="newProduct_upc_or_sku" v-validate data-vv-rules="required" data-vv-scope="product">

                                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('newProduct_upc_or_sku', 'required', 'product')">
                                                        <strong>{{ t('validation.required', {'attribute': 'upc_or_sku'}) }}</strong>
                                                    </span>
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        id="newProduct_sku"
                                                        name="newProduct_sku"
                                                        class="form-control"
                                                        :class="{'is-invalid': errors.has('newProduct_upc_or_sku', 'product')}"
                                                        v-model="newProduct.sku"
                                                    >

                                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('newProduct_upc_or_sku', 'required', 'product')">
                                                        <strong>{{ t('validation.required', {'attribute': 'upc_or_sku'}) }}</strong>
                                                    </span>
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        id="newProduct_name"
                                                        name="newProduct_name"
                                                        class="form-control"
                                                        :class="{'is-invalid': errors.has('newProduct_name', 'product')}"
                                                        v-model="newProduct.name"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        data-vv-scope="product"
                                                    >

                                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('newProduct_name', 'required', 'product')">
                                                        <strong>{{ t('validation.required', {'attribute': 'name'}) }}</strong>
                                                    </span>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" v-model="newProduct.serial">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" v-model="newProduct.location">
                                                </td>
                                                <td>
                                                    <i v-if="newProduct.loading" class="spinner-border spinner-border-sm"></i>

                                                    <button v-else type="button" class="btn btn-success" @click="createProduct()">
                                                        <i class="fa fa-save"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="upc-table-container">
                            <table class="table table-striped table-responsive" v-if="newUPC.physical_inventory_movements.length">
                                <thead>
                                <tr>
                                    <th width="1%">{{ t('validation.attributes.upc') }}</th>
                                    <th width="1%">{{ t('validation.attributes.sku') }}</th>
                                    <th>{{ t('validation.attributes.product') }}</th>
                                    <th width="15%" class="text-center">{{ t('validation.attributes.boxes') }}</th>
                                    <th width="15%" class="text-center">{{ t('validation.attributes.qtyPerBox') }}</th>
                                    <th width="15%" class="text-center">{{ t('validation.attributes.qty') }}</th>
                                    <th width="1%"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(movement, i) in newUPC.physical_inventory_movements">
                                    <td>{{ movement.product.upc }}</td>
                                    <td>{{ movement.product.sku }}</td>
                                    <td>{{ movement.product.name }}</td>
                                    <td class="text-center">

                                        <template v-if="movement.scan_method === 'boxes'">
                                            <input
                                                    type="number"
                                                    :id="'movement-product-' + i"
                                                    class="form-control"
                                                    v-model="movement.boxes_qty"
                                                    @blur="changeQTY(i)"
                                                    :disabled="newUPC.loadingInventory === movement.id"
                                            >
                                        </template>
                                    </td>
                                    <td class="text-center">

                                        <template v-if="movement.scan_method === 'boxes'">
                                            <input
                                                    type="number"
                                                    :id="'movement-product-qty-per-box' + i"
                                                    class="form-control"
                                                    v-model="movement.qty_per_box"
                                                    @blur="changeQTY(i)"
                                                    :disabled="newUPC.loadingInventory === movement.id"
                                            >
                                        </template>
                                    </td>
                                    <td class="text-center">

                                        <template v-if="movement.scan_method === 'boxes'">
                                            {{ movement.qty }}
                                        </template>
                                        <template v-else>
                                            <input
                                                    type="number"
                                                    :id="'movement-product-qty' + i"
                                                    class="form-control"
                                                    v-model="movement.qty"
                                                    @blur="changeQTY(i)"
                                                    :disabled="newUPC.loadingInventory === movement.id"
                                            >
                                        </template>
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

        <!-- Modal -->
        <div class="modal fade" id="modalProductDetail" tabindex="-1" role="dialog" aria-labelledby="modalProductDetail" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" v-if="productDetail.product">
                    <div class="modal-header pb-0 pt-2">
                        <div></div>
                        <div>
                            <button class="btn btn-link" type="button" data-dismiss="modal">X</button>
                        </div>
                    </div>
                    <div class="modal-body">

                        <h5>
                            <strong>
                                {{ productDetail.product.name }}
                                <small>
                                    <template v-if="productDetail.product.upc">
                                        {{ t('validation.attributes.upc') }}:
                                        {{ productDetail.product.upc }}
                                    </template>
                                    <template v-if="productDetail.product.sku">
                                        {{ t('validation.attributes.sku') }}:
                                        {{ productDetail.product.sku }}
                                    </template>
                                </small>
                            </strong>
                        </h5>

                        <div class="upc-table-container mt-3">
                            <table class="table table-striped table-responsive" v-if="productDetail.physical_inventory_movements.length">
                                <thead>
                                <tr>
                                    <th>{{ t('validation.attributes.date') }}</th>
                                    <th>{{ t('validation.attributes.createdBy') }}</th>
                                    <th width="1%" class="text-center pr-3">{{ t('validation.attributes.qty') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(movement, i) in productDetail.physical_inventory_movements">
                                    <td>{{ movement.created_at | date(true) }}</td>
                                    <td>{{ movement.user.name }}</td>
                                    <td class="text-center">{{ movement.qty }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <button
                type="button"
                class="d-none"
                id="openConfirmationChangeProduct"
                data-toggle="modal"
                data-target="#modalConfirmationChangeProduct"
        ></button>
        <div class="modal fade" id="modalConfirmationChangeProduct" tabindex="-1" role="dialog" aria-labelledby="modalConfirmationChangeProduct" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-body">

                        <h3>{{ t('form.confirmationChangeProduct') }}</h3>

                        <table class="table mt-4">
                            <thead>
                                <tr>
                                    <th width="33%">{{ t('validation.attributes.upc') }} / {{ t('validation.attributes.sku') }}</th>
                                    <th width="33%">{{ t('validation.attributes.scanMethod') }}</th>
                                    <th width="33%">{{ t('validation.attributes.qtyPerBox') }}</th>
                                </tr>
                            </thead>
                            <tr>
                                <td>{{ newUPC.upc }}</td>
                                <td>
                                    <input
                                            type="radio"
                                            name="scan_method_modal"
                                            value="units"
                                            @change="newUPC.qty_per_box = null"
                                            v-model="newUPC.scan_method"> {{ t('form.units') }} |

                                    <input
                                            type="radio"
                                            name="scan_method_modal"
                                            value="boxes"
                                            @change="newUPC.qty_per_box = null"
                                            v-model="newUPC.scan_method"> {{ t('form.boxes') }}
                                </td>
                                <td>
                                    <template v-if="newUPC.scan_method === 'boxes'">
                                        <input type="number" class="form-control" v-model="newUPC.qty_per_box">
                                    </template>
                                </td>
                            </tr>
                        </table>

                        <div class="text-center">
                            <button class="btn btn-success btn-lg" type="button" data-dismiss="modal" @click="createMovement()">
                                {{ t('form.accept') }}
                            </button>

                            <button class="btn btn-secondary btn-lg" type="button" data-dismiss="modal" @click="cancelConfirmation()">
                                {{ t('form.cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <audio id="audioError" src="/audio/error.mp3"></audio>
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
                    digital_inventory_observations: [],
                    inventory_crossover_enabled: false
                },
                modal: {
                    errors: [],
                    line: []
                },
                newUPC: {
                    upc: null,
                    scan_method: 'units',
                    qty_per_box: null,
                    loading: false,
                    loadingInventory: null,
                    error: false,
                    physical_inventory_movements: []
                },
                newProduct: {
                    upc: null,
                    sku: null,
                    name: null,
                    serial: null,
                    location: null,
                    loading: false,
                    show: false
                },
                productDetail: {
                    product: null,
                    physical_inventory_movements: []
                },
                accordion: {
                    observation: false,
                    inventory: true
                },
                newObservation: {
                    content: null,
                    loading: false
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

                if (this.newUPC.upc && (this.newUPC.scan_method === 'units' || parseInt(this.newUPC.qty_per_box))) {

                    if (this.newUPC.physical_inventory_movements.length) {

                        const lastMovement = this.newUPC.physical_inventory_movements[0];
                        const isSameProduct =
                            (lastMovement.product.upc && lastMovement.product.upc === this.newUPC.upc) ||
                            (lastMovement.product.sku && lastMovement.product.sku === this.newUPC.upc);

                        if (
                            isSameProduct &&
                            lastMovement.scan_method === this.newUPC.scan_method &&
                            lastMovement.qty_per_box == this.newUPC.qty_per_box
                        ) {
                            if (lastMovement.scan_method === 'boxes') {
                                lastMovement.boxes_qty++;
                            } else {
                                lastMovement.qty++;
                            }

                            this.newUPC.upc = null;

                            return this.changeQTY(0);
                        }

                        if (! isSameProduct) {
                            return this.changeProductConfirmation();
                        }
                    }

                    this.createMovement();
                }
            },

            changeProductConfirmation() {
                document.querySelector('#openConfirmationChangeProduct').click();
                document.querySelector('#audioError').play();
            },

            cancelConfirmation() {
                this.newUPC.upc = null;
            },

            createMovement() {
                this.newUPC.loading = true;

                ApiService.post('/warehouse/inventory', {
                    upc: this.newUPC.upc,
                    digital_inventory_id: this.form.id,
                    scan_method: this.newUPC.scan_method,
                    qty_per_box: this.newUPC.qty_per_box
                })
                    .then(res => {

                        if (! res.data.data) {
                            this.newUPC.error = true;
                            document.querySelector('#audioError').play();
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
            },

            createProduct() {
                this.$validator.validateAll('product').then(res => {

                    if (res) {
                        this.newProduct.loading = true;

                        ApiService.post('/warehouse/product', this.newProduct)
                            .then(res => {

                                if (res.data.success) {
                                    if (this.newProduct.upc) {
                                        this.newUPC.upc = this.newProduct.upc;
                                    } else {
                                        this.newUPC.upc = this.newProduct.sku;
                                    }

                                    this.addProduct();
                                }
                            })
                            .catch(err => {
                                this.newProduct.loading = false;
                            })
                    }
                })
            },

            openProductDetail(product) {
                this.productDetail = {
                    product: {...product},
                    physical_inventory_movements: this.form.physical_inventory_movements.filter(m => m.product_id === product.id)
                };
            },

            changeQTY(index) {
                const movement = this.newUPC.physical_inventory_movements[index];
                const qty = parseInt(movement.qty);
                const qtyPerBox = parseInt(movement.qty_per_box);
                const boxesQty = parseInt(movement.boxes_qty);

                if (
                        qty && qty > 0 &&
                        (movement.scan_method === 'units' || (qtyPerBox && qtyPerBox > 0 && boxesQty && boxesQty > 0))
                    ) {

                    this.newUPC.loadingInventory = movement.id;

                    ApiService.put('/warehouse/inventory/' + movement.id, {
                        qty,
                        qty_per_box: qtyPerBox,
                        boxes_qty: boxesQty
                    })
                        .then(res => {

                            if (res.data.success) {
                                this.newUPC.loadingInventory = null;

                                if (movement.scan_method === 'boxes') {
                                    movement.qty = movement.qty_per_box * movement.boxes_qty;
                                }
                            }

                        }).catch(err => {
                            this.newUPC.loadingInventory = null;
                        });

                } else {
                    if (! qty) {
                        this.newUPC.physical_inventory_movements[index].qty = 1;
                    }
                    if (! boxesQty) {
                        this.newUPC.physical_inventory_movements[index].boxes_qty = 1;
                    }
                    if (! qtyPerBox) {
                        this.newUPC.physical_inventory_movements[index].qty_per_box = 1;
                    }
                    document.querySelector('#audioError').play();
                    //document.querySelector('#movement-product-qty' + index).focus();
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
            },

            showNewProductForm() {
                this.newProduct.show = true;
                this.newProduct.upc = this.newUPC.upc;
                window.setTimeout(() => {
                    document.querySelector('#newProduct_name').focus();
                }, 500)
            },

            addObservation() {
                if (this.newObservation.content) {

                    this.newObservation.loading = true;

                    ApiService.post('/warehouse/inventory/' + this.form.uuid + '/observation', {
                        content: this.newObservation.content
                    }).then(res => {

                        if (res.data.success) {
                            this.newObservation.loading = false;
                            this.newObservation.content = null;
                            this.form.digital_inventory_observations = res.data.data;
                        }

                    }).catch(err => {
                        this.newObservation.loading = false;
                    })
                }
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
                            order: movement.id,
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
                            order: movement.id,
                            digital: 0,
                            physical: movement.qty
                        })
                    }
                });

                products.sort((a, b) => {
                    return b.order - a.order;
                });

                return products;
            }
        },

        watch: {
            "newUPC.upc"(old, value) {
                this.newUPC.error = false;
                this.newProduct = {
                    upc: null,
                    name: null,
                    serial: null,
                    location: null,
                    loading: false,
                    show: false
                };
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

    #modalConfirmationChangeProduct {
        background-color: rgba(0,0,0,0.5);
    }
</style>
