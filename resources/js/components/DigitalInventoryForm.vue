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

                        <div class="col-sm-6 col-lg-4 form-group" v-if="! editData">
                            <label>{{ t('form.template') }}</label>

                            <div>
                                <a href="/excel/digital_inventory_template.xlsx" download>
                                    <i class="fa fa-download"></i>
                                    {{ t('form.download') }}
                                </a>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-4 form-group" v-if="editData">
                            <label>{{ t('validation.attributes.createdBy') }}</label>
                            <input
                                    type="text"
                                    class="form-control"
                                    v-model="form.user.name"
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
                <div class="card-footer" v-if="! editData">
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
    </div>
</template>

<script>
    import ApiService from "../services/ApiService";

    export default {
        props: {
            editData: {
                type: Object,
                required: false
            }
        },

        mounted() {
            if (this.editData) {
                this.form = {...this.editData};
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
                    physical_inventory_movements: []
                },
                modal: {
                    errors: [],
                    line: []
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
            }
        },

        computed: {
            inventoryUnified() {
                const products = [];

                this.form.digital_inventory_movements.forEach(movement => {

                    const currentProduct = products.find(p => p.id === movement.product_id);

                    if (currentProduct) {
                        currentProduct.digital += movement.qty;
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
                        currentProduct.physical += movement.qty;
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
        }
    }
</script>
